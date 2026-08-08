<?php $success=$success??null; $error=$error??null; ?>
<div class="pagetitle d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div><h1>Roles &amp; Sub-Admins</h1>
  <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li><li class="breadcrumb-item active">Roles</li></ol></nav></div>
  <a href="<?= e(url('roles/create')) ?>" class="btn btn-primary btn-sm">Create admin</a>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section"><div class="card"><div class="card-body pt-3">
<table class="table table-hover align-middle">
  <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Modules</th><th>Active</th><th></th></tr></thead>
  <tbody>
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
