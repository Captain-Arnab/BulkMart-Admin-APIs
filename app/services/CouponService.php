<?php

/**
 * Coupon validation against offers table (admin-managed).
 */
class CouponService
{
    private PDO $db;
    private Offer $offers;

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo ?? db();
        $this->offers = new Offer($this->db);
    }

    public function getActiveByCode(string $code): ?array
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return null;
        }
        $stmt = $this->db->prepare(
            "SELECT o.*, c.name AS category_name
             FROM offers o
             LEFT JOIN categories c ON c.id = o.category_id
             WHERE o.is_active = 1
               AND o.coupon_code IS NOT NULL
               AND UPPER(o.coupon_code) = ?
               AND (o.valid_from IS NULL OR o.valid_from <= NOW())
               AND (o.valid_till IS NULL OR o.valid_till >= NOW())
             LIMIT 1"
        );
        $stmt->execute([$code]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * @param array<int,array<string,mixed>> $cartItems rows with product_id, quantity, price, category_id (optional)
     * @return array{offer:array,discount:float,eligible_subtotal:float}
     */
    public function calculate(array $offer, array $cartItems): array
    {
        $eligibleQty = 0.0;
        $eligibleSubtotal = 0.0;
        $categoryId = $offer['category_id'] !== null ? (int) $offer['category_id'] : null;

        foreach ($cartItems as $item) {
            $itemCat = isset($item['category_id']) ? (int) $item['category_id'] : null;
            if ($categoryId !== null && $itemCat !== $categoryId) {
                continue;
            }
            $qty = (float) $item['quantity'];
            $line = (float) $item['price'] * $qty;
            $eligibleQty += $qty;
            $eligibleSubtotal += $line;
        }

        $minQty = $offer['min_qty'] !== null && $offer['min_qty'] !== '' ? (float) $offer['min_qty'] : null;
        if ($minQty !== null && $eligibleQty < $minQty) {
            throw new DomainException(
                'Coupon requires minimum quantity of ' . $minQty . ($categoryId ? ' in the offer category' : '') . '.'
            );
        }
        if ($eligibleSubtotal <= 0) {
            throw new DomainException('No eligible cart items for this coupon.');
        }

        $value = (float) $offer['discount_value'];
        $discount = $offer['discount_type'] === 'percentage'
            ? round($eligibleSubtotal * ($value / 100), 2)
            : round(min($value, $eligibleSubtotal), 2);

        return [
            'offer'              => $offer,
            'discount'           => $discount,
            'eligible_subtotal'  => round($eligibleSubtotal, 2),
        ];
    }

    public function getCoupon(int $customerId): ?string
    {
        $stmt = $this->db->prepare('SELECT coupon_code FROM cart_meta WHERE customer_id = ?');
        $stmt->execute([$customerId]);
        $code = $stmt->fetchColumn();
        return $code !== false && $code !== null && $code !== '' ? (string) $code : null;
    }

    public function setCoupon(int $customerId, ?string $code): void
    {
        $existing = $this->db->prepare('SELECT customer_id FROM cart_meta WHERE customer_id = ?');
        $existing->execute([$customerId]);
        if ($existing->fetch()) {
            $this->db->prepare('UPDATE cart_meta SET coupon_code = ? WHERE customer_id = ?')
                ->execute([$code, $customerId]);
        } else {
            $this->db->prepare('INSERT INTO cart_meta (customer_id, coupon_code) VALUES (?,?)')
                ->execute([$customerId, $code]);
        }
    }

    public function clearCoupon(int $customerId): void
    {
        $this->setCoupon($customerId, null);
    }
}
