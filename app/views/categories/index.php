<?php
/** @var array $categories */
$success = $success ?? null;
$error = $error ?? null;
?>
<div class="pagetitle d-flex flex-wrap justify-content-between align-items-center gap-2">
  <div>
    <h1>Categories</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
        <li class="breadcrumb-item active">Categories</li>
      </ol>
    </nav>
  </div>
  <a href="<?= e(url('categories/create')) ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Category</a>
</div>

<form id="vcBulkDeleteForm" method="POST" action="<?= e(url('categories/bulk-delete')) ?>"></form>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="card vc-filter-card mb-3"><div class="card-body py-3">
    <form class="row g-2 align-items-end" method="GET" action="<?= e(url('categories')) ?>">
      <div class="col-md-6"><label class="form-label mb-1">Search</label><input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" class="form-control" placeholder="Category name"></div>
      <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?= e(url('categories')) ?>">Reset</a></div>
    </form>
  </div></div>
  <div class="card">
    <div class="card-body pt-3">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <div class="text-muted small" id="vcSelectedCount">0 selected</div>
        <button class="btn btn-sm btn-outline-danger" type="submit" form="vcBulkDeleteForm" id="vcBulkDeleteBtn" disabled
                onclick="return confirm('Delete the selected categories? Categories that still have products will be skipped.');">
          <i class="bi bi-trash me-1"></i>Delete selected
        </button>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th style="width:36px">
                <input class="form-check-input" type="checkbox" id="vcSelectAll" title="Select all" form="vcBulkDeleteForm">
              </th>
              <th>Image</th>
              <th>Name</th>
              <th>Products</th>
              <th>Updated</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$categories): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">No categories yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($categories as $cat): ?>
            <tr>
              <td>
                <input class="form-check-input vc-row-check" type="checkbox" name="ids[]" value="<?= (int) $cat['id'] ?>" form="vcBulkDeleteForm">
              </td>
              <td style="width:64px">
                <?php if (!empty($cat['image_url'])): ?>
                  <img src="<?= e(media($cat['image_url'])) ?>" alt="" class="rounded" style="width:48px;height:48px;object-fit:cover">
                <?php else: ?>
                  <div class="vc-thumb-placeholder"><i class="bi bi-tags"></i></div>
                <?php endif; ?>
              </td>
              <td class="fw-semibold"><?= e($cat['name']) ?></td>
              <td><span class="badge bg-primary"><?= (int) $cat['product_count'] ?></span></td>
              <td class="text-muted small"><?= e(date('d M Y', strtotime($cat['updated_at']))) ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="<?= e(url('categories/' . $cat['id'] . '/edit')) ?>">Edit</a>
                <form class="d-inline" method="POST" action="<?= e(url('categories/' . $cat['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this category? Categories that still have products cannot be deleted.');">
                  <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<script>
(function () {
  var all = document.getElementById('vcSelectAll');
  var boxes = document.querySelectorAll('.vc-row-check');
  var count = document.getElementById('vcSelectedCount');
  var btn = document.getElementById('vcBulkDeleteBtn');
  function sync() {
    var n = 0;
    boxes.forEach(function (b) { if (b.checked) n++; });
    if (count) count.textContent = n + ' selected';
    if (btn) btn.disabled = n === 0;
    if (all) all.checked = boxes.length > 0 && n === boxes.length;
  }
  if (all) {
    all.addEventListener('change', function () {
      boxes.forEach(function (b) { b.checked = all.checked; });
      sync();
    });
  }
  boxes.forEach(function (b) { b.addEventListener('change', sync); });
  sync();
})();
</script>
