<?php
/** @var ?array $product */
/** @var array $categories */
$isEdit = !empty($product);
$error = $error ?? null;
$p = $product ?? [];
$images = $images ?? [];
$returnQuery = $returnQuery ?? [];
$returnPath = $returnQuery !== [] ? ('products?' . http_build_query($returnQuery)) : 'products';
?>
<div class="pagetitle">
  <h1><?= e($title) ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= e(url($returnPath)) ?>">Products</a></li>
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
        <?php if ($isEdit): ?>
          <input type="hidden" name="return_to" value="<?= e(http_build_query($returnQuery)) ?>">
        <?php endif; ?>
        <div class="col-md-6">
          <label class="form-label">Name * <span class="text-muted fw-normal">(max <?= (int) VC_PRODUCT_NAME_MAX ?> characters)</span></label>
          <input type="text" name="name" class="form-control" required maxlength="<?= (int) VC_PRODUCT_NAME_MAX ?>" data-char-count value="<?= e($p['name'] ?? '') ?>">
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
          <input type="text" name="item_code" class="form-control" value="<?= e($p['item_code'] ?? '') ?>"
                 placeholder="Leave blank to auto-generate (e.g. VC-A7K2M9)">
          <div class="form-text">Blank on create = random unique code. Existing codes are kept on edit unless you change them.</div>
        </div>
        <div class="col-md-4">
          <label class="form-label">Batch no</label>
          <input type="text" name="batch_no" class="form-control" value="<?= e($p['batch_no'] ?? '') ?>"
                 placeholder="Filled as batches come in">
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
        <div class="col-12">
          <label class="form-label">Product images / files</label>
          <p class="text-muted small mb-2">Upload gallery files. Mark one as the cover. Cover is also used as the catalog thumbnail. <?= e(media_formats_hint()) ?></p>
          <?php if ($images): ?>
            <div class="vc-admin-gallery">
              <?php foreach ($images as $img): ?>
                <?php $imgId = (int) $img['id']; ?>
                <div class="vc-admin-gallery-card">
                  <?= media_preview_html($img['image_url']) ?>
                  <label class="form-label small mb-1">Order</label>
                  <input type="number" class="form-control form-control-sm" name="image_sort[<?= $imgId ?>]" value="<?= (int) $img['sort_order'] ?>" min="0">
                  <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" name="primary_image" id="primary_<?= $imgId ?>" value="<?= $imgId ?>" <?= (int) $img['is_primary'] === 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="primary_<?= $imgId ?>">Cover image</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remove_image[]" id="remove_<?= $imgId ?>" value="<?= $imgId ?>">
                    <label class="form-check-label text-danger" for="remove_<?= $imgId ?>">Remove</label>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <input type="file" name="images[]" id="vcProductImages" class="form-control" accept="<?= e(media_accept_attr()) ?>" multiple>
          <div class="vc-admin-gallery mt-3" id="vcNewImagePreview"></div>
        </div>
        <div class="col-12">
          <label class="form-label">Description <span class="text-muted fw-normal">(max <?= (int) VC_PRODUCT_DESC_MAX ?> characters)</span></label>
          <textarea name="description" class="form-control" rows="3" maxlength="<?= (int) VC_PRODUCT_DESC_MAX ?>" data-char-count><?= e($p['description'] ?? '') ?></textarea>
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
          <a href="<?= e(url($returnPath)) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
      <?php if ($isEdit): ?>
        <form method="POST" action="<?= e(url('products/' . (int) $p['id'] . '/delete')) ?>" class="mt-3"
              data-vc-confirm="Delete this product? Past orders keep the item name and price."
              data-vc-confirm-danger
              data-vc-confirm-title="Delete product">
          <input type="hidden" name="return_to" value="<?= e(http_build_query($returnQuery)) ?>">
          <button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i>Delete product</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>
<script>
(function () {
  var input = document.getElementById('vcProductImages');
  var preview = document.getElementById('vcNewImagePreview');
  if (!input || !preview) return;
  input.addEventListener('change', function () {
    preview.innerHTML = '';
    Array.prototype.forEach.call(input.files || [], function (file, i) {
      var card = document.createElement('div');
      card.className = 'vc-admin-gallery-card';
      if (file.type === 'application/pdf' || /\.pdf$/i.test(file.name || '')) {
        var frame = document.createElement('iframe');
        frame.className = 'vc-media-preview-pdf';
        frame.title = 'PDF preview';
        frame.src = URL.createObjectURL(file);
        card.appendChild(frame);
      } else {
        var img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        card.appendChild(img);
      }
      var radio = document.createElement('div');
      radio.className = 'form-check mt-2';
      radio.innerHTML = '<input class="form-check-input" type="radio" name="primary_image" id="primary_new_' + i + '" value="new:' + i + '">' +
        '<label class="form-check-label" for="primary_new_' + i + '">Cover image</label>';
      card.appendChild(radio);
      preview.appendChild(card);
    });
  });
})();
</script>
