<?php

class BulkEnquiryController extends Controller
{
    public function index(): void
    {
        $filters = [
            'status' => trim((string) ($_GET['status'] ?? '')),
            'q'      => trim((string) ($_GET['q'] ?? '')),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $this->view('bulk_enquiries/index', [
            'title'   => 'Bulk Enquiries',
            'filters' => $filters,
            'result'  => (new BulkEnquiry())->paginate($filters, $page),
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
    }

    public function show(string $id): void
    {
        $enquiry = (new BulkEnquiry())->find((int) $id);
        if (!$enquiry) {
            flash('error', 'Enquiry not found.');
            redirect('bulk-enquiries');
        }
        $this->view('bulk_enquiries/show', [
            'title'   => 'Bulk Enquiry #' . $enquiry['id'],
            'enquiry' => $enquiry,
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
    }

    public function updateStatus(string $id): void
    {
        $status = trim((string) ($_POST['status'] ?? ''));
        $notes = trim((string) ($_POST['admin_notes'] ?? ''));
        $model = new BulkEnquiry();
        $enquiry = $model->find((int) $id);
        if (!$enquiry) {
            flash('error', 'Enquiry not found.');
            redirect('bulk-enquiries');
        }
        if (!in_array($status, BulkEnquiry::STATUSES, true)) {
            flash('error', 'Invalid status.');
            redirect('bulk-enquiries/' . (int) $id);
        }
        $model->setStatus((int) $id, $status);
        $model->updateAdminNotes((int) $id, $notes !== '' ? $notes : null);
        flash('success', 'Enquiry updated.');
        redirect('bulk-enquiries/' . (int) $id);
    }
}
