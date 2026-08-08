<?php
/** @var array $navItems */
/** @var string $current */
$navItems = $navItems ?? rbac_sidebar_items();
$current = $current ?? current_path();
?>
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <?php foreach ($navItems as $item): ?>
      <?php
        $hasChildren = !empty($item['children']);
        $isActive = rbac_is_active($item['route'], $current);
        if ($hasChildren) {
            foreach ($item['children'] as $child) {
                if (rbac_is_active($child['route'], $current)) {
                    $isActive = true;
                    break;
                }
            }
        }
        $collapseId = 'nav-' . $item['key'];
      ?>

      <?php if ($hasChildren): ?>
        <li class="nav-item">
          <a class="nav-link <?= $isActive ? '' : 'collapsed' ?>"
             data-bs-target="#<?= e($collapseId) ?>"
             data-bs-toggle="collapse"
             href="#">
            <i class="bi <?= e($item['icon']) ?>"></i>
            <span><?= e($item['label']) ?></span>
            <i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="<?= e($collapseId) ?>"
              class="nav-content collapse <?= $isActive ? 'show' : '' ?>"
              data-bs-parent="#sidebar-nav">
            <?php foreach ($item['children'] as $child): ?>
              <li>
                <a href="<?= e(url($child['route'])) ?>"
                   class="<?= rbac_is_active($child['route'], $current) ? 'active' : '' ?>">
                  <i class="bi bi-circle"></i>
                  <span><?= e($child['label']) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </li>
      <?php else: ?>
        <li class="nav-item">
          <a class="nav-link <?= $isActive ? '' : 'collapsed' ?>" href="<?= e(url($item['route'])) ?>">
            <i class="bi <?= e($item['icon']) ?>"></i>
            <span><?= e($item['label']) ?></span>
          </a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
</aside>
