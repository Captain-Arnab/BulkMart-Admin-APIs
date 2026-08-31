<?php

class OrderApiController extends ApiController
{
    private Order $orders;
    private CheckoutService $checkout;
    private Cart $cart;
    private Product $products;
    private OrderService $orderService;

    public function __construct()
    {
        $this->orders = new Order();
        $this->checkout = new CheckoutService();
        $this->cart = new Cart();
        $this->products = new Product();
        $this->orderService = new OrderService();
    }

    public function deliverySlots(): never
    {
        $days = (int) (app_config('checkout.delivery_slot_days') ?? 7);
        $days = max(1, min(14, $days));
        $slots = [];
        $windows = [
            ['label' => 'Morning (8 AM – 12 PM)', 'start' => '08:00:00', 'end' => '12:00:00'],
            ['label' => 'Afternoon (12 PM – 4 PM)', 'start' => '12:00:00', 'end' => '16:00:00'],
            ['label' => 'Evening (4 PM – 8 PM)', 'start' => '16:00:00', 'end' => '20:00:00'],
        ];

        $pdo = db();
        $stmt = $pdo->prepare(
            "SELECT * FROM delivery_slots
             WHERE is_active = 1 AND slot_date >= CURDATE()
               AND booked_count < capacity
             ORDER BY slot_date ASC, start_time ASC
             LIMIT 50"
        );
        $stmt->execute();
        $dbSlots = $stmt->fetchAll();
        if ($dbSlots) {
            foreach ($dbSlots as $s) {
                $slots[] = [
                    'id'         => (int) $s['id'],
                    'date'       => $s['slot_date'],
                    'label'      => $s['slot_label'],
                    'start_time' => substr((string) $s['start_time'], 0, 5),
                    'end_time'   => substr((string) $s['end_time'], 0, 5),
                    'available'  => (int) $s['capacity'] - (int) $s['booked_count'],
                ];
            }
            $this->ok(['slots' => $slots]);
        }

        for ($i = 1; $i <= $days; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} day"));
            foreach ($windows as $idx => $w) {
                $slots[] = [
                    'id'         => (int) (date('Ymd', strtotime($date)) . ($idx + 1)),
                    'date'       => $date,
                    'label'      => $w['label'],
                    'start_time' => substr($w['start'], 0, 5),
                    'end_time'   => substr($w['end'], 0, 5),
                    'available'  => 50,
                ];
            }
        }
        $this->ok(['slots' => $slots]);
    }

    public function place(): never
    {
        try {
            $body = $this->input();
            $addressId = (int) ($body['address_id'] ?? 0);
            if ($addressId < 1) {
                $this->validationError(['address_id' => 'address_id is required.']);
            }

            $customer = $this->requireCustomer();
            if (($customer['kyc_status'] ?? '') !== 'approved') {
                $this->fail('KYC_REQUIRED', 'Your business verification must be approved before placing orders.', 403);
            }

            $result = $this->checkout->placeCodOrder($this->customerId(), [
                'address_id' => $addressId,
                'delivery_slot_id' => isset($body['delivery_slot_id']) ? (int) $body['delivery_slot_id'] : null,
                'notes' => $body['notes'] ?? null,
            ]);

            $this->ok([
                'message' => 'Order placed successfully.',
                'order'   => $this->formatOrder($result['order'], $result['items'], []),
            ], 201);
        } catch (DomainException $e) {
            $this->fail('VALIDATION_ERROR', $e->getMessage(), 422);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function placeMultiAddress(): never
    {
        try {
            $body = $this->input();
            $customer = $this->requireCustomer();
            if (($customer['kyc_status'] ?? '') !== 'approved') {
                $this->fail('KYC_REQUIRED', 'Your business verification must be approved before placing orders.', 403);
            }

            $result = $this->checkout->placeMultiAddressCodOrder($this->customerId(), $body);
            $orders = [];
            foreach ($result['orders'] as $pack) {
                $orders[] = [
                    'order_id'     => (int) ($pack['order']['id'] ?? 0),
                    'order_number' => $pack['order']['order_number'] ?? null,
                    'address_id'   => (int) ($pack['order']['address_id'] ?? 0),
                    'total'        => (float) ($pack['order']['total'] ?? 0),
                    'order'        => $this->formatOrder($pack['order'], $pack['items'], []),
                ];
            }

            $this->ok([
                'message'  => count($orders) . ' orders placed across your selected addresses.',
                'batch_id' => $result['batch_id'],
                'orders'   => $orders,
            ], 201);
        } catch (DomainException $e) {
            $this->fail('VALIDATION_ERROR', $e->getMessage(), 422);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function index(): never
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($_GET['per_page'] ?? 15)));
        $result = $this->orders->paginateForCustomer($this->customerId(), $page, $perPage);
        $this->ok([
            'orders' => array_map(function (array $o) {
                return [
                    'id'                       => (int) $o['id'],
                    'order_number'             => $o['order_number'],
                    'status'                   => $o['status'],
                    'status_label'             => Order::STATUS_LABELS[$o['status']] ?? $o['status'],
                    'subtotal'                 => (float) $o['subtotal'],
                    'delivery_fee'             => (float) $o['delivery_fee'],
                    'discount_amount'          => (float) ($o['discount_amount'] ?? 0),
                    'coupon_code'              => $o['coupon_code'] ?? null,
                    'batch_id'                 => $o['batch_id'] ?? null,
                    'is_multi_location'        => !empty($o['batch_id']),
                    'total'                    => (float) $o['total'],
                    'payment_method'           => $o['payment_method'],
                    'estimated_delivery_date'  => $o['estimated_delivery_date'],
                    'placed_at'                => $o['placed_at'],
                    'delivered_at'             => $o['delivered_at'],
                    'item_count'               => (int) ($o['item_count'] ?? 0),
                    'can_cancel'               => Order::canCancel((string) $o['status']),
                ];
            }, $result['rows']),
            'pagination' => [
                'page'     => $result['page'],
                'per_page' => $result['per_page'],
                'total'    => $result['total'],
                'pages'    => $result['pages'],
            ],
        ]);
    }

    public function show(string $id): never
    {
        $order = $this->orders->findForCustomer((int) $id, $this->customerId());
        if (!$order) {
            $this->fail('NOT_FOUND', 'Order not found.', 404);
        }
        $items = $this->orders->items((int) $id);
        $log = $this->orders->statusLog((int) $id);
        $this->ok(['order' => $this->formatOrder($order, $items, $log)]);
    }

    public function cancel(string $id): never
    {
        try {
            $body = $this->input();
            $reason = trim((string) ($body['reason'] ?? ''));
            $this->orderService->cancelByCustomer((int) $id, $this->customerId(), $reason !== '' ? $reason : null);
            $order = $this->orders->findForCustomer((int) $id, $this->customerId());
            $items = $this->orders->items((int) $id);
            $log = $this->orders->statusLog((int) $id);
            $this->ok([
                'message' => 'Order cancelled.',
                'order'   => $this->formatOrder($order ?? [], $items, $log),
            ]);
        } catch (DomainException $e) {
            $this->fail('VALIDATION_ERROR', $e->getMessage(), 422);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function reorder(string $id): never
    {
        try {
            $order = $this->orders->findForCustomer((int) $id, $this->customerId());
            if (!$order) {
                $this->fail('NOT_FOUND', 'Order not found.', 404);
            }
            $items = $this->orders->items((int) $id);
            $added = [];
            $skipped = [];
            foreach ($items as $line) {
                $pid = isset($line['product_id']) ? (int) $line['product_id'] : 0;
                $product = $pid > 0 ? $this->products->find($pid) : null;
                if (!$product || !(int) $product['is_active']) {
                    $skipped[] = [
                        'product_id' => $pid ?: null,
                        'name'       => $line['product_name_snapshot'] ?? null,
                        'reason'     => 'unavailable',
                    ];
                    continue;
                }
                $qty = (float) $line['quantity'];
                $moq = (float) $product['moq'];
                if ($qty < $moq) {
                    $qty = $moq;
                }
                if ((int) $product['in_stock'] === 0 || (float) $product['stock'] < $qty) {
                    $skipped[] = [
                        'product_id' => (int) $product['id'],
                        'name'       => $product['name'],
                        'reason'     => 'insufficient_stock',
                    ];
                    continue;
                }
                $existing = $this->cart->findByProduct($this->customerId(), (int) $product['id']);
                $newQty = $existing ? ((float) $existing['quantity'] + $qty) : $qty;
                if ($newQty > (float) $product['stock']) {
                    $skipped[] = [
                        'product_id' => (int) $product['id'],
                        'name'       => $product['name'],
                        'reason'     => 'insufficient_stock',
                    ];
                    continue;
                }
                $cartItemId = $this->cart->upsertItem($this->customerId(), (int) $product['id'], $newQty);
                $added[] = [
                    'cart_item_id' => $cartItemId,
                    'product_id'   => (int) $product['id'],
                    'quantity'     => $newQty,
                ];
            }
            if ($added === []) {
                $this->fail('VALIDATION_ERROR', 'No items from this order could be added to cart.', 422);
            }
            $this->ok([
                'message' => 'Items added to cart.',
                'added'   => $added,
                'skipped' => $skipped,
            ]);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function invoice(string $id): never
    {
        $order = $this->orders->findForCustomer((int) $id, $this->customerId());
        if (!$order) {
            $this->fail('NOT_FOUND', 'Order not found.', 404);
        }
        $items = $this->orders->items((int) $id);
        $format = strtolower((string) ($_GET['format'] ?? 'json'));

        $invoice = [
            'invoice_number' => 'INV-' . $order['order_number'],
            'order_number'   => $order['order_number'],
            'placed_at'      => $order['placed_at'],
            'status'         => $order['status'],
            'status_label'   => Order::STATUS_LABELS[$order['status']] ?? $order['status'],
            'payment_method' => $order['payment_method'],
            'customer'       => [
                'business_name' => $order['business_name'] ?? null,
                'owner_name'    => $order['owner_name'] ?? null,
                'mobile'        => $order['mobile'] ?? null,
                'gst_number'    => $order['gst_number'] ?? null,
            ],
            'billing_address' => [
                'line1'    => $order['line1'] ?? null,
                'line2'    => $order['line2'] ?? null,
                'city'     => $order['city'] ?? null,
                'state'    => $order['state'] ?? null,
                'pincode'  => $order['pincode'] ?? null,
                'landmark' => $order['landmark'] ?? null,
            ],
            'items' => array_map(static function (array $i) {
                return [
                    'name'       => $i['product_name_snapshot'],
                    'unit'       => $i['unit_snapshot'],
                    'quantity'   => (float) $i['quantity'],
                    'unit_price' => (float) $i['unit_price_snapshot'],
                    'line_total' => (float) $i['line_total'],
                ];
            }, $items),
            'subtotal'        => (float) $order['subtotal'],
            'discount_amount' => (float) ($order['discount_amount'] ?? 0),
            'coupon_code'     => $order['coupon_code'] ?? null,
            'delivery_fee'    => (float) $order['delivery_fee'],
            'total'           => (float) $order['total'],
            'company'         => [
                'name'  => (new AppSetting())->get('company_name') ?: 'VeggiiCart',
                'phone' => (new AppSetting())->get('support_phone'),
                'email' => (new AppSetting())->get('support_email'),
            ],
        ];

        if ($format === 'pdf') {
            $pdf = (new InvoicePdfService())->build($invoice);
            $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $invoice['invoice_number']) . '.pdf';
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($pdf));
            header('Cache-Control: private, max-age=0, must-revalidate');
            echo $pdf;
            exit;
        }

        if ($format === 'html') {
            $this->renderInvoiceHtml($invoice);
        }

        $this->ok(['invoice' => $invoice]);
    }

    /** @param array<string,mixed> $invoice */
    private function renderInvoiceHtml(array $invoice): never
    {
        $esc = static fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $rows = '';
        foreach ($invoice['items'] as $item) {
            $rows .= '<tr>'
                . '<td>' . $esc($item['name']) . '</td>'
                . '<td>' . $esc($item['unit']) . '</td>'
                . '<td style="text-align:right">' . $esc(number_format($item['quantity'], 2)) . '</td>'
                . '<td style="text-align:right">₹' . $esc(number_format($item['unit_price'], 2)) . '</td>'
                . '<td style="text-align:right">₹' . $esc(number_format($item['line_total'], 2)) . '</td>'
                . '</tr>';
        }
        $companyPhone = $esc($invoice['company']['phone'] ?? '');
        $companyEmail = $esc($invoice['company']['email'] ?? '');
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>' . $esc($invoice['invoice_number']) . '</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;color:#1E1F22;margin:0;background:#f4f7f4}
.wrap{max-width:820px;margin:24px auto;background:#fff;border:1px solid #e6efe7;border-radius:16px;padding:28px;box-shadow:0 10px 30px rgba(11,92,39,.08)}
.toolbar{display:flex;gap:10px;justify-content:flex-end;margin-bottom:18px}
.toolbar button,.toolbar a{appearance:none;border:0;border-radius:10px;padding:10px 14px;font-weight:700;cursor:pointer;text-decoration:none;color:#fff;background:#12833B}
.toolbar .ghost{background:#fff;color:#12833B;border:1px solid #b9dbbf}
h1{color:#12833B;margin:0 0 8px;font-size:28px}
.meta{color:#5f6b66;line-height:1.5}
table{width:100%;border-collapse:collapse;margin-top:24px}
th,td{border-bottom:1px solid #e6efe7;padding:10px 8px;text-align:left;font-size:14px}
th{background:#F4FAF6;color:#0B5C27}
.totals{margin-top:16px;width:300px;margin-left:auto}
.totals td{border:none;padding:6px 8px}
@media print{
body{background:#fff}
.wrap{box-shadow:none;border:0;margin:0;max-width:none;border-radius:0}
.toolbar{display:none !important}
}
</style></head><body><div class="wrap">
<div class="toolbar">
<button type="button" onclick="window.print()">Print</button>
</div>
<h1>' . $esc($invoice['company']['name']) . '</h1>
<p class="meta">Invoice <strong>' . $esc($invoice['invoice_number']) . '</strong><br>
Order ' . $esc($invoice['order_number']) . ' · ' . $esc($invoice['placed_at']) . '<br>
Status: ' . $esc($invoice['status_label'] ?? $invoice['status']) . ' · Payment: ' . $esc(strtoupper((string) $invoice['payment_method'])) . '</p>
<p><strong>Bill to</strong><br>'
            . $esc($invoice['customer']['business_name']) . '<br>'
            . $esc($invoice['customer']['owner_name']) . ' · ' . $esc($invoice['customer']['mobile']) . '<br>'
            . (!empty($invoice['customer']['gst_number']) ? 'GSTIN: ' . $esc($invoice['customer']['gst_number']) . '<br>' : '')
            . $esc($invoice['billing_address']['line1'])
            . (!empty($invoice['billing_address']['line2']) ? ', ' . $esc($invoice['billing_address']['line2']) : '')
            . (!empty($invoice['billing_address']['landmark']) ? ', ' . $esc($invoice['billing_address']['landmark']) : '')
            . ', ' . $esc($invoice['billing_address']['city'])
            . (!empty($invoice['billing_address']['state']) ? ', ' . $esc($invoice['billing_address']['state']) : '')
            . ' ' . $esc($invoice['billing_address']['pincode']) . '</p>
<table><thead><tr><th>Item</th><th>Unit</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
<tbody>' . $rows . '</tbody></table>
<table class="totals">
<tr><td>Subtotal</td><td style="text-align:right">₹' . $esc(number_format($invoice['subtotal'], 2)) . '</td></tr>
<tr><td>Discount' . ($invoice['coupon_code'] ? ' (' . $esc($invoice['coupon_code']) . ')' : '') . '</td>
<td style="text-align:right">₹' . $esc(number_format($invoice['discount_amount'], 2)) . '</td></tr>
<tr><td>Delivery</td><td style="text-align:right">₹' . $esc(number_format($invoice['delivery_fee'], 2)) . '</td></tr>
<tr><td><strong>Total</strong></td><td style="text-align:right"><strong>₹'
            . $esc(number_format($invoice['total'], 2)) . '</strong></td></tr>
</table>
<p class="meta" style="margin-top:28px;font-size:12px">Thank you for ordering with '
            . $esc($invoice['company']['name']) . '.'
            . (($companyPhone !== '' || $companyEmail !== '')
                ? '<br>Support: ' . trim($companyPhone . ($companyPhone && $companyEmail ? ' | ' : '') . $companyEmail)
                : '')
            . '</p>
</div></body></html>';

        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="' . $invoice['invoice_number'] . '.html"');
        echo $html;
        exit;
    }

    /**
     * @param array<string,mixed> $order
     * @param array<int,array<string,mixed>> $items
     * @param array<int,array<string,mixed>> $log
     */
    private function formatOrder(array $order, array $items, array $log): array
    {
        $status = (string) ($order['status'] ?? '');
        return [
            'id'                      => (int) ($order['id'] ?? 0),
            'order_number'            => $order['order_number'] ?? null,
            'status'                  => $status,
            'status_label'            => Order::STATUS_LABELS[$status] ?? $status,
            'can_cancel'              => $status !== '' && Order::canCancel($status),
            'subtotal'                => (float) ($order['subtotal'] ?? 0),
            'delivery_fee'            => (float) ($order['delivery_fee'] ?? 0),
            'discount_amount'         => (float) ($order['discount_amount'] ?? 0),
            'coupon_code'             => $order['coupon_code'] ?? null,
            'batch_id'                => $order['batch_id'] ?? null,
            'is_multi_location'       => !empty($order['batch_id']),
            'total'                   => (float) ($order['total'] ?? 0),
            'payment_method'          => $order['payment_method'] ?? null,
            'estimated_delivery_date' => $order['estimated_delivery_date'] ?? null,
            'placed_at'               => $order['placed_at'] ?? null,
            'delivered_at'            => $order['delivered_at'] ?? null,
            'address'                 => [
                'label'    => $order['address_label'] ?? null,
                'line1'    => $order['line1'] ?? null,
                'line2'    => $order['line2'] ?? null,
                'city'     => $order['city'] ?? null,
                'state'    => $order['state'] ?? null,
                'pincode'  => $order['pincode'] ?? null,
                'landmark' => $order['landmark'] ?? null,
            ],
            'items' => array_map(static function (array $i) {
                return [
                    'id'          => (int) $i['id'],
                    'product_id'  => $i['product_id'] !== null && $i['product_id'] !== '' ? (int) $i['product_id'] : null,
                    'name'        => display_name($i['product_name_snapshot'] ?? ''),
                    'unit'        => $i['unit_snapshot'],
                    'quantity'    => (float) $i['quantity'],
                    'unit_price'  => (float) $i['unit_price_snapshot'],
                    'line_total'  => (float) $i['line_total'],
                ];
            }, $items),
            'tracking' => array_map(static function (array $l) {
                return [
                    'status'       => $l['status'],
                    'status_label' => Order::STATUS_LABELS[$l['status']] ?? $l['status'],
                    'note'         => $l['note'],
                    'changed_at'   => $l['changed_at'],
                ];
            }, $log),
        ];
    }
}
