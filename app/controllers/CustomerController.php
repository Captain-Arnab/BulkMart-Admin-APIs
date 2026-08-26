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
            'documents'      => $model->documents((int) $id),
            'businessTypes'  => BusinessApiController::BUSINESS_TYPES,
            'error'          => flash('error'),
            'success'        => flash('success'),
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

    public function uploadDocument(string $id): void
    {
        $customerId = (int) $id;
        $model = new Customer();
        if (!$model->find($customerId)) {
            flash('error', 'Customer not found.');
            redirect('customers');
        }
        try {
            $type = Customer::normalizeDocumentType(trim((string) ($_POST['document_type'] ?? '')));
            if (!Customer::isUploadableDocumentType($type)) {
                throw new InvalidArgumentException('Select a valid document type.');
            }
            if (empty($_FILES['file']['name'])) {
                throw new InvalidArgumentException('Choose a document file to upload.');
            }
            $path = $this->storeKycDocument($_FILES['file'], $customerId);
            $model->addDocument($customerId, $type, $path);
            flash('success', 'Document uploaded.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('customers/' . $customerId . '/edit');
    }

    public function replaceDocument(string $id, string $docId): void
    {
        $customerId = (int) $id;
        $documentId = (int) $docId;
        $model = new Customer();
        $doc = $model->findDocument($documentId, $customerId);
        if (!$doc) {
            flash('error', 'Document not found.');
            redirect('customers/' . $customerId . '/edit');
        }
        try {
            if (empty($_FILES['file']['name'])) {
                throw new InvalidArgumentException('Choose a replacement file.');
            }
            $path = $this->storeKycDocument($_FILES['file'], $customerId);
            $model->updateDocumentFile($documentId, $customerId, $path);
            $this->unlinkDocumentFile((string) ($doc['file_url'] ?? ''));
            flash('success', 'Document replaced.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('customers/' . $customerId . '/edit');
    }

    public function deleteDocument(string $id, string $docId): void
    {
        $customerId = (int) $id;
        $documentId = (int) $docId;
        $model = new Customer();
        $oldPath = $model->deleteDocument($documentId, $customerId);
        if ($oldPath === null) {
            flash('error', 'Document not found.');
        } else {
            $this->unlinkDocumentFile($oldPath);
            flash('success', 'Document deleted.');
        }
        redirect('customers/' . $customerId . '/edit');
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

    private function storeKycDocument(array $file, int $customerId): string
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
        $subdir = 'kyc/' . $customerId;
        $dir = PUBLIC_PATH . '/uploads/' . $subdir;
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create upload directory.');
        }
        $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $map[$mime];
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Failed to save uploaded document.');
        }
        return 'uploads/' . $subdir . '/' . $name;
    }

    private function unlinkDocumentFile(string $path): void
    {
        if ($path === '' || preg_match('#^https?://#i', $path)) {
            return;
        }
        $full = PUBLIC_PATH . '/' . ltrim($path, '/');
        if (is_file($full)) {
            @unlink($full);
        }
    }
}
