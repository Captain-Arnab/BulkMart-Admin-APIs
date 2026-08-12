<?php

class OtpService
{
    private PDO $db;

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo ?? db();
    }

    /**
     * @return array{otp:string,expires_at:string,sms:array}
     */
    public function sendLoginOtp(string $mobile): array
    {
        $mobile = self::normalizeMobile($mobile);
        $this->assertRateLimit($mobile, 'login');

        $ttl = (int) (app_config('otp.ttl_seconds') ?? 300);
        $otp = (string) random_int(100000, 999999);
        $ttl = max(60, $ttl);

        // Invalidate previous unused login OTPs for this mobile
        $this->db->prepare(
            "UPDATE otp_codes SET consumed_at = NOW()
             WHERE mobile = ? AND purpose = 'login' AND consumed_at IS NULL"
        )->execute([$mobile]);

        $stmt = $this->db->prepare(
            'INSERT INTO otp_codes (mobile, otp_hash, purpose, expires_at)
             VALUES (?,?,?, DATE_ADD(NOW(), INTERVAL ? SECOND))'
        );
        $stmt->execute([$mobile, password_hash($otp, PASSWORD_DEFAULT), 'login', $ttl]);

        $expiresRow = $this->db->query('SELECT DATE_ADD(NOW(), INTERVAL ' . (int) $ttl . ' SECOND) AS e')->fetch();
        $expires = (string) ($expiresRow['e'] ?? '');

        $this->db->prepare(
            'INSERT INTO otp_rate_limits (mobile, purpose, sent_at) VALUES (?,?,NOW())'
        )->execute([$mobile, 'login']);

        $sms = SmsService::sendLoginOtp($mobile, $otp);

        return [
            'otp'        => $otp, // only used to attach DEV field in controller
            'expires_at' => $expires,
            'sms'        => $sms,
        ];
    }

    public function verifyLoginOtp(string $mobile, string $otp): bool
    {
        $mobile = self::normalizeMobile($mobile);
        $stmt = $this->db->prepare(
            "SELECT * FROM otp_codes
             WHERE mobile = ? AND purpose = 'login' AND consumed_at IS NULL
               AND expires_at > NOW()
             ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$mobile]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        if ((int) $row['attempts'] >= 5) {
            return false;
        }

        $ok = password_verify($otp, (string) $row['otp_hash']);
        $upd = $this->db->prepare('UPDATE otp_codes SET attempts = attempts + 1' . ($ok ? ', consumed_at = NOW()' : '') . ' WHERE id = ?');
        $upd->execute([(int) $row['id']]);
        return $ok;
    }

    private function assertRateLimit(string $mobile, string $purpose): void
    {
        $windowMin = (int) (app_config('otp.rate_limit_window_minutes') ?? 10);
        $max = (int) (app_config('otp.rate_limit_max') ?? 3);
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM otp_rate_limits
             WHERE mobile = ? AND purpose = ? AND sent_at >= (NOW() - INTERVAL ? MINUTE)'
        );
        $stmt->execute([$mobile, $purpose, $windowMin]);
        $count = (int) $stmt->fetchColumn();
        if ($count >= $max) {
            throw new DomainException("Too many OTP requests. Try again after {$windowMin} minutes.");
        }
    }

    public static function normalizeMobile(string $mobile): string
    {
        $digits = preg_replace('/\D+/', '', $mobile) ?? '';
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }
        if (!preg_match('/^[6-9]\d{9}$/', $digits)) {
            throw new InvalidArgumentException('Enter a valid 10-digit Indian mobile number.');
        }
        return $digits;
    }
}
