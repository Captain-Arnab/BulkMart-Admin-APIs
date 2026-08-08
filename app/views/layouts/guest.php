<?php
/** @var string $content */
/** @var string $title */
$pageTitle = ($title ?? 'Login') . ' · ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= e($pageTitle) ?></title>
  <link href="<?= e(asset('img/veggiicart_no_background.jpeg')) ?>" rel="icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="<?= e(asset('vendor/bootstrap/css/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('vendor/bootstrap-icons/bootstrap-icons.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('css/style.css')) ?>" rel="stylesheet">
  <link href="<?= e(asset('css/veggiicart-theme.css')) ?>" rel="stylesheet">
</head>
<body class="vc-login-body">
  <?= $content ?>
  <script src="<?= e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>
  <script src="<?= e(asset('js/main.js')) ?>"></script>
</body>
</html>
