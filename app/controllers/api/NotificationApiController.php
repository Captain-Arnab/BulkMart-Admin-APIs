<?php

class NotificationApiController extends ApiController
{
    private Notification $notifications;

    public function __construct()
    {
        $this->notifications = new Notification();
    }

    public function index(): never
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($_GET['per_page'] ?? 20)));
        $unreadOnly = isset($_GET['unread']) && in_array((string) $_GET['unread'], ['1', 'true'], true);
        $result = $this->notifications->paginateForCustomer(
            $this->customerId(),
            $page,
            $perPage,
            $unreadOnly ? true : null
        );
        $this->ok([
            'notifications' => array_map(static function (array $n) {
                return [
                    'id'         => (int) $n['id'],
                    'title'      => $n['title'],
                    'body'       => $n['body'],
                    'type'       => $n['type'],
                    'related_id' => $n['related_id'] !== null ? (int) $n['related_id'] : null,
                    'is_read'    => (int) $n['is_read'] === 1,
                    'created_at' => $n['created_at'],
                ];
            }, $result['rows']),
            'unread_count' => $result['unread'],
            'pagination'   => [
                'page'     => $result['page'],
                'per_page' => $result['per_page'],
                'total'    => $result['total'],
                'pages'    => $result['pages'],
            ],
        ]);
    }

    public function markRead(string $id): never
    {
        $row = $this->notifications->findForCustomer((int) $id, $this->customerId());
        if (!$row) {
            $this->fail('NOT_FOUND', 'Notification not found.', 404);
        }
        $this->notifications->markRead((int) $id, $this->customerId());
        $this->ok(['message' => 'Marked as read.', 'id' => (int) $id]);
    }

    public function markAllRead(): never
    {
        $this->notifications->markAllRead($this->customerId());
        $this->ok(['message' => 'All notifications marked as read.']);
    }
}
