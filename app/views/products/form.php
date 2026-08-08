<?php
/** @var ?array $product */
/** @var array $categories */
$isEdit = !empty($product);
$error = $error ?? null;
$p = $product ?? [];
?>
<div class="pagetitle">
  <h1><?= e($title) ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= e(url('products')) ?>">Products</a></li>
      <li class="breadcrumb-item active"><?= $isEdit ? 'Edit' : 'Add' ?></li>
    </ol>
  </nav>
</div>

<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="card">
    <div class="card-body pt-4">
      <form method="POST" enctype="multipart/form-data" class="row g-3"
            action="<?= e($isEdit ? url('products/' . $p['id'] . '/update') : url('products')) ?>">
        <div class="col-md-6">
          <label class="form-label">Name *</label>
          <input type="text" name="name" class="form-control" required value="<?= e($p['name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-select" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= (int)$cat['id'] ?>" <?= (int)($p['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
                <?= e($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Unit *</label>
          <input type="text" name="unit" class="form-control" required placeholder="per kg / per bunch"
                 value="<?= e($p['unit'] ?? 'per kg') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">MOQ *</label>
          <input type="number" step="0.01" min="0.01" name="moq" class="form-control" required
                 value="<?= e($p['moq'] ?? '1') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Price (₹) *</label>
          <input type="number" step="0.01" min="0" name="price" class="form-control" required
                 value="<?= e($p['price'] ?? '0') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Stock *</label>
          <input type="number" step="0.01" min="0" name="stock" class="form-control" required
                 value="<?= e($p['stock'] ?? '0') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Item code</label>
          <input type="text" name="item_code" class="form-control" value="<?= e($p['item_code'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Batch no</label>
          <input type="text" name="batch_no" class="form-control" value="<?= e($p['batch_no'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Grade</label>
          <select name="grade" class="form-select">
            <option value="">—</option>
            <?php foreach (['Premium','A','B'] as $g): ?>
              <option value="<?= $g ?>" <?= ($p['grade'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Origin</label>
          <input type="text" name="origin" class="form-control" value="<?= e($p['origin'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Image</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <?php if (!empty($p['image_url'])): ?>
            <div class="mt-2"><img src="<?= e(media($p['image_url'])) ?>" alt="" style="height:56px;border-radius:8px"></div>
          <?php endif; ?>
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"><?= e($p['description'] ?? '') ?></textarea>
        </div>
        <div class="col-md-3">
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="in_stock" id="in_stock"
              <?= !isset($p['in_stock']) || (int)$p['in_stock'] === 1 ? 'checked' : '' ?>>
            <label class="form-check-label" for="in_stock">In stock flag</label>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
              <?= !isset($p['is_active']) || (int)$p['is_active'] === 1 ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Active</label>
          </div>
        </div>
        <div class="col-12">
          <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save changes' : 'Create product' ?></button>
          <a href="<?= e(url('products')) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</section>
