<?php $error=$error??null; ?>
<div class="pagetitle"><h1>Reports &amp; Analytics</h1>
<nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li><li class="breadcrumb-item active">Reports</li></ol></nav></div>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section">
  <div class="card mb-3"><div class="card-body py-3">
    <form class="row g-2 align-items-end" method="GET" action="<?= e(url('reports')) ?>">
      <input type="hidden" name="run" value="1">
      <div class="col-md-3"><label class="form-label mb-1">From</label><input type="date" name="date_from" class="form-control" value="<?= e($date_from) ?>" required></div>
      <div class="col-md-3"><label class="form-label mb-1">To</label><input type="date" name="date_to" class="form-control" value="<?= e($date_to) ?>" required></div>
      <div class="col-md-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Run report</button>
        <a class="btn btn-outline-secondary" href="<?= e(url('reports/export?date_from='.urlencode($date_from).'&date_to='.urlencode($date_to))) ?>">Export CSV</a>
      </div>
    </form>
  </div></div>

  <?php if ($stats): ?>
  <div class="row g-3 mb-3">
    <div class="col-md-6"><div class="card"><div class="card-body"><h6 class="text-muted">Total orders</h6><h3><?= (int)$stats['order_count'] ?></h3></div></div></div>
    <div class="col-md-6"><div class="card"><div class="card-body"><h6 class="text-muted">Total revenue (ex. cancelled)</h6><h3>₹<?= e(number_format($stats['revenue'],2)) ?></h3></div></div></div>
  </div>
  <div class="row g-3">
    <div class="col-lg-6"><div class="card"><div class="card-body">
      <h5 class="card-title">Top 5 products by qty</h5>
      <table class="table table-sm"><thead><tr><th>Product</th><th>Qty</th></tr></thead><tbody>
        <?php if (!$stats['top_products']): ?><tr><td colspan="2" class="text-muted">No data</td></tr><?php endif; ?>
        <?php foreach ($stats['top_products'] as $p): ?><tr><td><?= e($p['name']) ?></td><td><?= e($p['qty']) ?></td></tr><?php endforeach; ?>
      </tbody></table>
    </div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body">
      <h5 class="card-title">Top 5 customers by value</h5>
      <table class="table table-sm"><thead><tr><th>Customer</th><th>Orders</th><th>Value</th></tr></thead><tbody>
        <?php if (!$stats['top_customers']): ?><tr><td colspan="3" class="text-muted">No data</td></tr><?php endif; ?>
        <?php foreach ($stats['top_customers'] as $c): ?><tr><td><?= e($c['business_name']) ?></td><td><?= (int)$c['orders'] ?></td><td>₹<?= e(number_format((float)$c['order_value'],2)) ?></td></tr><?php endforeach; ?>
      </tbody></table>
    </div></div></div>
  </div>
  <?php endif; ?>
</section>
