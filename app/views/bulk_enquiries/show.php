<?php
$success = $success ?? null;
$error = $error ?? null;
$enquiry = $enquiry ?? [];
?>
<div class="pagetitle">
  <h1>Bulk Enquiry #<?= (int) $enquiry['id'] ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= e(url('bulk-enquiries')) ?>">Bulk Enquiries</a></li>
      <li class="breadcrumb-item active">#<?= (int) $enquiry['id'] ?></li>
    </ol>
  </nav>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<section class="section">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Enquiry details</h5>
          <dl class="row mb-0">
            <dt class="col-sm-4">Name</dt>
            <dd class="col-sm-8"><?= e($enquiry['name']) ?></dd>

            <dt class="col-sm-4">Business name</dt>
            <dd class="col-sm-8"><?= e($enquiry['business_name'] ?: '—') ?></dd>

            <dt class="col-sm-4">Mobile</dt>
            <dd class="col-sm-8">
              <a href="tel:<?= e($enquiry['mobile']) ?>"><?= e($enquiry['mobile']) ?></a>
            </dd>

            <dt class="col-sm-4">Product</dt>
            <dd class="col-sm-8">
              <?php if (!empty($enquiry['product_id'])): ?>
                <?= e($enquiry['product_name'] ?: ('#' . $enquiry['product_id'])) ?>
                <?php if (!empty($enquiry['product_unit'])): ?>
                  <span class="text-muted">(<?= e($enquiry['product_unit']) ?>)</span>
                <?php endif; ?>
              <?php else: ?>
                —
              <?php endif; ?>
            </dd>

            <dt class="col-sm-4">Required quantity</dt>
            <dd class="col-sm-8"><strong><?= e($enquiry['required_quantity']) ?></strong></dd>

            <dt class="col-sm-4">Delivery location</dt>
            <dd class="col-sm-8"><?= e($enquiry['delivery_location']) ?></dd>

            <dt class="col-sm-4">Pincode</dt>
            <dd class="col-sm-8"><?= e($enquiry['pincode']) ?></dd>

            <dt class="col-sm-4">Preferred delivery date</dt>
            <dd class="col-sm-8">
              <?= !empty($enquiry['preferred_delivery_date'])
                  ? e(date('d M Y', strtotime($enquiry['preferred_delivery_date'])))
                  : '—' ?>
            </dd>

            <dt class="col-sm-4">Additional requirement</dt>
            <dd class="col-sm-8"><?= $enquiry['additional_requirement'] ? nl2br(e($enquiry['additional_requirement'])) : '—' ?></dd>

            <dt class="col-sm-4">Submitted</dt>
            <dd class="col-sm-8"><?= e(date('d M Y H:i', strtotime($enquiry['created_at']))) ?></dd>

            <?php if (!empty($enquiry['customer_id'])): ?>
              <dt class="col-sm-4">Linked customer</dt>
              <dd class="col-sm-8">
                #<?= (int) $enquiry['customer_id'] ?>
                <?= e($enquiry['customer_business_name'] ?: $enquiry['customer_owner_name'] ?: '') ?>
              </dd>
            <?php endif; ?>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Update lead</h5>
          <form method="POST" action="<?= e(url('bulk-enquiries/' . $enquiry['id'] . '/status')) ?>">
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <?php foreach (BulkEnquiry::STATUSES as $s): ?>
                  <option value="<?= e($s) ?>" <?= ($enquiry['status'] ?? '') === $s ? 'selected' : '' ?>>
                    <?= e(ucfirst($s)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Internal notes</label>
              <textarea name="admin_notes" class="form-control" rows="5" placeholder="Pricing discussion, follow-up reminders…"><?= e($enquiry['admin_notes'] ?? '') ?></textarea>
            </div>
            <button class="btn btn-primary w-100" type="submit">Save changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
