<?php

class AdminUser extends Model
{
    /** Modules that can be granted to sub-admins via checklist */
    public const GRANTABLE_MODULES = [
        'products'      => 'Products & Stock',
        'categories'    => 'Categories',
        'orders'        => 'Orders',
        'delivery'      => 'Delivery Management',
        'customers'     => 'Customers / KYC',
        'offers'        => 'Offers & Banners',
        'market_prices' => 'Market Prices',
        'support'         => 'Support Tickets',
        'bulk_enquiries'      => 'Bulk Enquiries',
        'serviceable_pincodes'=> 'Serviceable Pincodes',
        'reports'             => 'Reports & Analytics',
        'roles'               => 'Roles & Sub-Admins',
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM admin_users WHERE email = ? LIMIT 1', [strtolower($email)]);
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM admin_users WHERE id = ?', [$id]);
    }

    public function moduleKeys(int $adminUserId): array
    {
        $rows = $this->fetchAll(
            'SELECT module_key FROM role_permissions WHERE admin_user_id = ?',
            [$adminUserId]
        );
        return array_column($rows, 'module_key');
    }

    public function allWithModuleCounts(): array
    {
        return $this->fetchAll(
            "SELECT a.*,
                    (SELECT COUNT(*) FROM role_permissions rp WHERE rp.admin_user_id = a.id) AS module_count
             FROM admin_users a
             ORDER BY FIELD(a.role_type,'super_admin','sub_admin','delivery_manager'), a.name ASC"
        );
    }

    public function ensureUser(string $name, string $email, string $password, string $roleType, bool $resetPassword = false): int
    {
        $existing = $this->findByEmail($email);
        if ($existing) {
            if ($resetPassword) {
                $this->execute(
                    'UPDATE admin_users SET name = ?, role_type = ?, password_hash = ?, is_active = 1 WHERE id = ?',
                    [$name, $roleType, password_hash($password, PASSWORD_DEFAULT), $existing['id']]
                );
            } else {
                $this->execute(
                    'UPDATE admin_users SET name = ?, role_type = ?, is_active = 1 WHERE id = ?',
                    [$name, $roleType, $existing['id']]
                );
            }
            return (int) $existing['id'];
        }
        $this->execute(
            'INSERT INTO admin_users (name, email, password_hash, role_type, is_active) VALUES (?,?,?,?,1)',
            [$name, strtolower($email), password_hash($password, PASSWORD_DEFAULT), $roleType]
        );
        return (int) $this->db->lastInsertId();
    }

    public function ensureSuperAdmin(string $name, string $email, string $password): int
    {
        return $this->ensureUser($name, $email, $password, 'super_admin');
    }

    /**
     * Normalise an optional mobile to 10 digits (accepts spaces, dashes, +91 / 91 / 0 prefix).
     * Returns null for blank input; throws on anything that isn't an Indian mobile.
     */
    public static function normalizeMobile(?string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw) ?? '';
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 11 && $digits[0] === '0') {
            $digits = substr($digits, 1);
        }
        if (!preg_match('/^[6-9]\d{9}$/', $digits)) {
            throw new InvalidArgumentException('Mobile number must be a valid 10-digit Indian mobile (e.g. 98765 43210).');
        }
        return $digits;
    }

    public function createAdmin(array $data): int
    {
        $this->execute(
            'INSERT INTO admin_users (name, email, mobile, password_hash, role_type, is_active) VALUES (?,?,?,?,?,?)',
            [
                $data['name'],
                strtolower($data['email']),
                $data['mobile'] ?? null,
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role_type'],
                !empty($data['is_active']) ? 1 : 0,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function updateAdmin(int $id, array $data, ?string $newPassword = null): bool
    {
        if ($newPassword !== null && $newPassword !== '') {
            return $this->execute(
                'UPDATE admin_users SET name=?, email=?, mobile=?, role_type=?, is_active=?, password_hash=? WHERE id=?',
                [
                    $data['name'], strtolower($data['email']), $data['mobile'] ?? null, $data['role_type'],
                    !empty($data['is_active']) ? 1 : 0,
                    password_hash($newPassword, PASSWORD_DEFAULT), $id,
                ]
            );
        }
        return $this->execute(
            'UPDATE admin_users SET name=?, email=?, mobile=?, role_type=?, is_active=? WHERE id=?',
            [
                $data['name'], strtolower($data['email']), $data['mobile'] ?? null, $data['role_type'],
                !empty($data['is_active']) ? 1 : 0, $id,
            ]
        );
    }

    public function setActive(int $id, bool $active): bool
    {
        return $this->execute('UPDATE admin_users SET is_active = ? WHERE id = ?', [$active ? 1 : 0, $id]);
    }

    public function syncModules(int $adminUserId, array $moduleKeys): void
    {
        $this->execute('DELETE FROM role_permissions WHERE admin_user_id = ?', [$adminUserId]);
        foreach ($moduleKeys as $key) {
            if (!isset(self::GRANTABLE_MODULES[$key])) {
                continue;
            }
            $this->grantModule($adminUserId, $key);
        }
    }

    public function grantModule(int $adminUserId, string $moduleKey): void
    {
        $this->execute(
            'INSERT IGNORE INTO role_permissions (admin_user_id, module_key) VALUES (?, ?)',
            [$adminUserId, $moduleKey]
        );
    }

    public function updatePassword(int $id, string $password): bool
    {
        return $this->execute(
            'UPDATE admin_users SET password_hash = ? WHERE id = ?',
            [password_hash($password, PASSWORD_DEFAULT), $id]
        );
    }
}
