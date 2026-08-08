<?php

class AdminUser extends Model
{
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

    public function grantModule(int $adminUserId, string $moduleKey): void
    {
        $this->execute(
            'INSERT IGNORE INTO role_permissions (admin_user_id, module_key) VALUES (?, ?)',
            [$adminUserId, $moduleKey]
        );
    }
}
