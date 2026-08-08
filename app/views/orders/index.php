<?php
/** @var array $filters */
/** @var array $result */
$success = $success ?? null;
$error = $error ?? null;
?>
<div class="pagetitle d-flex flex-wrap justify-content-between align-items-center gap-2">
  <div>
    <h1>Orders</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
        <li class="breadcrumb-item active">Orders</li>
      </ol>
    </nav>
  </div>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="card mb-3">
    <div class="card-body py-3">
      <form class="row g-2 align-items-end" method="GET" action="<?= e(url('orders')) ?>">
        <div class="col-md-3">
          <label class="form-label mb-1">Search</label>
          <input type="text" name="q" value="<?= e($filters['q']) ?>" class="form-control" placeholder="Order # or customer">
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <?php foreach (Order::STATUS_LABELS as $key => $label): ?>
              <option value="<?= e($key) ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">From</label>
          <input type="date" name="date_from" value="<?= e($filters['date_from']) ?>" class="form-control">
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">To</label>
          <input type="date" name="date_to" value="<?= e($filters['date_to']) ?>" class="form-control">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-primary" type="submit">Filter</button>
          <a class="btn btn-outline-secondary" href="<?= e(url('orders')) ?>">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body pt-3">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Order</th>
              <th>Customer</th>
              <th>Items</th>
              <th>Total</th>
              <th>Status</th>
              <th>Placed</th>
              <th>Delivery Mgr</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$result['rows']): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">No orders found.</td></tr>
          <?php endif; ?>
          <?php foreach ($result['rows'] as $o): ?>
            <?php $badge = Order::badge($o['status']); ?>
            <tr>
              <td class="fw-semibold"><?= e($o['order_number']) ?></td>
              <td>
                <div><?= e($o['business_name']) ?></div>
                <div class="small text-muted"><?= e($o['mobile']) ?></div>
              </td>
              <td><?= (int) $o['item_count'] ?></td>
              <td>₹<?= e(number_format((float)$o['total'], 2)) ?></td>
              <td><span class="badge rounded-pill <?= e($badge['class']) ?>"><?= e($badge['label']) ?></span></td>
              <td class="small"><?= e(date('d M Y, H:i', strtotime($o['placed_at']))) ?></td>
              <td class="small"><?= e($o['delivery_manager_name'] ?: '—') ?></td>
              <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= e(url('orders/' . $o['id'])) ?>">View</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($result['pages'] > 1): ?>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
              <?php
                $qs = http_build_query(array_filter([
                    'q' => $filters['q'] ?: null,
                    'status' => $filters['status'] ?: null,
                    'date_from' => $filters['date_from'] ?: null,
                    'date_to' => $filters['date_to'] ?: null,
                    'page' => $p,
                ]));
              ?>
              <li class="page-item <?= $p === $result['page'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= e(url('orders?' . $qs)) ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>
    </div>
  </div>
</section>
