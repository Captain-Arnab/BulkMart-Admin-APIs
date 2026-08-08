<?php
$result = $result ?? null;
$success = $success ?? null;
$error = $error ?? null;
?>
<div class="pagetitle">
  <h1>Bulk Stock Update</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= e(url('products')) ?>">Products</a></li>
      <li class="breadcrumb-item active">Bulk Stock</li>
    </ol>
  </nav>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Upload CSV / XLSX</h5>
          <p class="text-muted small">Columns: <code>item_code</code> (or <code>id</code>) + <code>stock</code>.</p>
          <p class="small"><a href="<?= e(url('products/templates/stock')) ?>"><i class="bi bi-download me-1"></i>Download template</a></p>
          <form method="POST" enctype="multipart/form-data" action="<?= e(url('products/bulk-stock')) ?>">
            <input type="file" name="file" class="form-control mb-3" accept=".csv,.xlsx,.txt" required>
            <button class="btn btn-primary" type="submit">Update stock</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Last update summary</h5>
          <?php if (!$result): ?>
            <p class="text-muted mb-0">No stock update run yet in this session.</p>
          <?php else: ?>
            <p><strong><?= (int)$result['updated'] ?></strong> updated of <?= (int)$result['total'] ?> rows.
              <strong><?= count($result['failed']) ?></strong> failed.</p>
            <?php if ($result['failed']): ?>
              <div class="table-responsive" style="max-height:260px;overflow:auto">
                <table class="table table-sm">
                  <thead><tr><th>Line</th><th>Reason</th></tr></thead>
                  <tbody>
                  <?php foreach ($result['failed'] as $f): ?>
                    <tr><td><?= (int)$f['line'] ?></td><td><?= e($f['reason']) ?></td></tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
