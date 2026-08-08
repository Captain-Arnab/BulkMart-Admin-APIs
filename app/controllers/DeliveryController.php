<?php

class DeliveryController extends Controller
{
    public function index(): void
    {
        $user = auth_user();
        $tab = ($_GET['tab'] ?? 'queue') === 'history' ? 'history' : 'queue';
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $filters = [
            'assigned_to' => (int) $user['id'],
            'q'           => trim((string) ($_GET['q'] ?? '')),
        ];

        if ($user['role'] === 'super_admin' && empty($_GET['mine_only'])) {
            // Super Admin can see all delivery-assigned orders; optional filter
            if (!empty($_GET['manager_id'])) {
                $filters['assigned_to'] = (int) $_GET['manager_id'];
            } else {
                unset($filters['assigned_to']);
                // Only orders that have a DM assigned
                $filters['has_assignee'] = true;
            }
        } else {
            $filters['assigned_to'] = (int) $user['id'];
        }

        if ($tab === 'queue') {
            $filters['statuses'] = ['confirmed', 'delivery_date_set', 'out_for_delivery'];
        } else {
            $filters['statuses'] = ['delivered', 'cancelled'];
        }

        $model = new Order();
        // Extend paginate for has_assignee via temporary filter handling
        $result = $this->paginateDelivery($model, $filters, $page);

        $this->view('delivery/index', [
            'title'    => 'Delivery Management',
            'tab'      => $tab,
            'result'   => $result,
            'managers' => $user['role'] === 'super_admin' ? $model->deliveryManagers() : [],
            'filters'  => $filters,
            'success'  => flash('success'),
            'error'    => flash('error'),
            'codWarn'  => $_SESSION['cod_warn'] ?? null,
        ]);
        unset($_SESSION['cod_warn']);
    }

    public function show(string $id): void
    {
        $model = new Order();
        $order = $model->find((int) $id);
        if (!$order || !$this->canAccessOrder($order)) {
            flash('error', 'Order not found or not assigned to you.');
            redirect('delivery');
        }

        $this->view('delivery/show', [
            'title'   => 'Delivery · ' . $order['order_number'],
            'order'   => $order,
            'items'   => $model->items((int) $id),
            'log'     => $model->statusLog((int) $id),
            'success' => flash('success'),
            'error'   => flash('error'),
            'codWarn' => $_SESSION['cod_warn'] ?? null,
        ]);
        unset($_SESSION['cod_warn']);
    }

    public function setDate(string $id): void
    {
        $this->guardOrder((int) $id);
        try {
            (new OrderService())->setDeliveryDate((int) $id, trim((string) ($_POST['estimated_delivery_date'] ?? '')), (int) auth_user()['id']);
            flash('success', 'Estimated delivery date saved.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('delivery/' . (int) $id);
    }

    public function outForDelivery(string $id): void
    {
        $this->guardOrder((int) $id);
        try {
            (new OrderService())->markOutForDelivery((int) $id, (int) auth_user()['id']);
            flash('success', 'Marked out for delivery.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('delivery/' . (int) $id);
    }

    public function delivered(string $id): void
    {
        $this->guardOrder((int) $id);
        $cod = (float) ($_POST['cod_collected'] ?? 0);
        $ack = !empty($_POST['cod_mismatch_ack']);
        try {
            $result = (new OrderService())->markDelivered((int) $id, (int) auth_user()['id'], $cod, $ack);
            if (!empty($result['needs_confirm'])) {
                $_SESSION['cod_warn'] = $result;
                flash('error', $result['message']);
                redirect('delivery/' . (int) $id);
            }
            flash('success', 'Order marked as delivered.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('delivery/' . (int) $id);
    }

    private function canAccessOrder(array $order): bool
    {
        $user = auth_user();
        if (($user['role'] ?? '') === 'super_admin') {
            return true;
        }
        return (int) ($order['assigned_delivery_manager_id'] ?? 0) === (int) $user['id'];
    }

    private function guardOrder(int $id): array
    {
        $order = (new Order())->find($id);
        if (!$order || !$this->canAccessOrder($order)) {
            flash('error', 'Order not found or not assigned to you.');
            redirect('delivery');
        }
        return $order;
    }

    private function paginateDelivery(Order $model, array $filters, int $page): array
    {
        // Support has_assignee by wrapping query filters
        if (!empty($filters['has_assignee'])) {
            // Use assigned_to IS NOT NULL via custom statuses path — piggyback on model by SQL patch
            return $this->paginateWithAssigneeRequired($filters, $page);
        }
        return $model->paginate($filters, $page, 20);
    }

    private function paginateWithAssigneeRequired(array $filters, int $page, int $perPage = 20): array
    {
        $pdo = db();
        $where = ['o.assigned_delivery_manager_id IS NOT NULL'];
        $params = [];
        if (!empty($filters['statuses'])) {
            $in = implode(',', array_fill(0, count($filters['statuses']), '?'));
            $where[] = "o.status IN ($in)";
            foreach ($filters['statuses'] as $s) {
                $params[] = $s;
            }
        }
        if (!empty($filters['q'])) {
            $where[] = '(o.order_number LIKE ? OR c.business_name LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        $sqlWhere = implode(' AND ', $where);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders o INNER JOIN customers c ON c.id = o.customer_id WHERE $sqlWhere");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;
        $stmt = $pdo->prepare(
            "SELECT o.*, c.business_name, c.owner_name, c.mobile, dm.name AS delivery_manager_name,
                    (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count,
                    a.line1, a.city, a.pincode
             FROM orders o
             INNER JOIN customers c ON c.id = o.customer_id
             INNER JOIN addresses a ON a.id = o.address_id
             LEFT JOIN admin_users dm ON dm.id = o.assigned_delivery_manager_id
             WHERE $sqlWhere
             ORDER BY o.placed_at DESC
             LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return [
            'rows'     => $stmt->fetchAll(),
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => $pages,
        ];
    }
}
