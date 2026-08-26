<?php
/** @var array $customer */
/** @var array $documents */
/** @var array<int,array{key:string,label:string}> $businessTypes */
$error = $error ?? null;
$success = $success ?? null;
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

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="card mb-3">
    <div class="card-body pt-4">
      <h5 class="card-title">Profile details</h5>
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
          <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i>Save profile</button>
          <a href="<?= e(url('customers/' . $customer['id'])) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body pt-4" id="documents">
      <h5 class="card-title">KYC documents</h5>
      <p class="text-muted small mb-4">Upload new files, replace existing documents, or remove outdated ones. JPG, PNG, WEBP, or PDF up to 5MB.</p>

      <?php if ($documents): ?>
        <div class="table-responsive mb-4">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Document</th>
                <th>Uploaded</th>
                <th>Current file</th>
                <th>Replace</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($documents as $doc): ?>
                <?php
                  $docType = Customer::DOC_ALIASES[$doc['document_type']] ?? $doc['document_type'];
                  $docLabel = Customer::DOC_LABELS[$docType] ?? $doc['document_type'];
                ?>
                <tr>
                  <td class="fw-semibold"><?= e($docLabel) ?></td>
                  <td class="small text-muted"><?= e(date('d M Y, h:i A', strtotime($doc['uploaded_at']))) ?></td>
                  <td>
                    <a href="<?= e(media($doc['file_url'])) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-box-arrow-up-right me-1"></i>View
                    </a>
                  </td>
                  <td style="min-width: 220px;">
                    <form method="POST" action="<?= e(url('customers/' . $customer['id'] . '/documents/' . $doc['id'] . '/replace')) ?>" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                      <input type="file" name="file" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf" required>
                      <button class="btn btn-sm btn-primary text-nowrap" type="submit">Replace</button>
                    </form>
                  </td>
                  <td class="text-end">
                    <form method="POST" action="<?= e(url('customers/' . $customer['id'] . '/documents/' . $doc['id'] . '/delete')) ?>"
                          data-vc-confirm="Delete this document?"
                          data-vc-confirm-danger
                          data-vc-confirm-title="Delete document">
                      <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-light border mb-4">No documents uploaded yet.</div>
      <?php endif; ?>

      <h6 class="mb-3">Upload new document</h6>
      <form method="POST" action="<?= e(url('customers/' . $customer['id'] . '/documents')) ?>" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-5">
          <label class="form-label">Document type *</label>
          <select name="document_type" class="form-select" required>
            <option value="">Select type</option>
            <?php foreach (Customer::UPLOADABLE_DOC_TYPES as $typeKey): ?>
              <option value="<?= e($typeKey) ?>"><?= e(Customer::DOC_LABELS[$typeKey] ?? $typeKey) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-5">
          <label class="form-label">File *</label>
          <input type="file" name="file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-success w-100" type="submit"><i class="bi bi-upload me-1"></i>Upload</button>
        </div>
      </form>
    </div>
  </div>
</section>
