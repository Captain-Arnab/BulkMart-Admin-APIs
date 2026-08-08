<?php
/** @var string|null $error */
$error = $error ?? flash('error');
?>
<main>
  <div class="container">
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

            <div class="d-flex justify-content-center py-4">
              <a href="<?= e(url('login')) ?>" class="logo d-flex align-items-center w-auto flex-column text-center">
                <img src="<?= e(asset('img/veggiicart_white_background.jpeg')) ?>" alt="VeggiiCart" class="login-logo">
              </a>
            </div>

            <div class="card mb-3 vc-login-card">
              <div class="card-body">
                <div class="pt-4 pb-2">
                  <h5 class="card-title text-center pb-0 fs-4">Admin Sign In</h5>
                  <p class="text-center small">Wholesale operations console</p>
                </div>

                <?php if ($error): ?>
                  <div class="alert alert-danger py-2" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <form class="row g-3" method="POST" action="<?= e(url('login')) ?>" autocomplete="on">
                  <div class="col-12">
                    <label for="identity" class="form-label">Email or username</label>
                    <div class="input-group has-validation">
                      <span class="input-group-text"><i class="bi bi-person"></i></span>
                      <input type="text" name="identity" class="form-control" id="identity"
                             value="<?= e($_POST['identity'] ?? 'admin@veggiicart.com') ?>"
                             required autofocus>
                    </div>
                  </div>

                  <div class="col-12">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                  </div>

                  <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit">Login</button>
                  </div>
                </form>

                <div class="mt-3 small text-muted vc-temp-creds">
                  <strong>TEMP seed login</strong> (change before go-live):<br>
                  <?= e(SEED_ADMIN_EMAIL) ?> / <?= e(SEED_ADMIN_PASSWORD) ?>
                </div>
              </div>
            </div>

            <div class="credits">
              VeggiiCart B2B Admin · UI by <a href="https://bootstrapmade.com/">BootstrapMade</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>
