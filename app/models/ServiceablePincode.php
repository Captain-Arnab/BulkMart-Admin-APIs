<?php

class ServiceablePincode extends Model
{
    public function findActiveByPincode(string $pincode): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM serviceable_pincodes WHERE pincode = ? AND is_active = 1 LIMIT 1',
            [$pincode]
        );
    }

    public function findByPincode(string $pincode): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM serviceable_pincodes WHERE pincode = ? LIMIT 1',
            [$pincode]
        );
    }

    public function isServiceable(string $pincode): bool
    {
        return $this->findActiveByPincode($pincode) !== null;
    }

    public function check(string $pincode): array
    {
        $pincode = trim($pincode);
        if (!preg_match('/^\d{6}$/', $pincode)) {
            return ['serviceable' => false, 'city' => null, 'state' => null, 'valid_format' => false];
        }
        $row = $this->findActiveByPincode($pincode);
        return [
            'serviceable'  => $row !== null,
            'city'         => $row['city'] ?? null,
            'state'        => $row['state'] ?? null,
            'valid_format' => true,
        ];
    }

    public function all(?string $q = null, ?bool $activeOnly = null): array
    {
        $where = ['1=1'];
        $params = [];
        if ($q !== null && $q !== '') {
            $where[] = '(pincode LIKE ? OR city LIKE ? OR state LIKE ?)';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($activeOnly === true) {
            $where[] = 'is_active = 1';
        } elseif ($activeOnly === false) {
            $where[] = 'is_active = 0';
        }
        $sql = 'SELECT * FROM serviceable_pincodes WHERE ' . implode(' AND ', $where)
            . ' ORDER BY pincode ASC';
        return $this->fetchAll($sql, $params);
    }

    public function create(
        string $pincode,
        string $city = 'Hyderabad',
        string $state = 'Telangana',
        bool $isActive = true
    ): int {
        $this->execute(
            'INSERT INTO serviceable_pincodes (pincode, city, state, is_active) VALUES (?,?,?,?)',
            [$pincode, $city, $state, $isActive ? 1 : 0]
        );
        return (int) $this->db->lastInsertId();
    }

    public function setActive(int $id, bool $active): bool
    {
        return $this->execute(
            'UPDATE serviceable_pincodes SET is_active = ? WHERE id = ?',
            [$active ? 1 : 0, $id]
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM serviceable_pincodes WHERE id = ?', [$id]);
    }

    public static function unserviceableMessage(): string
    {
        return "We currently deliver only within Hyderabad — this pincode isn't serviceable yet";
    }
}
