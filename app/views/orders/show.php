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
    <h1>Order details</h1>
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
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card vc-order-summary mb-3">
        <div class="card-body">
          <div class="vc-order-summary-top">
            <div class="vc-order-summary-main">
              <div class="vc-order-hero-label">Order ID</div>
              <div class="vc-order-hero-id">#<?= e($order['order_number']) ?></div>
              <div class="vc-order-hero-meta">
                <span>Placed <?= e(date('d M Y · H:i', strtotime($order['placed_at']))) ?></span>
                <span class="dot"></span>
                <span>COD</span>
                <?php if ($order['estimated_delivery_date']): ?>
                  <span class="dot"></span>
                  <span>ETA <?= e(date('d M Y', strtotime($order['estimated_delivery_date']))) ?></span>
                <?php endif; ?>
              </div>
            </div>
            <div class="vc-order-summary-side">
              <span class="<?= e($badge['class']) ?> vc-status--lg">
                <i class="bi <?= e($badge['icon']) ?>"></i>
                <?= e($badge['label']) ?>
              </span>
              <div class="vc-order-hero-total">₹<?= e(number_format((float) $order['total'], 2)) ?></div>
            </div>
          </div>

          <?php
            $batchOrders = $batchOrders ?? [];
            if (!empty($order['batch_id']) && $batchOrders):
          ?>
            <div class="alert alert-info mt-3 mb-0 py-2">
              <strong>Multi-location batch</strong>
              — this order is part of a split checkout
              (<a href="<?= e(url('orders?batch_id=' . urlencode((string) $order['batch_id']))) ?>">view all <?= count($batchOrders) ?> orders</a>):
              <?php foreach ($batchOrders as $bo): ?>
                <?php if ((int) $bo['id'] === (int) $order['id']) continue; ?>
                <a class="ms-1" href="<?= e(url('orders/' . $bo['id'])) ?>"><?= e($bo['order_number']) ?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if ($order['status'] !== 'cancelled'): ?>
            <div class="vc-status-stepper" aria-label="Order progress">
              <?php foreach ($statusFlow as $i => $step): ?>
                <?php
                  $stepBadge = Order::badge($step);
                  $state = 'upcoming';
                  if ($currentIdx !== false && $currentIdx !== -1) {
                      if ($i < $currentIdx) {
                          $state = 'done';
                      } elseif ($i === $currentIdx) {
                          $state = 'current';
                      }
                  }
                ?>
                <div class="vc-step <?= e($state) ?>">
                  <div class="vc-step-dot"><i class="bi <?= e($stepBadge['icon']) ?>"></i></div>
                  <div class="vc-step-label"><?= e($stepBadge['label']) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="vc-cancelled-banner" role="status">
              <i class="bi bi-x-circle-fill"></i>
              <span>This order was cancelled. No further delivery actions are available.</span>
            </div>
          <?php endif; ?>

          <div class="vc-order-summary-grid">
            <div class="vc-info-block">
              <div class="vc-info-label"><i class="bi bi-shop"></i> Customer</div>
              <div class="vc-info-title"><?= e($order['business_name']) ?></div>
              <div><?= e($order['owner_name']) ?></div>
              <div class="text-muted"><i class="bi bi-phone"></i> <?= e($order['mobile']) ?></div>
              <?php if ($order['customer_email']): ?>
                <div class="small text-muted"><?= e($order['customer_email']) ?></div>
              <?php endif; ?>
            </div>
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
            <form method="POST" action="<?= e(url('orders/' . $order['id'] . '/status')) ?>" class="d-grid gap-2" id="order-status-form">
              <label class="form-label mb-0">Next step</label>
              <select name="status" id="order-next-status" class="form-select" required>
                <option value="">Select next status</option>
                <?php foreach ($next as $s): ?>
                  <?php $nb = Order::badge($s); ?>
                  <option value="<?= e($s) ?>"><?= e($nb['label']) ?></option>
                <?php endforeach; ?>
              </select>

              <div id="eta-field-wrap" class="vc-eta-field" hidden>
                <label class="form-label mb-1" for="estimated_delivery_date">
                  <i class="bi bi-calendar-event me-1"></i>Estimated delivery date
                </label>
                <input type="date"
                       name="estimated_delivery_date"
                       id="estimated_delivery_date"
                       class="form-control"
                       min="<?= e(date('Y-m-d')) ?>"
                       value="<?= e($order['estimated_delivery_date'] ?? '') ?>">
                <div class="form-text">Required when moving to “Delivery date set”.</div>
              </div>

              <div id="delivery-otp-wrap" hidden>
                <label class="form-label mb-1" for="delivery_otp">
                  <i class="bi bi-shield-lock me-1"></i>Customer delivery OTP
                </label>
                <input type="text"
                       name="delivery_otp"
                       id="delivery_otp"
                       class="form-control"
                       inputmode="numeric"
                       pattern="\d{6}"
                       maxlength="6"
                       autocomplete="one-time-code"
                       placeholder="6-digit OTP from customer">
                <div class="form-text">Required to mark delivered. Customer shares this only on physical handover.</div>
              </div>

              <button class="btn btn-primary" type="submit">Apply status</button>
            </form>
            <p class="small text-muted mt-2 mb-0">Forward-only. Cancel stays available until out for delivery.</p>
          <?php endif; ?>
        </div>
      </div>

      <?php if (in_array($order['status'], ['confirmed', 'delivery_date_set'], true)): ?>
      <div class="card vc-detail-card mb-3">
        <div class="card-body">
          <h5 class="card-title">Delivery date (ETA)</h5>
          <form method="POST" action="<?= e(url('orders/' . $order['id'] . '/set-date')) ?>" class="d-grid gap-2">
            <label class="form-label mb-0">Estimated delivery date</label>
            <input type="date"
                   name="estimated_delivery_date"
                   class="form-control"
                   required
                   min="<?= e(date('Y-m-d')) ?>"
                   value="<?= e($order['estimated_delivery_date'] ?? '') ?>">
            <button class="btn btn-outline-primary" type="submit">
              <?= $order['status'] === 'confirmed' ? 'Set date &amp; advance status' : 'Update delivery date' ?>
            </button>
          </form>
          <p class="small text-muted mt-2 mb-0">
            Set the delivery date here. If the order is still Confirmed, saving the date also advances status to “Delivery date set”.
          </p>
        </div>
      </div>
      <?php endif; ?>

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
<script>
(function () {
  var select = document.getElementById('order-next-status');
  var wrap = document.getElementById('eta-field-wrap');
  var input = document.getElementById('estimated_delivery_date');
  var otpWrap = document.getElementById('delivery-otp-wrap');
  var otpInput = document.getElementById('delivery_otp');
  if (!select) return;
  function sync() {
    if (wrap && input) {
      var needEta = select.value === 'delivery_date_set';
      wrap.hidden = !needEta;
      input.required = needEta;
    }
    if (otpWrap && otpInput) {
      var needOtp = select.value === 'delivered';
      otpWrap.hidden = !needOtp;
      otpInput.required = needOtp;
    }
  }
  select.addEventListener('change', sync);
  sync();
})();
</script>
