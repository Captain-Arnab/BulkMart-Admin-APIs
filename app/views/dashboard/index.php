<?php
/** @var array $kpis */
$kpis = $kpis ?? [];
$user = auth_user();
$name = $user['name'] ?? 'Admin';
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

<section class="section dashboard">
  <div class="vc-hero-banner mb-4">
    <div class="vc-hero-copy">
      <p class="vc-hero-eyebrow">VeggiiCart Operations</p>
      <h2>Welcome back, <?= e(explode(' ', $name)[0]) ?></h2>
      <p>Your B2B wholesale command center — track orders, stock, and deliveries in one place.</p>
    </div>
    <div class="vc-hero-glow" aria-hidden="true"></div>
  </div>

  <div class="row g-3">
    <?php foreach ($kpis as $i => $kpi): ?>
      <div class="col-xxl-3 col-md-6">
        <div class="card info-card vc-kpi-card <?= e($kpi['class']) ?> vc-fade-up" style="--delay: <?= (int) $i * 60 ?>ms">
          <div class="card-body">
            <h5 class="card-title"><?= e($kpi['label']) ?> <span>| Today</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi <?= e($kpi['icon']) ?>"></i>
              </div>
              <div class="ps-3">
                <h6><?= e($kpi['value']) ?></h6>
                <span class="vc-kpi-hint"><?= e($kpi['hint']) ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-lg-8">
      <div class="card vc-panel-card">
        <div class="card-body">
          <h5 class="card-title">Getting started</h5>
          <p class="text-muted mb-3">Module screens are scaffolded and ready. Real CRUD will connect once the MySQL schema is locked.</p>
          <div class="vc-quick-links">
            <a href="<?= e(url('products')) ?>" class="vc-quick-link"><i class="bi bi-box-seam"></i> Products</a>
            <a href="<?= e(url('orders')) ?>" class="vc-quick-link"><i class="bi bi-cart3"></i> Orders</a>
            <a href="<?= e(url('delivery')) ?>" class="vc-quick-link"><i class="bi bi-truck"></i> Delivery</a>
            <a href="<?= e(url('customers')) ?>" class="vc-quick-link"><i class="bi bi-people"></i> Customers</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card vc-panel-card vc-status-card">
        <div class="card-body">
          <h5 class="card-title">System status</h5>
          <ul class="vc-status-list">
            <li><span class="dot ok"></span> Admin panel shell live</li>
            <li><span class="dot ok"></span> Auth &amp; RBAC wired</li>
            <li><span class="dot warn"></span> Schema / CRUD pending</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
