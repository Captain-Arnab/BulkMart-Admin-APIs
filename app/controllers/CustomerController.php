<?php

class CustomerController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Customers',
            'module'      => 'customers',
            'moduleLabel' => 'Customers (KYC / Verification)',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Customers', 'url' => null],
            ],
        ]);
    }
}
