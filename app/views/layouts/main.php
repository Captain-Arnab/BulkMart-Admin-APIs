<?php
/** @var string $content */
/** @var string $title */
$user = auth_user();
$current = current_path();
$navItems = rbac_sidebar_items($user);
$pageTitle = ($title ?? 'Admin') . ' · ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="VeggiiCart Admin Panel">

  <link href="<?= e(asset('img/logo-mark.png')) ?>" rel="icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="<?= e(asset('vendor/bootstrap/css/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('vendor/bootstrap-icons/bootstrap-icons.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('vendor/boxicons/css/boxicons.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('vendor/quill/quill.snow.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('vendor/quill/quill.bubble.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('vendor/remixicon/remixicon.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('vendor/simple-datatables/style.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('css/style.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('css/veggiicart-theme.css')) ?>" rel="stylesheet">
</head>
<body>

<?php require VIEW_PATH . '/layouts/partials/header.php'; ?>
<?php require VIEW_PATH . '/layouts/partials/sidebar.php'; ?>

<main id="main" class="main">
  <?= $content ?>
</main>

<footer id="footer" class="footer">
  <div class="copyright">
    &copy; <?= date('Y') ?> <strong><span>VeggiiCart</span></strong>. All Rights Reserved
  </div>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script src="<?= e(asset('vendor/apexcharts/apexcharts.min.js')) ?>"></script>
<script src="<?= e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(asset('vendor/chart.js/chart.umd.js')) ?>"></script>
<script src="<?= e(asset('vendor/echarts/echarts.min.js')) ?>"></script>
<script src="<?= e(asset('vendor/quill/quill.js')) ?>"></script>
<script src="<?= e(asset('vendor/simple-datatables/simple-datatables.js')) ?>"></script>
<script src="<?= e(asset('vendor/tinymce/tinymce.min.js')) ?>"></script>
<script src="<?= e(asset('js/main.js')) ?>"></script>
<script src="<?= e(asset('js/vc-header-search.js')) ?>"></script>
<script src="<?= e(asset('js/vc-char-count.js')) ?>"></script>
<?php if (!empty($pageScripts) && is_array($pageScripts)): ?>
  <?php foreach ($pageScripts as $src): ?>
    <script src="<?= e($src) ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
