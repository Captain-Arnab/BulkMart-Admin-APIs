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
    'jwt' => [
        'secret'      => 'change-me-jwt-secret', // REQUIRED in production
        'access_ttl'  => 3600,      // 1 hour
        'refresh_ttl' => 2592000,   // 30 days
    ],
    'otp' => [
        'ttl_seconds'               => 300,
        'rate_limit_max'            => 3,
        'rate_limit_window_minutes' => 10,
    ],
    'sms' => [
        // Leave enabled=false until gateway credentials are ready (DEV MODE OTP logging).
        'enabled'   => false,
        'api_key'   => '',
        'sender_id' => 'VEGGCT',
        'endpoint'  => '', // provider HTTP endpoint
    ],
    'cors' => [
        // Flutter web / website origins — replace '*' before production
        'allowed_origins' => [
            '*',
            // 'http://localhost:3000',
            // 'https://app.veggiicart.com',
        ],
    ],
    'checkout' => [
        'delivery_fee'            => 0,
        'delivery_slot_days'      => 7,
        'require_kyc_approved'    => false,
    ],
];
