<?php

class WishlistApiController extends ApiController
{
    private Wishlist $wishlist;
    private Product $products;
    private Cart $cart;

    public function __construct()
    {
        $this->wishlist = new Wishlist();
        $this->products = new Product();
        $this->cart = new Cart();
    }

    public function index(): never
    {
        $rows = $this->wishlist->listForCustomer($this->customerId());
        $this->ok([
            'items' => array_map(fn (array $r) => $this->format($r), $rows),
            'count' => count($rows),
        ]);
    }

    public function add(): never
    {
        $body = $this->input();
        $productId = (int) ($body['product_id'] ?? 0);
        if ($productId < 1) {
            $this->validationError(['product_id' => 'product_id is required.']);
        }
        $product = $this->products->find($productId);
        if (!$product || !(int) $product['is_active']) {
            $this->fail('NOT_FOUND', 'Product not found.', 404);
        }
        $id = $this->wishlist->add($this->customerId(), $productId);
        $this->ok(['id' => $id, 'product_id' => $productId, 'message' => 'Added to wishlist.'], 201);
    }

    public function remove(string $id): never
    {
        // Allow remove by wishlist id OR product_id via query
        $ok = $this->wishlist->remove((int) $id, $this->customerId());
        if (!$ok) {
            $ok = $this->wishlist->removeByProduct($this->customerId(), (int) $id);
        }
        if (!$ok) {
            $this->fail('NOT_FOUND', 'Wishlist item not found.', 404);
        }
        $this->ok(['message' => 'Removed from wishlist.']);
    }

    public function moveToCart(string $id): never
    {
        try {
            $item = $this->wishlist->findItem((int) $id, $this->customerId());
            if (!$item) {
                // treat id as product_id
                $item = $this->wishlist->findByProduct($this->customerId(), (int) $id);
            }
            if (!$item) {
                $this->fail('NOT_FOUND', 'Wishlist item not found.', 404);
            }
            $product = $this->products->find((int) $item['product_id']);
            if (!$product || !(int) $product['is_active']) {
                $this->fail('NOT_FOUND', 'Product not found.', 404);
            }
            $qty = max((float) $product['moq'], 1.0);
            if ((int) $product['in_stock'] === 0 || (float) $product['stock'] < $qty) {
                $this->fail('VALIDATION_ERROR', 'Insufficient stock to move to cart.', 422);
            }
            $existing = $this->cart->findByProduct($this->customerId(), (int) $product['id']);
            $newQty = $existing ? max((float) $existing['quantity'], $qty) : $qty;
            if ($newQty > (float) $product['stock']) {
                $this->fail('VALIDATION_ERROR', 'Insufficient stock to move to cart.', 422);
            }
            $cartItemId = $this->cart->upsertItem($this->customerId(), (int) $product['id'], $newQty);
            $this->wishlist->remove((int) $item['id'], $this->customerId());
            $this->ok([
                'message'       => 'Moved to cart.',
                'cart_item_id'  => $cartItemId,
                'product_id'    => (int) $product['id'],
                'quantity'      => $newQty,
            ]);
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /** @param array<string,mixed> $r */
    private function format(array $r): array
    {
        return [
            'id'            => (int) $r['id'],
            'product_id'    => (int) $r['product_id'],
            'name'          => $r['name'],
            'unit'          => $r['unit'],
            'moq'           => (float) $r['moq'],
            'price'         => (float) $r['price'],
            'stock'         => (float) $r['stock'],
            'in_stock'      => (int) $r['in_stock'] === 1 && (float) $r['stock'] > 0,
            'is_active'     => (int) $r['is_active'] === 1,
            'image_url'     => $this->absoluteMedia($r['image_url'] ?? null),
            'category_id'   => (int) ($r['category_id'] ?? 0),
            'category_name' => $r['category_name'] ?? null,
            'created_at'    => $r['created_at'] ?? null,
        ];
    }
}
