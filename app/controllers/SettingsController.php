<?php

class SettingsController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Settings',
            'module'      => 'settings',
            'moduleLabel' => 'Settings',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Settings', 'url' => null],
            ],
        ]);
    }
}
