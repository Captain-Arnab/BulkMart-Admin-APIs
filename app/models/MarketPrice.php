<?php

class MarketPrice extends Model
{
    /** Products with today's market price override if any */
    public function listWithToday(): array
    {
        $today = date('Y-m-d');
        return $this->fetchAll(
            "SELECT p.id AS product_id, p.name, p.unit, p.price AS catalog_price,
                    mp.id AS market_price_id, mp.price AS market_price, mp.effective_date
             FROM products p
             LEFT JOIN market_prices mp ON mp.product_id = p.id AND mp.effective_date = ?
             WHERE p.is_active = 1
             ORDER BY p.name ASC",
            [$today]
        );
    }

    public function upsertToday(int $productId, float $price, int $adminId): void
    {
        $today = date('Y-m-d');
        $existing = $this->fetchOne(
            'SELECT id FROM market_prices WHERE product_id = ? AND effective_date = ?',
            [$productId, $today]
        );
        if ($existing) {
            $this->execute(
                'UPDATE market_prices SET price = ?, updated_by_admin_id = ? WHERE id = ?',
                [$price, $adminId, $existing['id']]
            );
        } else {
            $this->execute(
                'INSERT INTO market_prices (product_id, price, effective_date, updated_by_admin_id) VALUES (?,?,?,?)',
                [$productId, $price, $today, $adminId]
            );
        }
    }
}
