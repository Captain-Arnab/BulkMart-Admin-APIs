<?php

/**
 * Minimal HS256 JWT (no Composer dependency).
 */
class JwtService
{
    public static function issueAccessToken(int $customerId, array $extra = []): string
    {
        $ttl = (int) (app_config('jwt.access_ttl') ?? 3600);
        $now = time();
        $payload = array_merge($extra, [
            'sub'  => $customerId,
            'type' => 'access',
            'iat'  => $now,
            'exp'  => $now + max(60, $ttl),
        ]);
        return self::encode($payload);
    }

    public static function issueRefreshToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /** @return array<string,mixed> */
    public static function decode(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new RuntimeException('Invalid token format.');
        }
        [$h64, $p64, $s64] = $parts;
        $expected = self::sign($h64 . '.' . $p64);
        if (!hash_equals($expected, self::b64urlDecode($s64))) {
            throw new RuntimeException('Invalid token signature.');
        }
        $payload = json_decode(self::b64urlDecode($p64), true);
        if (!is_array($payload)) {
            throw new RuntimeException('Invalid token payload.');
        }
        if (($payload['type'] ?? '') !== 'access') {
            throw new RuntimeException('Invalid token type.');
        }
        if (!isset($payload['exp']) || (int) $payload['exp'] < time()) {
            throw new RuntimeException('Token expired.');
        }
        if (!isset($payload['sub'])) {
            throw new RuntimeException('Token missing subject.');
        }
        return $payload;
    }

    /** @param array<string,mixed> $payload */
    private static function encode(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $h64 = self::b64urlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $p64 = self::b64urlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $sig = self::b64urlEncode(self::sign($h64 . '.' . $p64));
        return $h64 . '.' . $p64 . '.' . $sig;
    }

    private static function sign(string $data): string
    {
        return hash_hmac('sha256', $data, self::secret(), true);
    }

    private static function secret(): string
    {
        $secret = (string) (app_config('jwt.secret') ?? '');
        if ($secret === '' || $secret === 'change-me-jwt-secret') {
            // Dev fallback derived from app path — set jwt.secret in config.local.php for production.
            $secret = 'veggiicart-dev-' . hash('sha256', APP_ROOT . '|jwt');
        }
        return $secret;
    }

    private static function b64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64urlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        if ($decoded === false) {
            throw new RuntimeException('Invalid token encoding.');
        }
        return $decoded;
    }
}
