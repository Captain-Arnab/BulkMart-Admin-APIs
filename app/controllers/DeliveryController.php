<?php

class DeliveryController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Delivery Management',
            'module'      => 'delivery',
            'moduleLabel' => 'Delivery Management',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Delivery Management', 'url' => null],
            ],
        ]);
    }
}
