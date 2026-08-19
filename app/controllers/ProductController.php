<?php

class ProductController extends Controller
{
    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $categoryId = (int) ($_GET['category_id'] ?? 0) ?: null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $lowStock = !empty($_GET['low_stock']);
        $result = (new Product())->paginateWithCategory(
            $q !== '' ? $q : null,
            $categoryId,
            $page,
            20,
            $lowStock,
            AnalyticsService::LOW_STOCK_THRESHOLD
        );
        $this->view('products/index', [
            'title'      => 'Products & Stock',
            'products'   => $result['rows'],
            'result'     => $result,
            'categories' => (new Category())->options(),
            'q'          => $q,
            'categoryId' => $categoryId,
            'lowStock'   => $lowStock,
            'success'    => flash('success'),
            'error'      => flash('error'),
        ]);
    }

    public function add(): void
    {
        $this->view('products/form', [
            'title'      => 'Add Product',
            'product'    => null,
            'images'     => [],
            'categories' => (new Category())->options(),
            'error'      => flash('error'),
        ]);
    }

    public function store(): void
    {
        try {
            $data = $this->validatedPayload();
            if (!empty($_FILES['image']['name'])) {
                $data['image_url'] = UploadService::storeImage($_FILES['image'], 'products');
            }
            $id = (new Product())->create($data);
            $this->saveProductImages($id);
            flash('success', 'Product created.');
            redirect('products');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('products/add');
        }
    }

    public function edit(string $id): void
    {
        $product = (new Product())->find((int) $id);
        if (!$product) {
            flash('error', 'Product not found.');
            redirect('products');
        }
        $this->view('products/form', [
            'title'      => 'Edit Product',
            'product'    => $product,
            'images'     => (new ProductImage())->forProduct((int) $id),
            'categories' => (new Category())->options(),
            'error'      => flash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $model = new Product();
        $product = $model->find((int) $id);
        if (!$product) {
            flash('error', 'Product not found.');
            redirect('products');
        }
        try {
            $data = $this->validatedPayload((int) $id);
            $data['image_url'] = $product['image_url'];
            if (!empty($_FILES['image']['name'])) {
                $data['image_url'] = UploadService::storeImage($_FILES['image'], 'products');
            }
            $model->update((int) $id, $data);
            $this->saveProductImages((int) $id);
            flash('success', 'Product updated.');
            redirect('products/' . (int) $id . '/edit');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('products/' . (int) $id . '/edit');
        }
    }

    public function deactivate(string $id): void
    {
        $model = new Product();
        $product = $model->find((int) $id);
        if (!$product) {
            flash('error', 'Product not found.');
            redirect('products');
        }
        $new = !((int) $product['is_active'] === 1);
        $model->setActive((int) $id, $new);
        flash('success', $new ? 'Product activated.' : 'Product deactivated.');
        redirect('products');
    }

    public function updateStock(string $id): void
    {
        $stock = (float) ($_POST['stock'] ?? -1);
        if ($stock < 0) {
            flash('error', 'Stock must be zero or greater.');
            redirect('products');
        }
        $model = new Product();
        if (!$model->find((int) $id)) {
            flash('error', 'Product not found.');
            redirect('products');
        }
        $model->updateStock((int) $id, $stock);
        flash('success', 'Stock updated.');
        redirect('products');
    }

    public function delete(string $id): void
    {
        $this->deleteByIds([(int) $id]);
        redirect('products');
    }

    public function bulkDelete(): void
    {
        $ids = $this->postedIds();
        $this->deleteByIds($ids);
        redirect('products');
    }

    /** @param array<int,int> $ids */
    private function deleteByIds(array $ids): void
    {
        $ids = array_values(array_unique(array_filter($ids, static fn (int $id): bool => $id > 0)));
        if ($ids === []) {
            flash('error', 'Select at least one product to delete.');
            return;
        }
        $model = new Product();
        $images = new ProductImage();
        $deleted = 0;
        foreach ($ids as $id) {
            $product = $model->find($id);
            if (!$product) {
                continue;
            }
            try {
                foreach ($images->forProduct($id) as $img) {
                    $images->deleteForProduct((int) $img['id'], $id);
                }
            } catch (Throwable $e) {
                // product_images may be absent on older DBs
            }
            try {
                $model->delete($id);
                $deleted++;
            } catch (Throwable $e) {
                flash('error', 'Could not delete "' . $product['name'] . '". Run database migration 007 (order items SET NULL) if this product is on past orders.');
                return;
            }
        }
        if ($deleted > 0) {
            flash('success', $deleted === 1 ? 'Product deleted.' : $deleted . ' products deleted.');
        } else {
            flash('error', 'No matching products to delete.');
        }
    }

    /** @return array<int,int> */
    private function postedIds(): array
    {
        return array_map('intval', (array) ($_POST['ids'] ?? []));
    }

    public function bulkUpload(): void
    {
        $this->view('products/bulk_upload', [
            'title'   => 'Bulk Upload Products',
            'result'  => $_SESSION['bulk_upload_result'] ?? null,
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
        unset($_SESSION['bulk_upload_result']);
    }

    public function bulkUploadStore(): void
    {
        try {
            $path = $this->storeImportFile('products_bulk');
            $sheet = SpreadsheetReader::read($path);
            @unlink($path);

            $required = ['name', 'category', 'unit', 'moq', 'price', 'stock'];
            foreach ($required as $col) {
                if (!in_array($col, $sheet['headers'], true)) {
                    throw new RuntimeException("Missing required column: {$col}");
                }
            }

            $catModel = new Category();
            $prodModel = new Product();
            $imported = 0;
            $failed = [];

            foreach ($sheet['rows'] as $i => $row) {
                $line = $i + 2;
                try {
                    $name = trim($row['name'] ?? '');
                    $categoryName = trim($row['category'] ?? '');
                    if ($name === '' || $categoryName === '') {
                        throw new RuntimeException('name and category are required');
                    }
                    $cat = $catModel->findByName($categoryName);
                    if (!$cat) {
                        throw new RuntimeException("category \"{$categoryName}\" not found");
                    }
                    $itemCode = trim($row['item_code'] ?? '') ?: null;
                    if ($itemCode && $prodModel->findByItemCode($itemCode)) {
                        throw new RuntimeException("duplicate item_code \"{$itemCode}\"");
                    }

                    $stock = (float) ($row['stock'] ?? 0);
                    $prodModel->create([
                        'category_id' => (int) $cat['id'],
                        'name'        => $name,
                        'unit'        => trim($row['unit'] ?? '') ?: 'per kg',
                        'moq'         => (float) ($row['moq'] ?? 1),
                        'price'       => (float) ($row['price'] ?? 0),
                        'stock'       => $stock,
                        'image_url'   => null,
                        'batch_no'    => trim($row['batch_no'] ?? '') ?: null,
                        'item_code'   => $itemCode,
                        'description' => trim($row['description'] ?? '') ?: null,
                        'grade'       => trim($row['grade'] ?? '') ?: null,
                        'origin'      => trim($row['origin'] ?? '') ?: null,
                        'in_stock'    => $this->parseBool($row['in_stock'] ?? '1', $stock > 0),
                        'is_active'   => $this->parseBool($row['is_active'] ?? '1', true),
                    ]);
                    $imported++;
                } catch (Throwable $e) {
                    $failed[] = ['line' => $line, 'reason' => $e->getMessage()];
                }
            }

            $_SESSION['bulk_upload_result'] = [
                'imported' => $imported,
                'failed'   => $failed,
                'total'    => count($sheet['rows']),
            ];
            flash('success', "Bulk upload finished: {$imported} imported, " . count($failed) . ' failed.');
            redirect('products/bulk-upload');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('products/bulk-upload');
        }
    }

    public function bulkStock(): void
    {
        $this->view('products/bulk_stock', [
            'title'   => 'Bulk Stock Update',
            'result'  => $_SESSION['bulk_stock_result'] ?? null,
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
        unset($_SESSION['bulk_stock_result']);
    }

    public function bulkStockStore(): void
    {
        try {
            $path = $this->storeImportFile('stock_bulk');
            $sheet = SpreadsheetReader::read($path);
            @unlink($path);

            if (!in_array('stock', $sheet['headers'], true)) {
                throw new RuntimeException('Missing required column: stock');
            }
            if (!in_array('item_code', $sheet['headers'], true) && !in_array('id', $sheet['headers'], true)) {
                throw new RuntimeException('Provide item_code or id column.');
            }

            $prodModel = new Product();
            $updated = 0;
            $failed = [];

            foreach ($sheet['rows'] as $i => $row) {
                $line = $i + 2;
                try {
                    $product = null;
                    $code = trim($row['item_code'] ?? '');
                    $id = (int) ($row['id'] ?? 0);
                    if ($code !== '') {
                        $product = $prodModel->findByItemCode($code);
                    } elseif ($id > 0) {
                        $product = $prodModel->find($id);
                    }
                    if (!$product) {
                        throw new RuntimeException('product not found');
                    }
                    $stock = (float) ($row['stock'] ?? -1);
                    if ($stock < 0) {
                        throw new RuntimeException('invalid stock');
                    }
                    $prodModel->updateStock((int) $product['id'], $stock);
                    $updated++;
                } catch (Throwable $e) {
                    $failed[] = ['line' => $line, 'reason' => $e->getMessage()];
                }
            }

            $_SESSION['bulk_stock_result'] = [
                'updated' => $updated,
                'failed'  => $failed,
                'total'   => count($sheet['rows']),
            ];
            flash('success', "Bulk stock update finished: {$updated} updated, " . count($failed) . ' failed.');
            redirect('products/bulk-stock');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('products/bulk-stock');
        }
    }

    public function downloadTemplate(string $type): void
    {
        if ($type === 'products') {
            $file = dirname(__DIR__, 2) . '/database/templates/products_bulk_upload.csv';
            $name = 'veggiicart_products_bulk_upload.csv';
        } elseif ($type === 'stock') {
            $file = dirname(__DIR__, 2) . '/database/templates/products_bulk_stock.csv';
            $name = 'veggiicart_bulk_stock_update.csv';
        } else {
            http_response_code(404);
            echo 'Template not found';
            return;
        }
        if (!is_file($file)) {
            http_response_code(404);
            echo 'Template missing';
            return;
        }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        readfile($file);
        exit;
    }

    private function storeImportFile(string $prefix): string
    {
        if (empty($_FILES['file']['name']) || ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Please choose a CSV or XLSX file to upload.');
        }
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'xlsx', 'txt'], true)) {
            throw new RuntimeException('Only .csv or .xlsx files are supported.');
        }
        $dir = PUBLIC_PATH . '/uploads/imports';
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create import directory.');
        }
        $dest = $dir . '/' . $prefix . '_' . date('YmdHis') . '.' . $ext;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
            throw new RuntimeException('Failed to store upload.');
        }
        return $dest;
    }

    private function validatedPayload(?int $ignoreId = null): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $unit = trim((string) ($_POST['unit'] ?? ''));
        $moq = (float) ($_POST['moq'] ?? 0);
        $price = (float) ($_POST['price'] ?? 0);
        $stock = (float) ($_POST['stock'] ?? 0);
        $itemCode = trim((string) ($_POST['item_code'] ?? '')) ?: null;

        if ($name === '' || $categoryId <= 0 || $unit === '') {
            throw new InvalidArgumentException('Name, category, and unit are required.');
        }
        if ($moq <= 0 || $price < 0 || $stock < 0) {
            throw new InvalidArgumentException('MOQ must be > 0; price/stock cannot be negative.');
        }
        if (!(new Category())->find($categoryId)) {
            throw new InvalidArgumentException('Selected category does not exist.');
        }
        if ($itemCode) {
            $existing = (new Product())->findByItemCode($itemCode);
            if ($existing && (int) $existing['id'] !== (int) $ignoreId) {
                throw new InvalidArgumentException("Item code \"{$itemCode}\" is already in use.");
            }
        }

        $inStock = isset($_POST['in_stock']) ? 1 : 0;
        if ($stock <= 0) {
            $inStock = 0;
        }

        return [
            'category_id' => $categoryId,
            'name'        => $name,
            'unit'        => $unit,
            'moq'         => $moq,
            'price'       => $price,
            'stock'       => $stock,
            'batch_no'    => trim((string) ($_POST['batch_no'] ?? '')) ?: null,
            'item_code'   => $itemCode,
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'grade'       => trim((string) ($_POST['grade'] ?? '')) ?: null,
            'origin'      => trim((string) ($_POST['origin'] ?? '')) ?: null,
            'in_stock'    => $inStock,
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            'image_url'   => null,
        ];
    }

    private function saveProductImages(int $productId): void
    {
        $images = new ProductImage();
        $remove = array_map('intval', (array) ($_POST['remove_image'] ?? []));
        foreach ($remove as $rid) {
            if ($rid > 0) {
                $images->deleteForProduct($rid, $productId);
            }
        }
        $sort = $_POST['image_sort'] ?? [];
        if (is_array($sort)) {
            foreach ($sort as $id => $order) {
                $images->updateSort((int) $id, $productId, (int) $order);
            }
        }
        $uploaded = $this->normalizeUploadedFiles('images');
        $existing = $images->forProduct($productId);
        $nextSort = 0;
        foreach ($existing as $row) {
            $nextSort = max($nextSort, (int) $row['sort_order'] + 1);
        }
        $newIds = [];
        foreach ($uploaded as $file) {
            $url = UploadService::storeImage($file, 'products');
            if ($url) {
                $newIds[] = $images->add($productId, $url, $nextSort, false);
                $nextSort++;
            }
        }
        $primary = (string) ($_POST['primary_image'] ?? '');
        if (str_starts_with($primary, 'new:') && $newIds !== []) {
            $idx = (int) substr($primary, 4);
            $pick = $newIds[$idx] ?? $newIds[0];
            $images->setPrimary($productId, $pick);
        } elseif (ctype_digit($primary) && (int) $primary > 0) {
            $images->setPrimary($productId, (int) $primary);
        }
        $images->ensurePrimary($productId);
    }

    /** @return array<int,array<string,mixed>> */
    private function normalizeUploadedFiles(string $field): array
    {
        if (empty($_FILES[$field]['name'])) {
            return [];
        }
        if (!is_array($_FILES[$field]['name'])) {
            return [$_FILES[$field]];
        }
        $out = [];
        foreach ($_FILES[$field]['name'] as $i => $name) {
            if ($name === '' || $name === null) {
                continue;
            }
            $out[] = [
                'name'     => (string) $name,
                'type'     => $_FILES[$field]['type'][$i] ?? '',
                'tmp_name' => $_FILES[$field]['tmp_name'][$i] ?? '',
                'error'    => $_FILES[$field]['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size'     => $_FILES[$field]['size'][$i] ?? 0,
            ];
        }
        return $out;
    }

    private function parseBool(string $value, bool $default): bool
    {
        $v = strtolower(trim($value));
        if ($v === '') {
            return $default;
        }
        return in_array($v, ['1', 'true', 'yes', 'y'], true);
    }
}
