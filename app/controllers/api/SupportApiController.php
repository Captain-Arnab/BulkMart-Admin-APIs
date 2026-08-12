<?php

class SupportApiController extends ApiController
{
    private SupportTicket $tickets;
    private Faq $faqs;
    private Order $orders;

    public function __construct()
    {
        $this->tickets = new SupportTicket();
        $this->faqs = new Faq();
        $this->orders = new Order();
    }

    public function faqs(): never
    {
        $q = trim((string) ($_GET['q'] ?? $_GET['search'] ?? ''));
        $category = trim((string) ($_GET['category'] ?? ''));
        $rows = $this->faqs->search($q !== '' ? $q : null, $category !== '' ? $category : null);
        $this->ok([
            'faqs' => array_map(static function (array $f) {
                return [
                    'id'         => (int) $f['id'],
                    'question'   => $f['question'],
                    'answer'     => $f['answer'],
                    'category'   => $f['category'],
                    'sort_order' => (int) $f['sort_order'],
                ];
            }, $rows),
        ]);
    }

    public function createTicket(): never
    {
        try {
            $body = $this->input();
            $subject = trim((string) ($body['subject_type'] ?? $body['subject'] ?? ''));
            $description = trim((string) ($body['description'] ?? ''));
            $fields = [];
            if ($subject === '') {
                $fields['subject_type'] = 'Subject is required.';
            }
            if ($description === '') {
                $fields['description'] = 'Description is required.';
            }
            $relatedOrderId = isset($body['related_order_id']) ? (int) $body['related_order_id'] : null;
            if ($relatedOrderId) {
                $order = $this->orders->findForCustomer($relatedOrderId, $this->customerId());
                if (!$order) {
                    $fields['related_order_id'] = 'Order not found.';
                }
            }
            if ($fields !== []) {
                $this->validationError($fields);
            }

            $id = $this->tickets->create($this->customerId(), $subject, $description, $relatedOrderId ?: null);
            NotificationService::notifyCustomer(
                $this->customerId(),
                'Support ticket created',
                "Ticket #{$id} has been submitted. We'll get back to you soon.",
                'verification',
                $id
            );
            $ticket = $this->tickets->findForCustomer($id, $this->customerId());
            $this->ok(['ticket' => $this->formatTicket($ticket ?? [], [])], 201);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function myTickets(): never
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($_GET['per_page'] ?? 15)));
        $result = $this->tickets->paginateForCustomer($this->customerId(), $page, $perPage);
        $this->ok([
            'tickets' => array_map(fn (array $t) => $this->formatTicket($t, []), $result['rows']),
            'pagination' => [
                'page'     => $result['page'],
                'per_page' => $result['per_page'],
                'total'    => $result['total'],
                'pages'    => $result['pages'],
            ],
        ]);
    }

    public function ticketDetail(string $id): never
    {
        $ticket = $this->tickets->findForCustomer((int) $id, $this->customerId());
        if (!$ticket) {
            $this->fail('NOT_FOUND', 'Ticket not found.', 404);
        }
        $replies = $this->tickets->replies((int) $id);
        $this->ok(['ticket' => $this->formatTicket($ticket, $replies)]);
    }

    /**
     * @param array<string,mixed> $t
     * @param array<int,array<string,mixed>> $replies
     */
    private function formatTicket(array $t, array $replies): array
    {
        return [
            'id'               => (int) ($t['id'] ?? 0),
            'subject_type'     => $t['subject_type'] ?? null,
            'description'      => $t['description'] ?? null,
            'status'           => $t['status'] ?? null,
            'related_order_id' => isset($t['related_order_id']) && $t['related_order_id'] !== null
                ? (int) $t['related_order_id'] : null,
            'order_number'     => $t['order_number'] ?? null,
            'created_at'       => $t['created_at'] ?? null,
            'updated_at'       => $t['updated_at'] ?? null,
            'replies'          => array_map(static function (array $r) {
                return [
                    'id'         => (int) $r['id'],
                    'message'    => $r['message'],
                    'from'       => $r['admin_user_id'] ? 'support' : 'customer',
                    'admin_name' => $r['admin_name'] ?? null,
                    'created_at' => $r['created_at'],
                ];
            }, $replies),
        ];
    }
}
