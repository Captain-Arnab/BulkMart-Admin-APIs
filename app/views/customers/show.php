<?php
$success = $success ?? null; $error = $error ?? null;
?>
<div class="pagetitle">
  <h1><?= e($customer['business_name']) ?></h1>
  <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('customers')) ?>">Customers</a></li><li class="breadcrumb-item active">Detail</li></ol></nav>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card"><div class="card-body">
        <h5 class="card-title">Profile</h5>
        <div class="row g-2">
          <div class="col-md-6"><div class="small text-muted">Owner</div><div><?= e($customer['owner_name']) ?></div></div>
          <div class="col-md-6"><div class="small text-muted">Mobile</div><div><?= e($customer['mobile']) ?></div></div>
          <div class="col-md-6"><div class="small text-muted">Email</div><div><?= e($customer['email'] ?: '—') ?></div></div>
          <div class="col-md-6"><div class="small text-muted">Business type</div><div><?= e($customer['business_type']) ?></div></div>
          <div class="col-md-4"><div class="small text-muted">GST</div><div><?= e($customer['gst_number'] ?: '—') ?></div></div>
          <div class="col-md-4"><div class="small text-muted">FSSAI</div><div><?= e($customer['fssai_number'] ?: '—') ?></div></div>
          <div class="col-md-4"><div class="small text-muted">PAN</div><div><?= e($customer['pan_number'] ?: '—') ?></div></div>
          <div class="col-md-6"><div class="small text-muted">KYC</div>
            <span class="badge <?= e(Customer::KYC_BADGE[$customer['kyc_status']] ?? 'bg-secondary') ?>"><?= e(ucfirst($customer['kyc_status'])) ?></span>
            <?php if ($customer['kyc_status']==='rejected' && $customer['kyc_rejection_reason']): ?>
              <div class="small text-danger mt-1"><?= e($customer['kyc_rejection_reason']) ?></div>
            <?php endif; ?>
          </div>
          <div class="col-md-6"><div class="small text-muted">Blocked</div><div><?= !empty($customer['is_blocked']) ? 'Yes' : 'No' ?></div></div>
        </div>
      </div></div>

      <div class="card mt-3"><div class="card-body">
        <h5 class="card-title">Documents</h5>
        <?php if (!$documents): ?><p class="text-muted mb-0">No documents uploaded.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($documents as $doc): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span><?= e(Customer::DOC_LABELS[$doc['document_type']] ?? $doc['document_type']) ?>
                  <span class="small text-muted">· <?= e(date('d M Y', strtotime($doc['uploaded_at']))) ?></span>
                </span>
                <a href="<?= e(media($doc['file_url'])) ?>" target="_blank" rel="noopener">View</a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div></div>

      <div class="card mt-3"><div class="card-body">
        <h5 class="card-title">Addresses</h5>
        <?php if (!$addresses): ?><p class="text-muted mb-0">No addresses.</p>
        <?php else: foreach ($addresses as $a): ?>
          <div class="mb-2 pb-2 border-bottom">
            <strong><?= e($a['label']) ?></strong><?php if ($a['is_default']): ?> <span class="badge bg-primary">Default</span><?php endif; ?><br>
            <?= e($a['line1']) ?><?php if ($a['line2']): ?>, <?= e($a['line2']) ?><?php endif; ?><br>
            <?= e($a['city']) ?>, <?= e($a['state']) ?> — <?= e($a['pincode']) ?>
          </div>
        <?php endforeach; endif; ?>
      </div></div>

      <div class="card mt-3"><div class="card-body">
        <h5 class="card-title">Order history</h5>
        <?php if (!$orders): ?><p class="text-muted mb-0">No orders yet.</p>
        <?php else: ?>
          <table class="table table-sm"><thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th></tr></thead><tbody>
          <?php foreach ($orders as $o): $b=Order::badge($o['status']); ?>
            <tr>
              <td><a href="<?= e(url('orders/'.$o['id'])) ?>"><?= e($o['order_number']) ?></a></td>
              <td><?= e(date('d M Y', strtotime($o['placed_at']))) ?></td>
              <td>₹<?= e(number_format((float)$o['total'],2)) ?></td>
              <td><span class="<?= e($b['class']) ?>"><i class="bi <?= e($b['icon']) ?>"></i> <?= e($b['label']) ?></span></td>
            </tr>
          <?php endforeach; ?>
          </tbody></table>
        <?php endif; ?>
      </div></div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-3"><div class="card-body">
        <h5 class="card-title">KYC actions</h5>
        <form method="POST" action="<?= e(url('customers/'.$customer['id'].'/approve')) ?>" class="mb-2">
          <button class="btn btn-success w-100" type="submit" <?= $customer['kyc_status']==='approved'?'disabled':'' ?>>Approve</button>
        </form>
        <form method="POST" action="<?= e(url('customers/'.$customer['id'].'/reject')) ?>">
          <label class="form-label">Reject reason</label>
          <textarea name="kyc_rejection_reason" class="form-control mb-2" rows="3" required placeholder="Required if rejecting"></textarea>
          <button class="btn btn-danger w-100" type="submit">Reject</button>
        </form>
      </div></div>
      <div class="card"><div class="card-body">
        <h5 class="card-title">Access</h5>
        <form method="POST" action="<?= e(url('customers/'.$customer['id'].'/toggle-block')) ?>">
          <button class="btn btn-outline-dark w-100" type="submit">
            <?= !empty($customer['is_blocked']) ? 'Unblock customer' : 'Block customer' ?>
          </button>
        </form>
      </div></div>
    </div>
  </div>
</section>
