<?php

class ServiceablePincodeController extends Controller
{
    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));
        $activeOnly = null;
        if ($status === 'active') {
            $activeOnly = true;
        } elseif ($status === 'inactive') {
            $activeOnly = false;
        }
        $this->view('serviceable_pincodes/index', [
            'title'    => 'Serviceable Pincodes',
            'rows'     => (new ServiceablePincode())->all($q !== '' ? $q : null, $activeOnly),
            'filters'  => ['q' => $q, 'status' => $status],
            'success'  => flash('success'),
            'error'    => flash('error'),
        ]);
    }

    public function store(): void
    {
        $pincode = trim((string) ($_POST['pincode'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? 'Hyderabad')) ?: 'Hyderabad';
        $state = trim((string) ($_POST['state'] ?? 'Telangana')) ?: 'Telangana';
        if (!preg_match('/^\d{6}$/', $pincode)) {
            flash('error', 'Enter a valid 6-digit pincode.');
            redirect('serviceable-pincodes');
        }
        $model = new ServiceablePincode();
        if ($model->findByPincode($pincode)) {
            flash('error', 'Pincode already exists.');
            redirect('serviceable-pincodes');
        }
        try {
            $model->create($pincode, $city, $state, true);
            flash('success', "Pincode {$pincode} added.");
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('serviceable-pincodes');
    }

    public function toggle(string $id): void
    {
        $model = new ServiceablePincode();
        $row = $model->find((int) $id);
        if (!$row) {
            flash('error', 'Pincode not found.');
            redirect('serviceable-pincodes');
        }
        $next = !((int) $row['is_active'] === 1);
        $model->setActive((int) $id, $next);
        flash('success', $next ? 'Pincode activated.' : 'Pincode deactivated.');
        redirect('serviceable-pincodes');
    }
}
