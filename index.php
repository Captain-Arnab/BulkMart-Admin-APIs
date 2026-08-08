<?php
/**
 * Project root — bounce into /public for XAMPP convenience.
 * Prefer pointing the vhost/document root at /public in production.
 */
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$target = ($base === '' ? '' : $base) . '/public/';

// Preserve path after project root if someone hits /veggiicart/something
$suffix = '';
if ($base !== '' && str_starts_with($uri, $base)) {
    $rest = substr($uri, strlen($base));
    if ($rest !== '' && $rest !== '/' && !str_starts_with($rest, '/public')) {
        $suffix = ltrim($rest, '/');
    }
}

header('Location: ' . $target . $suffix);
exit;
