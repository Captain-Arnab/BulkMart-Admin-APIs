<?php
/**
 * Manual ConnectBind SMS smoke test.
 *
 * Usage:
 *   php scripts/test_sms.php <10-digit-mobile> [login_otp|out_for_delivery|order_shipped|delivery_otp|all]
 *
 * Requires sms.enabled=true and credentials in config.local.php.
 * order_shipped / delivery_otp stay blocked until template IDs are confirmed.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';

spl_autoload_register(static function (string $class): void {
    $file = dirname(__DIR__) . '/app/services/' . $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

$mobile = $argv[1] ?? '';
$type = strtolower(trim((string) ($argv[2] ?? 'all')));

if ($mobile === '' || !preg_match('/^[6-9]\d{9}$/', preg_replace('/\D+/', '', $mobile) ?? '')) {
    fwrite(STDERR, "Usage: php scripts/test_sms.php <10-digit-mobile> [type]\n");
    fwrite(STDERR, "Types: login_otp | out_for_delivery | order_shipped | delivery_otp | all\n");
    exit(1);
}

$mobile = preg_replace('/\D+/', '', $mobile) ?? $mobile;
if (strlen($mobile) === 12 && str_starts_with($mobile, '91')) {
    $mobile = substr($mobile, 2);
}

$enabled = (bool) (app_config('sms.enabled') ?? false);
echo 'sms.enabled = ' . ($enabled ? 'true' : 'false') . PHP_EOL;
echo 'api_base_url = ' . (app_config('sms.api_base_url') ?? '') . PHP_EOL;
echo 'source = ' . (app_config('sms.source') ?? '') . PHP_EOL;

$types = $type === 'all'
    ? ['login_otp', 'out_for_delivery', 'order_shipped', 'delivery_otp']
    : [$type];

foreach ($types as $t) {
    echo PHP_EOL . "=== {$t} ===" . PHP_EOL;
    $tid = (string) (app_config('sms.templates.' . $t) ?? '');
    echo 'template_id = ' . ($tid !== '' ? $tid : '(missing)') . PHP_EOL;

    $result = match ($t) {
        'login_otp' => SmsService::sendLoginOtp($mobile, (string) random_int(100000, 999999)),
        'out_for_delivery' => SmsService::sendOutForDelivery($mobile, 'VC-TEST-' . date('His')),
        'order_shipped' => SmsService::sendOrderShipped(
            $mobile,
            'VC-TEST-' . date('His'),
            date('Y-m-d', strtotime('+1 day'))
        ),
        'delivery_otp' => SmsService::sendDeliveryOtp(
            $mobile,
            'VC-TEST-' . date('His'),
            (string) random_int(100000, 999999)
        ),
        default => null,
    };

    if ($result === null) {
        fwrite(STDERR, "Unknown type: {$t}\n");
        continue;
    }

    echo 'sent = ' . ($result['sent'] ? 'yes' : 'no') . PHP_EOL;
    echo 'dev_mode = ' . ($result['dev_mode'] ? 'yes' : 'no') . PHP_EOL;
    echo 'status = ' . ($result['status'] ?? '') . PHP_EOL;
    echo 'message_id = ' . ($result['message_id'] ?? '') . PHP_EOL;
    echo 'raw_response = ' . substr((string) ($result['raw_response'] ?? ''), 0, 200) . PHP_EOL;
    if (!$result['sent'] && str_contains((string) ($result['status'] ?? ''), '1703')) {
        echo 'HINT: Gateway code 1703 = invalid username or password. Check sms.username / sms.password with the vendor.' . PHP_EOL;
    }
    if ($result['sent'] && ($result['status'] ?? '') === 'sent') {
        echo 'HINT: Gateway accepted the message (1701). If the phone gets nothing, check DLT template / sender / operator DLR with the vendor.' . PHP_EOL;
    }
}

echo PHP_EOL . 'Check storage/logs/sms_gateway.log and sms_dev.log for full request/response detail.' . PHP_EOL;
