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
  <div class="col-12"><div class="card"><div class="card-body">
    <h5 class="card-title">Logo &amp; favicon</h5>
    <p class="text-muted small">These files apply to both the admin panel and the customer website (header, login, and browser tab). Only Super Admin can change them.</p>
    <?php if (!empty($canBrand)): ?>
    <form method="POST" action="<?= e(url('settings/branding')) ?>" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Logo</label>
        <input type="file" name="admin_logo" class="form-control" accept="<?= e(media_brand_accept_attr()) ?>">
        <div class="form-text">Shown in the admin header and on the customer website. JPG, PNG, GIF, WEBP, AVIF, BMP, or ICO. Max 5MB.</div>
        <div class="vc-admin-gallery mt-2"><div class="vc-admin-gallery-card"><img src="<?= e(admin_logo_src()) ?>" alt="Admin logo"></div></div>
        <?php if (!empty($settings['admin_logo_url'])): ?>
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="remove_admin_logo" value="1" id="remove_admin_logo">
            <label class="form-check-label" for="remove_admin_logo">Restore default logo</label>
          </div>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <label class="form-label">Favicon</label>
        <input type="file" name="admin_favicon" class="form-control" accept="<?= e(media_brand_accept_attr()) ?>">
        <div class="form-text">Browser tab icon for admin and website. Also used as the small collapsed-sidebar mark. Square PNG or ICO works best.</div>
        <div class="vc-admin-gallery mt-2"><div class="vc-admin-gallery-card"><img src="<?= e(admin_favicon_src()) ?>" alt="Favicon"></div></div>
        <?php if (!empty($settings['admin_favicon_url'])): ?>
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="remove_admin_favicon" value="1" id="remove_admin_favicon">
            <label class="form-check-label" for="remove_admin_favicon">Restore default favicon</label>
          </div>
        <?php endif; ?>
      </div>
      <div class="col-12"><button class="btn btn-primary" type="submit">Save branding</button></div>
    </form>
    <?php else: ?>
      <div class="d-flex flex-wrap gap-3 align-items-center">
        <div class="vc-admin-gallery-card"><img src="<?= e(admin_logo_src()) ?>" alt="Admin logo"></div>
        <div class="vc-admin-gallery-card"><img src="<?= e(admin_favicon_src()) ?>" alt="Favicon"></div>
        <p class="text-muted mb-0">Ask a Super Admin to change the logo or favicon.</p>
      </div>
    <?php endif; ?>
  </div></div></div>
</div></section>
