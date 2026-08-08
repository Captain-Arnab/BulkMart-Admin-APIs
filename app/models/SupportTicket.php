<?php

class SupportTicket extends Model
{
    public const STATUS_BADGE = [
        'open'        => 'bg-warning text-dark',
        'in_progress' => 'bg-info text-dark',
        'closed'      => 'bg-secondary',
    ];

    public function paginate(array $filters, int $page = 1, int $perPage = 15): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['status'])) {
            $where[] = 't.status = ?';
            $params[] = $filters['status'];
        }
        $sqlWhere = implode(' AND ', $where);
        $total = (int) ($this->fetchOne(
            "SELECT COUNT(*) AS c FROM support_tickets t WHERE $sqlWhere",
            $params
        )['c'] ?? 0);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;
        $rows = $this->fetchAll(
            "SELECT t.*, c.business_name, c.mobile, o.order_number
             FROM support_tickets t
             INNER JOIN customers c ON c.id = t.customer_id
             LEFT JOIN orders o ON o.id = t.related_order_id
             WHERE $sqlWhere
             ORDER BY t.created_at DESC
             LIMIT $perPage OFFSET $offset",
            $params
        );
        return compact('rows', 'total', 'page', 'pages') + ['per_page' => $perPage];
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT t.*, c.business_name, c.owner_name, c.mobile, c.email AS customer_email, o.order_number
             FROM support_tickets t
             INNER JOIN customers c ON c.id = t.customer_id
             LEFT JOIN orders o ON o.id = t.related_order_id
             WHERE t.id = ?",
            [$id]
        );
    }

    public function replies(int $ticketId): array
    {
        return $this->fetchAll(
            "SELECT r.*, a.name AS admin_name
             FROM support_ticket_replies r
             LEFT JOIN admin_users a ON a.id = r.admin_user_id
             WHERE r.ticket_id = ?
             ORDER BY r.created_at ASC",
            [$ticketId]
        );
    }

    public function addReply(int $ticketId, int $adminId, string $message): void
    {
        $this->execute(
            'INSERT INTO support_ticket_replies (ticket_id, admin_user_id, message) VALUES (?,?,?)',
            [$ticketId, $adminId, $message]
        );
    }

    public function setStatus(int $id, string $status): bool
    {
        return $this->execute('UPDATE support_tickets SET status = ? WHERE id = ?', [$status, $id]);
    }
}
