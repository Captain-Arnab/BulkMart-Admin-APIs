<?php

/**
 * Place customer COD orders — validates stock with row locks (same pattern as OrderService::deductStock).
 * Stock is deducted when admin confirms (OrderService), not at place time.
 */
class CheckoutService
{
    private PDO $db;
    private Order $orders;
    private Cart $cart;
    private Address $addresses;
    private CouponService $coupons;
    private ServiceablePincode $pincodes;

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo ?? db();
        $this->orders = new Order($this->db);
        $this->cart = new Cart($this->db);
        $this->addresses = new Address($this->db);
        $this->coupons = new CouponService($this->db);
        $this->pincodes = new ServiceablePincode($this->db);
    }

    /**
     * @param array{address_id:int,delivery_slot_id?:int|null,notes?:string|null} $input
     * @return array{order:array,items:array}
     */
    public function placeCodOrder(int $customerId, array $input): array
    {
        $addressId = (int) ($input['address_id'] ?? 0);
        $address = $this->addresses->findForCustomer($addressId, $customerId);
        if (!$address) {
            throw new DomainException('Delivery address not found.');
        }
        $this->assertAddressServiceable($address);

        $cartItems = $this->cart->itemsForCustomer($customerId);
        if ($cartItems === []) {
            throw new DomainException('Your cart is empty.');
        }

        $this->db->beginTransaction();
        try {
            $built = $this->buildLinesFromCartQuantities($cartItems);
            $result = $this->insertOrder(
                $customerId,
                $addressId,
                $built['lines'],
                $built['subtotal'],
                null,
                (string) ($input['notes'] ?? '')
            );
            $this->cart->clear($customerId);
            $this->coupons->clearCoupon($customerId);
            $this->db->commit();

            return [
                'order' => $this->orders->find($result['order_id']) ?? [],
                'items' => $this->orders->items($result['order_id']),
            ];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Multi-address checkout: one cart → N orders sharing batch_id.
     *
     * @param array{addresses:list<array{address_id:int,delivery_slot_id?:mixed,notes?:string,items:list<array{product_id:int,quantity:float|int}>}>} $input
     * @return array{batch_id:string,orders:list<array{order:array,items:array}>}
     */
    public function placeMultiAddressCodOrder(int $customerId, array $input): array
    {
        $blocks = $input['addresses'] ?? null;
        if (!is_array($blocks) || count($blocks) < 2) {
            throw new DomainException('Select at least two delivery addresses for a split order.');
        }

        $cartItems = $this->cart->itemsForCustomer($customerId);
        if ($cartItems === []) {
            throw new DomainException('Your cart is empty.');
        }

        $cartByProduct = [];
        foreach ($cartItems as $ci) {
            $pid = (int) $ci['product_id'];
            $cartByProduct[$pid] = ($cartByProduct[$pid] ?? 0) + (float) $ci['quantity'];
        }

        $allocatedByProduct = [];
        $normalizedBlocks = [];

        foreach ($blocks as $idx => $block) {
            if (!is_array($block)) {
                throw new DomainException('Invalid address block at index ' . $idx . '.');
            }
            $addressId = (int) ($block['address_id'] ?? 0);
            $address = $this->addresses->findForCustomer($addressId, $customerId);
            if (!$address) {
                throw new DomainException('Delivery address not found (id ' . $addressId . ').');
            }
            $this->assertAddressServiceable($address);

            $items = $block['items'] ?? [];
            if (!is_array($items) || $items === []) {
                throw new DomainException('Each address must have at least one allocated item.');
            }

            $blockLines = [];
            foreach ($items as $item) {
                $pid = (int) ($item['product_id'] ?? 0);
                $qty = (float) ($item['quantity'] ?? 0);
                if ($pid < 1 || $qty <= 0) {
                    throw new DomainException('Invalid product allocation for address #' . $addressId . '.');
                }
                if (!isset($cartByProduct[$pid])) {
                    throw new DomainException('Product #' . $pid . ' is not in your cart.');
                }
                $allocatedByProduct[$pid] = ($allocatedByProduct[$pid] ?? 0) + $qty;
                $blockLines[] = ['product_id' => $pid, 'quantity' => $qty];
            }

            $normalizedBlocks[] = [
                'address_id' => $addressId,
                'notes'      => trim((string) ($block['notes'] ?? '')),
                'items'      => $blockLines,
            ];
        }

        // Allocations must match cart exactly (all products, exact quantities).
        foreach ($cartByProduct as $pid => $cartQty) {
            $alloc = round((float) ($allocatedByProduct[$pid] ?? 0), 2);
            if (abs($alloc - round($cartQty, 2)) > 0.001) {
                throw new DomainException(
                    'Allocated quantity for a product does not match the cart total. '
                    . 'Each product must be fully distributed across addresses.'
                );
            }
        }
        foreach ($allocatedByProduct as $pid => $alloc) {
            if (!isset($cartByProduct[$pid])) {
                throw new DomainException('Extra product allocation is not allowed.');
            }
        }

        $batchId = $this->generateBatchId();

        $this->db->beginTransaction();
        try {
            // Validate stock/MOQ once against combined cart quantities (same as single checkout).
            $this->buildLinesFromCartQuantities($cartItems);

            $created = [];
            foreach ($normalizedBlocks as $block) {
                $pseudoCart = [];
                foreach ($block['items'] as $item) {
                    $pseudoCart[] = [
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'name'       => '',
                    ];
                }
                $built = $this->buildLinesFromQuantities($pseudoCart, false);
                $result = $this->insertOrder(
                    $customerId,
                    $block['address_id'],
                    $built['lines'],
                    $built['subtotal'],
                    $batchId,
                    $block['notes']
                );
                $created[] = [
                    'order' => $this->orders->find($result['order_id']) ?? [],
                    'items' => $this->orders->items($result['order_id']),
                ];
            }

            $this->cart->clear($customerId);
            $this->coupons->clearCoupon($customerId);

            $this->db->prepare(
                'INSERT INTO notifications (customer_id, title, body, type, related_id, is_read)
                 VALUES (?,?,?,?,?,0)'
            )->execute([
                $customerId,
                'Multi-location order placed',
                count($created) . ' orders were placed across your selected addresses. Pay on delivery.',
                'order',
                (int) ($created[0]['order']['id'] ?? 0) ?: null,
            ]);

            $this->db->commit();
            return ['batch_id' => $batchId, 'orders' => $created];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /** @param array<string,mixed> $address */
    private function assertAddressServiceable(array $address): void
    {
        $pin = trim((string) ($address['pincode'] ?? ''));
        if ($pin === '' || !$this->pincodes->isServiceable($pin)) {
            throw new DomainException(ServiceablePincode::unserviceableMessage());
        }
    }

    /**
     * Lock products and build order lines from cart-like rows.
     * When $enforceMoqPerLine is true (normal cart), each line must meet MOQ.
     * For split pieces, pass false and validate MOQ on totals separately via cart path.
     *
     * @param list<array<string,mixed>> $rows
     * @return array{lines:list<array<string,mixed>>,subtotal:float}
     */
    private function buildLinesFromCartQuantities(array $rows): array
    {
        return $this->buildLinesFromQuantities($rows, true);
    }

    /**
     * @param list<array<string,mixed>> $rows
     * @return array{lines:list<array<string,mixed>>,subtotal:float}
     */
    private function buildLinesFromQuantities(array $rows, bool $enforceMoqPerLine): array
    {
        $lines = [];
        $subtotal = 0.0;

        foreach ($rows as $ci) {
            $stmt = $this->db->prepare(
                'SELECT id, name, unit, moq, price, stock, is_active, in_stock, category_id FROM products WHERE id = ? FOR UPDATE'
            );
            $stmt->execute([(int) $ci['product_id']]);
            $product = $stmt->fetch();
            if (!$product || !(int) $product['is_active']) {
                throw new DomainException('Product "' . ($ci['name'] ?? '') . '" is no longer available.');
            }

            $qty = (float) $ci['quantity'];
            $moq = (float) $product['moq'];
            $stock = (float) $product['stock'];

            if ($enforceMoqPerLine && $qty < $moq) {
                throw new DomainException(
                    'Quantity for "' . $product['name'] . '" is below MOQ (' . $moq . ').'
                );
            }
            if ($stock < $qty || (int) $product['in_stock'] === 0) {
                throw new DomainException(
                    'Insufficient stock for "' . $product['name'] . '" (available ' . $stock . ', requested ' . $qty . ').'
                );
            }

            $unitPrice = (float) $product['price'];
            $lineTotal = round($unitPrice * $qty, 2);
            $subtotal += $lineTotal;
            $lines[] = [
                'product_id'            => (int) $product['id'],
                'product_name_snapshot' => $product['name'],
                'unit_snapshot'         => $product['unit'],
                'quantity'              => $qty,
                'unit_price_snapshot'   => $unitPrice,
                'line_total'            => $lineTotal,
                'category_id'           => (int) $product['category_id'],
                'price'                 => $unitPrice,
            ];
        }

        return ['lines' => $lines, 'subtotal' => round($subtotal, 2)];
    }

    /**
     * @param list<array<string,mixed>> $lines
     * @return array{order_id:int,order_number:string}
     */
    private function insertOrder(
        int $customerId,
        int $addressId,
        array $lines,
        float $subtotal,
        ?string $batchId,
        string $notes = ''
    ): array {
        $deliveryFee = (float) (app_config('checkout.delivery_fee') ?? 0);
        $settingsFee = (new AppSetting($this->db))->get('delivery_fee');
        if ($settingsFee !== null && $settingsFee !== '') {
            $deliveryFee = (float) $settingsFee;
        }

        $discount = 0.0;
        $couponCode = null;
        // Coupons apply only on single-address checkout (full cart). Multi-address skips coupon for fairness.
        if ($batchId === null) {
            $appliedCode = $this->coupons->getCoupon($customerId);
            if ($appliedCode) {
                $offer = $this->coupons->getActiveByCode($appliedCode);
                if (!$offer) {
                    throw new DomainException('Applied coupon is no longer valid.');
                }
                $calc = $this->coupons->calculate($offer, $lines);
                $discount = $calc['discount'];
                $couponCode = strtoupper((string) $offer['coupon_code']);
            }
        }

        $total = round(max(0, $subtotal - $discount) + $deliveryFee, 2);
        $orderNumber = $this->generateOrderNumber();

        $this->db->prepare(
            'INSERT INTO orders
              (order_number, customer_id, address_id, status, subtotal, delivery_fee, discount_amount, coupon_code, batch_id, total,
               payment_method, placed_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())'
        )->execute([
            $orderNumber,
            $customerId,
            $addressId,
            'placed',
            $subtotal,
            $deliveryFee,
            $discount,
            $couponCode,
            $batchId,
            $total,
            'COD',
        ]);
        $orderId = (int) $this->db->lastInsertId();

        $ins = $this->db->prepare(
            'INSERT INTO order_items
              (order_id, product_id, product_name_snapshot, unit_snapshot, quantity, unit_price_snapshot, line_total)
             VALUES (?,?,?,?,?,?,?)'
        );
        foreach ($lines as $line) {
            $ins->execute([
                $orderId,
                $line['product_id'],
                $line['product_name_snapshot'],
                $line['unit_snapshot'],
                $line['quantity'],
                $line['unit_price_snapshot'],
                $line['line_total'],
            ]);
        }

        $note = $batchId
            ? 'Placed via multi-address checkout (batch ' . $batchId . ')'
            : 'Placed via app (COD)';
        if ($notes !== '') {
            $note .= ' — ' . $notes;
        }
        $this->db->prepare(
            'INSERT INTO order_status_log (order_id, status, changed_by_admin_id, note) VALUES (?,?,NULL,?)'
        )->execute([$orderId, 'placed', $note]);

        if ($batchId === null) {
            $this->db->prepare(
                'INSERT INTO notifications (customer_id, title, body, type, related_id, is_read)
                 VALUES (?,?,?,?,?,0)'
            )->execute([
                $customerId,
                'Order placed',
                "Your order {$orderNumber} has been placed successfully. Pay on delivery.",
                'order',
                $orderId,
            ]);
        }

        return ['order_id' => $orderId, 'order_number' => $orderNumber];
    }

    private function generateOrderNumber(): string
    {
        return 'VC-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    private function generateBatchId(): string
    {
        // UUID v4
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
