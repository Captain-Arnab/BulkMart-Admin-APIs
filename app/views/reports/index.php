<?php
/** @var array $filters */
/** @var array $summary */
/** @var array $payload */
/** @var array $chartData */
$filters = $filters ?? [];
$summary = $summary ?? [];
$payload = $payload ?? [];
$topProducts = $payload['top_products'] ?? ['rows' => [], 'page' => 1, 'pages' => 1, 'max_qty' => 1];
$topCustomers = $payload['top_customers'] ?? ['rows' => [], 'page' => 1, 'pages' => 1];
$orders = $payload['orders'] ?? ['rows' => [], 'page' => 1, 'pages' => 1];
$categoriesList = $payload['categories_list'] ?? [];
$maxQty = max(1.0, (float) ($topProducts['max_qty'] ?? 1));

$qs = static function (array $overrides = []) use ($filters): string {
    $base = [
        'preset'      => $filters['preset'] ?? 'custom',
        'date_from'   => $filters['date_from'] ?? '',
        'date_to'     => $filters['date_to'] ?? '',
        'category_id' => $filters['category_id'] ?? '',
        'customer_q'  => $filters['customer_q'] ?? '',
        'status'      => $filters['status'] ?? '',
    ];
    foreach ($overrides as $k => $v) {
        $base[$k] = $v;
    }
    // Drop empty optional fields
    if ($base['preset'] !== 'custom') {
        unset($base['date_from'], $base['date_to']);
    }
    return http_build_query(array_filter($base, static fn ($v) => $v !== '' && $v !== null));
};

$pager = static function (string $param, array $pageData) use ($qs): string {
    $page = (int) ($pageData['page'] ?? 1);
    $pages = (int) ($pageData['pages'] ?? 1);
    if ($pages <= 1) {
        return '';
    }
    $html = '<nav class="vc-pagination vc-pagination--inline" aria-label="Pagination"><ul class="pagination vc-pager mb-0">';
    $mk = static function (int $p) use ($qs, $param): string {
        return e(url('reports?' . $qs([$param => $p])));
    };
    $html .= '<li class="page-item' . ($page <= 1 ? ' disabled' : '') . '"><a class="page-link" href="' . ($page <= 1 ? '#' : $mk($page - 1)) . '"><i class="bi bi-chevron-left"></i></a></li>';
    for ($i = 1; $i <= $pages; $i++) {
        $active = $i === $page ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $mk($i) . '">' . $i . '</a></li>';
    }
    $html .= '<li class="page-item' . ($page >= $pages ? ' disabled' : '') . '"><a class="page-link" href="' . ($page >= $pages ? '#' : $mk($page + 1)) . '"><i class="bi bi-chevron-right"></i></a></li>';
    $html .= '</ul></nav>';
    return $html;
};

