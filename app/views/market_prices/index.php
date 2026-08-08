<?php $success=$success??null; $error=$error??null; $filters=$filters??['q'=>'']; ?>
<div class="pagetitle"><h1>Market Prices</h1>
<nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li><li class="breadcrumb-item active">Market Prices</li></ol></nav></div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section">
  <div class="card vc-filter-card mb-3"><div class="card-body py-3">
    <form class="row g-2 align-items-end" method="GET" action="<?= e(url('market-prices')) ?>">
      <div class="col-md-6"><label class="form-label mb-1">Search product</label><input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" class="form-control" placeholder="Product name"></div>
      <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?= e(url('market-prices')) ?>">Reset</a></div>
    </form>
  </div></div>
  <div class="card"><div class="card-body">
  <h5 class="card-title">Today's market prices (<?= e(date('d M Y')) ?>)</h5>
  <p class="text-muted small">Catalog price is the product list price. Set a market override for the customer app home section.</p>
  <form method="POST" action="<?= e(url('market-prices/save')) ?>">
    <div class="table-responsive"><table class="table align-middle">
      <thead><tr><th>Product</th><th>Unit</th><th>Catalog price</th><th>Today's market price</th></tr></thead>
      <tbody>
      <?php if (!$rows): ?><tr><td colspan="4" class="text-muted text-center py-4">No products match.</td></tr><?php endif; ?>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= e($r['name']) ?></td>
          <td><?= e($r['unit']) ?></td>
          <td>₹<?= e(number_format((float)$r['catalog_price'],2)) ?></td>
          <td style="max-width:160px">
            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                   name="price[<?= (int)$r['product_id'] ?>]"
                   value="<?= e($r['market_price'] ?? $r['catalog_price']) ?>">
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <button class="btn btn-primary" type="submit">Save all</button>
  </form>
</div></div></section>
