<?php

/**
 * ConnectBind / DSTRI bulk SMS gateway (VEGGCT DLT templates).
 *
 * When sms.enabled is false, credentials are missing, or a template ID is still
 * CONFIRM_WITH_VENDOR, messages are logged in DEV MODE and not sent live.
 */
class SmsService
{
    public const TYPE_LOGIN_OTP = 'login_otp';
    public const TYPE_ORDER_SHIPPED = 'order_shipped';
    public const TYPE_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const TYPE_DELIVERY_OTP = 'delivery_otp';

    /** Exact DLT-approved bodies — match vendor format including line breaks (\n). */
    private const TEMPLATES = [
        self::TYPE_LOGIN_OTP =>
            "Dear Customer,\nuse this One Time Password {#var#} to log in to your veggiicart account.\nThis OTP will be valid for the next 5 mins.\nhttps://veggiicart.com",
        self::TYPE_ORDER_SHIPPED =>
            "Dear Customer,\nyour Order {#var#} has been shipped and is expected to be delivered by {#var#}.\nTrack your order in the VEGGIICART app.",
        self::TYPE_OUT_FOR_DELIVERY =>
            "Dear Customer,\nyour Order {#var#} is out for delivery and will reach you today.\nPlease keep the payment ready (Cash on Delivery).\nhttps://veggiicart.com",
        self::TYPE_DELIVERY_OTP =>
            "Dear Customer,\nyour delivery OTP for Order {#var#} is {#var#}.\nShare this OTP with our delivery partner only at the time of delivery to confirm receipt.\nValid for 30 mins.\n-VEGGIICART",
    ];

    /**
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string,status:string,raw_response:?string}
     */
    public static function sendLoginOtp(string $mobile, string $otp): array
    {
        return self::dispatch(
            $mobile,
            self::TYPE_LOGIN_OTP,
            [$otp],
            $otp
        );
    }

    /**
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string,status:string,raw_response:?string}
     */
    public static function sendOrderShipped(string $mobile, string $orderNumber, ?string $eta = null): array
    {
        $etaDisplay = self::formatEtaForSms($eta);
        return self::dispatch(
            $mobile,
            self::TYPE_ORDER_SHIPPED,
            [$orderNumber, $etaDisplay !== '' ? $etaDisplay : 'soon']
        );
    }

    /**
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string,status:string,raw_response:?string}
     */
    public static function sendOutForDelivery(string $mobile, string $orderNumber): array
    {
        return self::dispatch(
            $mobile,
            self::TYPE_OUT_FOR_DELIVERY,
            [$orderNumber]
        );
    }

    /**
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string,status:string,raw_response:?string}
     */
    public static function sendDeliveryOtp(string $mobile, string $orderNumber, string $otp): array
    {
        return self::dispatch(
            $mobile,
            self::TYPE_DELIVERY_OTP,
            [$orderNumber, $otp],
            $otp
        );
    }

    /**
     * estimated_delivery_date is DATE (Y-m-d). Vendor sample used a time like "12.45pm";
     * we only store a date, so format as e.g. "24 Aug 2026".
     */
    public static function formatEtaForSms(?string $eta): string
    {
        $eta = trim((string) $eta);
        if ($eta === '') {
            return '';
        }
        $ts = strtotime($eta);
        if ($ts === false) {
            return $eta;
        }
        return date('d M Y', $ts);
    }

