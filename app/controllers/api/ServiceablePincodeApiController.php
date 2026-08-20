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
        ]);
    }
}