$exportUrl = url('reports/export?' . $qs());
$presets = [
    'today' => 'Today',
    '7d'    => '7d',
    '30d'   => '30d',
    'month' => 'This Month',
    'custom'=> 'Custom',
];
?>
<div class="pagetitle vc-pagetitle">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-2">
    <div>
      <h1>Reports &amp; Analytics</h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
          <li class="breadcrumb-item active">Reports</li>
        </ol>
      </nav>
    </div>
    <a class="btn btn-outline-primary btn-sm" href="<?= e($exportUrl) ?>"><i class="bi bi-download"></i> Export to CSV</a>
  </div>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<section class="section vc-analytics">
  <div class="card vc-filter-card mb-3">
    <div class="card-body py-3">
      <form method="GET" action="<?= e(url('reports')) ?>" class="row g-2 align-items-end" id="reports-filter-form">
        <div class="col-lg-3">
          <label class="form-label mb-1">Quick range</label>
          <div class="btn-group w-100 flex-wrap" role="group">
            <?php foreach ($presets as $key => $label): ?>
              <?php if ($key === 'custom') continue; ?>
              <a href="<?= e(url('reports?' . $qs(['preset' => $key, 'pp' => 1, 'cp' => 1, 'op' => 1]))) ?>"
                 class="btn btn-sm <?= ($filters['preset'] ?? '') === $key ? 'btn-primary' : 'btn-outline-primary' ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">From</label>
          <input type="date" name="date_from" class="form-control" value="<?= e($filters['date_from'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">To</label>
          <input type="date" name="date_to" class="form-control" value="<?= e($filters['date_to'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">Category</label>
          <select name="category_id" class="form-select">
            <option value="">All</option>
            <?php foreach ($categoriesList as $cat): ?>
              <option value="<?= (int) $cat['id'] ?>" <?= ((int) ($filters['category_id'] ?? 0) === (int) $cat['id']) ? 'selected' : '' ?>>
                <?= e($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">Customer</label>
          <input type="text" name="customer_q" class="form-control" placeholder="Business / mobile" value="<?= e($filters['customer_q'] ?? '') ?>">
        </div>
        <div class="col-md-1">
          <input type="hidden" name="preset" value="custom">
          <input type="hidden" name="status" id="filter-status" value="<?= e($filters['status'] ?? '') ?>">
          <button type="submit" class="btn btn-primary w-100">Apply</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3 mb-3" id="section-summary">
    <div class="col-6 col-xl-3">
      <div class="card vc-stat-strip"><div class="card-body">
        <div class="label">Total orders</div>
        <div class="value"><?= (int) ($summary['orders'] ?? 0) ?></div>
      </div></div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card vc-stat-strip"><div class="card-body">
        <div class="label">Total revenue</div>
        <div class="value">₹<?= e(number_format((float) ($summary['revenue'] ?? 0), 0)) ?></div>
      </div></div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card vc-stat-strip"><div class="card-body">
        <div class="label">Avg order value</div>
        <div class="value">₹<?= e(number_format((float) ($summary['aov'] ?? 0), 0)) ?></div>
      </div></div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card vc-stat-strip"><div class="card-body">
        <div class="label">Units sold</div>
        <div class="value"><?= e(number_format((float) ($summary['units'] ?? 0), 0)) ?></div>
      </div></div>
    </div>
  </div>

  <div class="row g-3 mb-3" id="section-trend">
    <div class="col-12">
      <div class="card vc-chart-card">
        <div class="card-body">
          <h5 class="card-title">Revenue Trend</h5>
          <div class="vc-chart-frame vc-chart-frame--lg">
            <div class="vc-skeleton vc-skeleton-area" data-skeleton></div>
            <div id="report-trend"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-lg-5" id="section-status">
      <div class="card vc-chart-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0">Orders by Status</h5>
            <?php if (!empty($filters['status'])): ?>
              <a class="small" href="<?= e(url('reports?' . $qs(['status' => '', 'op' => 1]))) ?>">Clear status</a>
            <?php else: ?>
              <span class="small text-muted">Click a slice to filter</span>
            <?php endif; ?>
          </div>
          <div class="vc-chart-frame vc-chart-frame--donut">
            <div class="vc-skeleton vc-skeleton-donut" data-skeleton></div>
            <div id="report-status"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-7" id="section-categories">
      <div class="card vc-chart-card h-100">
        <div class="card-body">
          <h5 class="card-title">Category Performance</h5>
          <div class="row g-2">
            <div class="col-md-7">
              <div class="vc-chart-frame">
                <div class="vc-skeleton vc-skeleton-bars" data-skeleton></div>
                <div id="report-category-bars"></div>
              </div>
            </div>
            <div class="col-md-5">
              <div class="vc-chart-frame vc-chart-frame--donut">
                <div class="vc-skeleton vc-skeleton-donut" data-skeleton></div>
                <div id="report-category-share"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3" id="section-products">
    <div class="col-12">
      <div class="card vc-chart-card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title mb-0">Top Products</h5>
            <?= $pager('pp', $topProducts) ?>
          </div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th style="width:45%">Product</th>
                  <th>Units sold</th>
                  <th>Revenue</th>
                  <th>Stock</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($topProducts['rows'])): ?>
                  <tr><td colspan="4" class="text-muted">No product sales in this range.</td></tr>
                <?php endif; ?>
                <?php foreach ($topProducts['rows'] as $p): ?>
                  <?php $pct = max(4, ((float) $p['qty'] / $maxQty) * 100); ?>
                  <tr>
                    <td>
                      <div class="vc-inline-bar-track vc-inline-bar-track--table" style="--bar: <?= e((string) round($pct, 1)) ?>%">
                        <div class="vc-inline-bar-fill"></div>
                        <div class="vc-inline-bar-content"><span class="name"><?= e($p['name']) ?></span></div>
                      </div>
                    </td>
                    <td><?= e(rtrim(rtrim(number_format((float) $p['qty'], 1), '0'), '.')) ?></td>
                    <td>₹<?= e(number_format((float) $p['revenue'], 0)) ?></td>
                    <td><?= e(rtrim(rtrim(number_format((float) $p['stock'], 1), '0'), '.')) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3" id="section-customers">
    <div class="col-12">
      <div class="card vc-chart-card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title mb-0">Top Customers</h5>
            <?= $pager('cp', $topCustomers) ?>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Business</th>
                  <th>Orders</th>
                  <th>Total spend</th>
                  <th>Last order</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($topCustomers['rows'])): ?>
                  <tr><td colspan="4" class="text-muted">No customers in this range.</td></tr>
                <?php endif; ?>
                <?php foreach ($topCustomers['rows'] as $c): ?>
                  <tr>
                    <td><?= e($c['business_name']) ?></td>
                    <td><?= (int) $c['order_count'] ?></td>
                    <td>₹<?= e(number_format((float) $c['total_spend'], 0)) ?></td>
                    <td><?= e(date('d M Y', strtotime((string) $c['last_order_at']))) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3" id="section-orders">
    <div class="col-12">
      <div class="card vc-chart-card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h5 class="card-title mb-0">Order Detail <span class="text-muted fs-6">(<?= (int) ($orders['total'] ?? 0) ?> rows)</span></h5>
            <div class="d-flex align-items-center gap-2">
              <?= $pager('op', $orders) ?>
              <a class="btn btn-sm btn-outline-primary" href="<?= e($exportUrl) ?>">Export to CSV</a>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle" id="orders-detail-table">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Customer</th>
                  <th>Status</th>
                  <th>Total</th>
                  <th>Placed</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($orders['rows'])): ?>
                  <tr><td colspan="5" class="text-muted">No orders match these filters.</td></tr>
                <?php endif; ?>
                <?php foreach ($orders['rows'] as $o): ?>
                  <?php $badge = Order::badge($o['status']); ?>
                  <tr data-status="<?= e($o['status']) ?>">
                    <td><a href="<?= e(url('orders/' . (int) $o['id'])) ?>"><?= e($o['order_number']) ?></a></td>
                    <td><?= e($o['business_name']) ?></td>
                    <td><span class="<?= e($badge['class']) ?>"><i class="bi <?= e($badge['icon']) ?>"></i> <?= e($badge['label']) ?></span></td>
                    <td>₹<?= e(number_format((float) $o['total'], 2)) ?></td>
                    <td><?= e(date('d M Y H:i', strtotime((string) $o['placed_at']))) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  window.VC_REPORTS = <?= json_encode($chartData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  window.VC_REPORTS_FILTERS = <?= json_encode($filters, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
