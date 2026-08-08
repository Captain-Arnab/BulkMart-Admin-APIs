<?php

class OrderController extends Controller
{
    public function index(): void
    {
        $filters = [
            'status'    => trim((string) ($_GET['status'] ?? '')),
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to'   => trim((string) ($_GET['date_to'] ?? '')),
            'q'         => trim((string) ($_GET['q'] ?? '')),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = (new Order())->paginate($filters, $page, 15);

        $this->view('orders/index', [
            'title'   => 'Orders',
            'filters' => $filters,
            'result'  => $result,
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
    }

    public function show(string $id): void
    {
        $model = new Order();
        $order = $model->find((int) $id);
        if (!$order) {
            flash('error', 'Order not found.');
            redirect('orders');
        }

        $this->view('orders/show', [
            'title'    => 'Order ' . $order['order_number'],
            'order'    => $order,
            'items'    => $model->items((int) $id),
            'log'      => $model->statusLog((int) $id),
            'managers' => $model->deliveryManagers(),
            'next'     => Order::nextStatuses($order['status']),
            'success'  => flash('success'),
            'error'    => flash('error'),
        ]);
    }

    public function updateStatus(string $id): void
    {
        $status = trim((string) ($_POST['status'] ?? ''));
        try {
            (new OrderService())->changeStatus((int) $id, $status, (int) auth_user()['id']);
            flash('success', 'Order status updated to ' . (Order::STATUS_LABELS[$status] ?? $status) . '.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('orders/' . (int) $id);
    }

    public function assign(string $id): void
    {
        $managerId = (int) ($_POST['delivery_manager_id'] ?? 0);
        try {
            if ($managerId <= 0) {
                throw new InvalidArgumentException('Select a delivery manager.');
            }
            (new OrderService())->assignDeliveryManager((int) $id, $managerId, (int) auth_user()['id']);
            flash('success', 'Delivery manager assigned.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('orders/' . (int) $id);
    }
}
