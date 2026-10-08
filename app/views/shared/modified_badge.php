<?php
/** @var array $badgeOrder needs edit_count */
?>
<?php if (Order::isModified($badgeOrder)): ?>
  <span class="vc-status vc-status--modified" title="The customer used their one-time order edit">
    <i class="bi bi-pencil-square"></i>
    Modified
  </span>
<?php endif; ?>
