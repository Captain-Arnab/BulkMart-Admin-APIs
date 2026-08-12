<?php

class Banner extends Model
{
    public function all(): array
    {
        return $this->fetchAll('SELECT * FROM banners ORDER BY sort_order ASC, id DESC');
    }

    /** Home banners currently within active window. */
    public function activeHome(): array
    {
        return $this->fetchAll(
            "SELECT * FROM banners
             WHERE is_active = 1
               AND (active_from IS NULL OR active_from <= NOW())
               AND (active_to IS NULL OR active_to >= NOW())
             ORDER BY sort_order ASC, id DESC"
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM banners WHERE id = ?', [$id]);
    }

    public function create(array $d): int
    {
        $this->execute(
            'INSERT INTO banners (image_url, title, link, active_from, active_to, sort_order, is_active) VALUES (?,?,?,?,?,?,?)',
            [
                $d['image_url'], $d['title'], $d['link'] ?? null,
                $d['active_from'] ?: null, $d['active_to'] ?: null,
                (int) ($d['sort_order'] ?? 0), !empty($d['is_active']) ? 1 : 0,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        return $this->execute(
            'UPDATE banners SET image_url=?, title=?, link=?, active_from=?, active_to=?, sort_order=?, is_active=? WHERE id=?',
            [
                $d['image_url'], $d['title'], $d['link'] ?? null,
                $d['active_from'] ?: null, $d['active_to'] ?: null,
                (int) ($d['sort_order'] ?? 0), !empty($d['is_active']) ? 1 : 0, $id,
            ]
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM banners WHERE id = ?', [$id]);
    }
}
