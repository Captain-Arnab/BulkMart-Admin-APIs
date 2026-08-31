<?php
/** @var array $customer */
/** @var array $documents */
/** @var array $addresses */
/** @var array $orders */
/** @var array $activity */
$success = $success ?? null;
$error = $error ?? null;
$activity = $activity ?? [];

$owner = trim((string) ($customer['owner_name'] ?? ''));
$business = trim((string) ($customer['business_name'] ?? ''));
$displayTitle = $business !== '' ? $business : ($owner !== '' ? $owner : 'Customer');
$avatarUrl = !empty($customer['avatar_url']) ? media((string) $customer['avatar_url']) : '';
$initialSource = $owner !== '' ? $owner : ($business !== '' ? $business : 'C');
$parts = preg_split('/\s+/', $initialSource) ?: [];
$initials = strtoupper(
    count($parts) >= 2
        ? substr($parts[0], 0, 1) . substr($parts[1], 0, 1)
        : substr($initialSource, 0, 2)
);
$kyc = (string) ($customer['kyc_status'] ?? 'pending');
$kycClass = match ($kyc) {
    'approved' => 'is-approved',
    'rejected' => 'is-rejected',
    default    => 'is-pending',
};
$isBlocked = !empty($customer['is_blocked']);
$joined = !empty($customer['created_at']) ? date('d M Y', strtotime((string) $customer['created_at'])) : '—';
?>
<div class="pagetitle vc-cust-pagetitle d-flex flex-wrap justify-content-between align-items-end gap-2">
  <div>
    <h1><?= e($displayTitle) ?></h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= e(url('customers')) ?>">Customers</a></li>
        <li class="breadcrumb-item active">Detail</li>
      </ol>
    </nav>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="<?= e(url('customers')) ?>" class="btn btn-sm btn-outline-secondary vc-cust-back">
      <i class="bi bi-arrow-left me-1"></i>All customers
    </a>
    <a href="<?= e(url('customers/' . $customer['id'] . '/edit')) ?>" class="btn btn-sm btn-primary">
      <i class="bi bi-pencil me-1"></i>Edit profile
    </a>
  </div>
</div>

<?php if ($success): ?><div class="alert alert-success vc-fade-in"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger vc-fade-in"><?= e($error) ?></div><?php endif; ?>

