<?php
/** @var array $filters */
/** @var array $result */
$success = $success ?? null;
$error = $error ?? null;
$totalShown = count($result['rows'] ?? []);
$exportQs = http_build_query(array_filter([
    'q' => $filters['q'] ?? null,
    'status' => !empty($filters['pending']) ? '__pending__' : ($filters['status'] ?? null),
    'date_from' => $filters['date_from'] ?? null,
    'date_to' => $filters['date_to'] ?? null,
    'batch_id' => $filters['batch_id'] ?? null,
], static fn($v) => $v !== null && $v !== '' && $v !== false));
$exportUrl = url('orders/export' . ($exportQs !== '' ? '?' . $exportQs : ''));
?>
<div class="pagetitle vc-pagetitle d-flex flex-wrap justify-content-between align-items-end gap-2">
  <div>
    <h1>Orders</h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
        <li class="breadcrumb-item active">Orders</li>
      </ol>
    </nav>
  </div>
  <div class="d-flex flex-wrap align-items-center gap-2">
    <span class="vc-page-meta text-muted small">
      <?= (int) ($result['total'] ?? 0) ?> total · page <?= (int) ($result['page'] ?? 1) ?>
    </span>
    <a class="btn btn-outline-success btn-sm" href="<?= e($exportUrl) ?>">
      <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>
  </div>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (!empty($filters['batch_id'])): ?>
  <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2">
    <span>Showing orders in multi-location batch <code><?= e($filters['batch_id']) ?></code></span>
    <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('orders')) ?>">Clear batch filter</a>
  </div>
<?php endif; ?>

<section class="section vc-orders">
  <div class="card vc-filter-card mb-3">
    <div class="card-body py-3">
      <form class="row g-2 align-items-end" method="GET" action="<?= e(url('orders')) ?>">
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
            <option value="">All statuses</option>
            <option value="__pending__" <?= !empty($filters['pending']) ? 'selected' : '' ?>>Pending dispatch</option>
            <?php foreach (Order::STATUS_LABELS as $key => $label): ?>
              <option value="<?= e($key) ?>" <?= empty($filters['pending']) && $filters['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-2 col-md-4">
          <label class="form-label mb-1">From</label>
          <input type="date" name="date_from" value="<?= e($filters['date_from']) ?>" class="form-control">
        </div>
        <div class="col-lg-2 col-md-4">
          <label class="form-label mb-1">To</label>
          <input type="date" name="date_to" value="<?= e($filters['date_to']) ?>" class="form-control">
        </div>
        <div class="col-lg-3 col-md-4 d-flex gap-2">
          <button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
          <a class="btn btn-outline-secondary" href="<?= e(url('orders')) ?>">Reset</a>
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
              <th class="text-center">Items</th>
              <th>Total</th>
              <th>Status</th>
              <th>Placed</th>
              <th>Delivery Mgr</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$result['rows']): ?>
            <tr><td colspan="8" class="text-center text-muted py-5">No orders found for these filters.</td></tr>
          <?php endif; ?>
          <?php foreach ($result['rows'] as $o): ?>
            <?php $badge = Order::badge($o['status']); ?>
            <tr class="vc-order-row">
              <td>
                <a class="vc-order-id" href="<?= e(url('orders/' . $o['id'])) ?>">
                  <span class="vc-order-id-hash">#</span><?= e($o['order_number']) ?>
                </a>
                <?php if (!empty($o['batch_id'])): ?>
                  <div class="mt-1">
                    <span class="badge bg-info text-dark">Multi-location</span>
                    <a class="small ms-1" href="<?= e(url('orders?batch_id=' . urlencode((string) $o['batch_id']))) ?>">
                      View batch
                    </a>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <div class="vc-customer-cell">
                  <span class="name"><?= e($o['business_name']) ?></span>
                  <span class="meta"><i class="bi bi-phone"></i> <?= e($o['mobile']) ?></span>
                </div>
              </td>
              <td class="text-center">
                <span class="vc-count-chip"><?= (int) $o['item_count'] ?></span>
              </td>
              <td class="vc-money">₹<?= e(number_format((float) $o['total'], 2)) ?></td>
              <td>
                <span class="<?= e($badge['class']) ?>">
                  <i class="bi <?= e($badge['icon']) ?>"></i>
                  <?= e($badge['label']) ?>
                </span>
              </td>
              <td>
                <div class="vc-datetime">
                  <span class="d"><?= e(date('d M Y', strtotime($o['placed_at']))) ?></span>
                  <span class="t"><?= e(date('H:i', strtotime($o['placed_at']))) ?></span>
                </div>
              </td>
              <td class="small text-muted"><?= e($o['delivery_manager_name'] ?: '—') ?></td>
              <td class="text-end">
                <a class="btn btn-sm vc-btn-view" href="<?= e(url('orders/' . $o['id'])) ?>" title="View order">
                  View <i class="bi bi-arrow-right"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($result['pages'] > 1): ?>
        <?php
          $page = (int) $result['page'];
          $pages = (int) $result['pages'];
          $baseUrl = url('orders');
          $query = [
              'q' => $filters['q'] ?: null,
              'status' => !empty($filters['pending']) ? null : ($filters['status'] ?: null),
              'pending' => !empty($filters['pending']) ? '1' : null,
              'date_from' => $filters['date_from'] ?: null,
              'date_to' => $filters['date_to'] ?: null,
          ];
          require VIEW_PATH . '/shared/pagination.php';
        ?>
      <?php endif; ?>
    </div>
  </div>
</section>
