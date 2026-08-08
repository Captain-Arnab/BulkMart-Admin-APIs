<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'kpis'  => [
                [
                    'label' => 'Orders Today',
                    'value' => '48',
                    'hint'  => '12% vs yesterday',
                    'icon'  => 'bi-cart3',
                    'class' => 'sales-card',
                ],
                [
                    'label' => 'Pending Dispatch',
                    'value' => '17',
                    'hint'  => 'Awaiting assignment',
                    'icon'  => 'bi-truck',
                    'class' => 'customers-card',
                ],
                [
                    'label' => 'Revenue',
                    'value' => '₹2.4L',
                    'hint'  => 'Today (placeholder)',
                    'icon'  => 'bi-currency-rupee',
                    'class' => 'revenue-card',
                ],
                [
                    'label' => 'Low Stock',
                    'value' => '9',
                    'hint'  => 'SKUs below threshold',
                    'icon'  => 'bi-exclamation-triangle',
                    'class' => 'sales-card',
                ],
            ],
        ]);
    }
}
