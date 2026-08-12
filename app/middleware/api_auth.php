<?php

/**
 * CORS for /api/* — allow configured origins only.
 */
function api_apply_cors(): void
{
    $origins = app_config('cors.allowed_origins');
    if (!is_array($origins) || $origins === []) {
        $origins = ['*'];
    }

    $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allow = null;
    if (in_array('*', $origins, true)) {
        $allow = $requestOrigin !== '' ? $requestOrigin : '*';
    } elseif ($requestOrigin !== '' && in_array($requestOrigin, $origins, true)) {
        $allow = $requestOrigin;
    }

    if ($allow !== null) {
        header('Access-Control-Allow-Origin: ' . $allow);
        header('Vary: Origin');
        header('Access-Control-Allow-Credentials: true');
    }

    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, X-Requested-With');
    header('Access-Control-Max-Age: 86400');

    if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Require Bearer JWT; sets $GLOBALS['api_customer_id'].
 */
function require_api_auth(): void
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($header === '' && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        foreach ($headers as $k => $v) {
            if (strcasecmp($k, 'Authorization') === 0) {
                $header = $v;
                break;
            }
        }
    }

    if (!preg_match('/^Bearer\s+(\S+)$/i', $header, $m)) {
        ApiController::abort(401, 'UNAUTHORIZED', 'Missing or invalid Authorization header.');
    }

    try {
        $payload = JwtService::decode($m[1]);
    } catch (Throwable $e) {
        ApiController::abort(401, 'UNAUTHORIZED', 'Invalid or expired access token.');
    }

    $customerId = (int) ($payload['sub'] ?? 0);
    if ($customerId < 1) {
        ApiController::abort(401, 'UNAUTHORIZED', 'Invalid or expired access token.');
    }

    $customer = (new Customer())->find($customerId);
    if (!$customer) {
        ApiController::abort(401, 'UNAUTHORIZED', 'Customer not found.');
    }
    if ((int) ($customer['is_blocked'] ?? 0) === 1) {
        ApiController::abort(403, 'FORBIDDEN', 'Your account has been blocked. Contact support.');
    }

    $GLOBALS['api_customer_id'] = $customerId;
    $GLOBALS['api_customer'] = $customer;
}

function api_customer_id(): int
{
    return (int) ($GLOBALS['api_customer_id'] ?? 0);
}
