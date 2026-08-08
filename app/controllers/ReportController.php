<?php

class ReportController extends Controller
{
    public function index(): void
    {
        $from = trim((string) ($_GET['date_from'] ?? date('Y-m-01')));
        $to = trim((string) ($_GET['date_to'] ?? date('Y-m-d')));
        $run = isset($_GET['run']) || isset($_GET['date_from']);

        $stats = null;
        if ($run) {
            $stats = $this->buildStats($from, $to);
        }

        $this->view('reports/index', [
            'title'     => 'Reports & Analytics',
            'date_from' => $from,
            'date_to'   => $to,
            'stats'     => $stats,
            'error'     => flash('error'),
        ]);
    }

    public function export(): void
    {
        $from = trim((string) ($_GET['date_from'] ?? date('Y-m-01')));
        $to = trim((string) ($_GET['date_to'] ?? date('Y-m-d')));
        $stmt = db()->prepare(
            "SELECT o.order_number, c.business_name, o.status, o.subtotal, o.delivery_fee, o.total, o.payment_method, o.placed_at
             FROM orders o
             INNER JOIN customers c ON c.id = o.customer_id
             WHERE DATE(o.placed_at) BETWEEN ? AND ?
             ORDER BY o.placed_at ASC"
        );
        $stmt->execute([$from, $to]);
        $rows = $stmt->fetchAll();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="veggiicart_orders_' . $from . '_to_' . $to . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['order_number', 'business_name', 'status', 'subtotal', 'delivery_fee', 'total', 'payment_method', 'placed_at']);
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }

    private function buildStats(string $from, string $to): array
    {
        $pdo = db();
        $base = $pdo->prepare(
            "SELECT COUNT(*) AS order_count,
                    COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total ELSE 0 END),0) AS revenue
             FROM orders
             WHERE DATE(placed_at) BETWEEN ? AND ?"
        );
        $base->execute([$from, $to]);
        $summary = $base->fetch();

        $topProducts = $pdo->prepare(
            "SELECT oi.product_name_snapshot AS name, SUM(oi.quantity) AS qty
             FROM order_items oi
             INNER JOIN orders o ON o.id = oi.order_id
             WHERE DATE(o.placed_at) BETWEEN ? AND ? AND o.status != 'cancelled'
             GROUP BY oi.product_id, oi.product_name_snapshot
             ORDER BY qty DESC
             LIMIT 5"
        );
        $topProducts->execute([$from, $to]);

        $topCustomers = $pdo->prepare(
            "SELECT c.business_name, SUM(o.total) AS order_value, COUNT(*) AS orders
             FROM orders o
             INNER JOIN customers c ON c.id = o.customer_id
             WHERE DATE(o.placed_at) BETWEEN ? AND ? AND o.status != 'cancelled'
             GROUP BY c.id, c.business_name
             ORDER BY order_value DESC
             LIMIT 5"
        );
        $topCustomers->execute([$from, $to]);

        return [
            'order_count'   => (int) $summary['order_count'],
            'revenue'       => (float) $summary['revenue'],
            'top_products'  => $topProducts->fetchAll(),
            'top_customers' => $topCustomers->fetchAll(),
        ];
    }
}
