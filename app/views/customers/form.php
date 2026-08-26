<?php
/** @var array $customer */
/** @var array<int,array{key:string,label:string}> $businessTypes */
$error = $error ?? null;
$currentType = trim((string) ($customer['business_type'] ?? ''));
$typeLabels = array_column($businessTypes, 'label');
$typeInList = $currentType !== '' && in_array($currentType, $typeLabels, true);
?>
<div class="pagetitle d-flex flex-wrap justify-content-between align-items-end gap-2">
  <div>
    <h1><?= e($title) ?></h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= e(url('customers')) ?>">Customers</a></li>
        <li class="breadcrumb-item"><a href="<?= e(url('customers/' . $customer['id'])) ?>"><?= e($customer['business_name'] ?: 'Customer') ?></a></li>
        <li class="breadcrumb-item active">Edit</li>
      </ol>
    </nav>
  </div>
  <a href="<?= e(url('customers/' . $customer['id'])) ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i>Back to profile
  </a>
</div>

<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="card">
    <div class="card-body pt-4">
      <form method="POST" action="<?= e(url('customers/' . $customer['id'] . '/update')) ?>" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Owner name *</label>
          <input type="text" name="owner_name" class="form-control" required maxlength="120"
                 value="<?= e($customer['owner_name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Business name *</label>
          <input type="text" name="business_name" class="form-control" required maxlength="160"
                 value="<?= e($customer['business_name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Mobile *</label>
          <input type="tel" name="mobile" class="form-control" required maxlength="15"
                 value="<?= e($customer['mobile'] ?? '') ?>">
          <div class="form-text">10-digit Indian mobile number used for OTP login.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" maxlength="190"
                 value="<?= e($customer['email'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Business type *</label>
          <select name="business_type" class="form-select" required>
            <?php if ($currentType !== '' && !$typeInList): ?>
              <option value="<?= e($currentType) ?>" selected><?= e(ucfirst($currentType)) ?> (current)</option>
            <?php endif; ?>
            <?php foreach ($businessTypes as $type): ?>
              <option value="<?= e($type['label']) ?>" <?= strcasecmp($currentType, $type['label']) === 0 ? 'selected' : '' ?>>
                <?= e($type['label']) ?>
              </option>
            <?php endforeach; ?>
            <?php if ($currentType === 'unregistered'): ?>
              <option value="unregistered" selected>Unregistered (pending signup)</option>
            <?php endif; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">GST number</label>
          <input type="text" name="gst_number" class="form-control" maxlength="20"
                 value="<?= e($customer['gst_number'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">FSSAI number</label>
          <input type="text" name="fssai_number" class="form-control" maxlength="20"
                 value="<?= e($customer['fssai_number'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">PAN number</label>
          <input type="text" name="pan_number" class="form-control" maxlength="12"
                 value="<?= e($customer['pan_number'] ?? '') ?>">
        </div>
        <div class="col-12">
          <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i>Save changes</button>
          <a href="<?= e(url('customers/' . $customer['id'])) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</section>
