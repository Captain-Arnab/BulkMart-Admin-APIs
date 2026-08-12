<?php

/**
 * SMS gateway wrapper (VEGGCT templates).
 * When sms.enabled / credentials are missing, logs OTP/messages in DEV MODE.
 */
class SmsService
{
    public const TEMPLATE_LOGIN_OTP = 'VEGGCT_LOGIN_OTP';
    public const TEMPLATE_DELIVERY_OTP = 'VEGGCT_DELIVERY_OTP';
    public const TEMPLATE_ORDER_SHIPPED = 'VEGGCT_ORDER_SHIPPED';
    public const TEMPLATE_OUT_FOR_DELIVERY = 'VEGGCT_OUT_FOR_DELIVERY';

    /**
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string}
     */
    public static function sendLoginOtp(string $mobile, string $otp): array
    {
        $message = "Your VeggiiCart login OTP is {$otp}. Valid for 5 minutes. Do not share.";
        return self::dispatch($mobile, $message, self::TEMPLATE_LOGIN_OTP, ['otp' => $otp], $otp);
    }

    /**
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string}
     */
    public static function sendDeliveryOtp(string $mobile, string $otp, string $orderNumber): array
    {
        $message = "VeggiiCart delivery OTP for order {$orderNumber} is {$otp}.";
        return self::dispatch($mobile, $message, self::TEMPLATE_DELIVERY_OTP, [
            'otp' => $otp,
            'order_number' => $orderNumber,
        ], $otp);
    }

    /**
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string}
     */
    public static function sendOrderShipped(string $mobile, string $orderNumber, ?string $eta = null): array
    {
        $etaBit = $eta ? " ETA: {$eta}." : '';
        $message = "Your VeggiiCart order {$orderNumber} has been shipped.{$etaBit}";
        return self::dispatch($mobile, $message, self::TEMPLATE_ORDER_SHIPPED, [
            'order_number' => $orderNumber,
            'eta' => $eta,
        ]);
    }

    /**
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string}
     */
    public static function sendOutForDelivery(string $mobile, string $orderNumber): array
    {
        $message = "Your VeggiiCart order {$orderNumber} is out for delivery.";
        return self::dispatch($mobile, $message, self::TEMPLATE_OUT_FOR_DELIVERY, [
            'order_number' => $orderNumber,
        ]);
    }

    /**
     * @param array<string,mixed> $vars
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string}
     */
    private static function dispatch(
        string $mobile,
        string $message,
        string $template,
        array $vars = [],
        ?string $devOtp = null
    ): array {
        $enabled = (bool) (app_config('sms.enabled') ?? false);
        $apiKey = trim((string) (app_config('sms.api_key') ?? ''));
        $sender = trim((string) (app_config('sms.sender_id') ?? ''));
        $endpoint = trim((string) (app_config('sms.endpoint') ?? ''));

        if (!$enabled || $apiKey === '' || $endpoint === '') {
            self::logDev($mobile, $template, $message, $vars);
            return [
                'sent'       => false,
                'dev_mode'   => true,
                'message_id' => null,
                'dev_otp'    => $devOtp,
            ];
        }

        // TODO: wire live gateway (Msg91 / Fast2SMS / custom) using VEGGCT DLT templates.
        // Placeholder HTTP call — replace with provider-specific payload when credentials are ready.
        try {
            $payload = json_encode([
                'sender'   => $sender,
                'to'       => $mobile,
                'template' => $template,
                'message'  => $message,
                'vars'     => $vars,
            ], JSON_UNESCAPED_SLASHES);

            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey,
                ],
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 15,
            ]);
            $body = curl_exec($ch);
            $errno = curl_errno($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($errno || $status >= 400) {
                self::logDev($mobile, $template, $message, $vars + [
                    'gateway_error' => true,
                    'http_status'   => $status,
                    'body'          => is_string($body) ? $body : null,
                ]);
                return [
                    'sent'       => false,
                    'dev_mode'   => true,
                    'message_id' => null,
                    'dev_otp'    => $devOtp,
                ];
            }

            return [
                'sent'       => true,
                'dev_mode'   => false,
                'message_id' => is_string($body) ? substr($body, 0, 120) : null,
                'dev_otp'    => null,
            ];
        } catch (Throwable $e) {
            self::logDev($mobile, $template, $message, $vars + ['exception' => $e->getMessage()]);
            return [
                'sent'       => false,
                'dev_mode'   => true,
                'message_id' => null,
                'dev_otp'    => $devOtp,
            ];
        }
    }

    /** @param array<string,mixed> $vars */
    private static function logDev(string $mobile, string $template, string $message, array $vars): void
    {
        $dir = APP_ROOT . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $line = json_encode([
            'ts'       => date('c'),
            'mode'     => 'DEV MODE — remove before production',
            'mobile'   => $mobile,
            'template' => $template,
            'message'  => $message,
            'vars'     => $vars,
        ], JSON_UNESCAPED_SLASHES);
        @file_put_contents($dir . '/sms_dev.log', $line . PHP_EOL, FILE_APPEND);
    }
}
