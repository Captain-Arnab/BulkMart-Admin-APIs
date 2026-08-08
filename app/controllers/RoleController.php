<?php

class RoleController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Roles & Sub-Admins',
            'module'      => 'roles',
            'moduleLabel' => 'Roles & Sub-Admins',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Roles & Sub-Admins', 'url' => null],
            ],
        ]);
    }
}
