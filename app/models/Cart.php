<?php

class Cart extends Model
{
    public function itemsForCustomer(int $customerId): array
    {
        return $this->fetchAll(
            "SELECT ci.*,
                    p.name, p.unit, p.moq, p.price, p.stock, p.image_url, p.is_active, p.in_stock,
                    p.category_id, c.name AS category_name
             FROM cart_items ci
             INNER JOIN products p ON p.id = ci.product_id
             INNER JOIN categories c ON c.id = p.category_id
             WHERE ci.customer_id = ?
             ORDER BY ci.id ASC",
            [$customerId]
        );
    }

    public function findItem(int $itemId, int $customerId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM cart_items WHERE id = ? AND customer_id = ?',
            [$itemId, $customerId]
        );
    }

    public function findByProduct(int $customerId, int $productId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM cart_items WHERE customer_id = ? AND product_id = ?',
            [$customerId, $productId]
        );
    }

    public function upsertItem(int $customerId, int $productId, float $quantity): int
    {
        $existing = $this->findByProduct($customerId, $productId);
        if ($existing) {
            $this->execute(
                'UPDATE cart_items SET quantity = ? WHERE id = ?',
                [$quantity, (int) $existing['id']]
            );
            return (int) $existing['id'];
        }
        $this->execute(
            'INSERT INTO cart_items (customer_id, product_id, quantity) VALUES (?,?,?)',
            [$customerId, $productId, $quantity]
        );
        return (int) $this->db->lastInsertId();
    }

    public function updateQuantity(int $itemId, int $customerId, float $quantity): bool
    {
        return $this->execute(
            'UPDATE cart_items SET quantity = ? WHERE id = ? AND customer_id = ?',
            [$quantity, $itemId, $customerId]
        );
    }

    public function removeItem(int $itemId, int $customerId): bool
    {
        return $this->execute(
            'DELETE FROM cart_items WHERE id = ? AND customer_id = ?',
            [$itemId, $customerId]
        );
    }

    public function clear(int $customerId): bool
    {
        return $this->execute('DELETE FROM cart_items WHERE customer_id = ?', [$customerId]);
    }
}
