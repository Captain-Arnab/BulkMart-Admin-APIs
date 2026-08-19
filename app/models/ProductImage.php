<?php

class ProductImage extends Model
{
    protected string $table = 'product_images';

    /** @return array<int,array<string,mixed>> */
    public function forProduct(int $productId): array
    {
        return $this->fetchAll(
            'SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC, id ASC',
            [$productId]
        );
    }

    public function add(int $productId, string $url, int $sortOrder = 0, bool $primary = false): int
    {
        if ($primary) {
            $this->execute('UPDATE product_images SET is_primary = 0 WHERE product_id = ?', [$productId]);
        }
        $this->execute(
            'INSERT INTO product_images (product_id, image_url, sort_order, is_primary) VALUES (?,?,?,?)',
            [$productId, $url, $sortOrder, $primary ? 1 : 0]
        );
        $id = (int) $this->db->lastInsertId();
        $this->syncProductPrimary($productId);
        return $id;
    }

    public function deleteForProduct(int $id, int $productId): bool
    {
        $row = $this->fetchOne(
            'SELECT * FROM product_images WHERE id = ? AND product_id = ?',
            [$id, $productId]
        );
        if (!$row) {
            return false;
        }
        $ok = $this->execute(
            'DELETE FROM product_images WHERE id = ? AND product_id = ?',
            [$id, $productId]
        );
        $this->deleteFile((string) $row['image_url']);
        $remaining = $this->forProduct($productId);
        if ($remaining !== [] && !array_filter($remaining, static fn (array $r) => (int) $r['is_primary'] === 1)) {
            $this->execute(
                'UPDATE product_images SET is_primary = 1 WHERE id = ?',
                [(int) $remaining[0]['id']]
            );
        }
        $this->syncProductPrimary($productId);
        return $ok;
    }

    public function ensurePrimary(int $productId): void
    {
        $rows = $this->forProduct($productId);
        if ($rows === []) {
            $this->syncProductPrimary($productId);
            return;
        }
        foreach ($rows as $row) {
            if ((int) $row['is_primary'] === 1) {
                $this->syncProductPrimary($productId);
                return;
            }
        }
        $this->setPrimary($productId, (int) $rows[0]['id']);
    }

    public function setPrimary(int $productId, int $imageId): void
    {
        $this->execute('UPDATE product_images SET is_primary = 0 WHERE product_id = ?', [$productId]);
        $this->execute(
            'UPDATE product_images SET is_primary = 1 WHERE id = ? AND product_id = ?',
            [$imageId, $productId]
        );
        $this->syncProductPrimary($productId);
    }

    public function updateSort(int $imageId, int $productId, int $sortOrder): void
    {
        $this->execute(
            'UPDATE product_images SET sort_order = ? WHERE id = ? AND product_id = ?',
            [$sortOrder, $imageId, $productId]
        );
    }

    public function syncProductPrimary(int $productId): void
    {
        $row = $this->fetchOne(
            'SELECT image_url FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1',
            [$productId]
        );
        $url = $row['image_url'] ?? null;
        $this->execute('UPDATE products SET image_url = ? WHERE id = ?', [$url, $productId]);
    }

    private function deleteFile(string $url): void
    {
        if ($url === '' || !str_starts_with($url, 'uploads/')) {
            return;
        }
        $full = PUBLIC_PATH . '/' . $url;
        if (is_file($full)) {
            @unlink($full);
        }
    }
}
