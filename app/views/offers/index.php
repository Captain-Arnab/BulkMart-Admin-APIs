<?php $success=$success??null; $error=$error??null; ?>
<div class="pagetitle d-flex justify-content-between flex-wrap gap-2 align-items-center">
  <div><h1>Offers &amp; Banners</h1>
  <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li><li class="breadcrumb-item active">Offers</li></ol></nav></div>
  <div class="d-flex gap-2">
    <a href="<?= e(url('offers/banners/create')) ?>" class="btn btn-outline-primary btn-sm">Add banner</a>
    <a href="<?= e(url('offers/create')) ?>" class="btn btn-primary btn-sm">Add offer</a>
  </div>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="card vc-filter-card mb-3"><div class="card-body py-3">
    <form class="row g-2 align-items-end" method="GET" action="<?= e(url('offers')) ?>">
      <div class="col-md-5"><label class="form-label mb-1">Search</label><input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" class="form-control" placeholder="Title / coupon"></div>
      <div class="col-md-3"><label class="form-label mb-1">Active</label>
        <select name="active" class="form-select">
          <option value="">All</option>
          <option value="1" <?= ($filters['active'] ?? '') === '1' ? 'selected' : '' ?>>Active</option>
          <option value="0" <?= ($filters['active'] ?? '') === '0' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
      <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?= e(url('offers')) ?>">Reset</a></div>
    </form>
  </div></div>

  <div class="card mb-3"><div class="card-body">
    <h5 class="card-title">Banners</h5>
    <div class="table-responsive"><table class="table align-middle">
      <thead><tr><th>Image</th><th>Title</th><th>Sort</th><th>Active window</th><th>Active</th><th></th></tr></thead>
      <tbody>
      <?php if (!$banners): ?><tr><td colspan="6" class="text-muted">No banners yet.</td></tr><?php endif; ?>
      <?php foreach ($banners as $b): ?>
        <tr>
          <td><?php if ($b['image_url']): ?><img src="<?= e(media($b['image_url'])) ?>" style="height:40px;border-radius:4px" alt=""><?php endif; ?></td>
          <td><?= e($b['title']) ?><?php if ($b['link']): ?><div class="small text-muted"><?= e($b['link']) ?></div><?php endif; ?></td>
          <td><?= (int)$b['sort_order'] ?></td>
          <td class="small"><?= e(($b['active_from']?date('d M Y',strtotime($b['active_from'])):'—').' → '.($b['active_to']?date('d M Y',strtotime($b['active_to'])):'—')) ?></td>
          <td><?= (int)$b['is_active']?'Yes':'No' ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-outline-primary" href="<?= e(url('offers/banners/'.$b['id'].'/edit')) ?>">Edit</a>
            <form class="d-inline" method="POST" action="<?= e(url('offers/banners/'.$b['id'].'/delete')) ?>" onsubmit="return confirm('Delete banner?');"><button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div></div>

  <div class="card"><div class="card-body">
    <h5 class="card-title">Offers</h5>
    <div class="table-responsive"><table class="table align-middle">
      <thead><tr><th>Title</th><th>Discount</th><th>Min qty</th><th>Category</th><th>Coupon</th><th>Valid</th><th>Active</th><th></th></tr></thead>
      <tbody>
      <?php if (!$offers): ?><tr><td colspan="8" class="text-muted">No offers yet.</td></tr><?php endif; ?>
      <?php foreach ($offers as $o): ?>
        <tr>
          <td><?= e($o['title']) ?></td>
          <td><?= e($o['discount_type']==='percentage' ? $o['discount_value'].'%' : '₹'.$o['discount_value']) ?></td>
          <td><?= e($o['min_qty'] ?? '—') ?></td>
          <td><?= e($o['category_name'] ?? 'All') ?></td>
          <td><?= e($o['coupon_code'] ?: '—') ?></td>
          <td class="small"><?= e(($o['valid_from']?date('d M Y',strtotime($o['valid_from'])):'—').' → '.($o['valid_till']?date('d M Y',strtotime($o['valid_till'])):'—')) ?></td>
          <td><?= (int)$o['is_active']?'Yes':'No' ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-outline-primary" href="<?= e(url('offers/'.$o['id'].'/edit')) ?>">Edit</a>
            <form class="d-inline" method="POST" action="<?= e(url('offers/'.$o['id'].'/delete')) ?>" onsubmit="return confirm('Delete offer?');"><button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div></div>
</section>
