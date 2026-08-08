<?php

class SupportController extends Controller
{
    public function index(): void
    {
        $filters = ['status' => trim((string) ($_GET['status'] ?? ''))];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $this->view('support/index', [
            'title'   => 'Support Tickets',
            'filters' => $filters,
            'result'  => (new SupportTicket())->paginate($filters, $page),
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
    }

    public function show(string $id): void
    {
        $model = new SupportTicket();
        $ticket = $model->find((int) $id);
        if (!$ticket) {
            flash('error', 'Ticket not found.');
            redirect('support');
        }
        $this->view('support/show', [
            'title'   => 'Ticket #' . $ticket['id'],
            'ticket'  => $ticket,
            'replies' => $model->replies((int) $id),
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
    }

    public function reply(string $id): void
    {
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($message === '') {
            flash('error', 'Reply message is required.');
            redirect('support/' . (int) $id);
        }
        $model = new SupportTicket();
        $ticket = $model->find((int) $id);
        if (!$ticket) {
            flash('error', 'Ticket not found.');
            redirect('support');
        }
        $adminId = (int) auth_user()['id'];
        $model->addReply((int) $id, $adminId, $message);
        if ($ticket['status'] === 'open') {
            $model->setStatus((int) $id, 'in_progress');
        }
        NotificationService::notifyCustomer(
            (int) $ticket['customer_id'],
            'Support reply',
            'You have a new reply on your support ticket #' . $id . '.',
            'order',
            (int) $id
        );
        flash('success', 'Reply sent.');
        redirect('support/' . (int) $id);
    }

    public function updateStatus(string $id): void
    {
        $status = trim((string) ($_POST['status'] ?? ''));
        if (!in_array($status, ['open', 'in_progress', 'closed'], true)) {
            flash('error', 'Invalid status.');
            redirect('support/' . (int) $id);
        }
        $model = new SupportTicket();
        if (!$model->find((int) $id)) {
            flash('error', 'Ticket not found.');
            redirect('support');
        }
        $model->setStatus((int) $id, $status);
        flash('success', 'Ticket status updated.');
        redirect('support/' . (int) $id);
    }
}
