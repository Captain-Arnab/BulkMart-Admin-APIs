<?php

class AddressApiController extends ApiController
{
    private Address $addresses;

    public function __construct()
    {
        $this->addresses = new Address();
    }

    public function index(): never
    {
        $rows = $this->addresses->listForCustomer($this->customerId());
        $this->ok(['addresses' => array_map([$this, 'format'], $rows)]);
    }

    public function store(): never
    {
        try {
            $body = $this->input();
            $fields = $this->validateAddress($body);
            if ($fields !== []) {
                $this->validationError($fields);
            }
            $id = $this->addresses->create($this->customerId(), $this->normalize($body));
            $row = $this->addresses->findForCustomer($id, $this->customerId());
            $this->ok(['address' => $this->format($row ?? [])], 201);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function update(string $id): never
    {
        try {
            $body = $this->input();
            $fields = $this->validateAddress($body, false);
            if ($fields !== []) {
                $this->validationError($fields);
            }
            $ok = $this->addresses->update((int) $id, $this->customerId(), $this->normalize($body));
            if (!$ok) {
                $this->fail('NOT_FOUND', 'Address not found.', 404);
            }
            $row = $this->addresses->findForCustomer((int) $id, $this->customerId());
            $this->ok(['address' => $this->format($row ?? [])]);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function destroy(string $id): never
    {
        $ok = $this->addresses->delete((int) $id, $this->customerId());
        if (!$ok) {
            $this->fail('NOT_FOUND', 'Address not found.', 404);
        }
        $this->ok(['message' => 'Address deleted.']);
    }

    public function setDefault(string $id): never
    {
        $ok = $this->addresses->setDefault((int) $id, $this->customerId());
        if (!$ok) {
            $this->fail('NOT_FOUND', 'Address not found.', 404);
        }
        $row = $this->addresses->findForCustomer((int) $id, $this->customerId());
        $this->ok(['address' => $this->format($row ?? [])]);
    }

    /** @param array<string,mixed> $body */
    private function validateAddress(array $body, bool $requireAll = true): array
    {
        $fields = [];
        $required = ['line1', 'city', 'state', 'pincode'];
        foreach ($required as $k) {
            if ($requireAll || array_key_exists($k, $body)) {
                if (trim((string) ($body[$k] ?? '')) === '') {
                    $fields[$k] = ucfirst($k) . ' is required.';
                }
            }
        }
        if (isset($body['pincode']) && $body['pincode'] !== '' && !preg_match('/^\d{6}$/', (string) $body['pincode'])) {
            $fields['pincode'] = 'Enter a valid 6-digit pincode.';
        }
        return $fields;
    }

    /** @param array<string,mixed> $body @return array<string,mixed> */
    private function normalize(array $body): array
    {
        return [
            'label'      => trim((string) ($body['label'] ?? 'Shop')) ?: 'Shop',
            'line1'      => trim((string) ($body['line1'] ?? '')),
            'line2'      => trim((string) ($body['line2'] ?? '')) ?: null,
            'city'       => trim((string) ($body['city'] ?? '')),
            'state'      => trim((string) ($body['state'] ?? '')),
            'pincode'    => trim((string) ($body['pincode'] ?? '')),
            'landmark'   => trim((string) ($body['landmark'] ?? '')) ?: null,
            'geo_lat'    => isset($body['geo_lat']) && $body['geo_lat'] !== '' ? (float) $body['geo_lat'] : null,
            'geo_lng'    => isset($body['geo_lng']) && $body['geo_lng'] !== '' ? (float) $body['geo_lng'] : null,
            'is_default' => !empty($body['is_default']),
        ];
    }

    /** @param array<string,mixed> $row */
    private function format(array $row): array
    {
        return [
            'id'         => (int) ($row['id'] ?? 0),
            'label'      => $row['label'] ?? null,
            'line1'      => $row['line1'] ?? null,
            'line2'      => $row['line2'] ?? null,
            'city'       => $row['city'] ?? null,
            'state'      => $row['state'] ?? null,
            'pincode'    => $row['pincode'] ?? null,
            'landmark'   => $row['landmark'] ?? null,
            'geo_lat'    => isset($row['geo_lat']) ? (float) $row['geo_lat'] : null,
            'geo_lng'    => isset($row['geo_lng']) ? (float) $row['geo_lng'] : null,
            'is_default' => (int) ($row['is_default'] ?? 0) === 1,
            'created_at' => $row['created_at'] ?? null,
        ];
    }
}
