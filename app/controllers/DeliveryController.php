<?php

class DeliveryController extends Controller
{
    public function index(): void
    {
        $user = auth_user();
        $tab = ($_GET['tab'] ?? 'queue') === 'history' ? 'history' : 'queue';
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $uiFilters = [
            'q'          => trim((string) ($_GET['q'] ?? '')),
            'status'     => trim((string) ($_GET['status'] ?? '')),
            'eta_from'   => trim((string) ($_GET['eta_from'] ?? '')),
            'eta_to'     => trim((string) ($_GET['eta_to'] ?? '')),
            'manager_id' => trim((string) ($_GET['manager_id'] ?? '')),
            'mine_only'  => !empty($_GET['mine_only']),
        ];

        $filters = [
            'q'        => $uiFilters['q'],
            'eta_from' => $uiFilters['eta_from'],
            'eta_to'   => $uiFilters['eta_to'],
        ];

        $queueStatuses = ['confirmed', 'delivery_date_set', 'out_for_delivery'];
        $historyStatuses = ['delivered', 'cancelled'];
        $allowedStatuses = $tab === 'queue' ? $queueStatuses : $historyStatuses;

        if ($uiFilters['status'] !== '' && in_array($uiFilters['status'], $allowedStatuses, true)) {
            $filters['status'] = $uiFilters['status'];
        } else {
            $filters['statuses'] = $allowedStatuses;
        }

        if (($user['role'] ?? '') === 'super_admin') {
            if ($uiFilters['mine_only']) {
                $filters['assigned_to'] = (int) $user['id'];
            } elseif ($uiFilters['manager_id'] !== '') {
                $filters['assigned_to'] = (int) $uiFilters['manager_id'];
            } else {
                $filters['has_assignee'] = true;
            }
        } else {
            $filters['assigned_to'] = (int) $user['id'];
        }

        $model = new Order();
        $result = $model->paginate($filters, $page, 20);

        $this->view('delivery/index', [
            'title'      => 'Delivery Management',
            'tab'        => $tab,
            'result'     => $result,
            'managers'   => ($user['role'] ?? '') === 'super_admin' ? $model->deliveryManagers() : [],
            'filters'    => $uiFilters,
            'statusOpts' => $allowedStatuses,
            'success'    => flash('success'),
            'error'      => flash('error'),
            'codWarn'    => $_SESSION['cod_warn'] ?? null,
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
}
