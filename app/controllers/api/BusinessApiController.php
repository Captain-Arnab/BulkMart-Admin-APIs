<?php

class BusinessApiController extends ApiController
{
    public const BUSINESS_TYPES = [
        ['key' => 'retailer', 'label' => 'Retail Shop'],
        ['key' => 'kirana', 'label' => 'Kirana Store'],
        ['key' => 'supermarket', 'label' => 'Supermarket'],
        ['key' => 'hotel', 'label' => 'Hotel'],
        ['key' => 'restaurant', 'label' => 'Restaurant'],
        ['key' => 'caterer', 'label' => 'Catering Service'],
        ['key' => 'hostel', 'label' => 'Hostel'],
        ['key' => 'hospital', 'label' => 'Hospital'],
        ['key' => 'corporate_pantry', 'label' => 'Corporate Pantry'],
        ['key' => 'juice_shop', 'label' => 'Juice Shop'],
        ['key' => 'wholesaler', 'label' => 'Vendor/Reseller'],
        ['key' => 'other', 'label' => 'Other'],
    ];

    /** Form labels / aliases → canonical key */
    private const BUSINESS_TYPE_ALIASES = [
        'retail shop'        => 'retailer',
        'retailer'           => 'retailer',
        'supermarket'        => 'supermarket',
        'kirana store'       => 'kirana',
        'kirana'             => 'kirana',
        'hotel'              => 'hotel',
        'restaurant'         => 'restaurant',
        'catering service'   => 'caterer',
        'caterer'            => 'caterer',
        'vendor/reseller'    => 'wholesaler',
        'wholesaler'         => 'wholesaler',
        'hostel'             => 'hostel',
        'hospital'           => 'hospital',
        'corporate pantry'   => 'corporate_pantry',
        'juice shop'         => 'juice_shop',
        'other'              => 'other',
    ];

    private Customer $customers;

    public function __construct()
    {
        $this->customers = new Customer();
    }

    public function businessTypes(): never
    {
        $this->ok(['business_types' => self::BUSINESS_TYPES]);
    }

