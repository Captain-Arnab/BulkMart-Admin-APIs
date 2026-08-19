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
                if ((bool) (app_config('checkout.require_kyc_approved') ?? false)) {
                    $this->fail('KYC_REQUIRED', 'Your business verification must be approved before placing orders.', 403);
                }
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

        if (in_array($format, ['html', 'pdf'], true)) {
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
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>'
            . $esc($invoice['invoice_number']) . '</title>
<style>
body{font-family:Arial,sans-serif;color:#1E1F22;margin:32px}
h1{color:#12833B;margin:0 0 8px}
table{width:100%;border-collapse:collapse;margin-top:24px}
th,td{border-bottom:1px solid #ddd;padding:8px;text-align:left}
th{background:#F4FAF6}
.totals{margin-top:16px;width:280px;margin-left:auto}
.totals td{border:none;padding:4px 8px}
</style></head><body>
<h1>' . $esc($invoice['company']['name']) . '</h1>
<p>Invoice <strong>' . $esc($invoice['invoice_number']) . '</strong><br>
Order ' . $esc($invoice['order_number']) . ' · ' . $esc($invoice['placed_at']) . '</p>
<p><strong>Bill to</strong><br>'
            . $esc($invoice['customer']['business_name']) . '<br>'
            . $esc($invoice['customer']['owner_name']) . ' · ' . $esc($invoice['customer']['mobile']) . '<br>'
            . $esc($invoice['billing_address']['line1']) . ', '
            . $esc($invoice['billing_address']['city']) . ' '
            . $esc($invoice['billing_address']['pincode']) . '</p>
<table><thead><tr><th>Item</th><th>Unit</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
<tbody>' . $rows . '</tbody></table>
<table class="totals">
<tr><td>Subtotal</td><td style="text-align:right">₹' . $esc(number_format($invoice['subtotal'], 2)) . '</td></tr>
<tr><td>Discount' . ($invoice['coupon_code'] ? ' (' . $esc($invoice['coupon_code']) . ')' : '') . '</td>
<td style="text-align:right">₹' . $esc(number_format($invoice['discount_amount'], 2)) . '</td></tr>
<tr><td>Delivery</td><td style="text-align:right">₹' . $esc(number_format($invoice['delivery_fee'], 2)) . '</td></tr>
<tr><td><strong>Total (COD)</strong></td><td style="text-align:right"><strong>₹'
            . $esc(number_format($invoice['total'], 2)) . '</strong></td></tr>
</table>
<p style="margin-top:32px;color:#666;font-size:12px">Print this page or Save as PDF from your browser.</p>
</body></html>';

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
                    'name'        => $i['product_name_snapshot'],
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
