<?php

/**
 * Base JSON API controller — standard envelope + helpers.
 */
class ApiController extends Controller
{
    protected function ok(mixed $data = null, int $status = 200): never
    {
        $this->envelope(true, $data, null, $status);
    }

    protected function fail(string $code, string $message, int $status = 400, ?array $fields = null): never
    {
        $error = ['code' => $code, 'message' => $message];
        if ($fields !== null) {
            $error['fields'] = $fields;
        }
        $this->envelope(false, null, $error, $status);
    }

    /** @param array<string,string> $fields */
    protected function validationError(array $fields, string $message = 'Validation failed.'): never
    {
        $this->fail('VALIDATION_ERROR', $message, 422, $fields);
    }

    public static function abort(int $status, string $code, string $message): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'data'    => null,
            'error'   => ['code' => $code, 'message' => $message],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function envelope(bool $success, mixed $data, ?array $error, int $status): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'data'    => $data,
            'error'   => $error,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** @return array<string,mixed> */
    protected function jsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            $this->fail('INVALID_JSON', 'Request body must be valid JSON.', 400);
        }
        return $decoded;
    }

    /** @return array<string,mixed> */
    protected function input(): array
    {
        $json = $this->jsonBody();
        if ($json !== []) {
            return $json;
        }
        return array_merge($_GET, $_POST);
    }

    protected function customerId(): int
    {
        return api_customer_id();
    }

    protected function requireCustomer(): array
    {
        $c = $GLOBALS['api_customer'] ?? null;
        if (!is_array($c)) {
            self::abort(401, 'UNAUTHORIZED', 'Authentication required.');
        }
        return $c;
    }

    protected function handleException(Throwable $e): never
    {
        if ($e instanceof InvalidArgumentException || $e instanceof DomainException) {
            $this->fail('BAD_REQUEST', $e->getMessage(), 422);
        }
        if (APP_DEBUG) {
            $this->fail('SERVER_ERROR', $e->getMessage(), 500);
        }
        $this->fail('SERVER_ERROR', 'Something went wrong. Please try again later.', 500);
    }

    protected function absoluteMedia(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . media($path);
    }
}