    /**
     * @param list<string> $vars Values substituted into successive {#var#} placeholders
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string,status:string,raw_response:?string}
     */
    private static function dispatch(
        string $mobile,
        string $messageType,
        array $vars = [],
        ?string $devOtp = null
    ): array {
        $templateBody = self::TEMPLATES[$messageType] ?? '';
        if ($templateBody === '') {
            self::logGateway('unknown_template', $mobile, $messageType, '', null, null, ['vars' => $vars]);
            return self::result(false, true, null, $devOtp, 'unknown_template', null);
        }

        $message = self::fillTemplate($templateBody, $vars);
        $enabled = (bool) (app_config('sms.enabled') ?? false);
        $username = trim((string) (app_config('sms.username') ?? ''));
        $password = (string) (app_config('sms.password') ?? '');
        $entityId = trim((string) (app_config('sms.entity_id') ?? ''));
        $tmid = trim((string) (app_config('sms.tmid') ?? ''));
        $source = trim((string) (app_config('sms.source') ?? 'VEGGCT')) ?: 'VEGGCT';
        $baseUrl = rtrim(trim((string) (app_config('sms.api_base_url') ?? '')), '?&');
        $templateId = trim((string) (app_config('sms.templates.' . $messageType) ?? ''));

        if (!$enabled || $username === '' || $password === '' || $baseUrl === '') {
            self::logDev($mobile, $messageType, $message, $vars, 'disabled_or_missing_credentials');
            return self::result(false, true, null, $devOtp, 'dev_mode', null);
        }

        if ($templateId === '' || str_starts_with(strtoupper($templateId), 'CONFIRM_WITH_VENDOR')) {
            self::logDev($mobile, $messageType, $message, $vars + [
                'blocked' => 'template_id_unconfirmed',
                'template_id' => $templateId,
            ], 'template_blocked');
            return self::result(false, true, null, $devOtp, 'template_blocked', null);
        }

        try {
            $destination = self::formatDestination($mobile);
            $query = [
                'username'    => $username,
                'password'    => $password,
                'type'        => '0',
                'dlr'         => '1',
                'destination' => $destination,
                'source'      => $source,
                'message'     => $message,
                'entityid'    => $entityId,
                'tempid'      => $templateId,
                'tmid'        => $tmid,
            ];
            // http_build_query encodes spaces as +; gateway examples use %20 / %0A via rawurlencode.
            $parts = [];
            foreach ($query as $k => $v) {
                $parts[] = rawurlencode((string) $k) . '=' . rawurlencode((string) $v);
            }
            $url = $baseUrl . '?' . implode('&', $parts);

            self::logGateway('request', $mobile, $messageType, $message, self::redactUrl($url), null, [
                'destination' => $destination,
                'tempid'      => $templateId,
                'source'      => $source,
            ]);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_HTTPGET        => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            $body = curl_exec($ch);
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            $httpStatus = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $raw = is_string($body) ? $body : null;
            self::logGateway('response', $mobile, $messageType, $message, self::redactUrl($url), $raw, [
                'http_status' => $httpStatus,
                'curl_errno'  => $errno,
                'curl_error'  => $error !== '' ? $error : null,
            ]);

            if ($errno !== 0) {
                self::logDev($mobile, $messageType, $message, $vars + [
                    'gateway_error' => true,
                    'curl_errno'    => $errno,
                    'curl_error'    => $error,
                ], 'curl_error');
                return self::result(false, true, null, $devOtp, 'curl_error', $raw);
            }

            $parsed = self::interpretResponse($raw, $httpStatus);
            if ($parsed['ok']) {
                return self::result(
                    true,
                    false,
                    $parsed['message_id'],
                    null,
                    $parsed['status'],
                    $raw
                );
            }

            // Non-empty ambiguous response: treat as sent, unconfirmed (manual review via logs).
            if ($parsed['status'] === 'sent_unconfirmed') {
                return self::result(
                    true,
                    false,
                    $parsed['message_id'],
                    null,
                    'sent_unconfirmed',
                    $raw
                );
            }

            self::logDev($mobile, $messageType, $message, $vars + [
                'gateway_error' => true,
                'http_status'   => $httpStatus,
                'body'          => $raw,
                'status'        => $parsed['status'],
            ], 'gateway_failure');
            return self::result(false, true, null, $devOtp, $parsed['status'], $raw);
        } catch (Throwable $e) {
            self::logDev($mobile, $messageType, $message, $vars + ['exception' => $e->getMessage()], 'exception');
            return self::result(false, true, null, $devOtp, 'exception', null);
        }
    }

    /** @param list<string> $vars */
    private static function fillTemplate(string $template, array $vars): string
    {
        $i = 0;
        return (string) preg_replace_callback(
            '/\{#var#\}/',
            static function () use (&$i, $vars): string {
                $val = $vars[$i] ?? '';
                $i++;
                return (string) $val;
            },
            $template
        );
    }

