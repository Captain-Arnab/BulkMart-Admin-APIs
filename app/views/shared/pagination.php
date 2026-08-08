<?php
/**
 * Shared pagination.
 *
 * @var int $page
 * @var int $pages
 * @var string $baseUrl  e.g. url('orders') without query
 * @var array $query     current filters (page key overwritten)
 */
$page = max(1, (int) ($page ?? 1));
$pages = max(1, (int) ($pages ?? 1));
$baseUrl = $baseUrl ?? '';
$query = $query ?? [];

if ($pages <= 1) {
    return;
}

$link = static function (int $p) use ($baseUrl, $query): string {
    $q = $query;
    $q['page'] = $p;
    $qs = http_build_query(array_filter($q, static fn ($v) => $v !== null && $v !== ''));
    return $baseUrl . ($qs !== '' ? '?' . $qs : '');
};

$window = 2;
$start = max(1, $page - $window);
$end = min($pages, $page + $window);
?>
<nav class="vc-pagination mt-3" aria-label="Pagination">
  <ul class="pagination vc-pager mb-0">
    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= $page <= 1 ? '#' : e($link($page - 1)) ?>" aria-label="Previous" <?= $page <= 1 ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
        <i class="bi bi-chevron-left"></i>
      </a>
    </li>

    <?php if ($start > 1): ?>
      <li class="page-item"><a class="page-link" href="<?= e($link(1)) ?>">1</a></li>
      <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
    <?php endif; ?>

    <?php for ($p = $start; $p <= $end; $p++): ?>
      <li class="page-item <?= $p === $page ? 'active' : '' ?>">
        <a class="page-link" href="<?= e($link($p)) ?>" <?= $p === $page ? 'aria-current="page"' : '' ?>><?= $p ?></a>
      </li>
    <?php endfor; ?>

    <?php if ($end < $pages): ?>
      <?php if ($end < $pages - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
      <li class="page-item"><a class="page-link" href="<?= e($link($pages)) ?>"><?= $pages ?></a></li>
    <?php endif; ?>

    <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= $page >= $pages ? '#' : e($link($page + 1)) ?>" aria-label="Next" <?= $page >= $pages ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
        <i class="bi bi-chevron-right"></i>
      </a>
    </li>
  </ul>
  <div class="vc-pagination-meta">Page <?= $page ?> of <?= $pages ?></div>
</nav>
