<?php

class Product extends Model
{
    protected string $table = 'products';

    public const LOW_STOCK_THRESHOLD = 10;

    public function allWithCategory(?string $q = null, ?int $categoryId = null): array
    {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                WHERE 1=1";
        $params = [];

        if ($q !== null && $q !== '') {
            $sql .= " AND (p.name LIKE ? OR p.item_code LIKE ? OR p.batch_no LIKE ?)";
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY p.name ASC";
        return $this->fetchAll($sql, $params);
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT p.*, c.name AS category_name
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.id = ?",
            [$id]
        );
    }

    public function findByItemCode(string $code): ?array
    {
        return $this->fetchOne('SELECT * FROM products WHERE item_code = ?', [$code]);
    }

    public function create(array $data): int
    {
        $this->execute(
            "INSERT INTO products
              (category_id, name, unit, moq, price, stock, image_url, batch_no, item_code,
               description, grade, origin, in_stock, is_active)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [
                $data['category_id'],
                $data['name'],
                $data['unit'],
                $data['moq'],
                $data['price'],
                $data['stock'],
                $data['image_url'] ?? null,
                $data['batch_no'] ?? null,
                $data['item_code'] ?? null,
                $data['description'] ?? null,
                $data['grade'] ?? null,
                $data['origin'] ?? null,
                !empty($data['in_stock']) ? 1 : 0,
                !empty($data['is_active']) ? 1 : 0,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return $this->execute(
            "UPDATE products SET
                category_id = ?, name = ?, unit = ?, moq = ?, price = ?, stock = ?,
                image_url = ?, batch_no = ?, item_code = ?, description = ?, grade = ?,
                origin = ?, in_stock = ?, is_active = ?
             WHERE id = ?",
            [
                $data['category_id'],
                $data['name'],
                $data['unit'],
                $data['moq'],
                $data['price'],
                $data['stock'],
                $data['image_url'] ?? null,
                $data['batch_no'] ?? null,
                $data['item_code'] ?? null,
                $data['description'] ?? null,
                $data['grade'] ?? null,
                $data['origin'] ?? null,
                !empty($data['in_stock']) ? 1 : 0,
                !empty($data['is_active']) ? 1 : 0,
                $id,
            ]
        );
    }

    public function updateStock(int $id, float $stock): bool
    {
        $inStock = $stock > 0 ? 1 : 0;
        return $this->execute(
            'UPDATE products SET stock = ?, in_stock = ? WHERE id = ?',
            [$stock, $inStock, $id]
        );
    }

    public function setActive(int $id, bool $active): bool
    {
        return $this->execute('UPDATE products SET is_active = ? WHERE id = ?', [$active ? 1 : 0, $id]);
    }

    public function countAll(): int
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS c FROM products');
        return (int) ($row['c'] ?? 0);
    }

    public static function stockStatus(array $product): string
    {
        $stock = (float) ($product['stock'] ?? 0);
        $flag = (int) ($product['in_stock'] ?? 1);
        if ($flag === 0 || $stock <= 0) {
            return 'out';
        }
        if ($stock <= self::LOW_STOCK_THRESHOLD) {
            return 'low';
        }
        return 'ok';
    }

    public static function stockBadge(array $product): array
    {
        return match (self::stockStatus($product)) {
            'out' => ['label' => 'Out of stock', 'class' => 'bg-danger'],
            'low' => ['label' => 'Low stock', 'class' => 'bg-warning text-dark'],
            default => ['label' => 'In stock', 'class' => 'bg-success'],
        };
    }
}