    public function register(): never
    {
        try {
            $body = $this->input();
            $fields = [];
            $businessName = trim((string) ($body['business_name'] ?? ''));
            $ownerName = trim((string) ($body['owner_name'] ?? ''));
            $businessType = $this->normalizeBusinessType(trim((string) ($body['business_type'] ?? '')));
            if ($businessName === '') {
                $fields['business_name'] = 'Business name is required.';
            }
            if ($ownerName === '') {
                $fields['owner_name'] = 'Owner name is required.';
            }
            if ($businessType === '') {
                $fields['business_type'] = 'Business type is required.';
            }
            $validKeys = array_column(self::BUSINESS_TYPES, 'key');
            if ($businessType !== '' && !in_array($businessType, $validKeys, true)) {
                $fields['business_type'] = 'Invalid business type.';
            }
            if ($fields !== []) {
                $this->validationError($fields);
            }

            // Normalize to label for storage consistency with admin seeds
            $map = [];
            foreach (self::BUSINESS_TYPES as $t) {
                $map[$t['key']] = $t['label'];
            }
            $normalized = $map[$businessType] ?? $businessType;

            $manualReview = kyc_manual_review_enabled();
            $kycStatus = $manualReview ? 'pending' : 'approved';

            $id = $this->customerId();
            $this->customers->submitRegistration($id, [
                'business_name' => $businessName,
                'owner_name'    => $ownerName,
                'business_type' => $normalized,
                'gst_number'    => trim((string) ($body['gst_number'] ?? '')) ?: null,
                'fssai_number'  => trim((string) ($body['fssai_number'] ?? '')) ?: null,
                'pan_number'    => trim((string) ($body['pan_number'] ?? '')) ?: null,
                'email'         => trim((string) ($body['email'] ?? '')) ?: null,
            ], $kycStatus);

            // Optional address payload (Flutter / website parity)
            $addressPayload = $this->extractAddressPayload($body);
            $savedAddress = null;
            if ($addressPayload !== null) {
                $addressId = (new Address())->create($id, $addressPayload);
                $savedAddress = (new Address())->findForCustomer($addressId, $id);
            }

            if ($manualReview) {
                NotificationService::notifyCustomer(
                    $id,
                    'Registration received',
                    'Your business registration is under review.',
                    'verification',
                    $id
                );
            } else {
                NotificationService::notifyCustomer(
                    $id,
                    'KYC approved',
                    'Your business registration has been approved. You can now place wholesale orders on VeggiiCart.',
                    'verification',
                    $id
                );
            }

            $fresh = $this->customers->find($id);
            $this->ok([
                'message' => $manualReview
                    ? 'Registration submitted. KYC is pending review.'
                    : 'Registration submitted and approved.',
                'customer' => $this->customers->publicProfile($fresh ?? []),
                'address'  => $savedAddress ? [
                    'id'         => (int) ($savedAddress['id'] ?? 0),
                    'label'      => $savedAddress['label'] ?? null,
                    'line1'      => $savedAddress['line1'] ?? null,
                    'line2'      => $savedAddress['line2'] ?? null,
                    'city'       => $savedAddress['city'] ?? null,
                    'state'      => $savedAddress['state'] ?? null,
                    'pincode'    => $savedAddress['pincode'] ?? null,
                    'landmark'   => $savedAddress['landmark'] ?? null,
                    'is_default' => (int) ($savedAddress['is_default'] ?? 0) === 1,
                ] : null,
                'kyc_status' => $kycStatus,
            ]);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Accept nested address object or flat shop_address / delivery_address fields.
     * @param array<string,mixed> $body
     * @return array<string,mixed>|null
     */
    private function extractAddressPayload(array $body): ?array
    {
        $nested = $body['shop_address'] ?? $body['address'] ?? null;
        if (is_array($nested)) {
            $line1 = trim((string) ($nested['line1'] ?? $nested['address'] ?? ''));
            $city = trim((string) ($nested['city'] ?? ''));
            $state = trim((string) ($nested['state'] ?? ''));
            $pincode = trim((string) ($nested['pincode'] ?? ''));
            if ($line1 === '' || $city === '' || $state === '' || $pincode === '') {
                return null;
            }
            return [
                'label'      => trim((string) ($nested['label'] ?? 'Shop')) ?: 'Shop',
                'line1'      => $line1,
                'line2'      => trim((string) ($nested['line2'] ?? '')) ?: null,
                'city'       => $city,
                'state'      => $state,
                'pincode'    => $pincode,
                'landmark'   => trim((string) ($nested['landmark'] ?? '')) ?: null,
                'geo_lat'    => isset($nested['geo_lat']) && $nested['geo_lat'] !== '' ? (float) $nested['geo_lat'] : null,
                'geo_lng'    => isset($nested['geo_lng']) && $nested['geo_lng'] !== '' ? (float) $nested['geo_lng'] : null,
                'is_default' => true,
            ];
        }

        $line1 = trim((string) ($body['shop_address'] ?? $body['line1'] ?? ''));
        if ($line1 === '' || is_array($body['shop_address'] ?? null)) {
            $line1 = trim((string) ($body['delivery_address'] ?? ''));
        }
        $city = trim((string) ($body['city'] ?? ''));
        $state = trim((string) ($body['state'] ?? ''));
        $pincode = trim((string) ($body['pincode'] ?? ''));
        if ($line1 === '' || $city === '' || $state === '' || $pincode === '') {
            return null;
        }
        return [
            'label'      => 'Shop',
            'line1'      => $line1,
            'line2'      => trim((string) ($body['delivery_address'] ?? '')) !== $line1
                ? (trim((string) ($body['delivery_address'] ?? '')) ?: null)
                : null,
            'city'       => $city,
            'state'      => $state,
            'pincode'    => $pincode,
            'landmark'   => trim((string) ($body['landmark'] ?? '')) ?: null,
            'geo_lat'    => isset($body['geo_lat']) && $body['geo_lat'] !== '' ? (float) $body['geo_lat'] : null,
            'geo_lng'    => isset($body['geo_lng']) && $body['geo_lng'] !== '' ? (float) $body['geo_lng'] : null,
            'is_default' => true,
        ];
    }

    public function uploadDocument(): never
    {
        try {
            $type = trim((string) ($_POST['document_type'] ?? ($this->input()['document_type'] ?? '')));
            $type = Customer::DOC_ALIASES[$type] ?? $type;
            if ($type === '' || !isset(Customer::DOC_LABELS[$type])) {
                $this->validationError(['document_type' => 'Valid document_type is required.']);
            }
            if (empty($_FILES['file'])) {
                $this->validationError(['file' => 'Document file is required.']);
            }

            $file = $_FILES['file'];
            $path = $this->storeDocument($file, 'kyc/' . $this->customerId());
            $docId = $this->customers->addDocument($this->customerId(), $type, $path);

            $this->ok([
                'id'            => $docId,
                'document_type' => $type,
                'label'         => Customer::DOC_LABELS[$type],
                'file_url'      => $this->absoluteMedia($path),
            ], 201);
        } catch (RuntimeException $e) {
            $this->fail('UPLOAD_ERROR', $e->getMessage(), 422);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function listDocuments(): never
    {
        $docs = $this->customers->documents($this->customerId());
        $this->ok([
            'documents' => array_map(function (array $d) {
                return [
                    'id'            => (int) $d['id'],
                    'document_type' => $d['document_type'],
                    'label'         => Customer::DOC_LABELS[Customer::DOC_ALIASES[$d['document_type']] ?? $d['document_type']] ?? $d['document_type'],
                    'file_url'      => $this->absoluteMedia($d['file_url']),
                    'uploaded_at'   => $d['uploaded_at'],
                ];
            }, $docs),
        ]);
    }

    /** Re-submit KYC after rejection (resets status to pending). */
    public function resubmit(): never
    {
        try {
            $customer = $this->requireCustomer();
            if (($customer['kyc_status'] ?? '') !== 'rejected') {
                $this->fail('VALIDATION_ERROR', 'Re-submit is only allowed after a KYC rejection.', 422);
            }
            $body = $this->input();
            // Optional: update registration fields again
            if (!empty($body['business_name']) || !empty($body['owner_name']) || !empty($body['business_type'])) {
                $this->customers->submitRegistration($this->customerId(), [
                    'business_name' => trim((string) ($body['business_name'] ?? $customer['business_name'])),
                    'owner_name'    => trim((string) ($body['owner_name'] ?? $customer['owner_name'])),
                    'business_type' => trim((string) ($body['business_type'] ?? $customer['business_type'])),
                    'gst_number'    => array_key_exists('gst_number', $body) ? (trim((string) $body['gst_number']) ?: null) : $customer['gst_number'],
                    'fssai_number'  => array_key_exists('fssai_number', $body) ? (trim((string) $body['fssai_number']) ?: null) : ($customer['fssai_number'] ?? null),
                    'pan_number'    => array_key_exists('pan_number', $body) ? (trim((string) $body['pan_number']) ?: null) : ($customer['pan_number'] ?? null),
                    'email'         => array_key_exists('email', $body) ? (trim((string) $body['email']) ?: null) : $customer['email'],
                ]);
            } else {
                $ok = $this->customers->resubmitKyc($this->customerId());
                if (!$ok) {
                    $this->fail('VALIDATION_ERROR', 'Unable to re-submit KYC.', 422);
                }
            }

            NotificationService::notifyCustomer(
                $this->customerId(),
                'KYC re-submitted',
                'Your verification has been re-submitted for review.',
                'verification',
                $this->customerId()
            );

            $fresh = $this->customers->find($this->customerId());
            $this->ok([
                'message'  => 'KYC re-submitted for review.',
                'customer' => $this->customers->publicProfile($fresh ?? []),
            ]);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function verificationStatus(): never
    {
        $customer = $this->requireCustomer();
        $docs = $this->customers->documents((int) $customer['id']);
        $this->ok([
            'kyc_status'           => $customer['kyc_status'],
            'can_place_orders'     => (int) ($customer['is_blocked'] ?? 0) !== 1
                && ($customer['kyc_status'] ?? '') === 'approved',
            'kyc_rejection_reason' => $customer['kyc_rejection_reason'],
            'customer'             => array_merge($this->customers->publicProfile($customer), [
                'created_at' => $customer['created_at'] ?? null,
                'updated_at' => $customer['updated_at'] ?? null,
            ]),
            'documents'            => array_map(function (array $d) {
                $type = Customer::DOC_ALIASES[$d['document_type']] ?? $d['document_type'];
                return [
                    'id'            => (int) $d['id'],
                    'document_type' => $type,
                    'label'         => Customer::DOC_LABELS[$type] ?? $d['document_type'],
                    'file_url'      => $this->absoluteMedia($d['file_url']),
                    'uploaded_at'   => $d['uploaded_at'],
                ];
            }, $docs),
            'catalog'              => array_map(static function (string $key, string $label): array {
                return ['key' => $key, 'label' => $label];
            }, array_keys(Customer::DOC_LABELS), array_values(Customer::DOC_LABELS)),
        ]);
    }

    private function storeDocument(array $file, string $subdir): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Document upload failed.');
        }
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new RuntimeException('Document must be 5MB or smaller.');
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $map = [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'image/webp'      => 'webp',
            'application/pdf' => 'pdf',
        ];
        if (!isset($map[$mime])) {
            throw new RuntimeException('Only JPG, PNG, WEBP, or PDF files are allowed.');
        }
        $dir = PUBLIC_PATH . '/uploads/' . trim($subdir, '/');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create upload directory.');
        }
        $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $map[$mime];
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Failed to save uploaded document.');
        }
        return 'uploads/' . trim($subdir, '/') . '/' . $name;
    }

    private function normalizeBusinessType(string $raw): string
    {
        $key = strtolower(trim($raw));
        if ($key === '') {
            return '';
        }
        if (isset(self::BUSINESS_TYPE_ALIASES[$key])) {
            return self::BUSINESS_TYPE_ALIASES[$key];
        }
        $validKeys = array_column(self::BUSINESS_TYPES, 'key');
        if (in_array($key, $validKeys, true)) {
            return $key;
        }
        foreach (self::BUSINESS_TYPES as $t) {
            if (strtolower($t['label']) === $key) {
                return $t['key'];
            }
        }
        return $key;
    }
}
