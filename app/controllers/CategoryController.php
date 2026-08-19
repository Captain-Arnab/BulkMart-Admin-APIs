<?php

class CategoryController extends Controller
{
    public function index(): void
    {
        $filters = ['q' => trim((string) ($_GET['q'] ?? ''))];
        $categories = (new Category())->all();
        if ($filters['q'] !== '') {
            $q = mb_strtolower($filters['q']);
            $categories = array_values(array_filter($categories, static function (array $c) use ($q): bool {
                return str_contains(mb_strtolower((string) $c['name']), $q);
            }));
        }
        $this->view('categories/index', [
            'title'      => 'Categories',
            'categories' => $categories,
            'filters'    => $filters,
            'success'    => flash('success'),
            'error'      => flash('error'),
        ]);
    }

    public function create(): void
    {
        $this->view('categories/form', [
            'title'    => 'Add Category',
            'category' => null,
            'error'    => flash('error'),
        ]);
    }

    public function store(): void
    {
        try {
            $data = $this->validated();
            if (!empty($_FILES['image']['name'])) {
                $data['image_url'] = UploadService::storeImage($_FILES['image'], 'categories');
            }
            (new Category())->create($data);
            flash('success', 'Category created.');
            redirect('categories');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('categories/create');
        }
    }

    public function edit(string $id): void
    {
        $category = (new Category())->find((int) $id);
        if (!$category) {
            flash('error', 'Category not found.');
            redirect('categories');
        }
        $this->view('categories/form', [
            'title'    => 'Edit Category',
            'category' => $category,
            'error'    => flash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $model = new Category();
        $category = $model->find((int) $id);
        if (!$category) {
            flash('error', 'Category not found.');
            redirect('categories');
        }
        try {
            $data = $this->validated();
            $data['image_url'] = $category['image_url'];
            if (!empty($_FILES['image']['name'])) {
                $data['image_url'] = UploadService::storeImage($_FILES['image'], 'categories');
            }
            $model->update((int) $id, $data);
            flash('success', 'Category updated.');
            redirect('categories');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('categories/' . (int) $id . '/edit');
        }
    }

    public function delete(string $id): void
    {
        $model = new Category();
        $category = $model->find((int) $id);
        if (!$category) {
            flash('error', 'Category not found.');
            redirect('categories');
        }
        $count = $model->productCount((int) $id);
        if ($count > 0) {
            flash('error', "Cannot delete \"{$category['name']}\" — {$count} product(s) still use this category. Move or delete those products first.");
            redirect('categories');
        }
        $model->delete((int) $id);
        flash('success', 'Category deleted.');
        redirect('categories');
    }

    public function bulkDelete(): void
    {
        $ids = array_values(array_unique(array_filter(
            array_map('intval', (array) ($_POST['ids'] ?? [])),
            static fn (int $id): bool => $id > 0
        )));
        if ($ids === []) {
            flash('error', 'Select at least one category to delete.');
            redirect('categories');
        }
        $model = new Category();
        $deleted = 0;
        $skipped = [];
        foreach ($ids as $id) {
            $category = $model->find($id);
            if (!$category) {
                continue;
            }
            $count = $model->productCount($id);
            if ($count > 0) {
                $skipped[] = $category['name'] . ' (' . $count . ')';
                continue;
            }
            $model->delete($id);
            $deleted++;
        }
        if ($deleted > 0) {
            flash('success', $deleted === 1 ? 'Category deleted.' : $deleted . ' categories deleted.');
        }
        if ($skipped !== []) {
            flash('error', 'Could not delete (still have products): ' . implode(', ', $skipped) . '. Move or delete those products first.');
        }
        if ($deleted === 0 && $skipped === []) {
            flash('error', 'No matching categories to delete.');
        }
        redirect('categories');
    }

    private function validated(): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('Category name is required.');
        }
        if (mb_strlen($name) > 120) {
            throw new InvalidArgumentException('Category name is too long.');
        }
        return ['name' => $name, 'image_url' => null];
    }
}
