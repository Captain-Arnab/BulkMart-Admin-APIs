<?php
/**
 * Convert black-background logo JPEG → transparent PNG.
 */
$src = dirname(__DIR__) . '/public/assets/img/veggiicart_no_background.jpeg';
$dst = dirname(__DIR__) . '/public/assets/img/logo-transparent.png';
$icon = dirname(__DIR__) . '/public/assets/img/logo-mark.png';

if (!is_file($src)) {
    fwrite(STDERR, "Source missing: $src\n");
    exit(1);
}

$im = imagecreatefromjpeg($src);
$w = imagesx($im);
$h = imagesy($im);
echo "Source {$w}x{$h}\n";

// Sample corners to detect keying color
$samples = [
    imagecolorat($im, 2, 2),
    imagecolorat($im, $w - 3, 2),
    imagecolorat($im, 2, $h - 3),
    imagecolorat($im, $w - 3, $h - 3),
];
$avgR = $avgG = $avgB = 0;
foreach ($samples as $c) {
    $avgR += ($c >> 16) & 0xFF;
    $avgG += ($c >> 8) & 0xFF;
    $avgB += $c & 0xFF;
}
$avgR = (int) round($avgR / 4);
$avgG = (int) round($avgG / 4);
$avgB = (int) round($avgB / 4);
echo "Key color approx RGB($avgR,$avgG,$avgB)\n";

$out = imagecreatetruecolor($w, $h);
imagealphablending($out, false);
imagesavealpha($out, true);
$transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefilledrectangle($out, 0, 0, $w, $h, $transparent);

$threshold = 45; // distance from key color
for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $c = imagecolorat($im, $x, $y);
        $r = ($c >> 16) & 0xFF;
        $g = ($c >> 8) & 0xFF;
        $b = $c & 0xFF;
        $dist = abs($r - $avgR) + abs($g - $avgG) + abs($b - $avgB);
        if ($dist <= $threshold && $r < 60 && $g < 60 && $b < 60) {
            imagesetpixel($out, $x, $y, $transparent);
        } else {
            $col = imagecolorallocatealpha($out, $r, $g, $b, 0);
            imagesetpixel($out, $x, $y, $col);
        }
    }
}

imagepng($out, $dst);
echo "Wrote $dst\n";

// Crop top icon region (roughly top 55% centered) for collapsed mark
$cropH = (int) round($h * 0.55);
$cropW = (int) round($w * 0.7);
$cropX = (int) round(($w - $cropW) / 2);
$cropY = (int) round($h * 0.02);
$mark = imagecreatetruecolor(128, 128);
imagealphablending($mark, false);
imagesavealpha($mark, true);
$t2 = imagecolorallocatealpha($mark, 0, 0, 0, 127);
imagefilledrectangle($mark, 0, 0, 128, 128, $t2);
imagealphablending($mark, true);
imagecopyresampled($mark, $out, 8, 8, $cropX, $cropY, 112, 112, $cropW, $cropH);
imagealphablending($mark, false);
imagepng($mark, $icon);
echo "Wrote $icon\n";

imagedestroy($im);
imagedestroy($out);
imagedestroy($mark);
