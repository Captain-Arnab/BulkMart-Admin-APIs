<?php

class Offer extends Model
{
    public function all(): array
    {
        return $this->fetchAll(
            'SELECT o.*, c.name AS category_name
             FROM offers o
             LEFT JOIN categories c ON c.id = o.category_id
             ORDER BY o.id DESC'
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM offers WHERE id = ?', [$id]);
    }

    public function create(array $d): int
    {
        $this->execute(
            'INSERT INTO offers (title, discount_type, discount_value, min_qty, category_id, coupon_code, valid_from, valid_till, is_active)
             VALUES (?,?,?,?,?,?,?,?,?)',
            [
                $d['title'], $d['discount_type'], $d['discount_value'],
                $d['min_qty'] !== '' && $d['min_qty'] !== null ? $d['min_qty'] : null,
                $d['category_id'] ?: null, $d['coupon_code'] ?: null,
                $d['valid_from'] ?: null, $d['valid_till'] ?: null,
                !empty($d['is_active']) ? 1 : 0,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        return $this->execute(
            'UPDATE offers SET title=?, discount_type=?, discount_value=?, min_qty=?, category_id=?, coupon_code=?, valid_from=?, valid_till=?, is_active=? WHERE id=?',
            [
                $d['title'], $d['discount_type'], $d['discount_value'],
                $d['min_qty'] !== '' && $d['min_qty'] !== null ? $d['min_qty'] : null,
                $d['category_id'] ?: null, $d['coupon_code'] ?: null,
                $d['valid_from'] ?: null, $d['valid_till'] ?: null,
                !empty($d['is_active']) ? 1 : 0, $id,
            ]
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM offers WHERE id = ?', [$id]);
    }
}
