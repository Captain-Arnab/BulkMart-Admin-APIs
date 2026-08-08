<?php $success=$success??null; $error=$error??null; $filters=$filters??['q'=>'','role'=>'','active'=>'']; ?>
<div class="pagetitle d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div><h1>Roles &amp; Sub-Admins</h1>
  <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li><li class="breadcrumb-item active">Roles</li></ol></nav></div>
  <a href="<?= e(url('roles/create')) ?>" class="btn btn-primary btn-sm">Create admin</a>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section">
  <div class="card vc-filter-card mb-3"><div class="card-body py-3">
    <form class="row g-2 align-items-end" method="GET" action="<?= e(url('roles')) ?>">
      <div class="col-md-4"><label class="form-label mb-1">Search</label><input type="text" name="q" value="<?= e($filters['q']) ?>" class="form-control" placeholder="Name or email"></div>
      <div class="col-md-3"><label class="form-label mb-1">Role</label>
        <select name="role" class="form-select">
          <option value="">All</option>
          <?php foreach (['super_admin','sub_admin','delivery_manager'] as $r): ?>
            <option value="<?= $r ?>" <?= ($filters['role']??'')===$r?'selected':'' ?>><?= e($r) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label mb-1">Active</label>
        <select name="active" class="form-select">
          <option value="">All</option>
          <option value="1" <?= ($filters['active']??'')==='1'?'selected':'' ?>>Yes</option>
          <option value="0" <?= ($filters['active']??'')==='0'?'selected':'' ?>>No</option>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?= e(url('roles')) ?>">Reset</a></div>
    </form>
  </div></div>
  <div class="card"><div class="card-body pt-3">
<table class="table table-hover align-middle">
  <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Modules</th><th>Active</th><th></th></tr></thead>
  <tbody>
  <?php if (!$admins): ?><tr><td colspan="6" class="text-center text-muted py-4">No admins match filters.</td></tr><?php endif; ?>
  <?php foreach ($admins as $a): ?>
    <tr>
      <td class="fw-semibold"><?= e($a['name']) ?></td>
      <td><?= e($a['email']) ?></td>
      <td><span class="badge bg-secondary"><?= e($a['role_type']) ?></span></td>
      <td><?= $a['role_type']==='sub_admin' ? (int)$a['module_count'] : ($a['role_type']==='super_admin' ? 'All' : 'Delivery') ?></td>
      <td><?= (int)$a['is_active']===1 ? 'Yes' : 'No' ?></td>
      <td class="text-end text-nowrap">
        <a class="btn btn-sm btn-outline-primary" href="<?= e(url('roles/'.$a['id'].'/edit')) ?>">Edit</a>
        <?php if ((int)$a['id'] !== (int)auth_user()['id']): ?>
        <form class="d-inline" method="POST" action="<?= e(url('roles/'.$a['id'].'/toggle-active')) ?>">
          <button class="btn btn-sm btn-outline-secondary" type="submit"><?= (int)$a['is_active']===1?'Deactivate':'Activate' ?></button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div></div></section>
