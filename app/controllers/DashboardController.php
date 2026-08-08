<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $analytics = new AnalyticsService();
        $data = $analytics->dashboardPayload();

        $this->view('dashboard/index', [
            'title'         => 'Dashboard',
            'kpis'          => $data['kpis'],
            'sparklines'    => $data['sparklines'],
            'chartData'     => [
                'trends'        => $data['trends'],
                'status'        => $data['status'],
                'categories'    => $data['categories'],
                'top_products'  => $data['top_products'],
                'sparklines'    => $data['sparklines'],
                'status_colors' => $data['status_colors'],
                'status_labels' => $data['status_labels'],
            ],
            'topProducts'   => $data['top_products'],
            'lowStock'      => $data['low_stock'],
            'pageScripts'   => [asset('js/vc-analytics.js'), asset('js/vc-dashboard.js')],
        ]);
    }
}
