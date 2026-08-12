<?php

class Notification extends Model
{
    public function paginateForCustomer(int $customerId, int $page = 1, int $perPage = 20, ?bool $unreadOnly = null): array
    {
        $where = ['customer_id = ?'];
        $params = [$customerId];
        if ($unreadOnly === true) {
            $where[] = 'is_read = 0';
        }
        $sqlWhere = implode(' AND ', $where);
        $total = (int) ($this->fetchOne("SELECT COUNT(*) AS c FROM notifications WHERE $sqlWhere", $params)['c'] ?? 0);
        $unread = (int) ($this->fetchOne(
            'SELECT COUNT(*) AS c FROM notifications WHERE customer_id = ? AND is_read = 0',
            [$customerId]
        )['c'] ?? 0);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;
        $rows = $this->fetchAll(
            "SELECT * FROM notifications WHERE $sqlWhere ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        return compact('rows', 'total', 'page', 'pages', 'unread') + ['per_page' => $perPage];
    }

    public function findForCustomer(int $id, int $customerId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM notifications WHERE id = ? AND customer_id = ?',
            [$id, $customerId]
        );
    }

    public function markRead(int $id, int $customerId): bool
    {
        return $this->execute(
            'UPDATE notifications SET is_read = 1 WHERE id = ? AND customer_id = ?',
            [$id, $customerId]
        );
    }

    public function markAllRead(int $customerId): bool
    {
        return $this->execute(
            'UPDATE notifications SET is_read = 1 WHERE customer_id = ? AND is_read = 0',
            [$customerId]
        );
    }
}
