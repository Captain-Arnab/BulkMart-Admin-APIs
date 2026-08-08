<?php
/** @var array $order */
/** @var array $items */
/** @var array $log */
/** @var array $managers */
/** @var array $next */
$badge = Order::badge($order['status']);
$success = $success ?? null;
$error = $error ?? null;
?>
<div class="pagetitle">
  <h1>Order <?= e($order['order_number']) ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= e(url('orders')) ?>">Orders</a></li>
      <li class="breadcrumb-item active"><?= e($order['order_number']) ?></li>
    </ol>
  </nav>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h5 class="card-title mb-1">Order details</h5>
              <div class="text-muted small">Placed <?= e(date('d M Y, H:i', strtotime($order['placed_at']))) ?> · COD</div>
            </div>
            <span class="badge rounded-pill <?= e($badge['class']) ?>"><?= e($badge['label']) ?></span>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <h6 class="text-muted text-uppercase small">Customer</h6>
              <div class="fw-semibold"><?= e($order['business_name']) ?></div>
              <div><?= e($order['owner_name']) ?></div>
              <div><?= e($order['mobile']) ?></div>
              <?php if ($order['customer_email']): ?><div class="small text-muted"><?= e($order['customer_email']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
              <h6 class="text-muted text-uppercase small">Delivery address</h6>
              <div><?= e($order['line1']) ?></div>
              <?php if ($order['line2']): ?><div><?= e($order['line2']) ?></div><?php endif; ?>
              <div><?= e($order['city']) ?>, <?= e($order['state']) ?> — <?= e($order['pincode']) ?></div>
              <?php if ($order['landmark']): ?><div class="small text-muted">Landmark: <?= e($order['landmark']) ?></div><?php endif; ?>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table align-middle">
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
                  <td><?= e(rtrim(rtrim(number_format((float)$item['quantity'], 2), '0'), '.')) ?></td>
                  <td>₹<?= e(number_format((float)$item['unit_price_snapshot'], 2)) ?></td>
                  <td class="text-end">₹<?= e(number_format((float)$item['line_total'], 2)) ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">₹<?= e(number_format((float)$order['subtotal'], 2)) ?></td></tr>
                <tr><td colspan="3" class="text-end">Delivery fee</td><td class="text-end">₹<?= e(number_format((float)$order['delivery_fee'], 2)) ?></td></tr>
                <tr><td colspan="3" class="text-end fw-bold">Total (COD)</td><td class="text-end fw-bold">₹<?= e(number_format((float)$order['total'], 2)) ?></td></tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-body">
          <h5 class="card-title">Status history</h5>
          <?php if (!$log): ?>
            <p class="text-muted mb-0">No status changes logged yet.</p>
          <?php else: ?>
            <ul class="vc-timeline list-unstyled mb-0">
              <?php foreach ($log as $entry): ?>
                <?php $b = Order::badge($entry['status']); ?>
                <li class="d-flex gap-3 mb-3">
                  <span class="badge rounded-pill <?= e($b['class']) ?>"><?= e($b['label']) ?></span>
                  <div>
                    <div class="small text-muted"><?= e(date('d M Y, H:i', strtotime($entry['changed_at']))) ?>
                      <?php if ($entry['admin_name']): ?> · <?= e($entry['admin_name']) ?><?php endif; ?>
                    </div>
                    <?php if ($entry['note']): ?><div class="small"><?= e($entry['note']) ?></div><?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Update status</h5>
          <?php if (!$next): ?>
            <p class="text-muted mb-0">No further status changes available.</p>
          <?php else: ?>
            <form method="POST" action="<?= e(url('orders/' . $order['id'] . '/status')) ?>" class="d-grid gap-2">
              <select name="status" class="form-select" required>
                <option value="">Select next status</option>
                <?php foreach ($next as $s): ?>
                  <option value="<?= e($s) ?>"><?= e(Order::STATUS_LABELS[$s]) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-primary" type="submit">Apply status</button>
            </form>
            <p class="small text-muted mt-2 mb-0">Forward-only transitions. Cancel is available until the order is out for delivery.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Assign delivery manager</h5>
          <?php if ($order['delivery_manager_name']): ?>
            <p class="mb-2">Currently: <strong><?= e($order['delivery_manager_name']) ?></strong></p>
          <?php endif; ?>
          <?php if (in_array($order['status'], ['cancelled', 'delivered'], true)): ?>
            <p class="text-muted mb-0">Assignment locked for this status.</p>
          <?php elseif (!$managers): ?>
            <p class="text-muted mb-0">No delivery managers seeded yet.</p>
          <?php else: ?>
            <form method="POST" action="<?= e(url('orders/' . $order['id'] . '/assign')) ?>">
              <select name="delivery_manager_id" class="form-select mb-2" required>
                <option value="">Select manager</option>
                <?php foreach ($managers as $m): ?>
                  <option value="<?= (int)$m['id'] ?>" <?= (int)$order['assigned_delivery_manager_id'] === (int)$m['id'] ? 'selected' : '' ?>>
                    <?= e($m['name']) ?> (<?= e($m['email']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-outline-primary w-100" type="submit">Assign</button>
            </form>
            <p class="small text-muted mt-2 mb-0">If still Placed, assigning will auto-confirm and deduct stock.</p>
          <?php endif; ?>

          <?php if ($order['estimated_delivery_date']): ?>
            <hr>
            <div class="small">ETA: <strong><?= e(date('d M Y', strtotime($order['estimated_delivery_date']))) ?></strong></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