    private static function formatDestination(string $mobile): string
    {
        $digits = preg_replace('/\D+/', '', $mobile) ?? '';
        if (strlen($digits) === 10) {
            return '91' . $digits;
        }
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return $digits;
        }
        return $digits;
    }

    /**
     * ConnectBind / Route Mobile style: CODE|destination|messageId
     * 1701 = success; anything else is a rejection (SMS was not accepted).
     *
     * @return array{ok:bool,status:string,message_id:?string}
     */
    private static function interpretResponse(?string $raw, int $httpStatus): array
    {
        if ($httpStatus >= 400) {
            return ['ok' => false, 'status' => 'http_' . $httpStatus, 'message_id' => null];
        }
        $trim = trim((string) $raw);
        if ($trim === '') {
            return ['ok' => false, 'status' => 'empty_response', 'message_id' => null];
        }

        // Primary format: 1701|9198xxxxxxxx|uuid  OR  1703|9198xxxxxxxx
        if (preg_match('/^(\d{3,4})\|(.+)$/', $trim, $m)) {
            $code = $m[1];
            $rest = $m[2];
            $meaning = self::gatewayCodeMeaning($code);
            if ($code === '1701') {
                return [
                    'ok'         => true,
                    'status'     => 'sent',
                    'message_id' => substr($rest, 0, 120),
                ];
            }
            return [
                'ok'         => false,
                'status'     => 'gateway_' . $code . '_' . $meaning,
                'message_id' => substr($trim, 0, 120),
            ];
        }

        $lower = strtolower($trim);
        $errorHints = [
            'error', 'fail', 'invalid', 'denied', 'reject', 'unauthor',
            'insufficient', 'balance', 'blacklist', 'dlt', 'template not',
        ];
        foreach ($errorHints as $hint) {
            if (str_contains($lower, $hint)) {
                return ['ok' => false, 'status' => 'gateway_error', 'message_id' => substr($trim, 0, 120)];
            }
        }

        $successHints = ['success', 'submitted', 'accept', 'sent', 'ok', 'msgid', 'message id'];
        foreach ($successHints as $hint) {
            if (str_contains($lower, $hint)) {
                return ['ok' => true, 'status' => 'sent', 'message_id' => substr($trim, 0, 120)];
            }
        }

        // Unknown non-empty body — do not assume success
        return ['ok' => false, 'status' => 'sent_unconfirmed_review', 'message_id' => substr($trim, 0, 120)];
    }

    private static function gatewayCodeMeaning(string $code): string
    {
        return match ($code) {
            '1701' => 'success',
            '1702' => 'invalid_url_or_missing_param',
            '1703' => 'invalid_username_or_password',
            '1704' => 'invalid_type',
            '1705' => 'invalid_message',
            '1706' => 'invalid_destination',
            '1707' => 'invalid_source_sender_id',
            '1708' => 'invalid_dlr',
            '1709' => 'user_validation_failed',
            '1710' => 'internal_error',
            '1715' => 'response_timeout',
            '1025' => 'insufficient_credit',
            '1028' => 'spam_message',
            '1032' => 'dnd_reject',
            default => 'unknown_code',
        };
    }

    /**
     * @return array{sent:bool,dev_mode:bool,message_id:?string,dev_otp:?string,status:string,raw_response:?string}
     */
    private static function result(
        bool $sent,
        bool $devMode,
        ?string $messageId,
        ?string $devOtp,
        string $status,
        ?string $rawResponse
    ): array {
        return [
            'sent'          => $sent,
            'dev_mode'      => $devMode,
            'message_id'    => $messageId,
            'dev_otp'       => $devOtp,
            'status'        => $status,
            'raw_response'  => $rawResponse,
        ];
    }

    private static function redactUrl(string $url): string
    {
        return (string) preg_replace('/([?&]password=)[^&]*/i', '$1***', $url);
    }

    /** @param array<string,mixed> $vars */
    private static function logDev(
        string $mobile,
        string $template,
        string $message,
        array $vars,
        string $reason = 'dev_mode'
    ): void {
        $dir = APP_ROOT . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $line = json_encode([
            'ts'       => date('c'),
            'mode'     => 'DEV MODE — SMS not sent live',
            'reason'   => $reason,
            'mobile'   => $mobile,
            'template' => $template,
            'message'  => $message,
            'vars'     => $vars,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        @file_put_contents($dir . '/sms_dev.log', $line . PHP_EOL, FILE_APPEND);
    }

    /** @param array<string,mixed> $meta */
    private static function logGateway(
        string $phase,
        string $mobile,
        string $template,
        string $message,
        ?string $url,
        ?string $response,
        array $meta = []
    ): void {
        $dir = APP_ROOT . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $line = json_encode([
            'ts'       => date('c'),
            'phase'    => $phase,
            'mobile'   => $mobile,
            'template' => $template,
            'message'  => $message,
            'url'      => $url,
            'response' => $response,
            'meta'     => $meta,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        @file_put_contents($dir . '/sms_gateway.log', $line . PHP_EOL, FILE_APPEND);
    }
}
