<?php

class ReportController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Reports & Analytics',
            'module'      => 'reports',
            'moduleLabel' => 'Reports & Analytics',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Reports & Analytics', 'url' => null],
            ],
        ]);
    }
}
