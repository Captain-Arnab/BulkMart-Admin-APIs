<?php $success=$success??null; $error=$error??null; $settings=$settings??[]; ?>
<div class="pagetitle"><h1>Settings</h1>
<nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li><li class="breadcrumb-item active">Settings</li></ol></nav></div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section"><div class="row g-3">
  <div class="col-lg-6"><div class="card"><div class="card-body">
    <h5 class="card-title">Change password</h5>
    <form method="POST" action="<?= e(url('settings/password')) ?>" class="row g-3">
      <div class="col-12"><label class="form-label">Current password</label><input type="password" name="current_password" class="form-control" required></div>
      <div class="col-12"><label class="form-label">New password</label><input type="password" name="new_password" class="form-control" required minlength="6"></div>
      <div class="col-12"><label class="form-label">Confirm new password</label><input type="password" name="confirm_password" class="form-control" required minlength="6"></div>
      <div class="col-12"><button class="btn btn-primary" type="submit">Update password</button></div>
    </form>
  </div></div></div>
  <div class="col-lg-6"><div class="card"><div class="card-body">
    <h5 class="card-title">App settings</h5>
    <form method="POST" action="<?= e(url('settings/app')) ?>" class="row g-3">
      <div class="col-12"><label class="form-label">Company name</label><input type="text" name="company_name" class="form-control" value="<?= e($settings['company_name'] ?? '') ?>"></div>
      <div class="col-12"><label class="form-label">Support phone</label><input type="text" name="support_phone" class="form-control" value="<?= e($settings['support_phone'] ?? '') ?>"></div>
      <div class="col-12"><label class="form-label">Support email</label><input type="email" name="support_email" class="form-control" value="<?= e($settings['support_email'] ?? '') ?>"></div>
      <div class="col-12"><button class="btn btn-primary" type="submit">Save settings</button></div>
    </form>
  </div></div></div>
</div></section>
