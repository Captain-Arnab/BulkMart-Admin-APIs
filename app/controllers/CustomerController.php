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
}
