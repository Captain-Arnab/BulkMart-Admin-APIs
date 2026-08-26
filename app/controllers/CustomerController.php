<?php

class CustomerController extends Controller
{
    public function index(): void
    {
        $filters = [
            'kyc_status' => trim((string) ($_GET['kyc_status'] ?? '')),
            'q'          => trim((string) ($_GET['q'] ?? '')),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $this->view('customers/index', [
            'title'   => 'Customers',
            'filters' => $filters,
            'result'  => (new Customer())->paginate($filters, $page),
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
    }

    public function show(string $id): void
    {
        $model = new Customer();
        $customer = $model->find((int) $id);
        if (!$customer) {
            flash('error', 'Customer not found.');
            redirect('customers');
        }
        $this->view('customers/show', [
            'title'     => $customer['business_name'],
            'customer'  => $customer,
            'documents' => $model->documents((int) $id),
            'addresses' => $model->addresses((int) $id),
            'orders'    => $model->orders((int) $id),
            'success'   => flash('success'),
            'error'     => flash('error'),
        ]);
    }

    public function edit(string $id): void
    {
        $model = new Customer();
        $customer = $model->find((int) $id);
        if (!$customer) {
            flash('error', 'Customer not found.');
            redirect('customers');
        }
        $this->view('customers/form', [
            'title'          => 'Edit Customer',
            'customer'       => $customer,
            'businessTypes'  => BusinessApiController::BUSINESS_TYPES,
            'error'          => flash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $model = new Customer();
        $customer = $model->find((int) $id);
        if (!$customer) {
            flash('error', 'Customer not found.');
            redirect('customers');
        }
        try {
            $data = $this->validatedProfile((int) $id);
            $model->updateByAdmin((int) $id, $data);
            flash('success', 'Customer profile updated.');
            redirect('customers/' . (int) $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('customers/' . (int) $id . '/edit');
        }
    }

    public function approve(string $id): void
    {
        $model = new Customer();
        $customer = $model->find((int) $id);
        if (!$customer) {
            flash('error', 'Customer not found.');
            redirect('customers');
        }
        $model->updateKyc((int) $id, 'approved', null);
        NotificationService::notifyCustomer(
            (int) $id,
            'KYC approved',
            'Your business verification has been approved. You can now place wholesale orders on VeggiiCart.',
            'verification',
            (int) $id
        );
        flash('success', 'Customer KYC approved.');
        redirect('customers/' . (int) $id);
    }

    public function reject(string $id): void
    {
        $reason = trim((string) ($_POST['kyc_rejection_reason'] ?? ''));
        if ($reason === '') {
            flash('error', 'Rejection reason is required.');
            redirect('customers/' . (int) $id);
        }
        $model = new Customer();
        $customer = $model->find((int) $id);
        if (!$customer) {
            flash('error', 'Customer not found.');
            redirect('customers');
        }
        $model->updateKyc((int) $id, 'rejected', $reason);
        NotificationService::notifyCustomer(
            (int) $id,
            'KYC rejected',
            'Your verification was rejected: ' . $reason,
            'verification',
            (int) $id
        );
        flash('success', 'Customer KYC rejected.');
        redirect('customers/' . (int) $id);
    }

    public function toggleBlock(string $id): void
    {
        $model = new Customer();
        $customer = $model->find((int) $id);
        if (!$customer) {
            flash('error', 'Customer not found.');
            redirect('customers');
        }
        $blocked = !((int) ($customer['is_blocked'] ?? 0) === 1);
        $model->setBlocked((int) $id, $blocked);
        flash('success', $blocked ? 'Customer blocked.' : 'Customer unblocked.');
        redirect('customers/' . (int) $id);
    }

    /** @return array<string,?string> */
    private function validatedProfile(int $customerId): array
    {
        $ownerName = trim((string) ($_POST['owner_name'] ?? ''));
        $businessName = trim((string) ($_POST['business_name'] ?? ''));
        $businessType = trim((string) ($_POST['business_type'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $gst = trim((string) ($_POST['gst_number'] ?? ''));
        $fssai = trim((string) ($_POST['fssai_number'] ?? ''));
        $pan = trim((string) ($_POST['pan_number'] ?? ''));

        if ($ownerName === '') {
            throw new InvalidArgumentException('Owner name is required.');
        }
        if ($businessName === '') {
            throw new InvalidArgumentException('Business name is required.');
        }
        if ($businessType === '') {
            throw new InvalidArgumentException('Business type is required.');
        }

        try {
            $mobile = OtpService::normalizeMobile(trim((string) ($_POST['mobile'] ?? '')));
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException('Enter a valid 10-digit mobile number.');
        }

        if ((new Customer())->mobileTaken($mobile, $customerId)) {
            throw new InvalidArgumentException('This mobile number is already registered to another customer.');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Enter a valid email address or leave it blank.');
        }
        if ($email !== '' && (new Customer())->emailTaken($email, $customerId)) {
            throw new InvalidArgumentException('This email is already registered to another customer.');
        }

        return [
            'mobile'         => $mobile,
            'email'          => $email !== '' ? $email : null,
            'owner_name'     => $ownerName,
            'business_name'  => $businessName,
            'business_type'  => $businessType,
            'gst_number'     => $gst !== '' ? $gst : null,
            'fssai_number'   => $fssai !== '' ? $fssai : null,
            'pan_number'     => $pan !== '' ? $pan : null,
        ];
    }
}
