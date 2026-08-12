<?php

class Address extends Model
{
    public function listForCustomer(int $customerId): array
    {
        return $this->fetchAll(
            'SELECT * FROM addresses WHERE customer_id = ? ORDER BY is_default DESC, id ASC',
            [$customerId]
        );
    }

    public function findForCustomer(int $id, int $customerId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM addresses WHERE id = ? AND customer_id = ?',
            [$id, $customerId]
        );
    }

    public function create(int $customerId, array $d): int
    {
        $isDefault = !empty($d['is_default']);
        if ($isDefault) {
            $this->clearDefault($customerId);
        } else {
            $existing = $this->fetchOne(
                'SELECT id FROM addresses WHERE customer_id = ? LIMIT 1',
                [$customerId]
            );
            if (!$existing) {
                $isDefault = true;
            }
        }

        $this->execute(
            'INSERT INTO addresses
              (customer_id, label, line1, line2, city, state, pincode, landmark, geo_lat, geo_lng, is_default)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)',
            [
                $customerId,
                $d['label'] ?? 'Shop',
                $d['line1'],
                $d['line2'] ?? null,
                $d['city'],
                $d['state'],
                $d['pincode'],
                $d['landmark'] ?? null,
                $d['geo_lat'] ?? null,
                $d['geo_lng'] ?? null,
                $isDefault ? 1 : 0,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $customerId, array $d): bool
    {
        $row = $this->findForCustomer($id, $customerId);
        if (!$row) {
            return false;
        }
        if (!empty($d['is_default'])) {
            $this->clearDefault($customerId);
        }
        return $this->execute(
            'UPDATE addresses SET
                label=?, line1=?, line2=?, city=?, state=?, pincode=?, landmark=?,
                geo_lat=?, geo_lng=?, is_default=?
             WHERE id=? AND customer_id=?',
            [
                $d['label'] ?? $row['label'],
                $d['line1'] ?? $row['line1'],
                array_key_exists('line2', $d) ? $d['line2'] : $row['line2'],
                $d['city'] ?? $row['city'],
                $d['state'] ?? $row['state'],
                $d['pincode'] ?? $row['pincode'],
                array_key_exists('landmark', $d) ? $d['landmark'] : $row['landmark'],
                array_key_exists('geo_lat', $d) ? $d['geo_lat'] : $row['geo_lat'],
                array_key_exists('geo_lng', $d) ? $d['geo_lng'] : $row['geo_lng'],
                !empty($d['is_default']) ? 1 : (int) $row['is_default'],
                $id,
                $customerId,
            ]
        );
    }

    public function delete(int $id, int $customerId): bool
    {
        $row = $this->findForCustomer($id, $customerId);
        if (!$row) {
            return false;
        }
        $ok = $this->execute('DELETE FROM addresses WHERE id = ? AND customer_id = ?', [$id, $customerId]);
        if ($ok && (int) $row['is_default'] === 1) {
            $next = $this->fetchOne(
                'SELECT id FROM addresses WHERE customer_id = ? ORDER BY id ASC LIMIT 1',
                [$customerId]
            );
            if ($next) {
                $this->execute('UPDATE addresses SET is_default = 1 WHERE id = ?', [(int) $next['id']]);
            }
        }
        return $ok;
    }

    public function setDefault(int $id, int $customerId): bool
    {
        $row = $this->findForCustomer($id, $customerId);
        if (!$row) {
            return false;
        }
        $this->clearDefault($customerId);
        return $this->execute(
            'UPDATE addresses SET is_default = 1 WHERE id = ? AND customer_id = ?',
            [$id, $customerId]
        );
    }

    private function clearDefault(int $customerId): void
    {
        $this->execute('UPDATE addresses SET is_default = 0 WHERE customer_id = ?', [$customerId]);
    }
}
