<?php

class AuthApiController extends ApiController
{
    private OtpService $otp;
    private Customer $customers;
    private RefreshToken $tokens;

    public function __construct()
    {
        $this->otp = new OtpService();
        $this->customers = new Customer();
        $this->tokens = new RefreshToken();
    }

    public function sendOtp(): never
    {
        try {
            $body = $this->input();
            // Strict: OTP login accepts mobile only — never email/password.
            if (isset($body['password']) && trim((string) $body['password']) !== '') {
                $this->fail('INVALID_LOGIN_METHOD', 'Mobile login uses OTP only. Do not send a password.', 422);
            }
            $mobile = trim((string) ($body['mobile'] ?? ''));
            if ($mobile === '') {
                $this->validationError(['mobile' => 'Mobile number is required.']);
            }
            if (str_contains($mobile, '@') || filter_var($mobile, FILTER_VALIDATE_EMAIL)) {
                $this->validationError(['mobile' => 'Use the Email & Password tab for email login. Mobile login requires a phone number.']);
            }

            $purpose = strtolower(trim((string) ($body['purpose'] ?? 'login')));
            if ($purpose === 'register') {
                $existing = $this->customers->findByMobile(OtpService::normalizeMobile($mobile));
                if ($existing && Customer::isRegistrationComplete($existing)) {
                    $this->fail(
                        'MOBILE_ALREADY_REGISTERED',
                        'This mobile number is already registered with a business. Please login instead.',
                        422
                    );
                }
            }

            $result = $this->otp->sendLoginOtp($mobile);
            $data = [
                'mobile'     => OtpService::normalizeMobile($mobile),
                'expires_at' => $result['expires_at'],
                'message'    => 'OTP sent successfully.',
            ];
            // Include OTP in API response only when SMS is disabled (local dev) or the
            // live gateway send failed — never when a live send succeeded.
            $sms = $result['sms'] ?? [];
            if (!empty($sms['dev_mode']) || empty($sms['sent'])) {
                $data['dev_mode'] = true;
                $data['dev_otp'] = $result['otp'];
                $data['dev_note'] = empty($sms['sent']) && !empty(app_config('sms.enabled'))
                    ? 'SMS gateway send failed — OTP returned for fallback testing only'
                    : 'DEV MODE — SMS disabled; OTP returned for local testing';
            }
            $this->ok($data);
        } catch (InvalidArgumentException $e) {
            $this->validationError(['mobile' => $e->getMessage()]);
        } catch (DomainException $e) {
            $this->fail('RATE_LIMITED', $e->getMessage(), 429);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function resendOtp(): never
    {
        $this->sendOtp();
    }

    public function verifyOtp(): never
    {
        try {
            $body = $this->input();
            if (isset($body['password']) && trim((string) $body['password']) !== '') {
                $this->fail('INVALID_LOGIN_METHOD', 'Mobile login uses OTP only. Do not send a password.', 422);
            }
            $mobile = trim((string) ($body['mobile'] ?? ''));
            $otp = trim((string) ($body['otp'] ?? ''));
            $fields = [];
            if ($mobile === '') {
                $fields['mobile'] = 'Mobile number is required.';
            } elseif (str_contains($mobile, '@') || filter_var($mobile, FILTER_VALIDATE_EMAIL)) {
                $fields['mobile'] = 'Use the Email & Password tab for email login.';
            }
            if ($otp === '' || !preg_match('/^\d{4,8}$/', $otp)) {
                $fields['otp'] = 'Enter a valid OTP.';
            }
            if ($fields !== []) {
                $this->validationError($fields);
            }

            $mobile = OtpService::normalizeMobile($mobile);
            if (!$this->otp->verifyLoginOtp($mobile, $otp)) {
                $this->fail('INVALID_OTP', 'Invalid or expired OTP.', 401);
            }

            $purpose = strtolower(trim((string) ($body['purpose'] ?? 'login')));
            $customer = $this->customers->findByMobile($mobile);
            if ($purpose === 'register' && $customer && Customer::isRegistrationComplete($customer)) {
                $this->fail(
                    'MOBILE_ALREADY_REGISTERED',
                    'This mobile number is already registered with a business. Please login instead.',
                    422
                );
            }

            $isNew = false;
            if (!$customer) {
                $id = $this->customers->createFromMobile($mobile);
                $customer = $this->customers->find($id);
                $isNew = true;
            }
            if (!$customer) {
                $this->fail('SERVER_ERROR', 'Unable to create customer.', 500);
            }
            if ((int) ($customer['is_blocked'] ?? 0) === 1) {
                $this->fail('FORBIDDEN', 'Your account has been blocked. Contact support.', 403);
            }

            $this->ok($this->issueTokens($customer, $isNew));
        } catch (InvalidArgumentException $e) {
            $this->validationError(['mobile' => $e->getMessage()]);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /** Email + password login only — never mobile, never OTP. */
    public function emailLogin(): never
    {
        try {
            $body = $this->input();
            // Reject any attempt to use this endpoint as mobile+password login.
            if (isset($body['mobile']) && trim((string) $body['mobile']) !== '') {
                $this->fail(
                    'INVALID_LOGIN_METHOD',
                    'Mobile number + password is not supported. Use Mobile & OTP, or Email & Password.',
                    422
                );
            }
            if (isset($body['otp']) && trim((string) $body['otp']) !== '') {
                $this->fail('INVALID_LOGIN_METHOD', 'Email login uses password only — OTP is not accepted here.', 422);
            }

            $email = strtolower(trim((string) ($body['email'] ?? $body['username'] ?? $body['identifier'] ?? '')));
            $password = (string) ($body['password'] ?? '');
            $fields = [];

            // Block mobile-as-identifier on the email endpoint (the old generic form path).
            $digits = preg_replace('/\D+/', '', $email) ?? '';
            if ($email !== '' && !str_contains($email, '@') && preg_match('/^[6-9]\d{9}$/', $digits)) {
                $this->fail(
                    'INVALID_LOGIN_METHOD',
                    'Mobile number + password is not supported. Switch to the Mobile & OTP tab.',
                    422
                );
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fields['email'] = 'Enter a valid email address.';
            }
            if ($password === '') {
                $fields['password'] = 'Password is required.';
            }
            if ($fields !== []) {
                $this->validationError($fields);
            }

            $customer = $this->customers->findByEmail($email);
            if (!$customer) {
                $this->fail('INVALID_CREDENTIALS', 'Invalid email or password.', 401);
            }
            if (empty($customer['password_hash'])) {
                $this->fail(
                    'PASSWORD_NOT_SET',
                    'No password set for this account — log in with Mobile + OTP, or set a password from your Profile.',
                    401
                );
            }
            if (!password_verify($password, (string) $customer['password_hash'])) {
                $this->fail('INVALID_CREDENTIALS', 'Invalid email or password.', 401);
            }
            if ((int) ($customer['is_blocked'] ?? 0) === 1) {
                $this->fail('FORBIDDEN', 'Your account has been blocked. Contact support.', 403);
            }

            $this->ok($this->issueTokens($customer, false));
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function refreshToken(): never
    {
        try {
            $body = $this->input();
            $refresh = trim((string) ($body['refresh_token'] ?? ''));
            if ($refresh === '') {
                $this->validationError(['refresh_token' => 'Refresh token is required.']);
            }

            $row = $this->tokens->findValid($refresh);
            if (!$row) {
                $this->fail('UNAUTHORIZED', 'Invalid or expired refresh token.', 401);
            }

            $customer = $this->customers->find((int) $row['customer_id']);
            if (!$customer || (int) ($customer['is_blocked'] ?? 0) === 1) {
                $this->fail('UNAUTHORIZED', 'Invalid or expired refresh token.', 401);
            }

            // Rotate refresh token
            $this->tokens->revoke($refresh);
            $this->ok($this->issueTokens($customer, false));
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function logout(): never
    {
        try {
            $body = $this->input();
            $refresh = trim((string) ($body['refresh_token'] ?? ''));
            if ($refresh !== '') {
                $this->tokens->revoke($refresh);
                $this->ok(['message' => 'Logged out successfully.']);
            }

            // Optional: revoke all sessions if a valid access token is present
            $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
            if (preg_match('/^Bearer\s+(\S+)$/i', $header, $m)) {
                try {
                    $payload = JwtService::decode($m[1]);
                    $this->tokens->revokeAllForCustomer((int) $payload['sub']);
                } catch (Throwable $e) {
                    // ignore invalid access token on logout
                }
            }
            $this->ok(['message' => 'Logged out successfully.']);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /** @param array<string,mixed> $customer */
    private function issueTokens(array $customer, bool $isNew): array
    {
        $access = JwtService::issueAccessToken((int) $customer['id']);
        $refresh = JwtService::issueRefreshToken();
        $ttl = (int) (app_config('jwt.refresh_ttl') ?? 2592000);
        $this->tokens->store(
            (int) $customer['id'],
            $refresh,
            $ttl,
            substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            $_SERVER['REMOTE_ADDR'] ?? null
        );

        return [
            'access_token'  => $access,
            'refresh_token' => $refresh,
            'token_type'    => 'Bearer',
            'expires_in'    => (int) (app_config('jwt.access_ttl') ?? 3600),
            'is_new_user'   => $isNew,
            'customer'      => $this->customers->publicProfile($customer),
        ];
    }
}
