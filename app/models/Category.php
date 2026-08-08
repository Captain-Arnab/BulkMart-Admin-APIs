<?php

class Category extends Model
{
    protected string $table = 'categories';

    public function all(): array
    {
        return $this->fetchAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
             FROM categories c
             ORDER BY c.name ASC"
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    public function findByName(string $name): ?array
    {
        return $this->fetchOne('SELECT * FROM categories WHERE name = ?', [$name]);
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO categories (name, image_url) VALUES (?, ?)',
            [$data['name'], $data['image_url'] ?? null]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return $this->execute(
            'UPDATE categories SET name = ?, image_url = ? WHERE id = ?',
            [$data['name'], $data['image_url'] ?? null, $id]
        );
    }

    public function productCount(int $id): int
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS c FROM products WHERE category_id = ?', [$id]);
        return (int) ($row['c'] ?? 0);
    }

    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM categories WHERE id = ?', [$id]);
    }

    public function options(): array
    {
        return $this->fetchAll('SELECT id, name FROM categories ORDER BY name ASC');
    }
}
