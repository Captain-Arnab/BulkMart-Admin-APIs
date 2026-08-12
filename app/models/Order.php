<?php

class Order extends Model
{
    public const STATUSES = [
        'placed',
        'confirmed',
        'delivery_date_set',
        'out_for_delivery',
        'delivered',
        'cancelled',
    ];

    public const STATUS_LABELS = [
        'placed'            => 'Placed',
        'confirmed'         => 'Confirmed',
        'delivery_date_set' => 'Delivery date set',
        'out_for_delivery'  => 'Out for delivery',
        'delivered'         => 'Delivered',
        'cancelled'         => 'Cancelled',
    ];

    public const STATUS_BADGE = [
        'placed'            => 'vc-status vc-status--placed',
        'confirmed'         => 'vc-status vc-status--confirmed',
        'delivery_date_set' => 'vc-status vc-status--delivery_date_set',
        'out_for_delivery'  => 'vc-status vc-status--out_for_delivery',
        'delivered'         => 'vc-status vc-status--delivered',
        'cancelled'         => 'vc-status vc-status--cancelled',
    ];

    public const STATUS_ICONS = [
        'placed'            => 'bi-bag-plus',
        'confirmed'         => 'bi-check2-circle',
        'delivery_date_set' => 'bi-calendar-event',
        'out_for_delivery'  => 'bi-truck',
        'delivered'         => 'bi-box-seam',
        'cancelled'         => 'bi-x-circle',
    ];

    /** Forward graph (cancel handled separately). */
    public const FORWARD = [
        'placed'            => ['confirmed'],
        'confirmed'         => ['delivery_date_set'],
        'delivery_date_set' => ['out_for_delivery'],
        'out_for_delivery'  => ['delivered'],
        'delivered'         => [],
        'cancelled'         => [],
    ];

    public static function canCancel(string $status): bool
    {
        return in_array($status, ['placed', 'confirmed', 'delivery_date_set'], true);
    }

    public static function nextStatuses(string $current): array
    {
        $next = self::FORWARD[$current] ?? [];
        if (self::canCancel($current)) {
            $next[] = 'cancelled';
        }
        return $next;
    }

    public static function badge(string $status): array
    {
        return [
            'label' => self::STATUS_LABELS[$status] ?? $status,
            'class' => self::STATUS_BADGE[$status] ?? 'vc-status vc-status--placed',
            'icon'  => self::STATUS_ICONS[$status] ?? 'bi-circle',
            'key'   => $status,
        ];
    }

