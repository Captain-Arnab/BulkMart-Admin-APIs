<?php

class AdminActivityLog extends Model
{
    public function record(
        string $action,
        string $entityType,
        int $entityId,
        string $note = '',
        ?array $admin = null
    ): void {
        $admin = $admin ?? auth_user();
        $this->execute(
            'INSERT INTO admin_activity_log
              (admin_user_id, admin_name, action, entity_type, entity_id, note)
             VALUES (?,?,?,?,?,?)',
            [
                $admin ? (int) ($admin['id'] ?? 0) ?: null : null,
                $admin ? (string) ($admin['name'] ?? $admin['email'] ?? 'Admin') : 'Admin',
                $action,
                $entityType,
                $entityId,
                mb_substr($note, 0, 500),
            ]
        );
    }

    /** @return list<array<string,mixed>> */
    public function forEntity(string $entityType, int $entityId, int $limit = 20): array
    {
        $limit = max(1, min(100, $limit));
        return $this->fetchAll(
            "SELECT * FROM admin_activity_log
             WHERE entity_type = ? AND entity_id = ?
             ORDER BY created_at DESC
             LIMIT {$limit}",
            [$entityType, $entityId]
        );
    }
}
