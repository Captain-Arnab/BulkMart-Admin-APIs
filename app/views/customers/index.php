<?php
$success = $success ?? null; $error = $error ?? null;
?>
<div class="pagetitle d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1>Customers</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li><li class="breadcrumb-item active">Customers</li></ol></nav>
  </div>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section">
  <div class="card mb-3"><div class="card-body py-3">
    <form class="row g-2 align-items-end" method="GET" action="<?= e(url('customers')) ?>">
      <div class="col-md-4"><label class="form-label mb-1">Search</label><input type="text" name="q" value="<?= e($filters['q']) ?>" class="form-control" placeholder="Business / mobile"></div>
      <div class="col-md-3"><label class="form-label mb-1">KYC status</label>
        <select name="kyc_status" class="form-select">
          <option value="">All</option>
          <?php foreach (['pending','approved','rejected'] as $s): ?>
            <option value="<?= $s ?>" <?= $filters['kyc_status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?= e(url('customers')) ?>">Reset</a></div>
    </form>
  </div></div>
  <div class="card"><div class="card-body pt-3">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead><tr><th>Business</th><th>Owner</th><th>Mobile</th><th>Type</th><th>KYC</th><th>Registered</th><th></th></tr></thead>
        <tbody>
        <?php if (!$result['rows']): ?><tr><td colspan="7" class="text-center text-muted py-4">No customers found.</td></tr><?php endif; ?>
        <?php foreach ($result['rows'] as $c): ?>
          <tr>
            <td class="fw-semibold"><?= e($c['business_name']) ?><?php if (!empty($c['is_blocked'])): ?> <span class="badge bg-dark">Blocked</span><?php endif; ?></td>
            <td><?= e($c['owner_name']) ?></td>
            <td><?= e($c['mobile']) ?></td>
            <td><?= e($c['business_type']) ?></td>
            <td><span class="badge <?= e(Customer::KYC_BADGE[$c['kyc_status']] ?? 'bg-secondary') ?>"><?= e(ucfirst($c['kyc_status'])) ?></span></td>
            <td class="small"><?= e(date('d M Y', strtotime($c['created_at']))) ?></td>
            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= e(url('customers/'.$c['id'])) ?>">View</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if ($result['pages'] > 1): ?>
      <ul class="pagination pagination-sm mb-0">
        <?php for ($p=1;$p<=$result['pages'];$p++): ?>
          <li class="page-item <?= $p===$result['page']?'active':'' ?>"><a class="page-link" href="<?= e(url('customers?'.http_build_query(array_filter(['q'=>$filters['q']?:null,'kyc_status'=>$filters['kyc_status']?:null,'page'=>$p])))) ?>"><?= $p ?></a></li>
        <?php endfor; ?>
      </ul>
    <?php endif; ?>
  </div></div>
</section>