    /**
     * @return array{rows: array, total: int, page: int, per_page: int, pages: int}
     */
    public function paginate(array $filters, int $page = 1, int $perPage = 15): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'o.status = ?';
            $params[] = $filters['status'];
        } elseif (!empty($filters['pending'])) {
            $where[] = "o.status IN ('placed','confirmed','delivery_date_set')";
        }
        if (!empty($filters['date_from'])) {
            $where[] = 'DATE(o.placed_at) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'DATE(o.placed_at) <= ?';
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(o.order_number LIKE ? OR c.business_name LIKE ? OR c.owner_name LIKE ? OR c.mobile LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            array_push($params, $like, $like, $like, $like);
        }
        if (!empty($filters['assigned_to'])) {
            $where[] = 'o.assigned_delivery_manager_id = ?';
            $params[] = (int) $filters['assigned_to'];
        }
        if (!empty($filters['has_assignee'])) {
            $where[] = 'o.assigned_delivery_manager_id IS NOT NULL';
        }
        if (!empty($filters['eta_from'])) {
            $where[] = 'o.estimated_delivery_date >= ?';
            $params[] = $filters['eta_from'];
        }
        if (!empty($filters['eta_to'])) {
            $where[] = 'o.estimated_delivery_date <= ?';
            $params[] = $filters['eta_to'];
        }
        if (!empty($filters['statuses']) && is_array($filters['statuses'])) {
            $in = implode(',', array_fill(0, count($filters['statuses']), '?'));
            $where[] = "o.status IN ($in)";
            foreach ($filters['statuses'] as $s) {
                $params[] = $s;
            }
        }

        $sqlWhere = implode(' AND ', $where);
        $countRow = $this->fetchOne(
            "SELECT COUNT(*) AS c
             FROM orders o
             INNER JOIN customers c ON c.id = o.customer_id
             WHERE $sqlWhere",
            $params
        );
        $total = (int) ($countRow['c'] ?? 0);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $rows = $this->fetchAll(
            "SELECT o.*,
                    c.business_name, c.owner_name, c.mobile,
                    dm.name AS delivery_manager_name,
                    a.line1, a.line2, a.city, a.state, a.pincode, a.landmark,
                    (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
             FROM orders o
             INNER JOIN customers c ON c.id = o.customer_id
             INNER JOIN addresses a ON a.id = o.address_id
             LEFT JOIN admin_users dm ON dm.id = o.assigned_delivery_manager_id
             WHERE $sqlWhere
             ORDER BY o.placed_at DESC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        return [
            'rows'     => $rows,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => $pages,
        ];
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT o.*,
                    c.business_name, c.owner_name, c.mobile, c.email AS customer_email,
                    c.business_type, c.gst_number,
                    a.label AS address_label, a.line1, a.line2, a.city, a.state, a.pincode, a.landmark,
                    dm.name AS delivery_manager_name, dm.email AS delivery_manager_email
             FROM orders o
             INNER JOIN customers c ON c.id = o.customer_id
             INNER JOIN addresses a ON a.id = o.address_id
             LEFT JOIN admin_users dm ON dm.id = o.assigned_delivery_manager_id
             WHERE o.id = ?",
            [$id]
        );
    }

    public function items(int $orderId): array
    {
        return $this->fetchAll(
            'SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC',
            [$orderId]
        );
    }

    public function statusLog(int $orderId): array
    {
        return $this->fetchAll(
            "SELECT l.*, au.name AS admin_name
             FROM order_status_log l
             LEFT JOIN admin_users au ON au.id = l.changed_by_admin_id
             WHERE l.order_id = ?
             ORDER BY l.changed_at ASC, l.id ASC",
            [$orderId]
        );
    }

    public function deliveryManagers(): array
    {
        return $this->fetchAll(
            "SELECT id, name, email FROM admin_users
             WHERE role_type = 'delivery_manager' AND is_active = 1
             ORDER BY name ASC"
        );
    }

    public function updateFields(int $id, array $fields): bool
    {
        if ($fields === []) {
            return true;
        }
        $sets = [];
        $params = [];
        foreach ($fields as $col => $val) {
            $sets[] = "`$col` = ?";
            $params[] = $val;
        }
        $params[] = $id;
        return $this->execute('UPDATE orders SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }

    /**
     * Customer-facing order list.
     * @return array{rows: array, total: int, page: int, per_page: int, pages: int}
     */
    public function paginateForCustomer(int $customerId, int $page = 1, int $perPage = 15): array
    {
        $total = (int) ($this->fetchOne(
            'SELECT COUNT(*) AS c FROM orders WHERE customer_id = ?',
            [$customerId]
        )['c'] ?? 0);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;
        $rows = $this->fetchAll(
            "SELECT o.id, o.order_number, o.status, o.subtotal, o.delivery_fee, o.discount_amount, o.coupon_code, o.total,
                    o.payment_method, o.estimated_delivery_date, o.placed_at, o.delivered_at,
                    (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
             FROM orders o
             WHERE o.customer_id = ?
             ORDER BY o.placed_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            [$customerId]
        );
        return compact('rows', 'total', 'page', 'pages') + ['per_page' => $perPage];
    }

    public function findForCustomer(int $id, int $customerId): ?array
    {
        $order = $this->find($id);
        if (!$order || (int) $order['customer_id'] !== $customerId) {
            return null;
        }
        return $order;
    }
}
