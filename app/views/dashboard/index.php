<?php
/** @var array $kpis */
/** @var array $sparklines */
/** @var array $chartData */
/** @var array $topProducts */
/** @var array $lowStock */
$kpis = $kpis ?? [];
$topProducts = $topProducts ?? [];
$lowStock = $lowStock ?? [];
$user = auth_user();
$name = $user['name'] ?? 'Admin';
$maxQty = 1.0;
foreach ($topProducts as $tp) {
    $maxQty = max($maxQty, (float) ($tp['qty'] ?? 0));
}
$reportsBase = url('reports');
?>
<div class="pagetitle vc-pagetitle">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-2">
    <div>
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>
    <div class="vc-date-chip">
      <i class="bi bi-calendar3"></i>
      <?= e(date('D, d M Y')) ?>
    </div>
  </div>
</div>

<section class="section dashboard vc-analytics">
  <div class="vc-hero-banner mb-4">
    <div class="vc-hero-copy">
      <p class="vc-hero-eyebrow">VeggiiCart Operations</p>
      <h2>Welcome back, <?= e(explode(' ', $name)[0]) ?></h2>
      <p>Live wholesale pulse — orders, revenue, and stock at a glance.</p>
    </div>
    <div class="vc-hero-glow" aria-hidden="true"></div>
  </div>

  <div class="row g-3">
    <?php foreach ($kpis as $i => $kpi): ?>
      <div class="col-xxl-3 col-md-6">
        <?php $href = !empty($kpi['href']) ? url($kpi['href']) : ''; ?>
        <<?= $href !== '' ? 'a href="' . e($href) . '"' : 'div' ?>
           class="card info-card vc-kpi-card vc-kpi-spark vc-kpi-link <?= e($kpi['class']) ?> vc-fade-up"
           style="--delay: <?= (int) $i * 60 ?>ms"
           data-tone="<?= e($kpi['tone'] ?? 'primary') ?>"
           <?= $href !== '' ? 'title="Open details"' : '' ?>>
          <div class="card-body">
            <h5 class="card-title"><?= e($kpi['label']) ?> <span>| Today</span></h5>
            <div class="d-flex align-items-center justify-content-between gap-2">
              <div class="d-flex align-items-center min-w-0">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                  <i class="bi <?= e($kpi['icon']) ?>"></i>
                </div>
                <div class="ps-3 min-w-0">
                  <h6 class="mb-0"><?= e($kpi['value']) ?></h6>
                  <span class="vc-kpi-hint"><?= e($kpi['hint']) ?></span>
                </div>
              </div>
              <div class="vc-spark-wrap flex-shrink-0">
                <div class="vc-skeleton vc-skeleton-spark" data-skeleton></div>
                <div id="spark-<?= e($kpi['key']) ?>" class="vc-spark-chart" aria-hidden="true"></div>
              </div>
            </div>
          </div>
        </<?= $href !== '' ? 'a' : 'div' ?>>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-lg-8">
      <div class="card vc-chart-card">
        <div class="card-body">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <h5 class="card-title mb-0">Revenue &amp; Orders Trend</h5>
            <div class="d-flex align-items-center gap-2">
              <div class="btn-group btn-group-sm vc-range-toggle" role="group" aria-label="Trend range">
                <button type="button" class="btn btn-outline-primary" data-trend-range="7">7d</button>
                <button type="button" class="btn btn-outline-primary active" data-trend-range="30">30d</button>
                <button type="button" class="btn btn-outline-primary" data-trend-range="90">90d</button>
              </div>
              <a class="vc-view-report" href="<?= e($reportsBase . '?preset=30d#section-trend') ?>">View full report →</a>
            </div>
          </div>
          <div class="vc-chart-frame">
            <div class="vc-skeleton vc-skeleton-area" data-skeleton></div>
            <div id="chart-trend"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card vc-chart-card h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <h5 class="card-title mb-0">Order Status</h5>
            <div class="d-flex align-items-center gap-2">
              <span class="small text-muted">Click slice</span>
              <a class="vc-view-report" href="<?= e($reportsBase . '?preset=30d#section-status') ?>">View full report →</a>
            </div>
          </div>
          <div class="vc-chart-frame vc-chart-frame--donut">
            <div class="vc-skeleton vc-skeleton-donut" data-skeleton></div>
            <div id="chart-status"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-lg-6">
      <div class="card vc-chart-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <h5 class="card-title mb-0">Top Categories by Sales</h5>
            <a class="vc-view-report" href="<?= e($reportsBase . '?preset=30d#section-categories') ?>">View full report →</a>
          </div>
          <div class="vc-chart-frame">
            <div class="vc-skeleton vc-skeleton-bars" data-skeleton></div>
            <div id="chart-categories"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card vc-chart-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <h5 class="card-title mb-0">Top 5 Products</h5>
            <a class="vc-view-report" href="<?= e($reportsBase . '?preset=30d#section-products') ?>">View full report →</a>
          </div>
          <?php if (!$topProducts): ?>
            <p class="text-muted mb-0">No product sales in the last 30 days.</p>
          <?php else: ?>
            <ul class="vc-inline-bars list-unstyled mb-0">
              <?php foreach ($topProducts as $p): ?>
                <?php $pct = max(4, ((float) $p['qty'] / $maxQty) * 100); ?>
                <li class="vc-inline-bar-row">
                  <div class="vc-inline-bar-track" style="--bar: <?= e((string) round($pct, 1)) ?>%">
                    <div class="vc-inline-bar-fill"></div>
                    <div class="vc-inline-bar-content">
                      <span class="name"><?= e($p['name']) ?></span>
                      <span class="meta"><?= e(rtrim(rtrim(number_format((float) $p['qty'], 1), '0'), '.')) ?> units · ₹<?= e(number_format((float) $p['revenue'], 0)) ?></span>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-12">
      <div class="card vc-chart-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <h5 class="card-title mb-0">Low Stock Watchlist</h5>
            <a class="vc-view-report" href="<?= e(url('products')) ?>">Manage stock →</a>
          </div>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Stock</th>
                  <th>Unit</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!$lowStock): ?>
                  <tr><td colspan="4" class="text-muted">All products above threshold.</td></tr>
                <?php endif; ?>
                <?php foreach ($lowStock as $row): ?>
                  <tr>
                    <td><?= e($row['name']) ?></td>
                    <td><span class="badge bg-warning text-dark"><?= e(rtrim(rtrim(number_format((float) $row['stock'], 1), '0'), '.')) ?></span></td>
                    <td><?= e($row['unit']) ?></td>
                    <td class="text-end">
                      <a href="<?= e(url('products/' . (int) $row['id'] . '/edit')) ?>" class="btn btn-sm btn-outline-primary">Restock</a>
                    </td>
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
  window.VC_DASHBOARD = <?= json_encode($chartData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
