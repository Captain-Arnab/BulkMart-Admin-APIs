<?php
// Better icon mark from light transparent logo (carts only)
$src = dirname(__DIR__) . '/public/assets/img/logo-on-light.png';
$dst = dirname(__DIR__) . '/public/assets/img/logo-mark.png';
$im = imagecreatefrompng($src);
imagesavealpha($im, true);
$w = imagesx($im);
$h = imagesy($im);

// Carts occupy roughly top-center band
$cropX = (int) round($w * 0.28);
$cropY = (int) round($h * 0.08);
$cropW = (int) round($w * 0.44);
$cropH = (int) round($h * 0.42);

$size = 160;
$mark = imagecreatetruecolor($size, $size);
imagealphablending($mark, false);
imagesavealpha($mark, true);
$t = imagecolorallocatealpha($mark, 0, 0, 0, 127);
imagefilledrectangle($mark, 0, 0, $size, $size, $t);
imagealphablending($mark, true);
imagecopyresampled($mark, $im, 10, 10, $cropX, $cropY, $size - 20, $size - 20, $cropW, $cropH);
imagealphablending($mark, false);
imagepng($mark, $dst);
echo "Wrote logo-mark.png\n";
