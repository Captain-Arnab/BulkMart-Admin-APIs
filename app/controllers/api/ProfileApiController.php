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

            $id = $this->customerId();
            $this->customers->updateProfile($id, $allowed);
            $fresh = $this->customers->find($id);
            $this->ok($this->customers->publicProfile($fresh ?? []));
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
