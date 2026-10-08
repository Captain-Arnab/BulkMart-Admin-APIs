<?php
/**
 * Delivery address block + Copy Address / Copy Location Link / Share via WhatsApp.
 *
 * @var array $addrOrder   row from Order::find() or Order::paginate()
 * @var bool  $addrCompact optional — table-cell layout with icon-only actions
 */
$addrCompact = !empty($addrCompact);
$addrText = Order::formatAddress($addrOrder);
$addrMaps = Order::mapsLink($addrOrder);
$addrDmMobile = trim((string) ($addrOrder['delivery_manager_mobile'] ?? ''));
$addrWhatsApp = Order::whatsappShareUrl($addrOrder, $addrDmMobile !== '' ? $addrDmMobile : null);
$addrWhatsAppTitle = $addrDmMobile !== ''
    ? 'Share via WhatsApp with ' . ($addrOrder['delivery_manager_name'] ?? 'delivery manager') . ' (' . $addrDmMobile . ')'
    : 'Share via WhatsApp';
$addrMapsNote = $addrMaps['exact'] ? 'Pinned GPS location' : 'Approximate location (no GPS pin, searches the address)';
?>
<div class="vc-addr-block<?= $addrCompact ? ' vc-addr-block--compact' : '' ?>">
  <?php if (trim((string) ($addrOrder['address_label'] ?? '')) !== ''): ?>
    <span class="vc-addr-tag"><i class="bi bi-tag-fill"></i><?= e($addrOrder['address_label']) ?></span>
  <?php endif; ?>
  <div class="vc-addr-lines">
    <div class="vc-addr-street"><?= e($addrOrder['line1'] ?? '') ?></div>
    <?php if (!empty($addrOrder['line2'])): ?><div><?= e($addrOrder['line2']) ?></div><?php endif; ?>
    <div><?= e($addrOrder['city'] ?? '') ?>, <?= e($addrOrder['state'] ?? '') ?> — <strong><?= e($addrOrder['pincode'] ?? '') ?></strong></div>
    <?php if (!empty($addrOrder['landmark'])): ?>
      <div class="vc-addr-landmark"><i class="bi bi-signpost-2"></i> <?= e($addrOrder['landmark']) ?></div>
    <?php endif; ?>
  </div>

  <div class="vc-addr-actions">
    <button type="button"
            class="btn btn-sm vc-btn-addr"
            data-vc-copy="<?= e($addrText) ?>"
            data-vc-copy-done="Address copied"
            title="Copy Address">
      <i class="bi bi-clipboard"></i><?php if (!$addrCompact): ?> Copy Address<?php endif; ?>
    </button>
    <button type="button"
            class="btn btn-sm vc-btn-addr"
            data-vc-copy="<?= e($addrMaps['url']) ?>"
            data-vc-copy-done="<?= $addrMaps['exact'] ? 'Location link copied' : 'Approximate location link copied' ?>"
            title="Copy Location Link — <?= e($addrMapsNote) ?>">
      <i class="bi bi-geo-alt"></i><?php if (!$addrCompact): ?> Copy Location Link<?php endif; ?>
    </button>
    <a class="btn btn-sm vc-btn-whatsapp"
       href="<?= e($addrWhatsApp) ?>"
       target="_blank"
       rel="noopener"
       title="<?= e($addrWhatsAppTitle) ?>">
      <i class="bi bi-whatsapp"></i><?php if (!$addrCompact): ?> Share via WhatsApp<?php endif; ?>
    </a>
  </div>

  <div class="vc-addr-geo <?= $addrMaps['exact'] ? 'is-exact' : 'is-approx' ?>">
    <span title="<?= e($addrMaps['exact'] ? 'The location link opens the exact GPS pin' : 'No GPS pin saved — the location link searches the address') ?>">
      <i class="bi <?= $addrMaps['exact'] ? 'bi-pin-map-fill' : 'bi-exclamation-triangle' ?>"></i>
      <?= e($addrMaps['exact'] ? 'Pinned GPS location' : 'Approximate location') ?>
    </span>
    <a href="<?= e($addrMaps['url']) ?>" target="_blank" rel="noopener">Open map <i class="bi bi-box-arrow-up-right"></i></a>
  </div>
</div>
