<?php

class OrderController extends Controller
{
    public function index(): void
    {
        $filters = $this->listFilters();
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

    public function export(): void
    {
        $filters = $this->listFilters();
        $rows = (new Order())->exportRows($filters);
        $headers = [
            'Order Number', 'Batch ID', 'Business Name', 'Owner Name', 'Mobile',
            'Items', 'Subtotal', 'Delivery Fee', 'Discount', 'Total', 'Payment Method',
            'Status', 'Placed At', 'Estimated Delivery', 'Delivery Manager',
            'Address', 'City', 'State', 'Pincode',
        ];
        $data = [];
        foreach ($rows as $o) {
            $address = trim(implode(', ', array_filter([
                $o['line1'] ?? '',
                $o['line2'] ?? '',
                $o['landmark'] ?? '',
            ])));
            $data[] = [
                $o['order_number'] ?? '',
                $o['batch_id'] ?? '',
                $o['business_name'] ?? '',
                $o['owner_name'] ?? '',
                $o['mobile'] ?? '',
                $o['item_count'] ?? '',
                $o['subtotal'] ?? '',
                $o['delivery_fee'] ?? '',
                $o['discount_amount'] ?? '',
                $o['total'] ?? '',
                $o['payment_method'] ?? '',
                Order::STATUS_LABELS[$o['status'] ?? ''] ?? ($o['status'] ?? ''),
                $o['placed_at'] ?? '',
                $o['estimated_delivery_date'] ?? '',
                $o['delivery_manager_name'] ?? '',
                $address,
                $o['city'] ?? '',
                $o['state'] ?? '',
                $o['pincode'] ?? '',
            ];
        }
        SpreadsheetWriter::downloadXlsx('veggiicart_orders_' . date('Y-m-d') . '.xlsx', $headers, $data);
    }

    /** @return array{status: string, date_from: string, date_to: string, q: string, pending: bool, batch_id: string} */
    private function listFilters(): array
    {
        $statusRaw = trim((string) ($_GET['status'] ?? ''));
        $pending = !empty($_GET['pending']) || $statusRaw === '__pending__';
        $status = ($pending || $statusRaw === '__pending__') ? '' : $statusRaw;
        return [
            'status'    => $status,
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to'   => trim((string) ($_GET['date_to'] ?? '')),
            'q'         => trim((string) ($_GET['q'] ?? '')),
            'pending'   => $pending,
            'batch_id'  => trim((string) ($_GET['batch_id'] ?? '')),
        ];
    }

    public function show(string $id): void
    {
        $model = new Order();
        $order = $model->find((int) $id);
        if (!$order) {
            flash('error', 'Order not found.');
            redirect('orders');
        }

        $batchOrders = [];
        if (!empty($order['batch_id'])) {
            $batchOrders = $model->listByBatchId((string) $order['batch_id']);
        }

        $this->view('orders/show', [
            'title'       => 'Order ' . $order['order_number'],
            'order'       => $order,
            'batchOrders' => $batchOrders,
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
        $eta = trim((string) ($_POST['estimated_delivery_date'] ?? ''));
        $deliveryOtp = trim((string) ($_POST['delivery_otp'] ?? ''));
        try {
            (new OrderService())->changeStatus(
                (int) $id,
                $status,
                (int) auth_user()['id'],
                null,
                $eta !== '' ? $eta : null,
                $deliveryOtp !== '' ? $deliveryOtp : null
            );
            flash('success', 'Order status updated to ' . (Order::STATUS_LABELS[$status] ?? $status) . '.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('orders/' . (int) $id);
    }

    public function setDate(string $id): void
    {
        try {
            (new OrderService())->setDeliveryDate(
                (int) $id,
                trim((string) ($_POST['estimated_delivery_date'] ?? '')),
                (int) auth_user()['id']
            );
            flash('success', 'Estimated delivery date saved.');
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
