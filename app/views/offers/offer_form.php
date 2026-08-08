<?php $isEdit=!empty($offer); $error=$error??null; ?>
<div class="pagetitle"><h1><?= e($title) ?></h1>
<nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('offers')) ?>">Offers</a></li><li class="breadcrumb-item active">Offer</li></ol></nav></div>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section"><div class="card"><div class="card-body pt-4">
<form method="POST" class="row g-3" action="<?= e($isEdit?url('offers/'.$offer['id'].'/update'):url('offers')) ?>">
  <div class="col-md-6"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($offer['title']??'') ?>"></div>
  <div class="col-md-3"><label class="form-label">Discount type *</label>
    <select name="discount_type" class="form-select" required>
      <option value="percentage" <?= ($offer['discount_type']??'')==='percentage'?'selected':'' ?>>Percentage</option>
      <option value="flat" <?= ($offer['discount_type']??'')==='flat'?'selected':'' ?>>Flat</option>
    </select>
  </div>
  <div class="col-md-3"><label class="form-label">Discount value *</label><input type="number" step="0.01" min="0.01" name="discount_value" class="form-control" required value="<?= e($offer['discount_value']??'') ?>"></div>
  <div class="col-md-3"><label class="form-label">Min qty</label><input type="number" step="0.01" min="0" name="min_qty" class="form-control" value="<?= e($offer['min_qty']??'') ?>"></div>
  <div class="col-md-3"><label class="form-label">Category</label>
    <select name="category_id" class="form-select"><option value="">All</option>
      <?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)($offer['category_id']??0)===(int)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3"><label class="form-label">Coupon code</label><input type="text" name="coupon_code" class="form-control" value="<?= e($offer['coupon_code']??'') ?>"></div>
  <div class="col-md-3"><label class="form-label">Valid from</label><input type="datetime-local" name="valid_from" class="form-control" value="<?= e(!empty($offer['valid_from'])?date('Y-m-d\TH:i',strtotime($offer['valid_from'])):'') ?>"></div>
  <div class="col-md-3"><label class="form-label">Valid till</label><input type="datetime-local" name="valid_till" class="form-control" value="<?= e(!empty($offer['valid_till'])?date('Y-m-d\TH:i',strtotime($offer['valid_till'])):'') ?>"></div>
  <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= !isset($offer['is_active'])||(int)$offer['is_active']===1?'checked':'' ?>><label class="form-check-label" for="is_active">Active</label></div></div>
  <div class="col-12"><button class="btn btn-primary" type="submit">Save</button> <a href="<?= e(url('offers')) ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form>
</div></div></section>
