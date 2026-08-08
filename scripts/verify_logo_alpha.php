<?php
foreach (['logo-transparent.png', 'logo-on-light.png', 'logo-mark.png'] as $f) {
    $p = dirname(__DIR__) . '/public/assets/img/' . $f;
    if (!file_exists($p)) {
        echo "$f missing\n";
        continue;
    }
    $i = imagecreatefrompng($p);
    imagesavealpha($i, true);
    $w = imagesx($i);
    $h = imagesy($i);
    $c = imagecolorat($i, 5, 5);
    $a = ($c & 0x7F000000) >> 24;
    $r = ($c >> 16) & 0xFF;
    $g = ($c >> 8) & 0xFF;
    $b = $c & 0xFF;
    echo "$f {$w}x{$h} corner rgba($r,$g,$b,a=$a)\n";
}
