<?php
/** @var array|null $user */
$user = $user ?? auth_user();
$displayName = $user['name'] ?? 'Admin';
$shortName = explode(' ', $displayName)[0] ?? 'Admin';
$roleLabel = match ($user['role'] ?? '') {
    'super_admin' => 'Super Admin',
    'sub_admin' => 'Sub-Admin',
    'delivery_manager' => 'Delivery Manager',
    default => 'Admin',
};
?>
<header id="header" class="header fixed-top d-flex align-items-center">
  <div class="d-flex align-items-center justify-content-between">
    <a href="<?= e(url('dashboard')) ?>" class="logo d-flex align-items-center">
      <img src="<?= e(asset('img/logo-mark.png')) ?>" alt="VeggiiCart" class="logo-icon">
      <img src="<?= e(asset('img/logo-on-light.png')) ?>" alt="VeggiiCart" class="logo-full">
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>

  <div class="search-bar">
    <form class="search-form d-flex align-items-center" method="GET" action="<?= e(url('dashboard')) ?>" onsubmit="return false;">
      <input type="text" name="query" placeholder="Search modules…" title="Search" disabled>
      <button type="submit" title="Search" disabled><i class="bi bi-search"></i></button>
    </form>
  </div>

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <div class="vc-avatar rounded-circle d-flex align-items-center justify-content-center">
            <?= e(strtoupper(substr($displayName, 0, 1))) ?>
          </div>
          <span class="d-none d-md-block dropdown-toggle ps-2"><?= e($shortName) ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6><?= e($displayName) ?></h6>
            <span><?= e($roleLabel) ?></span>
          </li>
          <li><hr class="dropdown-divider"></li>
          <?php if (rbac_can('settings')): ?>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="<?= e(url('settings')) ?>">
              <i class="bi bi-gear"></i><span>Settings</span>
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <?php endif; ?>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="<?= e(url('logout')) ?>">
              <i class="bi bi-box-arrow-right"></i><span>Sign Out</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</header>
