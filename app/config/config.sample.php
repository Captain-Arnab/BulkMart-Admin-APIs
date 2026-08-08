<?php
/**
 * Copy this file to config.local.php and fill in your credentials.
 * config.local.php is gitignored — never commit real secrets.
 */
return [
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'name'    => 'veggiicart',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        // Leave empty to auto-detect from the request, or set e.g. '/VGS/veggiicart/public'
        'base_url'         => '',
        'session_lifetime' => 7200, // seconds (2 hours)
        'debug'            => true,
    ],
];
