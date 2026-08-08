<?php
/** @var string $tab */
/** @var array $result */
/** @var array $managers */
/** @var array $filters */
/** @var array $statusOpts */
$success = $success ?? null;
$error = $error ?? null;
$user = auth_user();
$filters = $filters ?? ['q' => '', 'status' => '', 'eta_from' => '', 'eta_to' => '', 'manager_id' => '', 'mine_only' => false];
$statusOpts = $statusOpts ?? [];

$qsBase = static function (array $extra = []) use ($tab, $filters): string {
    $params = array_merge([
        'tab'        => $tab,
        'q'          => $filters['q'] ?: null,
        'status'     => $filters['status'] ?: null,
        'eta_from'   => $filters['eta_from'] ?: null,
        'eta_to'     => $filters['eta_to'] ?: null,
        'manager_id' => $filters['manager_id'] ?: null,
        'mine_only'  => !empty($filters['mine_only']) ? '1' : null,
    ], $extra);
    return http_build_query(array_filter($params, static fn ($v) => $v !== null && $v !== ''));
};
?>
<div class="pagetitle vc-pagetitle d-flex flex-wrap justify-content-between align-items-end gap-2">
  <div>
    <h1>Delivery Management</h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= e(url(auth_home_path())) ?>">Home</a></li>
        <li class="breadcrumb-item active">Delivery</li>
      </ol>
    </nav>
  </div>
  <div class="vc-page-meta text-muted small"><?= (int) ($result['total'] ?? 0) ?> orders</div>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section vc-delivery-page">
  <ul class="nav nav-pills vc-tab-pills mb-3">
    <li class="nav-item">
      <a class="nav-link <?= $tab === 'queue' ? 'active' : '' ?>" href="<?= e(url('delivery?tab=queue')) ?>">Active queue</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tab === 'history' ? 'active' : '' ?>" href="<?= e(url('delivery?tab=history')) ?>">History</a>
    </li>
  </ul>

  <div class="card vc-filter-card mb-3">
    <div class="card-body py-3">
      <form class="row g-2 align-items-end" method="GET" action="<?= e(url('delivery')) ?>">
        <input type="hidden" name="tab" value="<?= e($tab) ?>">
        <div class="col-lg-3 col-md-6">
          <label class="form-label mb-1">Search</label>
          <div class="vc-field-icon">
            <i class="bi bi-search"></i>
            <input type="text" name="q" value="<?= e($filters['q']) ?>" class="form-control" placeholder="Order # or customer">
          </div>
        </div>
        <div class="col-lg-2 col-md-6">
          <label class="form-label mb-1">Status</label>
          <select name="status" class="form-select">
            <option value="">All in <?= $tab === 'queue' ? 'queue' : 'history' ?></option>
            <?php foreach ($statusOpts as $s): ?>
              <option value="<?= e($s) ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= e(Order::STATUS_LABELS[$s] ?? $s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-2 col-md-4">
          <label class="form-label mb-1">ETA from</label>
          <input type="date" name="eta_from" value="<?= e($filters['eta_from']) ?>" class="form-control">
        </div>
        <div class="col-lg-2 col-md-4">
          <label class="form-label mb-1">ETA to</label>
          <input type="date" name="eta_to" value="<?= e($filters['eta_to']) ?>" class="form-control">
        </div>
        <?php if (($user['role'] ?? '') === 'super_admin' && $managers): ?>
        <div class="col-lg-2 col-md-4">
          <label class="form-label mb-1">Manager</label>
          <select name="manager_id" class="form-select">
            <option value="">All managers</option>
            <?php foreach ($managers as $m): ?>
              <option value="<?= (int) $m['id'] ?>" <?= (string) $filters['manager_id'] === (string) $m['id'] ? 'selected' : '' ?>><?= e($m['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
        <div class="col-lg-3 col-md-8 d-flex gap-2 flex-wrap">
          <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
          <a class="btn btn-outline-secondary" href="<?= e(url('delivery?tab=' . urlencode($tab))) ?>">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card vc-orders-card">
    <div class="card-body pt-3">
      <div class="table-responsive">
        <table class="table vc-orders-table align-middle mb-0">
          <thead>
            <tr>
              <th>Order</th>
              <th>Customer</th>
              <th>Area</th>
              <th>ETA</th>
              <th>Status</th>
              <th>COD</th>
              <?php if (($user['role'] ?? '') === 'super_admin'): ?><th>Manager</th><?php endif; ?>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$result['rows']): ?>
            <tr><td colspan="8" class="text-center text-muted py-5">No delivery orders match these filters.</td></tr>
          <?php endif; ?>
          <?php foreach ($result['rows'] as $o): ?>
            <?php $badge = Order::badge($o['status']); ?>
            <tr>
              <td>
                <a class="vc-order-id" href="<?= e(url('delivery/' . $o['id'])) ?>">
                  <span class="vc-order-id-hash">#</span><?= e($o['order_number']) ?>
                </a>
                <div class="small text-muted"><?= e(date('d M Y, H:i', strtotime($o['placed_at']))) ?></div>
              </td>
              <td>
                <div class="vc-customer-cell">
                  <span class="name"><?= e($o['business_name']) ?></span>
                  <span class="meta"><i class="bi bi-phone"></i> <?= e($o['mobile']) ?></span>
                </div>
              </td>
              <td class="small">
                <?= e($o['city'] ?? '') ?>
                <?php if (!empty($o['pincode'])): ?><div class="text-muted"><?= e($o['pincode']) ?></div><?php endif; ?>
              </td>
              <td>
                <?php if (!empty($o['estimated_delivery_date'])): ?>
                  <span class="vc-eta-chip"><i class="bi bi-calendar2-week"></i> <?= e(date('d M Y', strtotime($o['estimated_delivery_date']))) ?></span>
                <?php else: ?>
                  <span class="text-muted small">Not set</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="<?= e($badge['class']) ?>">
                  <i class="bi <?= e($badge['icon']) ?>"></i>
                  <?= e($badge['label']) ?>
                </span>
              </td>
              <td class="vc-money">₹<?= e(number_format((float) $o['total'], 2)) ?></td>
              <?php if (($user['role'] ?? '') === 'super_admin'): ?>
                <td class="small text-muted"><?= e($o['delivery_manager_name'] ?: '—') ?></td>
              <?php endif; ?>
              <td class="text-end">
                <a class="btn btn-sm vc-btn-view" href="<?= e(url('delivery/' . $o['id'])) ?>">
                  Open <i class="bi bi-arrow-right"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if (($result['pages'] ?? 1) > 1): ?>
        <?php
          $page = (int) $result['page'];
          $pages = (int) $result['pages'];
          $baseUrl = url('delivery');
          $query = [
              'tab' => $tab,
              'q' => $filters['q'] ?: null,
              'status' => $filters['status'] ?: null,
              'eta_from' => $filters['eta_from'] ?: null,
              'eta_to' => $filters['eta_to'] ?: null,
              'manager_id' => $filters['manager_id'] ?: null,
              'mine_only' => !empty($filters['mine_only']) ? '1' : null,
          ];
          require VIEW_PATH . '/shared/pagination.php';
        ?>
      <?php endif; ?>
    </div>
  </div>
</section>
