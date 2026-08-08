<?php
/** @var string|null $error */
$error = $error ?? flash('error');
$showSeedHints = defined('APP_DEBUG') && APP_DEBUG;
?>
<div class="vc-login">
  <aside class="vc-login-brand" aria-hidden="false">
    <div class="vc-login-brand-bg" aria-hidden="true"></div>
    <div class="vc-login-brand-content">
      <img src="<?= e(asset('img/logo-on-light.png')) ?>" alt="VeggiiCart" class="vc-login-brand-logo">
      <p class="vc-login-brand-tag">Your reliable B2B produce partner</p>
      <ul class="vc-login-brand-points">
        <li><i class="bi bi-box-seam"></i> Products &amp; stock control</li>
        <li><i class="bi bi-truck"></i> Orders &amp; delivery ops</li>
        <li><i class="bi bi-graph-up-arrow"></i> Live wholesale analytics</li>
      </ul>
    </div>
    <p class="vc-login-brand-foot">&copy; <?= date('Y') ?> VeggiiCart</p>
  </aside>

  <main class="vc-login-panel">
    <div class="vc-login-panel-inner">
      <div class="vc-login-mobile-brand d-lg-none">
        <img src="<?= e(asset('img/logo-on-light.png')) ?>" alt="VeggiiCart" class="vc-login-mobile-logo">
      </div>

      <div class="vc-login-card">
        <div class="vc-login-card-head">
          <h1>Admin sign in</h1>
          <p>Wholesale operations console</p>
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
            Sign in <i class="bi bi-arrow-right ms-1"></i>
          </button>
        </form>

        <?php if ($showSeedHints): ?>
          <details class="vc-login-seed">
            <summary>Demo accounts (debug)</summary>
            <div class="vc-login-seed-body">
              <div><strong>Super Admin</strong><br><?= e(SEED_ADMIN_EMAIL) ?> / <?= e(SEED_ADMIN_PASSWORD) ?></div>
              <div class="mt-2"><strong>TEST Delivery Mgr</strong><br>delivery@veggiicart.com / Delivery@123</div>
            </div>
          </details>
        <?php endif; ?>
      </div>
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
