<?php
/** @var array $order */
/** @var array $items */
/** @var array $log */
$badge = Order::badge($order['status']);
$success = $success ?? null;
$error = $error ?? null;
$codWarn = $codWarn ?? null;
?>
<div class="pagetitle">
  <h1><?= e($order['order_number']) ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('delivery')) ?>">Delivery</a></li>
      <li class="breadcrumb-item active"><?= e($order['order_number']) ?></li>
    </ol>
  </nav>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($codWarn): ?>
  <div class="alert alert-warning">
    <?= e($codWarn['message']) ?>
    <form method="POST" action="<?= e(url('delivery/' . $order['id'] . '/delivered')) ?>" class="mt-2 d-flex gap-2 align-items-end">
      <input type="hidden" name="cod_collected" value="<?= e($codWarn['cod_collected']) ?>">
      <input type="hidden" name="cod_mismatch_ack" value="1">
      <button class="btn btn-warning btn-sm" type="submit">Confirm anyway</button>
    </form>
  </div>
<?php endif; ?>

<section class="section">
  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <h5 class="card-title mb-0">Delivery details</h5>
            <span class="<?= e($badge['class']) ?>">
              <i class="bi <?= e($badge['icon']) ?>"></i>
              <?= e($badge['label']) ?>
            </span>
          </div>
          <div class="mb-3">
            <div class="fw-semibold"><?= e($order['business_name']) ?></div>
            <div><?= e($order['owner_name']) ?> · <?= e($order['mobile']) ?></div>
            <div class="mt-2">
              <?= e($order['line1']) ?><?php if ($order['line2']): ?>, <?= e($order['line2']) ?><?php endif; ?><br>
              <?= e($order['city']) ?>, <?= e($order['state']) ?> — <?= e($order['pincode']) ?>
            </div>
          </div>
          <table class="table table-sm">
            <thead><tr><th>Item</th><th>Qty</th><th class="text-end">Total</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= e($item['product_name_snapshot']) ?></td>
                <td><?= e(rtrim(rtrim(number_format((float)$item['quantity'], 2), '0'), '.')) ?> <?= e($item['unit_snapshot']) ?></td>
                <td class="text-end">₹<?= e(number_format((float)$item['line_total'], 2)) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr><th colspan="2">COD total</th><th class="text-end">₹<?= e(number_format((float)$order['total'], 2)) ?></th></tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-body">
          <h5 class="card-title">Status history</h5>
          <ul class="list-unstyled mb-0">
            <?php foreach ($log as $entry): ?>
              <?php $b = Order::badge($entry['status']); ?>
              <li class="mb-2">
                <span class="<?= e($b['class']) ?>">
                  <i class="bi <?= e($b['icon']) ?>"></i>
                  <?= e($b['label']) ?>
                </span>
                <span class="small text-muted ms-2"><?= e(date('d M Y, H:i', strtotime($entry['changed_at']))) ?></span>
                <?php if ($entry['note']): ?><div class="small"><?= e($entry['note']) ?></div><?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Actions</h5>

          <?php if (in_array($order['status'], ['confirmed', 'delivery_date_set'], true)): ?>
            <form method="POST" action="<?= e(url('delivery/' . $order['id'] . '/set-date')) ?>" class="mb-3">
              <label class="form-label">Estimated delivery date</label>
              <input type="date" name="estimated_delivery_date" class="form-control mb-2" required
                     value="<?= e($order['estimated_delivery_date'] ?? '') ?>"
                     min="<?= e(date('Y-m-d')) ?>">
              <button class="btn btn-outline-primary w-100" type="submit">Save delivery date</button>
            </form>
          <?php endif; ?>

          <?php if ($order['status'] === 'delivery_date_set'): ?>
            <form method="POST" action="<?= e(url('delivery/' . $order['id'] . '/out-for-delivery')) ?>" class="mb-3"
                  onsubmit="return confirm('Mark this order out for delivery?');">
              <button class="btn btn-warning w-100" type="submit">Mark out for delivery</button>
            </form>
          <?php endif; ?>

          <?php if ($order['status'] === 'out_for_delivery'): ?>
            <form method="POST" action="<?= e(url('delivery/' . $order['id'] . '/delivered')) ?>">
              <label class="form-label">COD amount collected (₹)</label>
              <input type="number" step="0.01" min="0" name="cod_collected" class="form-control mb-2"
                     value="<?= e($order['total']) ?>" required>
              <button class="btn btn-success w-100" type="submit">Mark delivered</button>
            </form>
          <?php endif; ?>

          <?php if (in_array($order['status'], ['delivered', 'cancelled'], true)): ?>
            <p class="text-muted mb-0">No further delivery actions for this order.</p>
          <?php endif; ?>

          <?php if ($order['status'] === 'confirmed' && empty($order['estimated_delivery_date'])): ?>
            <p class="small text-muted mt-2 mb-0">Set an ETA to move this order into the delivery schedule.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
