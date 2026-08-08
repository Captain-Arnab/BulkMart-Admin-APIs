<?php

class CategoryController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Categories',
            'module'      => 'categories',
            'moduleLabel' => 'Categories',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Categories', 'url' => null],
            ],
        ]);
    }
}
