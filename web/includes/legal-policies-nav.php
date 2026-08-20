<?php
/**
 * Shared Policies menu for legal pages.
 * Expects $currentPolicy: privacy|terms|shipping-returns|cancellation
 */
$currentPolicy = $currentPolicy ?? '';
$policyNavItems = [
    [
        'key'   => 'privacy',
        'href'  => 'privacy-policy.php',
        'icon'  => 'fa-shield-halved',
        'label' => 'Privacy Policy',
    ],
    [
        'key'   => 'terms',
        'href'  => 'terms-and-conditions.php',
        'icon'  => 'fa-file-contract',
        'label' => 'Terms & Conditions',
    ],
    [
        'key'   => 'shipping-returns',
        'href'  => 'shipping-returns-policy.php',
        'icon'  => 'fa-truck-fast',
        'label' => 'Shipping & Returns Policy',
    ],
    [
        'key'   => 'cancellation',
        'href'  => 'cancellation-policy.php',
        'icon'  => 'fa-ban',
        'label' => 'Cancellation Policy',
    ],
];
?>
<div class="vc-legal-policies-menu">
    <span class="vc-legal-policies-label">Policies</span>
    <h3>All Policies</h3>
    <nav class="vc-legal-policies-nav" aria-label="Policies">
        <?php foreach ($policyNavItems as $item): ?>
            <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
               class="<?= $currentPolicy === $item['key'] ? 'active' : '' ?>">
                <i class="fa-solid <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</div>
