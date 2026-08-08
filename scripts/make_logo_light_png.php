<?php
$src = dirname(__DIR__) . '/public/assets/img/veggiicart_white_background.jpeg';
$dst = dirname(__DIR__) . '/public/assets/img/logo-on-light.png';
$im = imagecreatefromjpeg($src);
$w = imagesx($im);
$h = imagesy($im);
$out = imagecreatetruecolor($w, $h);
imagealphablending($out, false);
imagesavealpha($out, true);
$transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefilledrectangle($out, 0, 0, $w, $h, $transparent);
for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $c = imagecolorat($im, $x, $y);
        $r = ($c >> 16) & 0xFF;
        $g = ($c >> 8) & 0xFF;
        $b = $c & 0xFF;
        if ($r > 245 && $g > 245 && $b > 245) {
            imagesetpixel($out, $x, $y, $transparent);
        } else {
            $a = 0;
            if ($r > 230 && $g > 230 && $b > 230) {
                $a = (int) (((($r + $g + $b) / 3) - 230) / 25 * 127);
                if ($a > 127) {
                    $a = 127;
                }
            }
            $col = imagecolorallocatealpha($out, $r, $g, $b, $a);
            imagesetpixel($out, $x, $y, $col);
        }
    }
}
imagepng($out, $dst);
echo "Wrote $dst {$w}x{$h}\n";
