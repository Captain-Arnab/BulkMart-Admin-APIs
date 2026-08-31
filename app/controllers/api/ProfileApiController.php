<?php

class ProfileApiController extends ApiController
{
    private Customer $customers;

    public function __construct()
    {
        $this->customers = new Customer();
    }

    public function show(): never
    {
        $customer = $this->requireCustomer();
        $this->ok($this->customers->publicProfile($customer));
    }

    public function update(): never
    {
        try {
            $body = $this->input();
            $allowed = [];
            foreach (['email', 'owner_name', 'business_name', 'gst_number', 'fssai_number', 'pan_number'] as $k) {
                if (array_key_exists($k, $body)) {
                    $allowed[$k] = is_string($body[$k]) ? trim($body[$k]) : $body[$k];
                }
            }
            if (isset($allowed['email']) && $allowed['email'] !== '' && !filter_var($allowed['email'], FILTER_VALIDATE_EMAIL)) {
                $this->validationError(['email' => 'Enter a valid email address.']);
            }
            if (isset($allowed['email']) && $allowed['email'] === '') {
                $allowed['email'] = null;
            }
            if (isset($allowed['email']) && $allowed['email'] !== null) {
                $allowed['email'] = strtolower((string) $allowed['email']);
                if ($this->customers->emailTaken($allowed['email'], $this->customerId())) {
                    $this->validationError(['email' => 'This email is already linked to another account.']);
                }
            }

            $id = $this->customerId();
            $this->customers->updateProfile($id, $allowed);

            $password = (string) ($body['password'] ?? '');
            if ($password !== '') {
                if (strlen($password) < 6) {
                    $this->validationError(['password' => 'Password must be at least 6 characters.']);
                }
                $this->customers->setPassword($id, $password);
            }

            $fresh = $this->customers->find($id);
            $this->ok($this->customers->publicProfile($fresh ?? []));
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function changePassword(): never
    {
        try {
            $body = $this->input();
            $current = (string) ($body['current_password'] ?? '');
            $password = (string) ($body['password'] ?? $body['new_password'] ?? '');
            $confirm = (string) ($body['password_confirmation'] ?? $body['confirm_password'] ?? $password);
            $fields = [];
            if ($password === '' || strlen($password) < 6) {
                $fields['password'] = 'New password must be at least 6 characters.';
            }
            if ($password !== $confirm) {
                $fields['password_confirmation'] = 'Password confirmation does not match.';
            }
            if ($fields !== []) {
                $this->validationError($fields);
            }

            $customer = $this->requireCustomer();
            if (!empty($customer['password_hash'])) {
                if ($current === '' || !password_verify($current, (string) $customer['password_hash'])) {
                    $this->validationError(['current_password' => 'Current password is incorrect.']);
                }
            }

            $this->customers->setPassword((int) $customer['id'], $password);
            $this->ok(['message' => 'Password updated successfully.']);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function uploadAvatar(): never
    {
        try {
            if (empty($_FILES['avatar']) && empty($_FILES['file'])) {
                $this->validationError(['avatar' => 'Avatar image is required.']);
            }
            $file = $_FILES['avatar'] ?? $_FILES['file'];
            $path = UploadService::storeImage($file, 'avatars/' . $this->customerId());
            if ($path === null) {
                $this->validationError(['avatar' => 'Avatar image is required.']);
            }
            $this->customers->updateProfile($this->customerId(), ['avatar_url' => $path]);
            $fresh = $this->customers->find($this->customerId());
            $this->ok($this->customers->publicProfile($fresh ?? []));
        } catch (RuntimeException $e) {
            $this->fail('UPLOAD_ERROR', $e->getMessage(), 422);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function removeAvatar(): never
    {
        $customer = $this->requireCustomer();
        $old = $customer['avatar_url'] ?? null;
        $this->customers->clearAvatar($this->customerId());
        if ($old && !preg_match('#^https?://#i', (string) $old)) {
            $full = PUBLIC_PATH . '/' . ltrim((string) $old, '/');
            if (is_file($full)) {
                @unlink($full);
            }
        }
        $fresh = $this->customers->find($this->customerId());
        $this->ok($this->customers->publicProfile($fresh ?? []));
    }
}