<section class="section vc-cust-detail">
  <div class="vc-cust-hero vc-fade-up">
    <div class="vc-cust-hero-cover" aria-hidden="true">
      <span class="vc-cust-hero-orb vc-cust-hero-orb--a"></span>
      <span class="vc-cust-hero-orb vc-cust-hero-orb--b"></span>
    </div>

    <div class="vc-cust-hero-body">
      <div class="vc-cust-avatar-wrap">
        <?php if ($avatarUrl !== ''): ?>
          <img class="vc-cust-avatar" src="<?= e($avatarUrl) ?>" alt="<?= e($displayTitle) ?>">
        <?php else: ?>
          <div class="vc-cust-avatar vc-cust-avatar--initials" aria-hidden="true"><?= e($initials) ?></div>
        <?php endif; ?>
      </div>

      <div class="vc-cust-hero-meta">
        <div class="vc-cust-hero-tags">
          <span class="vc-cust-chip vc-cust-chip--kyc <?= e($kycClass) ?>">
            <i class="bi bi-shield-check"></i>
            KYC <?= e(ucfirst($kyc)) ?>
          </span>
          <?php if ($isBlocked): ?>
            <span class="vc-cust-chip vc-cust-chip--blocked"><i class="bi bi-slash-circle"></i> Blocked</span>
          <?php else: ?>
            <span class="vc-cust-chip vc-cust-chip--ok"><i class="bi bi-check2-circle"></i> Active</span>
          <?php endif; ?>
          <?php if (!empty($customer['business_type'])): ?>
            <span class="vc-cust-chip"><i class="bi bi-shop"></i> <?= e(ucfirst((string) $customer['business_type'])) ?></span>
          <?php endif; ?>
        </div>
        <h2 class="vc-cust-hero-name"><?= e($displayTitle) ?></h2>
        <?php if ($owner !== '' && strcasecmp($owner, $displayTitle) !== 0): ?>
          <p class="vc-cust-hero-sub">Owner · <?= e($owner) ?></p>
        <?php endif; ?>
        <div class="vc-cust-hero-contacts">
          <a href="tel:<?= e((string) $customer['mobile']) ?>"><i class="bi bi-telephone"></i> <?= e($customer['mobile']) ?></a>
          <?php if (!empty($customer['email'])): ?>
            <a href="mailto:<?= e((string) $customer['email']) ?>"><i class="bi bi-envelope"></i> <?= e($customer['email']) ?></a>
          <?php endif; ?>
          <span><i class="bi bi-calendar3"></i> Joined <?= e($joined) ?></span>
        </div>
      </div>
    </div>

    <div class="vc-cust-hero-stats">
      <div class="vc-cust-stat">
        <strong><?= count($orders) ?></strong>
        <span>Orders</span>
      </div>
      <div class="vc-cust-stat">
        <strong><?= count($addresses) ?></strong>
        <span>Addresses</span>
      </div>
      <div class="vc-cust-stat">
        <strong><?= count($documents) ?></strong>
        <span>Docs</span>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-2 align-items-start">
    <div class="col-lg-8 d-flex flex-column gap-3">
      <div class="vc-cust-card vc-fade-up" style="--delay:60ms">
        <div class="vc-cust-card-head">
          <h3><i class="bi bi-person-vcard"></i> Profile details</h3>
        </div>
        <div class="vc-cust-grid">
          <div class="vc-cust-field">
            <span>Owner</span>
            <strong><?= e($owner !== '' ? $owner : '—') ?></strong>
          </div>
          <div class="vc-cust-field">
            <span>Business</span>
            <strong><?= e($business !== '' ? $business : '—') ?></strong>
          </div>
          <div class="vc-cust-field">
            <span>Mobile</span>
            <strong><?= e($customer['mobile'] ?: '—') ?></strong>
          </div>
          <div class="vc-cust-field">
            <span>Email</span>
            <strong><?= e($customer['email'] ?: '—') ?></strong>
          </div>
          <div class="vc-cust-field">
            <span>Business type</span>
            <strong><?= e($customer['business_type'] ? ucfirst((string) $customer['business_type']) : '—') ?></strong>
          </div>
          <div class="vc-cust-field">
            <span>GST</span>
            <strong><?= e($customer['gst_number'] ?: '—') ?></strong>
          </div>
          <div class="vc-cust-field">
            <span>FSSAI</span>
            <strong><?= e($customer['fssai_number'] ?: '—') ?></strong>
          </div>
          <div class="vc-cust-field">
            <span>PAN</span>
            <strong><?= e($customer['pan_number'] ?: '—') ?></strong>
          </div>
        </div>
        <?php if ($kyc === 'rejected' && !empty($customer['kyc_rejection_reason'])): ?>
          <div class="vc-cust-reject-note">
            <i class="bi bi-exclamation-triangle"></i>
            <div>
              <strong>Rejection reason</strong>
              <p><?= e($customer['kyc_rejection_reason']) ?></p>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="vc-cust-card vc-fade-up" style="--delay:120ms">
        <div class="vc-cust-card-head">
          <h3><i class="bi bi-files"></i> Documents</h3>
          <span class="vc-cust-count"><?= count($documents) ?></span>
          <a href="<?= e(url('customers/' . $customer['id'] . '/edit')) ?>#documents" class="btn btn-sm btn-outline-primary ms-auto">Manage</a>
        </div>
        <?php if (!$documents): ?>
          <div class="vc-cust-empty">
            <i class="bi bi-folder2-open"></i>
            <p>No documents uploaded yet.</p>
          </div>
        <?php else: ?>
          <div class="vc-cust-doc-list">
            <?php foreach ($documents as $doc): ?>
              <a class="vc-cust-doc" href="<?= e(media($doc['file_url'])) ?>" target="_blank" rel="noopener">
                <span class="vc-cust-doc-icon"><i class="bi bi-file-earmark-text"></i></span>
                <span class="vc-cust-doc-body">
                  <strong><?= e(Customer::DOC_LABELS[$doc['document_type']] ?? $doc['document_type']) ?></strong>
                  <small><?= e(date('d M Y', strtotime($doc['uploaded_at']))) ?></small>
                </span>
                <i class="bi bi-box-arrow-up-right"></i>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="vc-cust-card vc-fade-up" style="--delay:180ms">
        <div class="vc-cust-card-head">
          <h3><i class="bi bi-geo-alt"></i> Addresses</h3>
          <span class="vc-cust-count"><?= count($addresses) ?></span>
        </div>
        <?php if (!$addresses): ?>
          <div class="vc-cust-empty">
            <i class="bi bi-geo"></i>
            <p>No addresses saved.</p>
          </div>
        <?php else: ?>
          <div class="vc-cust-addr-grid">
            <?php foreach ($addresses as $a): ?>
              <article class="vc-cust-addr<?= !empty($a['is_default']) ? ' is-default' : '' ?>">
                <div class="vc-cust-addr-top">
                  <strong><?= e($a['label'] ?: 'Address') ?></strong>
                  <?php if (!empty($a['is_default'])): ?>
                    <span class="badge">Default</span>
                  <?php endif; ?>
                </div>
                <p>
                  <?= e($a['line1']) ?><?php if (!empty($a['line2'])): ?>, <?= e($a['line2']) ?><?php endif; ?><br>
                  <?= e($a['city']) ?>, <?= e($a['state']) ?> — <?= e($a['pincode']) ?>
                </p>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="vc-cust-card vc-fade-up" style="--delay:240ms">
        <div class="vc-cust-card-head">
          <h3><i class="bi bi-bag-check"></i> Order history</h3>
          <span class="vc-cust-count"><?= count($orders) ?></span>
        </div>
        <?php if (!$orders): ?>
          <div class="vc-cust-empty">
            <i class="bi bi-cart3"></i>
            <p>No orders yet.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table vc-cust-orders align-middle mb-0">
              <thead>
                <tr>
                  <th>Order</th>
                  <th>Date</th>
                  <th>Total</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $o): $b = Order::badge($o['status']); ?>
                  <tr>
                    <td><a class="vc-cust-order-link" href="<?= e(url('orders/' . $o['id'])) ?>"><?= e($o['order_number']) ?></a></td>
                    <td><?= e(date('d M Y', strtotime($o['placed_at']))) ?></td>
                    <td>₹<?= e(number_format((float) $o['total'], 2)) ?></td>
                    <td><span class="<?= e($b['class']) ?>"><i class="bi <?= e($b['icon']) ?>"></i> <?= e($b['label']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-3">
      <div class="vc-cust-card vc-cust-actions vc-fade-up" style="--delay:100ms">
        <div class="vc-cust-card-head">
          <h3><i class="bi bi-shield-lock"></i> KYC actions</h3>
        </div>
        <form method="POST" action="<?= e(url('customers/' . $customer['id'] . '/approve')) ?>" class="mb-3">
          <button class="btn btn-success w-100 vc-cust-btn-approve" type="submit" <?= $kyc === 'approved' ? 'disabled' : '' ?>>
            <i class="bi bi-check-lg me-1"></i>
            <?= $kyc === 'approved' ? 'Already approved' : 'Approve KYC' ?>
          </button>
        </form>
        <form method="POST" action="<?= e(url('customers/' . $customer['id'] . '/reject')) ?>">
          <label class="form-label">Reject reason</label>
          <textarea name="kyc_rejection_reason" class="form-control mb-2" rows="3" required placeholder="Required if rejecting"></textarea>
          <button class="btn btn-outline-danger w-100" type="submit">
            <i class="bi bi-x-lg me-1"></i>Reject KYC
          </button>
        </form>
      </div>

      <div class="vc-cust-card vc-fade-up" style="--delay:130ms">
        <div class="vc-cust-card-head">
          <h3><i class="bi bi-key"></i> Reset password</h3>
        </div>
        <?php
          $hasPassword = !empty($customer['password_hash']);
          $activity = $activity ?? [];
        ?>
        <p class="vc-cust-access-note mb-3">
          <?php if ($hasPassword): ?>
            This customer already has a login password. Use this form to set a new one (e.g. forgot-password support calls).
          <?php else: ?>
            No password set yet — Email &amp; Password login will not work until one is set here or by the customer.
          <?php endif; ?>
        </p>
        <form
          method="POST"
          action="<?= e(url('customers/' . $customer['id'] . '/reset-password')) ?>"
          autocomplete="off"
          data-vc-confirm="Reset this customer's password? They will be notified."
          data-vc-confirm-title="Reset password"
          data-vc-confirm-yes="Yes, reset">
          <div class="mb-2">
            <label class="form-label" for="vcAdminNewPassword">New password</label>
            <input
              type="password"
              class="form-control"
              id="vcAdminNewPassword"
              name="password"
              minlength="6"
              required
              placeholder="Min. 6 characters"
              autocomplete="new-password">
          </div>
          <div class="mb-3">
            <label class="form-label" for="vcAdminConfirmPassword">Confirm password</label>
            <input
              type="password"
              class="form-control"
              id="vcAdminConfirmPassword"
              name="password_confirmation"
              minlength="6"
              required
              placeholder="Re-enter password"
              autocomplete="new-password">
          </div>
          <button class="btn btn-outline-primary w-100" type="submit">
            <i class="bi bi-arrow-repeat me-1"></i>Reset password
          </button>
        </form>
        <?php
          $passwordLogs = array_values(array_filter(
              $activity,
              static fn ($row) => ($row['action'] ?? '') === 'customer_password_reset'
          ));
        ?>
        <?php if ($passwordLogs): ?>
          <hr class="my-3">
          <p class="small text-muted mb-2">Recent password resets</p>
          <ul class="list-unstyled small mb-0 vc-cust-password-log">
            <?php foreach (array_slice($passwordLogs, 0, 5) as $log): ?>
              <li class="mb-1">
                <i class="bi bi-clock-history me-1"></i>
                <?= e($log['note'] ?: ('By ' . ($log['admin_name'] ?? 'Admin'))) ?>
                <?php if (!empty($log['created_at'])): ?>
                  <span class="text-muted">(<?= e(date('d M Y H:i', strtotime((string) $log['created_at']))) ?>)</span>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="vc-cust-card vc-fade-up" style="--delay:160ms">
        <div class="vc-cust-card-head">
          <h3><i class="bi bi-sliders"></i> Access</h3>
        </div>
        <p class="vc-cust-access-note">
          <?php if ($isBlocked): ?>
            This customer is blocked and cannot place orders.
          <?php elseif (($customer['kyc_status'] ?? '') === 'approved'): ?>
            This customer's KYC is approved and they can place orders.
          <?php else: ?>
            This customer's KYC is <?= e($customer['kyc_status'] ?? 'pending') ?> — approve KYC above before they can place orders.
          <?php endif; ?>
        </p>
        <form method="POST" action="<?= e(url('customers/' . $customer['id'] . '/toggle-block')) ?>">
          <button class="btn <?= $isBlocked ? 'btn-success' : 'btn-outline-dark' ?> w-100" type="submit">
            <i class="bi bi-<?= $isBlocked ? 'unlock' : 'lock' ?> me-1"></i>
            <?= $isBlocked ? 'Unblock customer' : 'Block customer' ?>
          </button>
        </form>
      </div>
    </div>
  </div>
</section>
