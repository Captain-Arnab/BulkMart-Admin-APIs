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
          <label class="form-label">Name *</label>
          <input type="text" name="name" class="form-control" required maxlength="120"
                 value="<?= e($category['name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Image</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <?php if (!empty($category['image_url'])): ?>
            <div class="mt-2"><img src="<?= e(media($category['image_url'])) ?>" alt="" style="height:56px;border-radius:8px"></div>
          <?php endif; ?>
        </div>
        <div class="col-12">
          <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save changes' : 'Create category' ?></button>
          <a href="<?= e(url('categories')) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
      <?php if ($isEdit): ?>
        <form method="POST" action="<?= e(url('categories/' . (int) $category['id'] . '/delete')) ?>" class="mt-3"
              onsubmit="return confirm('Delete this category? Categories that still have products cannot be deleted.');">
          <button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i>Delete category</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>
