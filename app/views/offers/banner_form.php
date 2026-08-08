<?php $isEdit=!empty($banner); $error=$error??null; ?>
<div class="pagetitle"><h1><?= e($title) ?></h1>
<nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('offers')) ?>">Offers</a></li><li class="breadcrumb-item active">Banner</li></ol></nav></div>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section"><div class="card"><div class="card-body pt-4">
<form method="POST" enctype="multipart/form-data" class="row g-3" action="<?= e($isEdit?url('offers/banners/'.$banner['id'].'/update'):url('offers/banners')) ?>">
  <div class="col-md-6"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($banner['title']??'') ?>"></div>
  <div class="col-md-6"><label class="form-label">Link</label><input type="text" name="link" class="form-control" value="<?= e($banner['link']??'') ?>"></div>
  <div class="col-md-4"><label class="form-label">Active from</label><input type="datetime-local" name="active_from" class="form-control" value="<?= e(!empty($banner['active_from'])?date('Y-m-d\TH:i',strtotime($banner['active_from'])):'') ?>"></div>
  <div class="col-md-4"><label class="form-label">Active to</label><input type="datetime-local" name="active_to" class="form-control" value="<?= e(!empty($banner['active_to'])?date('Y-m-d\TH:i',strtotime($banner['active_to'])):'') ?>"></div>
  <div class="col-md-4"><label class="form-label">Sort order</label><input type="number" name="sort_order" class="form-control" value="<?= e($banner['sort_order']??'0') ?>"></div>
  <div class="col-md-6"><label class="form-label">Image <?= $isEdit?'':'*' ?></label><input type="file" name="image" class="form-control" accept="image/*" <?= $isEdit?'':'required' ?>>
    <?php if (!empty($banner['image_url'])): ?><img src="<?= e(media($banner['image_url'])) ?>" class="mt-2" style="height:60px" alt=""><?php endif; ?>
  </div>
  <div class="col-md-6"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= !isset($banner['is_active'])||(int)$banner['is_active']===1?'checked':'' ?>><label class="form-check-label" for="is_active">Active</label></div></div>
  <div class="col-12"><button class="btn btn-primary" type="submit">Save</button> <a href="<?= e(url('offers')) ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form>
</div></div></section>
