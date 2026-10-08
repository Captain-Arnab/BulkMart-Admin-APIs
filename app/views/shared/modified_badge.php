<?php
/**
 * @var array $badgeOrder       needs edit_count
 * @var bool  $badgeShowCount   optional — detail pages show "Edited Nx"
 * @var bool  $badgeLarge       optional — match a vc-status--lg status pill next to it
 */
$badgeEdits = (int) ($badgeOrder['edit_count'] ?? 0);
?>
<?php if (Order::isModified($badgeOrder)): ?>
  <span class="vc-status vc-status--modified<?= !empty($badgeLarge) ? ' vc-status--lg' : '' ?>"
        title="The customer edited this order <?= $badgeEdits ?> of <?= Order::ORDER_EDIT_MAX_COUNT ?> allowed time(s)">
    <i class="bi bi-pencil-square"></i>
    Modified<?php if (!empty($badgeShowCount)): ?> · Edited <?= $badgeEdits ?>x<?php endif; ?>
  </span>
<?php endif; ?>
<?php $badgeShowCount = false; $badgeLarge = false; ?>
