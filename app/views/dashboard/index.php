<?php
/** @var array $kpis */
$kpis = $kpis ?? [];
?>
<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <div class="row">
    <?php foreach ($kpis as $kpi): ?>
      <div class="col-xxl-3 col-md-6">
        <div class="card info-card <?= e($kpi['class']) ?>">
          <div class="card-body">
            <h5 class="card-title"><?= e($kpi['label']) ?> <span>| Today</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi <?= e($kpi['icon']) ?>"></i>
              </div>
              <div class="ps-3">
                <h6><?= e($kpi['value']) ?></h6>
                <span class="text-muted small pt-2"><?= e($kpi['hint']) ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Welcome to VeggiiCart Admin</h5>
          <p class="mb-0">
            This is the operations shell for your B2B wholesale platform.
            Module CRUD will be wired once the MySQL schema is finalized.
            Use the sidebar to preview each area — placeholders are ready for demos.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>
