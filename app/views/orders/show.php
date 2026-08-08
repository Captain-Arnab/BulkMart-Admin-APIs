<?php
/** @var array $order */
/** @var array $items */
/** @var array $log */
/** @var array $managers */
/** @var array $next */
$badge = Order::badge($order['status']);
$success = $success ?? null;
$error = $error ?? null;

$statusFlow = ['placed', 'confirmed', 'delivery_date_set', 'out_for_delivery', 'delivered'];
$currentIdx = array_search($order['status'], $statusFlow, true);
if ($order['status'] === 'cancelled') {
    $currentIdx = -1;
}
?>
<div class="pagetitle vc-pagetitle d-flex flex-wrap justify-content-between align-items-end gap-2">
  <div>
    <h1 class="d-flex align-items-center gap-2 flex-wrap">
      <span>Order</span>
      <span class="vc-order-id-title"><?= e($order['order_number']) ?></span>
    </h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= e(url('orders')) ?>">Orders</a></li>
        <li class="breadcrumb-item active"><?= e($order['order_number']) ?></li>
      </ol>
    </nav>
  </div>
  <a href="<?= e(url('orders')) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>All orders</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section vc-order-detail">
  <div class="vc-order-hero card mb-3">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
          <div class="vc-order-hero-label">Order ID</div>
          <div class="vc-order-hero-id">#<?= e($order['order_number']) ?></div>
          <div class="vc-order-hero-meta">
            Placed <?= e(date('d M Y · H:i', strtotime($order['placed_at']))) ?>
            <span class="dot"></span> COD
            <?php if ($order['estimated_delivery_date']): ?>
              <span class="dot"></span> ETA <?= e(date('d M Y', strtotime($order['estimated_delivery_date']))) ?>
            <?php endif; ?>
          </div>
        </div>
        <div class="text-end">
          <span class="<?= e($badge['class']) ?> vc-status--lg">
            <i class="bi <?= e($badge['icon']) ?>"></i>
            <?= e($badge['label']) ?>
          </span>
          <div class="vc-order-hero-total mt-2">₹<?= e(number_format((float) $order['total'], 2)) ?></div>
        </div>
      </div>

      <?php if ($order['status'] !== 'cancelled'): ?>
        <div class="vc-status-stepper mt-4" aria-label="Order progress">
          <?php foreach ($statusFlow as $i => $step): ?>
            <?php
              $stepBadge = Order::badge($step);
              $state = 'upcoming';
              if ($currentIdx !== false && $currentIdx !== -1) {
                  if ($i < $currentIdx) $state = 'done';
                  elseif ($i === $currentIdx) $state = 'current';
              }
            ?>
            <div class="vc-step <?= e($state) ?>">
              <div class="vc-step-dot"><i class="bi <?= e($stepBadge['icon']) ?>"></i></div>
              <div class="vc-step-label"><?= e($stepBadge['label']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="vc-cancelled-banner mt-3">
          <i class="bi bi-x-circle"></i> This order was cancelled.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card vc-detail-card mb-3">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="vc-info-block">
                <div class="vc-info-label"><i class="bi bi-shop"></i> Customer</div>
                <div class="vc-info-title"><?= e($order['business_name']) ?></div>
                <div><?= e($order['owner_name']) ?></div>
                <div class="text-muted"><i class="bi bi-phone"></i> <?= e($order['mobile']) ?></div>
                <?php if ($order['customer_email']): ?>
                  <div class="small text-muted"><?= e($order['customer_email']) ?></div>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="vc-info-block">
                <div class="vc-info-label"><i class="bi bi-geo-alt"></i> Delivery address</div>
                <div><?= e($order['line1']) ?></div>
                <?php if ($order['line2']): ?><div><?= e($order['line2']) ?></div><?php endif; ?>
                <div><?= e($order['city']) ?>, <?= e($order['state']) ?> — <?= e($order['pincode']) ?></div>
                <?php if ($order['landmark']): ?>
                  <div class="small text-muted">Landmark: <?= e($order['landmark']) ?></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card vc-detail-card mb-3">
        <div class="card-body">
          <h5 class="card-title">Line items</h5>
          <div class="table-responsive">
            <table class="table vc-line-items align-middle mb-0">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Qty</th>
                  <th>Unit price</th>
                  <th class="text-end">Line total</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= e($item['product_name_snapshot']) ?></div>
                    <div class="small text-muted"><?= e($item['unit_snapshot']) ?></div>
                  </td>
                  <td><?= e(rtrim(rtrim(number_format((float) $item['quantity'], 2), '0'), '.')) ?></td>
                  <td>₹<?= e(number_format((float) $item['unit_price_snapshot'], 2)) ?></td>
                  <td class="text-end fw-semibold">₹<?= e(number_format((float) $item['line_total'], 2)) ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="text-end text-muted">Subtotal</td>
                  <td class="text-end">₹<?= e(number_format((float) $order['subtotal'], 2)) ?></td>
                </tr>
                <tr>
                  <td colspan="3" class="text-end text-muted">Delivery fee</td>
                  <td class="text-end">₹<?= e(number_format((float) $order['delivery_fee'], 2)) ?></td>
                </tr>
                <tr class="vc-total-row">
                  <td colspan="3" class="text-end">Total (COD)</td>
                  <td class="text-end">₹<?= e(number_format((float) $order['total'], 2)) ?></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="card vc-detail-card">
        <div class="card-body">
          <h5 class="card-title">Status history</h5>
          <?php if (!$log): ?>
            <p class="text-muted mb-0">No status changes logged yet.</p>
          <?php else: ?>
            <ul class="vc-timeline list-unstyled mb-0">
              <?php foreach ($log as $entry): ?>
                <?php $b = Order::badge($entry['status']); ?>
                <li class="vc-timeline-item">
                  <span class="vc-timeline-rail" aria-hidden="true"></span>
                  <span class="<?= e($b['class']) ?>">
                    <i class="bi <?= e($b['icon']) ?>"></i>
                    <?= e($b['label']) ?>
                  </span>
                  <div class="vc-timeline-body">
                    <div class="small text-muted">
                      <?= e(date('d M Y, H:i', strtotime($entry['changed_at']))) ?>
                      <?php if ($entry['admin_name']): ?> · <?= e($entry['admin_name']) ?><?php endif; ?>
                    </div>
                    <?php if ($entry['note']): ?><div class="small mt-1"><?= e($entry['note']) ?></div><?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card vc-detail-card vc-action-card mb-3">
        <div class="card-body">
          <h5 class="card-title">Update status</h5>
          <?php if (!$next): ?>
            <p class="text-muted mb-0">No further status changes available.</p>
          <?php else: ?>
            <form method="POST" action="<?= e(url('orders/' . $order['id'] . '/status')) ?>" class="d-grid gap-2">
              <label class="form-label mb-0">Next step</label>
              <select name="status" class="form-select" required>
                <option value="">Select next status</option>
                <?php foreach ($next as $s): ?>
                  <?php $nb = Order::badge($s); ?>
                  <option value="<?= e($s) ?>"><?= e($nb['label']) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-primary" type="submit">Apply status</button>
            </form>
            <p class="small text-muted mt-2 mb-0">Forward-only. Cancel stays available until out for delivery.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="card vc-detail-card">
        <div class="card-body">
          <h5 class="card-title">Delivery manager</h5>
          <?php if ($order['delivery_manager_name']): ?>
            <div class="vc-assignee mb-3">
              <div class="vc-assignee-avatar"><?= e(strtoupper(substr($order['delivery_manager_name'], 0, 1))) ?></div>
              <div>
                <div class="fw-semibold"><?= e($order['delivery_manager_name']) ?></div>
                <div class="small text-muted">Assigned</div>
              </div>
            </div>
          <?php endif; ?>
          <?php if (in_array($order['status'], ['cancelled', 'delivered'], true)): ?>
            <p class="text-muted mb-0">Assignment locked for this status.</p>
          <?php elseif (!$managers): ?>
            <p class="text-muted mb-0">No delivery managers available.</p>
          <?php else: ?>
            <form method="POST" action="<?= e(url('orders/' . $order['id'] . '/assign')) ?>">
              <select name="delivery_manager_id" class="form-select mb-2" required>
                <option value="">Select manager</option>
                <?php foreach ($managers as $m): ?>
                  <option value="<?= (int) $m['id'] ?>" <?= (int) $order['assigned_delivery_manager_id'] === (int) $m['id'] ? 'selected' : '' ?>>
                    <?= e($m['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-outline-primary w-100" type="submit">
                <?= $order['delivery_manager_name'] ? 'Reassign' : 'Assign' ?>
              </button>
            </form>
            <p class="small text-muted mt-2 mb-0">If still Placed, assigning auto-confirms and deducts stock.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
