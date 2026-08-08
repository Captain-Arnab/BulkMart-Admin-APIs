<?php $success=$success??null; $error=$error??null; ?>
<div class="pagetitle"><h1>Ticket #<?= (int)$ticket['id'] ?></h1>
<nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('support')) ?>">Support</a></li><li class="breadcrumb-item active">#<?= (int)$ticket['id'] ?></li></ol></nav></div>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<section class="section"><div class="row g-3">
  <div class="col-lg-8">
    <div class="card"><div class="card-body">
      <h5 class="card-title"><?= e($ticket['subject_type']) ?></h5>
      <p><?= nl2br(e($ticket['description'])) ?></p>
      <div class="small text-muted">From <?= e($ticket['business_name']) ?> (<?= e($ticket['mobile']) ?>)
        <?php if ($ticket['related_order_id']): ?> · Order <a href="<?= e(url('orders/'.$ticket['related_order_id'])) ?>"><?= e($ticket['order_number']) ?></a><?php endif; ?>
      </div>
    </div></div>
    <div class="card mt-3"><div class="card-body">
      <h5 class="card-title">Replies</h5>
      <?php if (!$replies): ?><p class="text-muted">No replies yet.</p><?php endif; ?>
      <?php foreach ($replies as $r): ?>
        <div class="border rounded p-2 mb-2">
          <div class="small text-muted"><?= e($r['admin_name'] ?: 'Admin') ?> · <?= e(date('d M Y H:i', strtotime($r['created_at']))) ?></div>
          <div><?= nl2br(e($r['message'])) ?></div>
        </div>
      <?php endforeach; ?>
      <form method="POST" action="<?= e(url('support/'.$ticket['id'].'/reply')) ?>" class="mt-3">
        <textarea name="message" class="form-control mb-2" rows="3" required placeholder="Write a reply…"></textarea>
        <button class="btn btn-primary" type="submit">Send reply</button>
      </form>
    </div></div>
  </div>
  <div class="col-lg-4"><div class="card"><div class="card-body">
    <h5 class="card-title">Status</h5>
    <form method="POST" action="<?= e(url('support/'.$ticket['id'].'/status')) ?>">
      <select name="status" class="form-select mb-2">
        <?php foreach (['open','in_progress','closed'] as $s): ?>
          <option value="<?= $s ?>" <?= $ticket['status']===$s?'selected':'' ?>><?= e(str_replace('_',' ',$s)) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-outline-primary w-100" type="submit">Update status</button>
    </form>
  </div></div></div>
</div></section>
