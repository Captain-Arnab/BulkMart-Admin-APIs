<?php
/** @var ?array $category */
$isEdit = !empty($category);
$error = $error ?? null;
?>
<div class="pagetitle">
  <h1><?= e($title) ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= e(url('categories')) ?>">Categories</a></li>
      <li class="breadcrumb-item active"><?= $isEdit ? 'Edit' : 'Add' ?></li>
    </ol>
  </nav>
</div>

<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="card">
    <div class="card-body pt-4">
      <form method="POST" enctype="multipart/form-data"
            action="<?= e($isEdit ? url('categories/' . $category['id'] . '/update') : url('categories')) ?>"
            class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Name * <span class="text-muted fw-normal">(max <?= (int) VC_CATEGORY_NAME_MAX ?> characters)</span></label>
          <input type="text" name="name" class="form-control" required maxlength="<?= (int) VC_CATEGORY_NAME_MAX ?>"
                 data-char-count value="<?= e($category['name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Image / file</label>
          <input type="file" name="image" class="form-control" accept="<?= e(media_accept_attr()) ?>">
          <div class="form-text"><?= e(media_formats_hint()) ?></div>
          <?php if (!empty($category['image_url'])): ?>
            <div class="vc-admin-gallery mt-2"><div class="vc-admin-gallery-card"><?= media_preview_html($category['image_url']) ?></div></div>
          <?php endif; ?>
        </div>
        <div class="col-12">
          <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save changes' : 'Create category' ?></button>
          <a href="<?= e(url('categories')) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
      <?php if ($isEdit): ?>
        <form method="POST" action="<?= e(url('categories/' . (int) $category['id'] . '/delete')) ?>" class="mt-3"
              data-vc-confirm="Delete this category? Categories that still have products cannot be deleted."
              data-vc-confirm-danger
              data-vc-confirm-title="Delete category">
          <button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i>Delete category</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>
