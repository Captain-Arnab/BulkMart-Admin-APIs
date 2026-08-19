<?php
/** @var array $products */
/** @var array $categories */
/** @var array $result */
$success = $success ?? null;
$error = $error ?? null;
$q = $q ?? '';
$categoryId = $categoryId ?? null;
$result = $result ?? ['page' => 1, 'pages' => 1, 'total' => count($products ?? [])];
?>
<div class="pagetitle d-flex flex-wrap justify-content-between align-items-center gap-2">
  <div>
    <h1>Products &amp; Stock</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
        <li class="breadcrumb-item active">Products</li>
      </ol>
    </nav>
  </div>
  <div class="d-flex flex-wrap gap-2 align-items-center">
    <span class="text-muted small"><?= (int) $result['total'] ?> products · 20 / page</span>
    <a href="<?= e(url('products/bulk-upload')) ?>" class="btn btn-outline-primary btn-sm">Bulk Upload</a>
    <a href="<?= e(url('products/bulk-stock')) ?>" class="btn btn-outline-primary btn-sm">Bulk Stock</a>
    <a href="<?= e(url('products/add')) ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
  </div>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="card mb-3">
    <div class="card-body py-3">
      <form class="row g-2 align-items-end" method="GET" action="<?= e(url('products')) ?>">
        <div class="col-md-5">
          <label class="form-label mb-1">Search</label>
          <input type="text" name="q" value="<?= e($q) ?>" class="form-control" placeholder="Name, item code, batch…">
        </div>
        <div class="col-md-3">
          <label class="form-label mb-1">Category</label>
          <select name="category_id" class="form-select">
            <option value="">All categories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= (int) $cat['id'] ?>" <?= (int) $categoryId === (int) $cat['id'] ? 'selected' : '' ?>>
                <?= e($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label mb-1">Stock</label>
          <select name="low_stock" class="form-select">
            <option value="">All</option>
            <option value="1" <?= !empty($lowStock) ? 'selected' : '' ?>>Low stock only</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-primary" type="submit">Filter</button>
          <a class="btn btn-outline-secondary" href="<?= e(url('products')) ?>">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body pt-3">
      <div class="table-responsive">
        <table class="table table-hover align-middle vc-products-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Category</th>
              <th>Item Code</th>
              <th>Batch No</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Status</th>
              <th>Active</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$products): ?>
            <tr><td colspan="9" class="text-center text-muted py-4">No products found.</td></tr>
          <?php endif; ?>
          <?php foreach ($products as $p): ?>
            <?php $badge = Product::stockBadge($p); ?>
            <tr class="<?= (int)$p['is_active'] === 1 ? '' : 'table-secondary' ?>">
              <td>
                <div class="d-flex align-items-center gap-2">
                  <?php if (!empty($p['image_url'])): ?>
                    <img src="<?= e(media($p['image_url'])) ?>" alt="" class="rounded" style="width:44px;height:44px;object-fit:cover">
                  <?php else: ?>
                    <div class="vc-thumb-placeholder"><i class="bi bi-box-seam"></i></div>
                  <?php endif; ?>
                  <div>
                    <div class="fw-semibold"><?= e($p['name']) ?></div>
                    <div class="small text-muted"><?= e($p['unit']) ?></div>
                  </div>
                </div>
              </td>
              <td><?= e($p['category_name']) ?></td>
              <td><code><?= e($p['item_code'] ?: '—') ?></code></td>
              <td><?= e($p['batch_no'] ?: '—') ?></td>
              <td>₹<?= e(number_format((float)$p['price'], 2)) ?></td>
              <td style="min-width:140px">
                <form method="POST" action="<?= e(url('products/' . $p['id'] . '/stock')) ?>" class="d-flex gap-1">
                  <input type="number" step="0.01" min="0" name="stock" value="<?= e($p['stock']) ?>" class="form-control form-control-sm" style="max-width:90px">
                  <button class="btn btn-sm btn-outline-primary" type="submit" title="Save stock">Save</button>
                </form>
              </td>
              <td><span class="badge <?= e($badge['class']) ?>"><?= e($badge['label']) ?></span></td>
              <td><?= (int)$p['is_active'] === 1 ? '<span class="text-success">Yes</span>' : '<span class="text-muted">No</span>' ?></td>
              <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-primary" href="<?= e(url('products/' . $p['id'] . '/edit')) ?>">Edit</a>
                <form class="d-inline" method="POST" action="<?= e(url('products/' . $p['id'] . '/deactivate')) ?>">
                  <button class="btn btn-sm btn-outline-secondary" type="submit">
                    <?= (int)$p['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php if (($result['pages'] ?? 1) > 1): ?>
        <?php
          $page = (int) $result['page'];
          $pages = (int) $result['pages'];
          $baseUrl = url('products');
          $query = [
              'q' => $q !== '' ? $q : null,
              'category_id' => $categoryId ?: null,
              'low_stock' => !empty($lowStock) ? '1' : null,
          ];
          require VIEW_PATH . '/shared/pagination.php';
        ?>
      <?php endif; ?>
    </div>
  </div>
</section>
