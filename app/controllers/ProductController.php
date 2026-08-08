<?php

class ProductController extends Controller
{
    public function index(): void
    {
        $this->placeholder('Products & Stock', 'products', 'Products & Stock');
    }

    public function add(): void
    {
        $this->placeholder('Add Product', 'products', 'Products & Stock', 'Add Product');
    }

    public function bulkUpload(): void
    {
        $this->placeholder('Bulk Upload', 'products', 'Products & Stock', 'Bulk Upload');
    }

    public function bulkStock(): void
    {
        $this->placeholder('Bulk Stock Update', 'products', 'Products & Stock', 'Bulk Stock Update');
    }

    private function placeholder(string $title, string $module, string $parent, ?string $child = null): void
    {
        $this->view('shared/placeholder', [
            'title'       => $title,
            'module'      => $module,
            'moduleLabel' => $parent,
            'breadcrumb'  => array_filter([
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => $parent, 'url' => $child ? url('products') : null],
                $child ? ['label' => $child, 'url' => null] : null,
            ]),
        ]);
    }
}
