<?php

class ServiceablePincodeApiController extends ApiController
{
    public function check(): never
    {
        $pincode = trim((string) ($_GET['pincode'] ?? ''));
        $result = (new ServiceablePincode())->check($pincode);
        $this->ok([
            'serviceable' => (bool) $result['serviceable'],
            'city'        => $result['city'],
            'state'       => $result['state'] ?? null,
        ]);
    }

    /** Public list of active serviceable pincodes (for address form dropdown). */
    public function index(): never
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $rows = (new ServiceablePincode())->all($q !== '' ? $q : null, true);
        $this->ok([
            'pincodes' => array_map(static function (array $row): array {
                return [
                    'pincode' => (string) $row['pincode'],
                    'city'    => (string) ($row['city'] ?? 'Hyderabad'),
                    'state'   => (string) ($row['state'] ?? 'Telangana'),
                ];
            }, $rows),
        ]);
    }
}
