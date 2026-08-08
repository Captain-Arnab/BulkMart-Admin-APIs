<?php

/**
 * Order status transitions, stock adjustments, notifications.
 */
class OrderService
{
    private PDO $db;
    private Order $orders;

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo ?? db();
        $this->orders = new Order($this->db);
    }

    public function changeStatus(int $orderId, string $newStatus, int $adminId, ?string $note = null): void
    {
        $order = $this->orders->find($orderId);
        if (!$order) {
            throw new RuntimeException('Order not found.');
        }

        $current = $order['status'];
        if ($current === $newStatus) {
            return;
        }

        $allowed = Order::nextStatuses($current);
        if (!in_array($newStatus, $allowed, true)) {
            throw new RuntimeException(
                'Invalid status change from "' . Order::STATUS_LABELS[$current] . '" to "' . (Order::STATUS_LABELS[$newStatus] ?? $newStatus) . '".'
            );
        }

        $this->db->beginTransaction();
        try {
            if ($newStatus === 'confirmed' && $current === 'placed') {
                $this->deductStock($orderId);
            }

            if ($newStatus === 'cancelled' && $this->wasStockDeducted($orderId, $current)) {
                $this->restoreStock($orderId);
            }

            $fields = ['status' => $newStatus];
            if ($newStatus === 'delivered') {
                $fields['delivered_at'] = date('Y-m-d H:i:s');
            }
            $this->orders->updateFields($orderId, $fields);
            $this->logStatus($orderId, $newStatus, $adminId, $note);
            $this->notifyCustomer((int) $order['customer_id'], $orderId, $newStatus, $order['order_number']);

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function assignDeliveryManager(int $orderId, int $managerId, int $adminId): void
    {
        $order = $this->orders->find($orderId);
        if (!$order) {
            throw new RuntimeException('Order not found.');
        }
        if ($order['status'] === 'cancelled' || $order['status'] === 'delivered') {
            throw new RuntimeException('Cannot assign a delivery manager to a cancelled/delivered order.');
        }

        $mgr = $this->db->prepare("SELECT id, name FROM admin_users WHERE id = ? AND role_type = 'delivery_manager' AND is_active = 1");
        $mgr->execute([$managerId]);
        $manager = $mgr->fetch();
        if (!$manager) {
            throw new RuntimeException('Selected user is not an active delivery manager.');
        }

        $this->db->beginTransaction();
        try {
            // Auto-confirm on assign if still placed (stock-safe)
            if ($order['status'] === 'placed') {
                $this->deductStock($orderId);
                $this->orders->updateFields($orderId, [
                    'status' => 'confirmed',
                    'assigned_delivery_manager_id' => $managerId,
                ]);
                $this->logStatus($orderId, 'confirmed', $adminId, 'Auto-confirmed on delivery assignment');
                $this->notifyCustomer((int) $order['customer_id'], $orderId, 'confirmed', $order['order_number']);
            } else {
                $this->orders->updateFields($orderId, [
                    'assigned_delivery_manager_id' => $managerId,
                ]);
            }

            $this->insertNotification(
                (int) $order['customer_id'],
                'Order update',
                'Your order ' . $order['order_number'] . ' is confirmed and assigned for delivery.',
                'order',
                $orderId
            );

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function setDeliveryDate(int $orderId, string $date, int $adminId): void
    {
        $order = $this->orders->find($orderId);
        if (!$order) {
            throw new RuntimeException('Order not found.');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new RuntimeException('Invalid delivery date.');
        }

        $this->db->beginTransaction();
        try {
            if ($order['status'] === 'confirmed') {
                $this->orders->updateFields($orderId, [
                    'estimated_delivery_date' => $date,
                    'status' => 'delivery_date_set',
                ]);
                $this->logStatus($orderId, 'delivery_date_set', $adminId, 'ETA set to ' . $date);
                $this->notifyCustomer((int) $order['customer_id'], $orderId, 'delivery_date_set', $order['order_number'], $date);
            } elseif ($order['status'] === 'delivery_date_set') {
                $this->orders->updateFields($orderId, ['estimated_delivery_date' => $date]);
                $this->logStatus($orderId, 'delivery_date_set', $adminId, 'ETA updated to ' . $date);
            } else {
                throw new RuntimeException('Delivery date can only be set when order is confirmed or already has a date.');
            }
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function markOutForDelivery(int $orderId, int $adminId): void
    {
        $this->changeStatus($orderId, 'out_for_delivery', $adminId);
    }

    public function markDelivered(int $orderId, int $adminId, float $codCollected, bool $codMismatchAck = false): array
    {
        $order = $this->orders->find($orderId);
        if (!$order) {
            throw new RuntimeException('Order not found.');
        }

        $total = (float) $order['total'];
        $mismatch = abs($codCollected - $total) > 0.009;
        if ($mismatch && !$codMismatchAck) {
            return [
                'needs_confirm' => true,
                'order_total'   => $total,
                'cod_collected' => $codCollected,
                'message'       => 'COD amount (₹' . number_format($codCollected, 2) . ') does not match order total (₹' . number_format($total, 2) . '). Confirm to proceed anyway.',
            ];
        }

        $note = 'COD collected ₹' . number_format($codCollected, 2);
        if ($mismatch) {
            $note .= ' (mismatch with total ₹' . number_format($total, 2) . ')';
        }
        $this->changeStatus($orderId, 'delivered', $adminId, $note);
        return ['needs_confirm' => false];
    }

    private function deductStock(int $orderId): void
    {
        $items = $this->orders->items($orderId);
        foreach ($items as $item) {
            $stmt = $this->db->prepare('SELECT id, name, stock FROM products WHERE id = ? FOR UPDATE');
            $stmt->execute([(int) $item['product_id']]);
            $product = $stmt->fetch();
            if (!$product) {
                throw new RuntimeException('Product missing for line item.');
            }
            $newStock = (float) $product['stock'] - (float) $item['quantity'];
            if ($newStock < 0) {
                throw new RuntimeException(
                    'Cannot confirm — insufficient stock for "' . $product['name'] . '" (available ' . $product['stock'] . ', ordered ' . $item['quantity'] . ').'
                );
            }
            $inStock = $newStock > 0 ? 1 : 0;
            $upd = $this->db->prepare('UPDATE products SET stock = ?, in_stock = ? WHERE id = ?');
            $upd->execute([$newStock, $inStock, $product['id']]);
        }
    }

    private function restoreStock(int $orderId): void
    {
        $items = $this->orders->items($orderId);
        foreach ($items as $item) {
            $stmt = $this->db->prepare('SELECT id, stock FROM products WHERE id = ? FOR UPDATE');
            $stmt->execute([(int) $item['product_id']]);
            $product = $stmt->fetch();
            if (!$product) {
                continue;
            }
            $newStock = (float) $product['stock'] + (float) $item['quantity'];
            $upd = $this->db->prepare('UPDATE products SET stock = ?, in_stock = 1 WHERE id = ?');
            $upd->execute([$newStock, $product['id']]);
        }
    }

    private function wasStockDeducted(int $orderId, string $currentStatus): bool
    {
        if (in_array($currentStatus, ['confirmed', 'delivery_date_set', 'out_for_delivery', 'delivered'], true)) {
            return true;
        }
        $row = $this->fetchOne(
            "SELECT COUNT(*) AS c FROM order_status_log WHERE order_id = ? AND status = 'confirmed'",
            [$orderId]
        );
        return ((int) ($row['c'] ?? 0)) > 0;
    }

    private function logStatus(int $orderId, string $status, int $adminId, ?string $note = null): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO order_status_log (order_id, status, changed_by_admin_id, note) VALUES (?,?,?,?)'
        );
        $stmt->execute([$orderId, $status, $adminId, $note]);
    }

    private function notifyCustomer(int $customerId, int $orderId, string $status, string $orderNumber, ?string $eta = null): void
    {
        $copy = match ($status) {
            'confirmed' => [
                'title' => 'Order confirmed',
                'body'  => "Your order {$orderNumber} has been confirmed. We'll notify you when it's out for delivery.",
            ],
            'delivery_date_set' => [
                'title' => 'Delivery date set',
                'body'  => "Your order {$orderNumber} is scheduled for delivery on {$eta}.",
            ],
            'out_for_delivery' => [
                'title' => 'Out for delivery',
                'body'  => "Good news! Your order {$orderNumber} is out for delivery.",
            ],
            'delivered' => [
                'title' => 'Order delivered',
                'body'  => "Your order {$orderNumber} has been delivered. Thank you for ordering with VeggiiCart!",
            ],
            'cancelled' => [
                'title' => 'Order cancelled',
                'body'  => "Your order {$orderNumber} has been cancelled. Contact support if you have questions.",
            ],
            default => [
                'title' => 'Order update',
                'body'  => "Your order {$orderNumber} status is now " . (Order::STATUS_LABELS[$status] ?? $status) . '.',
            ],
        };

        $this->insertNotification($customerId, $copy['title'], $copy['body'], 'order', $orderId);
    }

    private function insertNotification(int $customerId, string $title, string $body, string $type, ?int $relatedId): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notifications (customer_id, title, body, type, related_id, is_read) VALUES (?,?,?,?,?,0)'
        );
        $stmt->execute([$customerId, $title, $body, $type, $relatedId]);
    }

    private function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
