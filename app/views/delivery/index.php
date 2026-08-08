<?php
/** @var string $tab */
/** @var array $result */
/** @var array $managers */
$success = $success ?? null;
$error = $error ?? null;
$user = auth_user();
?>
<div class="pagetitle">
  <h1>Delivery Management</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url(auth_home_path())) ?>">Home</a></li>
      <li class="breadcrumb-item active">Delivery</li>
    </ol>
  </nav>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<ul class="nav nav-tabs nav-tabs-bordered mb-3">
  <li class="nav-item">
    <a class="nav-link <?= $tab === 'queue' ? 'active' : '' ?>" href="<?= e(url('delivery?tab=queue')) ?>">Active queue</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?= $tab === 'history' ? 'active' : '' ?>" href="<?= e(url('delivery?tab=history')) ?>">History</a>
  </li>
</ul>

<section class="section">
  <?php if (!$result['rows']): ?>
    <div class="card"><div class="card-body py-5 text-center text-muted">No orders in this view.</div></div>
  <?php endif; ?>

  <div class="row g-3">
    <?php foreach ($result['rows'] as $o): ?>
      <?php $badge = Order::badge($o['status']); ?>
      <div class="col-lg-6">
        <div class="card vc-delivery-card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <div class="fw-bold"><?= e($o['order_number']) ?></div>
                <div class="text-muted small"><?= e(date('d M Y, H:i', strtotime($o['placed_at']))) ?></div>
              </div>
              <span class="<?= e($badge['class']) ?>">
                <i class="bi <?= e($badge['icon']) ?>"></i>
                <?= e($badge['label']) ?>
              </span>
            </div>
            <div class="mb-2">
              <div class="fw-semibold"><?= e($o['business_name']) ?></div>
              <div class="small"><?= e($o['mobile']) ?></div>
              <div class="small text-muted">
                <?= e($o['line1'] ?? '') ?>, <?= e($o['city'] ?? '') ?> <?= e($o['pincode'] ?? '') ?>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div class="small text-muted"><?= (int)$o['item_count'] ?> items · ₹<?= e(number_format((float)$o['total'], 2)) ?> COD</div>
              <a href="<?= e(url('delivery/' . $o['id'])) ?>" class="btn btn-sm btn-primary">Open</a>
            </div>
            <?php if (!empty($o['delivery_manager_name']) && ($user['role'] ?? '') === 'super_admin'): ?>
              <div class="small mt-2 text-muted">Assigned: <?= e($o['delivery_manager_name']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if ($result['pages'] > 1): ?>
    <nav class="mt-3">
      <ul class="pagination pagination-sm">
        <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
          <li class="page-item <?= $p === $result['page'] ? 'active' : '' ?>">
            <a class="page-link" href="<?= e(url('delivery?tab=' . $tab . '&page=' . $p)) ?>"><?= $p ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</section>
