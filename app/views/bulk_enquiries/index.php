<?php
$success = $success ?? null;
$error = $error ?? null;
$filters = $filters ?? ['status' => '', 'q' => ''];
?>
<div class="pagetitle">
  <h1>Bulk Enquiries</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
      <li class="breadcrumb-item active">Bulk Enquiries</li>
    </ol>
  </nav>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section">
  <div class="card vc-filter-card mb-3">
    <div class="card-body py-3">
      <form class="row g-2 align-items-end" method="GET" action="<?= e(url('bulk-enquiries')) ?>">
        <div class="col-md-4">
          <label class="form-label mb-1">Search</label>
          <input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" class="form-control" placeholder="Name / business / mobile / product">
        </div>
        <div class="col-md-3">
          <label class="form-label mb-1">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <?php foreach (BulkEnquiry::STATUSES as $s): ?>
              <option value="<?= e($s) ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-primary" type="submit">Filter</button>
          <a class="btn btn-outline-secondary" href="<?= e(url('bulk-enquiries')) ?>">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body pt-3">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name / Business</th>
              <th>Mobile</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Location</th>
              <th>Preferred date</th>
              <th>Status</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$result['rows']): ?>
              <tr><td colspan="10" class="text-muted text-center py-4">No bulk enquiries yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($result['rows'] as $row): ?>
              <tr>
                <td>#<?= (int) $row['id'] ?></td>
                <td>
                  <strong><?= e($row['name']) ?></strong>
                  <?php if (!empty($row['business_name'])): ?>
                    <div class="small text-muted"><?= e($row['business_name']) ?></div>
                  <?php endif; ?>
                </td>
                <td><?= e($row['mobile']) ?></td>
                <td><?= e($row['product_name'] ?: '—') ?></td>
                <td><?= e($row['required_quantity']) ?></td>
                <td>
                  <?= e($row['delivery_location']) ?>
                  <div class="small text-muted"><?= e($row['pincode']) ?></div>
                </td>
                <td class="small">
                  <?= !empty($row['preferred_delivery_date'])
                      ? e(date('d M Y', strtotime($row['preferred_delivery_date'])))
                      : '—' ?>
                </td>
                <td>
                  <span class="badge <?= e(BulkEnquiry::STATUS_BADGE[$row['status']] ?? 'bg-secondary') ?>"
                    <?php if (!empty(BulkEnquiry::STATUS_STYLE[$row['status']])): ?>
                      style="<?= e(BulkEnquiry::STATUS_STYLE[$row['status']]) ?>"
                    <?php endif; ?>>
                    <?= e(ucfirst($row['status'])) ?>
                  </span>
                </td>
                <td class="small"><?= e(date('d M Y H:i', strtotime($row['created_at']))) ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" href="<?= e(url('bulk-enquiries/' . $row['id'])) ?>">Open</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
