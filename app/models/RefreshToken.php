<?php

class RefreshToken extends Model
{
    public function store(int $customerId, string $rawToken, int $ttlSeconds, ?string $ua = null, ?string $ip = null): void
    {
        $hash = JwtService::hashToken($rawToken);
        $ttlSeconds = max(60, $ttlSeconds);
        $this->execute(
            'INSERT INTO refresh_tokens (customer_id, token_hash, expires_at, user_agent, ip_address)
             VALUES (?,?, DATE_ADD(NOW(), INTERVAL ? SECOND), ?, ?)',
            [$customerId, $hash, $ttlSeconds, $ua, $ip]
        );
    }

    public function findValid(string $rawToken): ?array
    {
        $hash = JwtService::hashToken($rawToken);
        return $this->fetchOne(
            'SELECT * FROM refresh_tokens
             WHERE token_hash = ? AND revoked_at IS NULL AND expires_at > NOW()',
            [$hash]
        );
    }

    public function revoke(string $rawToken): void
    {
        $hash = JwtService::hashToken($rawToken);
        $this->execute(
            'UPDATE refresh_tokens SET revoked_at = NOW() WHERE token_hash = ? AND revoked_at IS NULL',
            [$hash]
        );
    }

    public function revokeAllForCustomer(int $customerId): void
    {
        $this->execute(
            'UPDATE refresh_tokens SET revoked_at = NOW()
             WHERE customer_id = ? AND revoked_at IS NULL',
            [$customerId]
        );
    }
}
