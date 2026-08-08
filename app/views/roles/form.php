<?php
$isEdit = !empty($admin);
$error = $error ?? null;
$modules = $modules ?? [];
?>
<div class="pagetitle"><h1><?= e($title) ?></h1>
<nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('roles')) ?>">Roles</a></li><li class="breadcrumb-item active"><?= $isEdit?'Edit':'Create' ?></li></ol></nav></div>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section"><div class="card"><div class="card-body pt-4">
<form method="POST" action="<?= e($isEdit ? url('roles/'.$admin['id'].'/update') : url('roles')) ?>" class="row g-3" id="roleForm">
  <div class="col-md-6"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required value="<?= e($admin['name'] ?? '') ?>"></div>
  <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required value="<?= e($admin['email'] ?? '') ?>"></div>
  <div class="col-md-6"><label class="form-label">Password <?= $isEdit ? '(leave blank to keep)' : '*' ?></label>
    <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required minlength="6"' ?>></div>
  <div class="col-md-6"><label class="form-label">Role type *</label>
    <select name="role_type" id="role_type" class="form-select" required>
      <?php foreach (['sub_admin'=>'Sub-Admin','delivery_manager'=>'Delivery Manager','super_admin'=>'Super Admin (high privilege)'] as $k=>$l): ?>
        <option value="<?= $k ?>" <?= ($admin['role_type'] ?? 'sub_admin')===$k?'selected':'' ?>><?= e($l) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-12" id="modulesBox">
    <label class="form-label">Module permissions (Sub-Admin)</label>
    <div class="row">
      <?php foreach (AdminUser::GRANTABLE_MODULES as $key=>$label): ?>
        <div class="col-md-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="modules[]" value="<?= e($key) ?>" id="m_<?= e($key) ?>"
              <?= in_array($key, $modules, true) ? 'checked' : '' ?>>
            <label class="form-check-label" for="m_<?= e($key) ?>"><?= e($label) ?></label>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="col-12"><div class="form-check">
    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= !isset($admin['is_active']) || (int)$admin['is_active']===1 ? 'checked' : '' ?>>
    <label class="form-check-label" for="is_active">Active</label>
  </div></div>
  <div class="col-12">
    <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save' : 'Create' ?></button>
    <a href="<?= e(url('roles')) ?>" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>
</div></div></section>
<script>
(function(){
  const sel=document.getElementById('role_type');
  const box=document.getElementById('modulesBox');
  function sync(){ box.style.display = sel.value==='sub_admin' ? '' : 'none'; }
  sel.addEventListener('change', sync); sync();
})();
</script>
