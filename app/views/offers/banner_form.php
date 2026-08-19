<?php $isEdit=!empty($banner); $error=$error??null; ?>
<div class="pagetitle"><h1><?= e($title) ?></h1>
<nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('offers')) ?>">Offers</a></li><li class="breadcrumb-item active">Banner</li></ol></nav></div>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section"><div class="card"><div class="card-body pt-4">
<p class="text-muted small mb-3">All fields are optional. Title and description appear on the website hero when provided. Active dates and the Active checkbox are not required — leave dates empty for no window, and uncheck Active to hide the banner.</p>
<form method="POST" enctype="multipart/form-data" class="row g-3" action="<?= e($isEdit?url('offers/banners/'.$banner['id'].'/update'):url('offers/banners')) ?>">
  <div class="col-md-6">
    <label class="form-label">Title <span class="text-muted fw-normal">(max <?= (int) VC_BANNER_TITLE_MAX ?> characters)</span></label>
    <input type="text" name="title" class="form-control" maxlength="<?= (int) VC_BANNER_TITLE_MAX ?>" data-char-count value="<?= e($banner['title']??'') ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Link <span class="text-muted fw-normal">(max <?= (int) VC_BANNER_LINK_MAX ?> characters)</span></label>
    <input type="text" name="link" class="form-control" maxlength="<?= (int) VC_BANNER_LINK_MAX ?>" data-char-count value="<?= e($banner['link']??'') ?>">
  </div>
  <div class="col-12">
    <label class="form-label">Description <span class="text-muted fw-normal">(max <?= (int) VC_BANNER_DESC_MAX ?> characters)</span></label>
    <textarea name="description" class="form-control" rows="3" maxlength="<?= (int) VC_BANNER_DESC_MAX ?>" data-char-count><?= e($banner['description']??'') ?></textarea>
  </div>
  <div class="col-md-4"><label class="form-label">Active from</label><input type="datetime-local" name="active_from" class="form-control" value="<?= e(!empty($banner['active_from'])?date('Y-m-d\TH:i',strtotime($banner['active_from'])):'') ?>"></div>
  <div class="col-md-4"><label class="form-label">Active to</label><input type="datetime-local" name="active_to" class="form-control" value="<?= e(!empty($banner['active_to'])?date('Y-m-d\TH:i',strtotime($banner['active_to'])):'') ?>"></div>
  <div class="col-md-4"><label class="form-label">Sort order</label><input type="number" name="sort_order" class="form-control" value="<?= e($banner['sort_order']??'0') ?>"></div>
  <div class="col-md-6">
    <label class="form-label">Image / file</label>
    <input type="file" name="image" class="form-control" accept="<?= e(media_accept_attr()) ?>">
    <div class="form-text"><?= e(media_formats_hint()) ?></div>
    <?php if (!empty($banner['image_url'])): ?>
      <div class="vc-admin-gallery mt-2"><div class="vc-admin-gallery-card"><?= media_preview_html($banner['image_url']) ?></div></div>
    <?php endif; ?>
  </div>
  <div class="col-md-6"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= !isset($banner['is_active'])||(int)$banner['is_active']===1?'checked':'' ?>><label class="form-check-label" for="is_active">Active</label></div></div>
  <div class="col-12"><button class="btn btn-primary" type="submit">Save</button> <a href="<?= e(url('offers')) ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form>
</div></div></section>
