<?php

class CartApiController extends ApiController
{
    private Cart $cart;
    private Product $products;
    private CouponService $coupons;

    public function __construct()
    {
        $this->cart = new Cart();
        $this->products = new Product();
        $this->coupons = new CouponService();
    }

    public function show(): never
    {
        $this->ok($this->payload($this->customerId()));
    }

    public function addItem(): never
    {
        try {
            $body = $this->input();
            $productId = (int) ($body['product_id'] ?? 0);
            $qty = (float) ($body['quantity'] ?? 0);
            if ($productId < 1) {
                $this->validationError(['product_id' => 'product_id is required.']);
            }
            if ($qty <= 0) {
                $this->validationError(['quantity' => 'quantity must be greater than 0.']);
            }

            $product = $this->products->find($productId);
            if (!$product || !(int) $product['is_active']) {
                $this->fail('NOT_FOUND', 'Product not found.', 404);
            }

            $existing = $this->cart->findByProduct($this->customerId(), $productId);
            $newQty = $existing ? ((float) $existing['quantity'] + $qty) : $qty;
            if (!empty($body['replace']) || !empty($body['absolute'])) {
                $newQty = $qty;
            }

            $this->assertMoqAndStock($product, $newQty);
            $itemId = $this->cart->upsertItem($this->customerId(), $productId, $newQty);
            $payload = $this->payload($this->customerId());
            $payload['added_item_id'] = $itemId;
            $this->ok($payload, 201);
        } catch (DomainException $e) {
            $this->fail('VALIDATION_ERROR', $e->getMessage(), 422);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function updateItem(string $id): never
    {
        try {
            $body = $this->input();
            $qty = (float) ($body['quantity'] ?? 0);
            if ($qty <= 0) {
                $this->validationError(['quantity' => 'quantity must be greater than 0.']);
            }
            $item = $this->cart->findItem((int) $id, $this->customerId());
            if (!$item) {
                $this->fail('NOT_FOUND', 'Cart item not found.', 404);
            }
            $product = $this->products->find((int) $item['product_id']);
            if (!$product || !(int) $product['is_active']) {
                $this->fail('NOT_FOUND', 'Product not found.', 404);
            }
            $this->assertMoqAndStock($product, $qty);
            $this->cart->updateQuantity((int) $id, $this->customerId(), $qty);
            $this->ok($this->payload($this->customerId()));
        } catch (DomainException $e) {
            $this->fail('VALIDATION_ERROR', $e->getMessage(), 422);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function removeItem(string $id): never
    {
        $ok = $this->cart->removeItem((int) $id, $this->customerId());
        if (!$ok) {
            $this->fail('NOT_FOUND', 'Cart item not found.', 404);
        }
        $this->ok($this->payload($this->customerId()));
    }

    public function applyCoupon(): never
    {
        try {
            $body = $this->input();
            $code = trim((string) ($body['coupon_code'] ?? $body['code'] ?? ''));
            if ($code === '') {
                $this->validationError(['coupon_code' => 'coupon_code is required.']);
            }
            $offer = $this->coupons->getActiveByCode($code);
            if (!$offer) {
                $this->fail('INVALID_COUPON', 'Invalid or expired coupon code.', 422);
            }
            $items = $this->cart->itemsForCustomer($this->customerId());
            if ($items === []) {
                $this->fail('VALIDATION_ERROR', 'Cart is empty.', 422);
            }
            $this->coupons->calculate($offer, $items); // throws if ineligible
            $this->coupons->setCoupon($this->customerId(), strtoupper((string) $offer['coupon_code']));
            $this->ok($this->payload($this->customerId()));
        } catch (DomainException $e) {
            $this->fail('VALIDATION_ERROR', $e->getMessage(), 422);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function removeCoupon(): never
    {
        $this->coupons->clearCoupon($this->customerId());
        $this->ok($this->payload($this->customerId()));
    }

    /** @param array<string,mixed> $product */
    private function assertMoqAndStock(array $product, float $qty): void
    {
        $moq = (float) $product['moq'];
        $stock = (float) $product['stock'];
        if ($qty < $moq) {
            throw new DomainException(
                'Quantity must be at least MOQ (' . $moq . ') for "' . $product['name'] . '".'
            );
        }
        if ((int) $product['in_stock'] === 0 || $stock < $qty) {
            throw new DomainException(
                'Insufficient stock for "' . $product['name'] . '" (available ' . $stock . ').'
            );
        }
    }

    private function payload(int $customerId): array
    {
        $items = $this->cart->itemsForCustomer($customerId);
        $subtotal = 0.0;
        $formatted = [];
        foreach ($items as $ci) {
            $line = round((float) $ci['price'] * (float) $ci['quantity'], 2);
            $subtotal += $line;
            $formatted[] = [
                'id'            => (int) $ci['id'],
                'product_id'    => (int) $ci['product_id'],
                'name'          => display_name($ci['name'] ?? ''),
                'unit'          => $ci['unit'],
                'moq'           => (float) $ci['moq'],
                'price'         => (float) $ci['price'],
                'stock'         => (float) $ci['stock'],
                'quantity'      => (float) $ci['quantity'],
                'line_total'    => $line,
                'category_id'   => (int) ($ci['category_id'] ?? 0),
                'image_url'     => $this->absoluteMedia($ci['image_url'] ?? null),
                'category_name' => $ci['category_name'] ?? null,
                'in_stock'      => (int) $ci['in_stock'] === 1 && (float) $ci['stock'] > 0,
                'is_active'     => (int) $ci['is_active'] === 1,
            ];
        }
        $subtotal = round($subtotal, 2);
        $discount = 0.0;
        $coupon = null;
        $code = $this->coupons->getCoupon($customerId);
        if ($code && $items !== []) {
            $offer = $this->coupons->getActiveByCode($code);
            if ($offer) {
                try {
                    $calc = $this->coupons->calculate($offer, $items);
                    $discount = $calc['discount'];
                    $coupon = [
                        'code'           => strtoupper((string) $offer['coupon_code']),
                        'title'          => $offer['title'],
                        'discount_type'  => $offer['discount_type'],
                        'discount_value' => (float) $offer['discount_value'],
                        'discount'       => $discount,
                    ];
                } catch (DomainException $e) {
                    $coupon = [
                        'code'  => $code,
                        'error' => $e->getMessage(),
                    ];
                }
            } else {
                $coupon = ['code' => $code, 'error' => 'Coupon is no longer valid.'];
            }
        }

        return [
            'items'      => $formatted,
            'item_count' => count($formatted),
            'subtotal'   => $subtotal,
            'discount'   => $discount,
            'total'      => round(max(0, $subtotal - $discount), 2),
            'coupon'     => $coupon,
        ];
    }
}
