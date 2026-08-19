<?php

class CatalogApiController extends ApiController
{
    private Category $categories;
    private Product $products;
    private Banner $banners;
    private Offer $offers;
    private MarketPrice $marketPrices;

    public function __construct()
    {
        $this->categories = new Category();
        $this->products = new Product();
        $this->banners = new Banner();
        $this->offers = new Offer();
        $this->marketPrices = new MarketPrice();
    }

    public function categories(): never
    {
        $rows = $this->categories->all();
        $this->ok([
            'categories' => array_map(fn (array $c) => $this->formatCategory($c), $rows),
        ]);
    }

    public function categoryDetail(string $id): never
    {
        $c = $this->categories->find((int) $id);
        if (!$c) {
            $this->fail('NOT_FOUND', 'Category not found.', 404);
        }
        $c['product_count'] = $this->categories->productCount((int) $id);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($_GET['per_page'] ?? 20)));
        $result = $this->products->paginateWithCategory(null, (int) $id, $page, $perPage, false, 20, true, false);
        $this->ok([
            'category' => $this->formatCategory($c),
            'products' => array_map([$this, 'formatProduct'], $result['rows']),
            'pagination' => [
                'page'     => $result['page'],
                'per_page' => $result['per_page'],
                'total'    => $result['total'],
                'pages'    => $result['pages'],
            ],
        ]);
    }

    public function products(): never
    {
        $q = trim((string) ($_GET['q'] ?? $_GET['search'] ?? ''));
        $categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($_GET['per_page'] ?? 20)));
        $inStockOnly = isset($_GET['in_stock']) && in_array((string) $_GET['in_stock'], ['1', 'true'], true);

        $result = $this->products->paginateWithCategory(
            $q !== '' ? $q : null,
            $categoryId ?: null,
            $page,
            $perPage,
            false,
            20,
            true,
            $inStockOnly
        );

        $this->ok([
            'products'  => array_map([$this, 'formatProduct'], $result['rows']),
            'pagination'=> [
                'page'     => $result['page'],
                'per_page' => $result['per_page'],
                'total'    => $result['total'],
                'pages'    => $result['pages'],
            ],
        ]);
    }

    public function search(): never
    {
        $_GET['q'] = $_GET['q'] ?? $_GET['query'] ?? '';
        $this->products();
    }

    public function productDetail(string $id): never
    {
        $p = $this->products->find((int) $id);
        if (!$p || !(int) $p['is_active']) {
            $this->fail('NOT_FOUND', 'Product not found.', 404);
        }
        $this->ok(['product' => $this->formatProduct($p, true)]);
    }

    public function similar(string $id): never
    {
        $p = $this->products->find((int) $id);
        if (!$p || !(int) $p['is_active']) {
            $this->fail('NOT_FOUND', 'Product not found.', 404);
        }
        $limit = min(20, max(1, (int) ($_GET['limit'] ?? 8)));
        $rows = $this->products->similar((int) $id, $limit);
        $this->ok(['products' => array_map([$this, 'formatProduct'], $rows)]);
    }

    public function frequentlyBought(string $id): never
    {
        $p = $this->products->find((int) $id);
        if (!$p || !(int) $p['is_active']) {
            $this->fail('NOT_FOUND', 'Product not found.', 404);
        }
        $limit = min(20, max(1, (int) ($_GET['limit'] ?? 8)));
        $rows = $this->products->frequentlyBoughtTogether((int) $id, $limit);
        $this->ok(['products' => array_map([$this, 'formatProduct'], $rows)]);
    }

    public function marketPrices(): never
    {
        $rows = $this->marketPrices->todaysOverrides();
        // If no overrides today, fall back to catalog list with market_price=null
        if ($rows === []) {
            $all = $this->marketPrices->listWithToday();
            $rows = array_map(static function (array $r) {
                return [
                    'product_id'    => (int) $r['product_id'],
                    'name'          => $r['name'],
                    'unit'          => $r['unit'],
                    'catalog_price' => (float) $r['catalog_price'],
                    'market_price'  => $r['market_price'] !== null ? (float) $r['market_price'] : null,
                    'effective_date'=> $r['effective_date'] ?? date('Y-m-d'),
                ];
            }, $all);
            $this->ok([
                'date'    => date('Y-m-d'),
                'prices'  => $rows,
                'has_overrides' => false,
            ]);
        }

        $this->ok([
            'date' => date('Y-m-d'),
            'has_overrides' => true,
            'prices' => array_map(function (array $r) {
                return [
                    'product_id'    => (int) $r['product_id'],
                    'name'          => $r['name'],
                    'unit'          => $r['unit'],
                    'moq'           => isset($r['moq']) ? (float) $r['moq'] : null,
                    'catalog_price' => (float) $r['catalog_price'],
                    'market_price'  => (float) $r['market_price'],
                    'effective_date'=> $r['effective_date'],
                    'image_url'     => $this->absoluteMedia($r['image_url'] ?? null),
                    'category_name' => $r['category_name'] ?? null,
                    'in_stock'      => isset($r['in_stock']) ? ((int) $r['in_stock'] === 1) : null,
                ];
            }, $rows),
        ]);
    }

    public function banners(): never
    {
        $rows = $this->banners->activeHome();
        $this->ok([
            'banners' => array_map(function (array $b) {
                return [
                    'id'          => (int) $b['id'],
                    'title'       => $b['title'],
                    'description' => $b['description'] ?? null,
                    'image_url'   => $this->absoluteMedia($b['image_url'] ?? null),
                    'link'        => $b['link'],
                    'sort_order'  => (int) $b['sort_order'],
                ];
            }, $rows),
        ]);
    }

    public function offers(): never
    {
        $rows = $this->offers->activeList();
        $this->ok([
            'offers' => array_map(static function (array $o) {
                return [
                    'id'             => (int) $o['id'],
                    'title'          => $o['title'],
                    'discount_type'  => $o['discount_type'],
                    'discount_value' => (float) $o['discount_value'],
                    'min_qty'        => $o['min_qty'] !== null ? (float) $o['min_qty'] : null,
                    'category_id'    => $o['category_id'] !== null ? (int) $o['category_id'] : null,
                    'category_name'  => $o['category_name'] ?? null,
                    'coupon_code'    => $o['coupon_code'],
                    'valid_from'     => $o['valid_from'],
                    'valid_till'     => $o['valid_till'],
                ];
            }, $rows),
        ]);
    }

    /** @param array<string,mixed> $c */
    private function formatCategory(array $c): array
    {
        return [
            'id'            => (int) $c['id'],
            'name'          => $c['name'],
            'image_url'     => $this->absoluteMedia($c['image_url'] ?? null),
            'product_count' => (int) ($c['product_count'] ?? 0),
        ];
    }

    /** @param array<string,mixed> $p */
    private function formatProduct(array $p, bool $detail = false): array
    {
        $out = [
            'id'            => (int) $p['id'],
            'category_id'   => (int) $p['category_id'],
            'category_name' => $p['category_name'] ?? null,
            'name'          => $p['name'],
            'unit'          => $p['unit'],
            'moq'           => (float) $p['moq'],
            'price'         => (float) $p['price'],
            'stock'         => (float) $p['stock'],
            'in_stock'      => (int) $p['in_stock'] === 1 && (float) $p['stock'] > 0,
            'image_url'     => $this->absoluteMedia($p['image_url'] ?? null),
            'item_code'     => $p['item_code'] ?? null,
            'batch_no'      => $p['batch_no'] ?? null,
            'grade'         => $p['grade'] ?? null,
            'origin'        => $p['origin'] ?? null,
        ];
        if ($detail) {
            $out['description'] = $p['description'] ?? null;
            $out['images'] = $this->formatImages((int) $p['id'], $out['image_url']);
        }
        return $out;
    }

    /** @return array<int,array{url:?string,is_primary:bool,sort_order:int}> */
    private function formatImages(int $productId, ?string $fallbackUrl): array
    {
        $rows = [];
        try {
            $rows = (new ProductImage())->forProduct($productId);
        } catch (Throwable $e) {
            $rows = [];
        }
        if ($rows === []) {
            if ($fallbackUrl) {
                return [['url' => $fallbackUrl, 'is_primary' => true, 'sort_order' => 0]];
            }
            return [];
        }
        return array_map(function (array $r) {
            return [
                'url'        => $this->absoluteMedia($r['image_url'] ?? null),
                'is_primary' => (int) ($r['is_primary'] ?? 0) === 1,
                'sort_order' => (int) ($r['sort_order'] ?? 0),
            ];
        }, $rows);
    }
}
