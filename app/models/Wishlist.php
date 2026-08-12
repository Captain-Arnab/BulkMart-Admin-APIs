<?php

class Wishlist extends Model
{
    public function listForCustomer(int $customerId): array
    {
        return $this->fetchAll(
            "SELECT w.id, w.product_id, w.created_at,
                    p.name, p.unit, p.moq, p.price, p.stock, p.image_url, p.is_active, p.in_stock,
                    p.category_id, c.name AS category_name
             FROM wishlists w
             INNER JOIN products p ON p.id = w.product_id
             INNER JOIN categories c ON c.id = p.category_id
             WHERE w.customer_id = ?
             ORDER BY w.created_at DESC",
            [$customerId]
        );
    }

    public function findItem(int $id, int $customerId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM wishlists WHERE id = ? AND customer_id = ?',
            [$id, $customerId]
        );
    }

    public function findByProduct(int $customerId, int $productId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM wishlists WHERE customer_id = ? AND product_id = ?',
            [$customerId, $productId]
        );
    }

    public function add(int $customerId, int $productId): int
    {
        $existing = $this->findByProduct($customerId, $productId);
        if ($existing) {
            return (int) $existing['id'];
        }
        $this->execute(
            'INSERT INTO wishlists (customer_id, product_id) VALUES (?,?)',
            [$customerId, $productId]
        );
        return (int) $this->db->lastInsertId();
    }

    public function remove(int $id, int $customerId): bool
    {
        return $this->execute(
            'DELETE FROM wishlists WHERE id = ? AND customer_id = ?',
            [$id, $customerId]
        );
    }

    public function removeByProduct(int $customerId, int $productId): bool
    {
        return $this->execute(
            'DELETE FROM wishlists WHERE customer_id = ? AND product_id = ?',
            [$customerId, $productId]
        );
    }
}
