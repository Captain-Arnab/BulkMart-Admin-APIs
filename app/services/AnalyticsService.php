<?php

declare(strict_types=1);

/**
 * Shared analytics queries for Dashboard + Reports.
 */
class AnalyticsService
{
    public const LOW_STOCK_THRESHOLD = 20;

    /** Brand-aligned status colors (match badge intent without Chart.js defaults). */
    public const STATUS_COLORS = [
        'placed'            => '#8AA896',
        'confirmed'         => '#12833B',
        'delivery_date_set' => '#3D9B5F',
        'out_for_delivery'  => '#F5A623',
        'delivered'         => '#0B5C27',
        'cancelled'         => '#D64545',
    ];

    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? db();
    }

    public function formatMoney(float $amount): string
    {
        if ($amount >= 100000) {
            return '₹' . rtrim(rtrim(number_format($amount / 100000, 2), '0'), '.') . 'L';
        }
        if ($amount >= 1000) {
            return '₹' . number_format($amount, 0);
        }
        return '₹' . number_format($amount, 2);
    }

    /**
     * @return array{date_from:string,date_to:string,category_id:?int,customer_q:string,status:string,preset:string}
     */
    public function normalizeFilters(array $input): array
    {
        $preset = trim((string) ($input['preset'] ?? ''));
        $today = new DateTimeImmutable('today');

        switch ($preset) {
            case 'today':
                $from = $today->format('Y-m-d');
                $to = $from;
                break;
            case '7d':
                $from = $today->modify('-6 days')->format('Y-m-d');
                $to = $today->format('Y-m-d');
                break;
            case '30d':
                $from = $today->modify('-29 days')->format('Y-m-d');
                $to = $today->format('Y-m-d');
                break;
            case '90d':
                $from = $today->modify('-89 days')->format('Y-m-d');
                $to = $today->format('Y-m-d');
                break;
            case 'month':
                $from = $today->format('Y-m-01');
                $to = $today->format('Y-m-d');
                break;
            default:
                $from = trim((string) ($input['date_from'] ?? $today->modify('-29 days')->format('Y-m-d')));
                $to = trim((string) ($input['date_to'] ?? $today->format('Y-m-d')));
                $preset = $preset !== '' ? $preset : 'custom';
                break;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
            $from = $today->modify('-29 days')->format('Y-m-d');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            $to = $today->format('Y-m-d');
        }
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $categoryId = isset($input['category_id']) && $input['category_id'] !== ''
            ? (int) $input['category_id']
            : null;
        if ($categoryId !== null && $categoryId <= 0) {
            $categoryId = null;
        }

        return [
            'date_from'   => $from,
            'date_to'     => $to,
            'category_id' => $categoryId,
            'customer_q'  => trim((string) ($input['customer_q'] ?? '')),
            'status'      => trim((string) ($input['status'] ?? '')),
            'preset'      => $preset,
        ];
    }

    public function dashboardPayload(): array
    {
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');
        $kpis = $this->kpiCards($today);
        $sparklines = $this->kpiSparklines(7);

        return [
            'kpis'            => $kpis,
            'sparklines'      => $sparklines,
            'trends'          => [
                '7'  => $this->revenueOrdersTrend(7),
                '30' => $this->revenueOrdersTrend(30),
                '90' => $this->revenueOrdersTrend(90),
            ],
            'status'          => $this->statusBreakdown(null, null, true),
            'categories'      => $this->categoryPerformance(
                (new DateTimeImmutable('today'))->modify('-29 days')->format('Y-m-d'),
                $today
            ),
            'top_products'    => $this->topProducts(
                (new DateTimeImmutable('today'))->modify('-29 days')->format('Y-m-d'),
                $today,
                5,
                0
            ),
            'low_stock'       => $this->lowStock(self::LOW_STOCK_THRESHOLD, 8),
            'status_colors'   => self::STATUS_COLORS,
            'status_labels'   => Order::STATUS_LABELS,
        ];
    }

    public function reportsPayload(array $filters, int $productsPage = 1, int $customersPage = 1, int $ordersPage = 1): array
    {
        $from = $filters['date_from'];
        $to = $filters['date_to'];
        $perPage = 10;

        $summary = $this->summaryStats($from, $to, $filters);
        $trend = $this->revenueOrdersTrendRange($from, $to, $filters);
        $status = $this->statusBreakdown($from, $to, false, $filters);
        $categories = $this->categoryPerformance($from, $to, $filters);
        $topProducts = $this->topProductsPaginated($from, $to, $filters, $productsPage, $perPage);
        $topCustomers = $this->topCustomersPaginated($from, $to, $filters, $customersPage, $perPage);
        $orders = $this->orderDetailPaginated($from, $to, $filters, $ordersPage, $perPage);

        return [
            'filters'       => $filters,
            'summary'       => $summary,
            'trend'         => $trend,
            'status'        => $status,
            'categories'    => $categories,
            'top_products'  => $topProducts,
            'top_customers' => $topCustomers,
            'orders'        => $orders,
            'categories_list' => $this->pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll(),
            'status_colors' => self::STATUS_COLORS,
            'status_labels' => Order::STATUS_LABELS,
        ];
    }

    private function kpiCards(string $today): array
    {
        $ordersToday = (int) $this->scalar(
            "SELECT COUNT(*) FROM orders WHERE DATE(placed_at) = ?",
            [$today]
        );
        $pending = (int) $this->scalar(
            "SELECT COUNT(*) FROM orders WHERE status IN ('placed','confirmed','delivery_date_set')"
        );
        $revenueToday = (float) $this->scalar(
            "SELECT COALESCE(SUM(total),0) FROM orders
             WHERE DATE(placed_at) = ? AND status != 'cancelled'",
            [$today]
        );
        $lowStock = (int) $this->scalar(
            'SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock < ?',
            [self::LOW_STOCK_THRESHOLD]
        );

        return [
            [
                'key'   => 'orders',
                'label' => 'Orders Today',
                'value' => (string) $ordersToday,
                'hint'  => 'Placed today',
                'icon'  => 'bi-cart3',
                'tone'  => 'primary',
                'class' => 'sales-card',
            ],
            [
                'key'   => 'pending',
                'label' => 'Pending Dispatch',
                'value' => (string) $pending,
                'hint'  => 'Awaiting dispatch',
                'icon'  => 'bi-truck',
                'tone'  => 'amber',
                'class' => 'customers-card',
            ],
            [
                'key'   => 'revenue',
                'label' => 'Revenue',
                'value' => $this->formatMoney($revenueToday),
                'hint'  => 'Today (ex. cancelled)',
                'icon'  => 'bi-currency-rupee',
                'tone'  => 'primary',
                'class' => 'revenue-card',
            ],
            [
                'key'   => 'low_stock',
                'label' => 'Low Stock',
                'value' => (string) $lowStock,
                'hint'  => 'Below ' . self::LOW_STOCK_THRESHOLD . ' units',
                'icon'  => 'bi-exclamation-triangle',
                'tone'  => 'amber',
                'class' => 'sales-card',
            ],
        ];
    }

    /** @return array{labels:string[],orders:float[],pending:float[],revenue:float[],demand:float[]} */
    private function kpiSparklines(int $days): array
    {
        $labels = [];
        $orders = [];
        $pending = [];
        $revenue = [];
        $demand = [];
        $today = new DateTimeImmutable('today');

        for ($i = $days - 1; $i >= 0; $i--) {
            $d = $today->modify("-{$i} days")->format('Y-m-d');
            $labels[] = $today->modify("-{$i} days")->format('D');
            $orders[] = (float) $this->scalar(
                'SELECT COUNT(*) FROM orders WHERE DATE(placed_at) = ?',
                [$d]
            );
            $pending[] = (float) $this->scalar(
                "SELECT COUNT(*) FROM orders
                 WHERE DATE(placed_at) = ?
                   AND status IN ('placed','confirmed','delivery_date_set')",
                [$d]
            );
            $revenue[] = (float) $this->scalar(
                "SELECT COALESCE(SUM(total),0) FROM orders
                 WHERE DATE(placed_at) = ? AND status != 'cancelled'",
                [$d]
            );
            $demand[] = (float) $this->scalar(
                "SELECT COALESCE(SUM(oi.quantity),0)
                 FROM order_items oi
                 INNER JOIN orders o ON o.id = oi.order_id
                 WHERE DATE(o.placed_at) = ? AND o.status != 'cancelled'",
                [$d]
            );
        }

        return compact('labels', 'orders', 'pending', 'revenue', 'demand');
    }

    /** @return array{labels:string[],revenue:float[],orders:float[]} */
    public function revenueOrdersTrend(int $days): array
    {
        $today = new DateTimeImmutable('today');
        $from = $today->modify('-' . ($days - 1) . ' days')->format('Y-m-d');
        $to = $today->format('Y-m-d');
        return $this->revenueOrdersTrendRange($from, $to);
    }

    /** @return array{labels:string[],revenue:float[],orders:float[]} */
    public function revenueOrdersTrendRange(string $from, string $to, array $filters = []): array
    {
        [$where, $params] = $this->orderFilterSql($from, $to, $filters, 'o', false);
        $sql = "SELECT DATE(o.placed_at) AS d,
                       COUNT(*) AS order_count,
                       COALESCE(SUM(CASE WHEN o.status != 'cancelled' THEN o.total ELSE 0 END),0) AS revenue
                FROM orders o
                INNER JOIN customers c ON c.id = o.customer_id
                WHERE {$where}
                GROUP BY DATE(o.placed_at)
                ORDER BY d ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $byDay = [];
        foreach ($stmt->fetchAll() as $row) {
            $byDay[$row['d']] = $row;
        }

        $labels = [];
        $revenue = [];
        $orders = [];
        $start = new DateTimeImmutable($from);
        $end = new DateTimeImmutable($to);
        for ($d = $start; $d <= $end; $d = $d->modify('+1 day')) {
            $key = $d->format('Y-m-d');
            $labels[] = $d->format('d M');
            $revenue[] = isset($byDay[$key]) ? (float) $byDay[$key]['revenue'] : 0.0;
            $orders[] = isset($byDay[$key]) ? (float) $byDay[$key]['order_count'] : 0.0;
        }

        return compact('labels', 'revenue', 'orders');
    }

    /**
     * @return array{labels:string[],series:float[],colors:string[],total_active:int,map:array}
     */
    public function statusBreakdown(?string $from, ?string $to, bool $currentSnapshot = false, array $filters = []): array
    {
        if ($currentSnapshot || $from === null || $to === null) {
            $rows = $this->pdo->query(
                'SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status'
            )->fetchAll();
        } else {
            [$where, $params] = $this->orderFilterSql($from, $to, $filters, 'o', false);
            $stmt = $this->pdo->prepare(
                "SELECT o.status, COUNT(*) AS cnt
                 FROM orders o
                 INNER JOIN customers c ON c.id = o.customer_id
                 WHERE {$where}
                 GROUP BY o.status"
            );
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
        }

        $map = [];
        foreach ($rows as $row) {
            $map[$row['status']] = (int) $row['cnt'];
        }

        $labels = [];
        $series = [];
        $colors = [];
        $totalActive = 0;
        foreach (Order::STATUSES as $status) {
            $cnt = $map[$status] ?? 0;
            $labels[] = Order::STATUS_LABELS[$status] ?? $status;
            $series[] = $cnt;
            $colors[] = self::STATUS_COLORS[$status] ?? '#8AA896';
            if ($status !== 'cancelled' && $status !== 'delivered') {
                $totalActive += $cnt;
            }
        }

        return [
            'labels'       => $labels,
            'series'       => $series,
            'colors'       => $colors,
            'total_active' => $totalActive,
            'map'          => $map,
            'keys'         => Order::STATUSES,
        ];
    }

    /**
     * @return array{labels:string[],revenue:float[],qty:float[],colors:string[]}
     */
    public function categoryPerformance(string $from, string $to, array $filters = []): array
    {
        // Category chart always lists all categories; filters apply to the sales subquery.
        $subFilters = $filters;
        unset($subFilters['category_id']);
        [$where, $params] = $this->orderFilterSql($from, $to, $subFilters, 'o', false);
        if (!empty($filters['category_id'])) {
            $where .= ' AND p.category_id = ?';
            $params[] = (int) $filters['category_id'];
        }
        $sql = "SELECT cat.id, cat.name,
                       COALESCE(x.revenue, 0) AS revenue,
                       COALESCE(x.qty, 0) AS qty
                FROM categories cat
                LEFT JOIN (
                    SELECT p.category_id,
                           SUM(oi.line_total) AS revenue,
                           SUM(oi.quantity) AS qty
                    FROM order_items oi
                    INNER JOIN orders o ON o.id = oi.order_id
                    INNER JOIN customers c ON c.id = o.customer_id
                    INNER JOIN products p ON p.id = oi.product_id
                    WHERE {$where} AND o.status != 'cancelled'
                    GROUP BY p.category_id
                ) x ON x.category_id = cat.id
                ORDER BY revenue DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $palette = ['#12833B', '#0B5C27', '#3D9B5F', '#F5A623', '#8AA896', '#1AA34A'];
        $labels = [];
        $revenue = [];
        $qty = [];
        $colors = [];
        foreach ($rows as $i => $row) {
            $labels[] = $row['name'];
            $revenue[] = (float) $row['revenue'];
            $qty[] = (float) $row['qty'];
            $colors[] = $palette[$i % count($palette)];
        }

        return compact('labels', 'revenue', 'qty', 'colors');
    }

    /** @return list<array> */
    public function topProducts(string $from, string $to, int $limit = 5, int $offset = 0, array $filters = []): array
    {
        $page = $this->topProductsPaginated($from, $to, $filters, 1, $limit);
        return $page['rows'];
    }

    /** @return array{rows:list,total:int,page:int,pages:int,per_page:int,max_qty:float} */
    public function topProductsPaginated(string $from, string $to, array $filters, int $page, int $perPage): array
    {
        [$where, $params] = $this->orderFilterSql($from, $to, $filters, 'o', true);
        $countSql = "SELECT COUNT(*) FROM (
            SELECT oi.product_id
            FROM order_items oi
            INNER JOIN orders o ON o.id = oi.order_id
            INNER JOIN customers c ON c.id = o.customer_id
            INNER JOIN products p ON p.id = oi.product_id
            WHERE {$where} AND o.status != 'cancelled'
            GROUP BY oi.product_id
        ) t";
        $total = (int) $this->scalar($countSql, $params);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT oi.product_id,
                       oi.product_name_snapshot AS name,
                       SUM(oi.quantity) AS qty,
                       SUM(oi.line_total) AS revenue,
                       COALESCE(p.stock, 0) AS stock
                FROM order_items oi
                INNER JOIN orders o ON o.id = oi.order_id
                INNER JOIN customers c ON c.id = o.customer_id
                LEFT JOIN products p ON p.id = oi.product_id
                WHERE {$where} AND o.status != 'cancelled'
                GROUP BY oi.product_id, oi.product_name_snapshot, p.stock
                ORDER BY qty DESC
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $maxQty = 0.0;
        foreach ($rows as $r) {
            $maxQty = max($maxQty, (float) $r['qty']);
        }

        return [
            'rows'     => $rows,
            'total'    => $total,
            'page'     => $page,
            'pages'    => $pages,
            'per_page' => $perPage,
            'max_qty'  => $maxQty > 0 ? $maxQty : 1.0,
        ];
    }

    /** @return array{rows:list,total:int,page:int,pages:int,per_page:int} */
    public function topCustomersPaginated(string $from, string $to, array $filters, int $page, int $perPage): array
    {
        [$where, $params] = $this->orderFilterSql($from, $to, $filters, 'o', false);
        $where .= " AND o.status != 'cancelled'";
        $countSql = "SELECT COUNT(*) FROM (
            SELECT o.customer_id
            FROM orders o
            INNER JOIN customers c ON c.id = o.customer_id
            WHERE {$where}
            GROUP BY o.customer_id
        ) t";
        $total = (int) $this->scalar($countSql, $params);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT c.id, c.business_name,
                       COUNT(*) AS order_count,
                       SUM(o.total) AS total_spend,
                       MAX(o.placed_at) AS last_order_at
                FROM orders o
                INNER JOIN customers c ON c.id = o.customer_id
                WHERE {$where}
                GROUP BY c.id, c.business_name
                ORDER BY total_spend DESC
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return [
            'rows'     => $stmt->fetchAll(),
            'total'    => $total,
            'page'     => $page,
            'pages'    => $pages,
            'per_page' => $perPage,
        ];
    }

    /** @return array{rows:list,total:int,page:int,pages:int,per_page:int} */
    public function orderDetailPaginated(string $from, string $to, array $filters, int $page, int $perPage): array
    {
        [$where, $params] = $this->orderFilterSql($from, $to, $filters, 'o', false);
        $total = (int) $this->scalar(
            "SELECT COUNT(*) FROM orders o INNER JOIN customers c ON c.id = o.customer_id WHERE {$where}",
            $params
        );
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT o.id, o.order_number, o.status, o.total, o.payment_method, o.placed_at,
                       c.business_name
                FROM orders o
                INNER JOIN customers c ON c.id = o.customer_id
                WHERE {$where}
                ORDER BY o.placed_at DESC
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return [
            'rows'     => $stmt->fetchAll(),
            'total'    => $total,
            'page'     => $page,
            'pages'    => $pages,
            'per_page' => $perPage,
        ];
    }

    /** @return list<array> */
    public function exportOrders(string $from, string $to, array $filters): array
    {
        [$where, $params] = $this->orderFilterSql($from, $to, $filters, 'o', false);
        $sql = "SELECT o.order_number, c.business_name, o.status, o.subtotal, o.delivery_fee, o.total,
                       o.payment_method, o.placed_at
                FROM orders o
                INNER JOIN customers c ON c.id = o.customer_id
                WHERE {$where}
                ORDER BY o.placed_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** @return list<array> */
    public function lowStock(int $threshold = self::LOW_STOCK_THRESHOLD, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, stock, unit, item_code
             FROM products
             WHERE is_active = 1 AND stock < ?
             ORDER BY stock ASC
             LIMIT ' . (int) $limit
        );
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    /** @return array{orders:int,revenue:float,aov:float,units:float} */
    public function summaryStats(string $from, string $to, array $filters = []): array
    {
        [$where, $params] = $this->orderFilterSql($from, $to, $filters, 'o', false);
        $row = $this->pdo->prepare(
            "SELECT COUNT(*) AS order_count,
                    COALESCE(SUM(CASE WHEN o.status != 'cancelled' THEN o.total ELSE 0 END),0) AS revenue,
                    COALESCE(SUM(CASE WHEN o.status != 'cancelled' THEN 1 ELSE 0 END),0) AS paid_orders
             FROM orders o
             INNER JOIN customers c ON c.id = o.customer_id
             WHERE {$where}"
        );
        $row->execute($params);
        $summary = $row->fetch() ?: ['order_count' => 0, 'revenue' => 0, 'paid_orders' => 0];

        [$whereItems, $paramsItems] = $this->orderFilterSql($from, $to, $filters, 'o', true);
        $units = (float) $this->scalar(
            "SELECT COALESCE(SUM(oi.quantity),0)
             FROM order_items oi
             INNER JOIN orders o ON o.id = oi.order_id
             INNER JOIN customers c ON c.id = o.customer_id
             INNER JOIN products p ON p.id = oi.product_id
             WHERE {$whereItems} AND o.status != 'cancelled'",
            $paramsItems
        );

        $paid = (int) $summary['paid_orders'];
        $revenue = (float) $summary['revenue'];

        return [
            'orders'  => (int) $summary['order_count'],
            'revenue' => $revenue,
            'aov'     => $paid > 0 ? $revenue / $paid : 0.0,
            'units'   => $units,
        ];
    }

    /**
     * @return array{0:string,1:list}
     */
    private function orderFilterSql(string $from, string $to, array $filters, string $alias = 'o', bool $joinProducts = false): array
    {
        $where = ["DATE({$alias}.placed_at) BETWEEN ? AND ?"];
        $params = [$from, $to];

        if (!empty($filters['status'])) {
            $where[] = "{$alias}.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['customer_q'])) {
            $where[] = '(c.business_name LIKE ? OR c.mobile LIKE ?)';
            $q = '%' . $filters['customer_q'] . '%';
            $params[] = $q;
            $params[] = $q;
        }
        if ($joinProducts && !empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = (int) $filters['category_id'];
        } elseif (!$joinProducts && !empty($filters['category_id'])) {
            $where[] = "EXISTS (
                SELECT 1 FROM order_items oi2
                INNER JOIN products p2 ON p2.id = oi2.product_id
                WHERE oi2.order_id = {$alias}.id AND p2.category_id = ?
            )";
            $params[] = (int) $filters['category_id'];
        }

        return [implode(' AND ', $where), $params];
    }

    /** @param list<mixed> $params */
    private function scalar(string $sql, array $params = []): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
}
