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

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo ?? db();
        $this->orders = new Order($this->db);
        $this->cart = new Cart($this->db);
        $this->addresses = new Address($this->db);
        $this->coupons = new CouponService($this->db);
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

        $cartItems = $this->cart->itemsForCustomer($customerId);
        if ($cartItems === []) {
            throw new DomainException('Your cart is empty.');
        }

        $deliveryFee = (float) (app_config('checkout.delivery_fee') ?? 0);
        $settingsFee = (new AppSetting($this->db))->get('delivery_fee');
        if ($settingsFee !== null && $settingsFee !== '') {
            $deliveryFee = (float) $settingsFee;
        }

        $this->db->beginTransaction();
        try {
            $lines = [];
            $subtotal = 0.0;

            foreach ($cartItems as $ci) {
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

                if ($qty < $moq) {
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

            $subtotal = round($subtotal, 2);
            $discount = 0.0;
            $couponCode = null;
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

            $total = round(max(0, $subtotal - $discount) + $deliveryFee, 2);
            $orderNumber = $this->generateOrderNumber();

            $this->db->prepare(
                'INSERT INTO orders
                  (order_number, customer_id, address_id, status, subtotal, delivery_fee, discount_amount, coupon_code, total,
                   payment_method, placed_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,NOW())'
            )->execute([
                $orderNumber,
                $customerId,
                $addressId,
                'placed',
                $subtotal,
                $deliveryFee,
                $discount,
                $couponCode,
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

            $this->db->prepare(
                'INSERT INTO order_status_log (order_id, status, changed_by_admin_id, note) VALUES (?,?,NULL,?)'
            )->execute([$orderId, 'placed', 'Placed via app (COD)']);

            $this->cart->clear($customerId);
            $this->coupons->clearCoupon($customerId);

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

            $this->db->commit();

            $order = $this->orders->find($orderId);
            $items = $this->orders->items($orderId);
            return ['order' => $order ?? [], 'items' => $items];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    private function generateOrderNumber(): string
    {
        return 'VC-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
