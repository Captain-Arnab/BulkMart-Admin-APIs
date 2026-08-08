<?php

class ReportController extends Controller
{
    public function index(): void
    {
        $analytics = new AnalyticsService();
        $filters = $analytics->normalizeFilters($_GET);
        $productsPage = max(1, (int) ($_GET['pp'] ?? 1));
        $customersPage = max(1, (int) ($_GET['cp'] ?? 1));
        $ordersPage = max(1, (int) ($_GET['op'] ?? 1));

        $payload = $analytics->reportsPayload($filters, $productsPage, $customersPage, $ordersPage);

        $this->view('reports/index', [
            'title'       => 'Reports & Analytics',
            'filters'     => $payload['filters'],
            'summary'     => $payload['summary'],
            'payload'     => $payload,
            'chartData'   => [
                'trend'         => $payload['trend'],
                'status'        => $payload['status'],
                'categories'    => $payload['categories'],
                'status_colors' => $payload['status_colors'],
                'status_labels' => $payload['status_labels'],
            ],
            'error'       => flash('error'),
            'pageScripts' => [asset('js/vc-analytics.js'), asset('js/vc-reports.js')],
        ]);
    }

    public function export(): void
    {
        $analytics = new AnalyticsService();
        $filters = $analytics->normalizeFilters($_GET);
        $rows = $analytics->exportOrders($filters['date_from'], $filters['date_to'], $filters);

        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename="veggiicart_orders_'
            . $filters['date_from'] . '_to_' . $filters['date_to'] . '.csv"'
        );
        $out = fopen('php://output', 'w');
        fputcsv($out, ['order_number', 'business_name', 'status', 'subtotal', 'delivery_fee', 'total', 'payment_method', 'placed_at']);
        foreach ($rows as $row) {
            fputcsv($out, [
                $row['order_number'],
                $row['business_name'],
                $row['status'],
                $row['subtotal'],
                $row['delivery_fee'],
                $row['total'],
                $row['payment_method'],
                $row['placed_at'],
            ]);
        }
        fclose($out);
        exit;
    }
}
