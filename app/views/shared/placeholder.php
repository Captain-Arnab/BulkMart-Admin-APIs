<?php
/** @var string $title */
/** @var string $moduleLabel */
/** @var array $breadcrumb */
$moduleLabel = $moduleLabel ?? $title ?? 'Module';
$breadcrumb = $breadcrumb ?? [];
?>
<div class="pagetitle">
  <h1><?= e($title ?? 'Module') ?></h1>
  <nav>
    <ol class="breadcrumb">
      <?php foreach ($breadcrumb as $crumb): ?>
        <?php if (!empty($crumb['url'])): ?>
          <li class="breadcrumb-item"><a href="<?= e($crumb['url']) ?>"><?= e($crumb['label']) ?></a></li>
        <?php else: ?>
          <li class="breadcrumb-item active"><?= e($crumb['label']) ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body py-5 text-center">
          <div class="vc-coming-soon-icon mb-3">
            <i class="bi bi-tools"></i>
          </div>
          <h5 class="card-title mb-2">Coming soon</h5>
          <p class="text-muted mb-0">
            <?= e($moduleLabel) ?> management will be built here.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>
