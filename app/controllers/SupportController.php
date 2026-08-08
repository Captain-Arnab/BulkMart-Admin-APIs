<?php

class SupportController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Support Tickets',
            'module'      => 'support',
            'moduleLabel' => 'Support Tickets',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Support Tickets', 'url' => null],
            ],
        ]);
    }
}
