<?php

class OrderController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Orders',
            'module'      => 'orders',
            'moduleLabel' => 'Orders',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Orders', 'url' => null],
            ],
        ]);
    }
}
