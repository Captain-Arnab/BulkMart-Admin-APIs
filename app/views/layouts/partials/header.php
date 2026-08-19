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

$searchModules = [];
foreach (rbac_sidebar_items($user) as $item) {
    $searchModules[] = [
        'label' => $item['label'],
        'route' => $item['route'],
        'icon'  => $item['icon'] ?? 'bi-grid',
        'url'   => url($item['route']),
    ];
    foreach ($item['children'] ?? [] as $child) {
        $searchModules[] = [
            'label' => $item['label'] . ' · ' . $child['label'],
            'route' => $child['route'],
            'icon'  => $item['icon'] ?? 'bi-circle',
            'url'   => url($child['route']),
        ];
    }
}
?>
<header id="header" class="header fixed-top d-flex align-items-center">
  <div class="d-flex align-items-center justify-content-between">
    <a href="<?= e(url('dashboard')) ?>" class="logo d-flex align-items-center">
      <img src="<?= e(admin_logo_mark_src()) ?>" alt="VeggiiCart" class="logo-icon">
      <img src="<?= e(admin_logo_src()) ?>" alt="VeggiiCart" class="logo-full">
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>

  <div class="search-bar vc-module-search" id="vc-module-search">
    <form class="search-form d-flex align-items-center" role="search" autocomplete="off" onsubmit="return false;">
      <i class="bi bi-search vc-search-icon" aria-hidden="true"></i>
      <input type="search"
             id="vc-module-search-input"
             name="query"
             placeholder="Search modules…"
             aria-label="Search modules"
             aria-autocomplete="list"
             aria-controls="vc-module-search-results"
             aria-expanded="false">
      <button type="button" class="vc-search-clear" id="vc-module-search-clear" title="Clear" hidden>
        <i class="bi bi-x-lg"></i>
      </button>
    </form>
    <div class="vc-search-results" id="vc-module-search-results" role="listbox" hidden></div>
  </div>

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
      <li class="nav-item d-block d-lg-none">
        <a class="nav-link nav-icon search-bar-toggle" href="#" title="Search">
          <i class="bi bi-search"></i>
        </a>
      </li>
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
          <?php if (rbac_can('settings') || in_array($user['role'] ?? '', ['super_admin', 'sub_admin'], true)): ?>
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
<script>
  window.VC_MODULES = <?= json_encode($searchModules, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
