<?php
$success = $success ?? null;
$error = $error ?? null;
$filters = $filters ?? ['q' => '', 'status' => ''];
$rows = $rows ?? [];
?>
<div class="pagetitle">
  <h1>Serviceable Pincodes</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
      <li class="breadcrumb-item active">Serviceable Pincodes</li>
    </ol>
  </nav>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Add pincode</h5>
          <form method="POST" action="<?= e(url('serviceable-pincodes')) ?>" class="row g-2">
            <div class="col-12">
              <label class="form-label">Pincode *</label>
              <input type="text" name="pincode" class="form-control" required pattern="\d{6}" maxlength="6" placeholder="500001">
            </div>
            <div class="col-12">
              <label class="form-label">City</label>
              <input type="text" name="city" class="form-control" value="Hyderabad">
            </div>
            <div class="col-12">
              <label class="form-label">State</label>
              <input type="text" name="state" class="form-control" value="Telangana">
            </div>
            <div class="col-12">
              <button class="btn btn-primary w-100" type="submit">Add</button>
            </div>
          </form>
          <p class="small text-muted mt-3 mb-0">Add active pincodes for serviceable cities. Deactivate instead of deleting to keep history.</p>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <form class="row g-2 align-items-end mb-3" method="GET" action="<?= e(url('serviceable-pincodes')) ?>">
            <div class="col-md-5">
              <label class="form-label mb-1">Search</label>
              <input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" class="form-control" placeholder="Pincode, city or state">
            </div>
            <div class="col-md-4">
              <label class="form-label mb-1">Status</label>
              <select name="status" class="form-select">
                <option value="">All</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
              <button class="btn btn-primary" type="submit">Filter</button>
              <a class="btn btn-outline-secondary" href="<?= e(url('serviceable-pincodes')) ?>">Reset</a>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Pincode</th>
                  <th>City</th>
                  <th>State</th>
                  <th>Status</th>
                  <th>Added</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!$rows): ?>
                  <tr><td colspan="6" class="text-center text-muted py-4">No pincodes found.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                  <tr>
                    <td><code><?= e($row['pincode']) ?></code></td>
                    <td><?= e($row['city']) ?></td>
                    <td><?= e($row['state'] ?? 'Telangana') ?></td>
                    <td>
                      <?php if ((int) $row['is_active'] === 1): ?>
                        <span class="badge bg-success">Active</span>
                      <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                      <?php endif; ?>
                    </td>
                    <td class="small"><?= e(date('d M Y', strtotime($row['created_at']))) ?></td>
                    <td class="text-end">
                      <form method="POST" action="<?= e(url('serviceable-pincodes/' . $row['id'] . '/toggle')) ?>" class="d-inline">
                        <button class="btn btn-sm btn-outline-<?= (int)$row['is_active'] === 1 ? 'secondary' : 'success' ?>" type="submit">
                          <?= (int)$row['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="small text-muted"><?= count($rows) ?> pincode(s)</div>
        </div>
      </div>
    </div>
  </div>
</section>
