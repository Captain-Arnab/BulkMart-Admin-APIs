<?php $success=$success??null; $error=$error??null; ?>
<div class="pagetitle"><h1>Support Tickets</h1>
<nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('dashboard')) ?>">Home</a></li><li class="breadcrumb-item active">Support</li></ol></nav></div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section">
  <div class="card mb-3"><div class="card-body py-3">
    <form class="row g-2 align-items-end" method="GET" action="<?= e(url('support')) ?>">
      <div class="col-md-3"><label class="form-label mb-1">Status</label>
        <select name="status" class="form-select">
          <option value="">All</option>
          <?php foreach (['open','in_progress','closed'] as $s): ?>
            <option value="<?= $s ?>" <?= ($filters['status']??'')===$s?'selected':'' ?>><?= e(str_replace('_',' ',$s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3"><button class="btn btn-primary" type="submit">Filter</button></div>
    </form>
  </div></div>
  <div class="card"><div class="card-body pt-3">
    <table class="table table-hover align-middle">
      <thead><tr><th>ID</th><th>Customer</th><th>Subject</th><th>Status</th><th>Order</th><th>Created</th><th></th></tr></thead>
      <tbody>
      <?php if (!$result['rows']): ?><tr><td colspan="7" class="text-muted text-center py-4">No tickets.</td></tr><?php endif; ?>
      <?php foreach ($result['rows'] as $t): ?>
        <tr>
          <td>#<?= (int)$t['id'] ?></td>
          <td><?= e($t['business_name']) ?></td>
          <td><?= e($t['subject_type']) ?></td>
          <td><span class="badge <?= e(SupportTicket::STATUS_BADGE[$t['status']] ?? 'bg-secondary') ?>"><?= e(str_replace('_',' ',$t['status'])) ?></span></td>
          <td><?= $t['order_number'] ? e($t['order_number']) : '—' ?></td>
          <td class="small"><?= e(date('d M Y', strtotime($t['created_at']))) ?></td>
          <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= e(url('support/'.$t['id'])) ?>">Open</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div></div>
</section>
