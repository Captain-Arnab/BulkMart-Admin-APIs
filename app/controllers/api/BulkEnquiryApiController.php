<?php

/**
 * Public bulk enquiry API.
 *
 * Flutter note (app team): Product Detail should mirror website qty tiers
 * (25/50/75/100 KG chips + "Get Bulk Quote" for >100 KG) and POST here.
 * Skip fixed KG chips for non-kg units (per bunch / per dozen).
 */
class BulkEnquiryApiController extends ApiController
{
    public function create(): never
    {
        try {
            optional_api_auth();
            $body = $this->input();

            $name = trim((string) ($body['name'] ?? ''));
            $businessName = trim((string) ($body['business_name'] ?? ''));
            $mobile = preg_replace('/\s+/', '', (string) ($body['mobile'] ?? '')) ?? '';
            $productId = isset($body['product_id']) && $body['product_id'] !== '' && $body['product_id'] !== null
                ? (int) $body['product_id']
                : null;
            $requiredQty = trim((string) ($body['required_quantity'] ?? ''));
            $location = trim((string) ($body['delivery_location'] ?? ''));
            $pincode = trim((string) ($body['pincode'] ?? ''));
            $prefDate = trim((string) ($body['preferred_delivery_date'] ?? ''));
            $extra = trim((string) ($body['additional_requirement'] ?? ''));

            $fields = [];
            if ($name === '') {
                $fields['name'] = 'Name is required.';
            }
            if ($mobile === '' || !preg_match('/^[0-9+\-]{8,20}$/', $mobile)) {
                $fields['mobile'] = 'A valid mobile number is required.';
            }
            if ($requiredQty === '') {
                $fields['required_quantity'] = 'Required quantity is required.';
            }
            if ($location === '') {
                $fields['delivery_location'] = 'Delivery location is required.';
            }
            if ($pincode === '' || !preg_match('/^[0-9]{4,12}$/', $pincode)) {
                $fields['pincode'] = 'A valid pincode is required.';
            }

            $preferredDate = null;
            if ($prefDate !== '') {
                $dt = DateTime::createFromFormat('Y-m-d', $prefDate);
                if (!$dt || $dt->format('Y-m-d') !== $prefDate) {
                    $fields['preferred_delivery_date'] = 'Use YYYY-MM-DD for preferred delivery date.';
                } else {
                    $preferredDate = $prefDate;
                }
            }

            $productName = null;
            if ($productId !== null) {
                if ($productId < 1) {
                    $fields['product_id'] = 'Invalid product.';
                } else {
                    $product = (new Product())->find($productId);
                    if (!$product) {
                        $fields['product_id'] = 'Product not found.';
                    } else {
                        $productName = (string) ($product['name'] ?? '');
                    }
                }
            }

            if ($fields !== []) {
                $this->validationError($fields);
            }

            $customerId = api_customer_id();
            if ($customerId < 1) {
                $customerId = null;
            }

            $id = (new BulkEnquiry())->create([
                'customer_id'               => $customerId,
                'name'                      => $name,
                'business_name'             => $businessName !== '' ? $businessName : null,
                'mobile'                    => $mobile,
                'product_id'                => $productId,
                'required_quantity'         => $requiredQty,
                'delivery_location'         => $location,
                'pincode'                   => $pincode,
                'preferred_delivery_date'   => $preferredDate,
                'additional_requirement'    => $extra !== '' ? $extra : null,
            ]);

            $this->notifyTeam($id, $name, $businessName, $mobile, $productName, $requiredQty, $location, $pincode);

            $this->ok([
                'enquiry_id' => $id,
                'message'    => 'Thanks — our team will contact you within 24 hours.',
            ], 201);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    private function notifyTeam(
        int $id,
        string $name,
        string $businessName,
        string $mobile,
        ?string $productName,
        string $qty,
        string $location,
        string $pincode
    ): void {
        $settings = new AppSetting();
        $to = trim((string) ($settings->get('bulk_enquiry_notify_email') ?: 'veggiicart@gmail.com'));
        $phone = trim((string) ($settings->get('bulk_enquiry_notify_phone') ?: '+91 8099999086'));

        $subject = 'New bulk enquiry #' . $id . ' — VeggiiCart';
        $lines = [
            'A new bulk enquiry was submitted.',
            '',
            'Enquiry ID: #' . $id,
            'Name: ' . $name,
            'Business: ' . ($businessName !== '' ? $businessName : '—'),
            'Mobile: ' . $mobile,
            'Product: ' . ($productName ?: '—'),
            'Required quantity: ' . $qty,
            'Delivery: ' . $location . ' (' . $pincode . ')',
            '',
            'Review in Admin → Bulk Enquiries.',
            'Support contact on file: ' . $phone,
        ];
        $body = implode("\n", $lines);
        $headers = 'From: noreply@veggiicart.com' . "\r\n" .
            'Reply-To: ' . $mobile . "\r\n" .
            'Content-Type: text/plain; charset=UTF-8';

        if ($to !== '') {
            @mail($to, $subject, $body, $headers);
        }
    }
}
