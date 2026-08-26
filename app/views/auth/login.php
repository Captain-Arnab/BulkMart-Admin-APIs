<?php
/** @var string|null $error */
$error = $error ?? flash('error');
$showSeedHints = defined('APP_DEBUG') && APP_DEBUG;
?>
<div class="vc-login">
  <aside class="vc-login-brand">
    <div class="vc-login-brand-bg" aria-hidden="true">
      <span class="vc-login-orb vc-login-orb--1"></span>
      <span class="vc-login-orb vc-login-orb--2"></span>
      <span class="vc-login-orb vc-login-orb--3"></span>
      <svg class="vc-login-leaf vc-login-leaf--a" viewBox="0 0 120 160" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M60 8C28 40 18 78 22 118c22-18 40-28 58-32-6-28-8-52-20-78z" fill="currentColor" opacity=".18"/>
        <path d="M60 8c12 26 18 50 20 78 8 2 20 10 34 28C110 74 96 36 60 8z" fill="currentColor" opacity=".12"/>
        <path d="M60 20v108" stroke="currentColor" stroke-width="3" stroke-linecap="round" opacity=".28"/>
      </svg>
      <svg class="vc-login-leaf vc-login-leaf--b" viewBox="0 0 120 160" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M60 8C28 40 18 78 22 118c22-18 40-28 58-32-6-28-8-52-20-78z" fill="currentColor" opacity=".14"/>
        <path d="M60 20v100" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity=".22"/>
      </svg>
    </div>

    <div class="vc-login-brand-top">
      <img src="<?= e(admin_logo_src()) ?>" alt="VeggiiCart" class="vc-login-brand-logo">
    </div>

    <div class="vc-login-brand-content">
      <p class="vc-login-brand-eyebrow">Wholesale admin</p>
      <h2 class="vc-login-brand-tag">Your reliable B2B produce partner</h2>
      <p class="vc-login-brand-lead">Run stock, orders, and delivery from one calm console built for fresh-produce ops.</p>
      <ul class="vc-login-brand-points">
        <li style="--i:0">
          <i class="bi bi-box-seam" aria-hidden="true"></i>
          <span>
            <strong>Products &amp; stock</strong>
            <small>Live inventory for every SKU</small>
          </span>
        </li>
        <li style="--i:1">
          <i class="bi bi-truck" aria-hidden="true"></i>
          <span>
            <strong>Orders &amp; delivery</strong>
            <small>Route-ready wholesale fulfilment</small>
          </span>
        </li>
        <li style="--i:2">
          <i class="bi bi-graph-up-arrow" aria-hidden="true"></i>
          <span>
            <strong>Live analytics</strong>
            <small>Revenue and ops at a glance</small>
          </span>
        </li>
      </ul>
    </div>

    <p class="vc-login-brand-foot">&copy; <?= date('Y') ?> VeggiiCart · Fresh produce, wholesale-first</p>
  </aside>

  <main class="vc-login-panel">
    <div class="vc-login-panel-inner">
      <div class="vc-login-mobile-brand d-lg-none">
        <img src="<?= e(admin_logo_src()) ?>" alt="VeggiiCart" class="vc-login-mobile-logo">
        <p>Wholesale operations</p>
      </div>

      <div class="vc-login-card">
        <div class="vc-login-card-accent" aria-hidden="true"></div>
        <div class="vc-login-card-head">
          <h1>Welcome back</h1>
          <p>Sign in to continue to your admin console</p>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger vc-login-alert" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i><?= e($error) ?>
          </div>
        <?php endif; ?>

        <form class="vc-login-form" method="POST" action="<?= e(url('login')) ?>" autocomplete="on">
          <div class="vc-login-field">
            <label for="identity">Email or username</label>
            <div class="vc-login-input">
              <i class="bi bi-person" aria-hidden="true"></i>
              <input type="text" name="identity" id="identity"
                     value="<?= e($_POST['identity'] ?? ($showSeedHints ? SEED_ADMIN_EMAIL : '')) ?>"
                     placeholder="you@veggiicart.com"
                     required autofocus>
            </div>
          </div>

          <div class="vc-login-field">
            <label for="password">Password</label>
            <div class="vc-login-input">
              <i class="bi bi-lock" aria-hidden="true"></i>
              <input type="password" name="password" id="password" placeholder="Enter your password" required>
              <button type="button" class="vc-login-eye" id="toggle-password" aria-label="Show password" title="Show password">
                <i class="bi bi-eye" aria-hidden="true"></i>
              </button>
            </div>
          </div>

          <button class="btn btn-primary vc-login-submit" type="submit">
            Sign in <i class="bi bi-arrow-right" aria-hidden="true"></i>
          </button>
        </form>

        <!-- <?php if ($showSeedHints): ?>
          <details class="vc-login-seed">
            <summary><i class="bi bi-key me-1" aria-hidden="true"></i> TEST-ONLY accounts (APP_DEBUG)</summary>
            <div class="vc-login-seed-body">
              <p class="small text-danger mb-2"><?= e(TEST_ONLY_ADMIN_NOTE) ?></p>
              <div><strong>Super Admin (TEST)</strong><br><?= e(SEED_ADMIN_EMAIL) ?> / <?= e(SEED_ADMIN_PASSWORD) ?></div>
              <div class="mt-2"><strong>Delivery Mgr (TEST)</strong><br>delivery@veggiicart.com / Delivery@123</div>
              <div class="mt-2"><strong>Sub-Admin (TEST)</strong><br>subadmin@veggiicart.com / SubAdmin@123</div>
            </div>
          </details>
        <?php endif; ?> -->
      </div>

      <p class="vc-login-secure">
        <i class="bi bi-shield-check" aria-hidden="true"></i>
        Secured staff access · VeggiiCart admin
      </p>
    </div>
  </main>
</div>
<script>
(function () {
  var btn = document.getElementById('toggle-password');
  var input = document.getElementById('password');
  if (!btn || !input) return;
  btn.addEventListener('click', function () {
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.innerHTML = show ? '<i class="bi bi-eye-slash" aria-hidden="true"></i>' : '<i class="bi bi-eye" aria-hidden="true"></i>';
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    btn.title = show ? 'Hide password' : 'Show password';
  });
})();
</script>
