<?php

/**
 * Front controller — all requests dispatch through here.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/middleware/auth.php';
require_once dirname(__DIR__) . '/app/middleware/rbac.php';
require_once dirname(__DIR__) . '/app/middleware/api_auth.php';
require_once dirname(__DIR__) . '/app/core/Router.php';
require_once dirname(__DIR__) . '/app/core/Controller.php';
require_once dirname(__DIR__) . '/app/core/Model.php';

spl_autoload_register(static function (string $class): void {
    $map = [
        APP_PATH . '/controllers/api/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
        APP_PATH . '/services/' . $class . '.php',
        APP_PATH . '/core/' . $class . '.php',
    ];
    foreach ($map as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = app_base_url();
$relPath = $uriPath;
if ($base !== '' && str_starts_with($relPath, $base)) {
    $relPath = substr($relPath, strlen($base)) ?: '/';
}
$apiAt = strpos($relPath, '/api/');
if ($apiAt === false) {
    $apiAt = strpos($uriPath, '/api/');
    if ($apiAt !== false) {
        $relPath = substr($uriPath, $apiAt);
    }
}
$isApi = str_starts_with('/' . trim($relPath, '/'), '/api');

if ($isApi) {
    api_apply_cors();
} else {
    auth_start_session();
}

$router = new Router();

// ---------------------------------------------------------------------------
// REST API v1 (JSON + JWT) — customer / Flutter / website
// ---------------------------------------------------------------------------
$apiAuth = ['require_api_auth'];

$router->post('/api/v1/auth/send-otp', [AuthApiController::class, 'sendOtp']);
$router->post('/api/v1/auth/resend-otp', [AuthApiController::class, 'resendOtp']);
$router->post('/api/v1/auth/verify-otp', [AuthApiController::class, 'verifyOtp']);
$router->post('/api/v1/auth/email-login', [AuthApiController::class, 'emailLogin']);
$router->post('/api/v1/auth/refresh-token', [AuthApiController::class, 'refreshToken']);
$router->post('/api/v1/auth/logout', [AuthApiController::class, 'logout']); // refresh_token in body; Bearer optional

$router->get('/api/v1/business-types', [BusinessApiController::class, 'businessTypes']);
$router->post('/api/v1/business/register', [BusinessApiController::class, 'register'], $apiAuth);
$router->post('/api/v1/business/documents', [BusinessApiController::class, 'uploadDocument'], $apiAuth);
$router->get('/api/v1/business/documents', [BusinessApiController::class, 'listDocuments'], $apiAuth);
$router->post('/api/v1/business/resubmit', [BusinessApiController::class, 'resubmit'], $apiAuth);
$router->get('/api/v1/business/verification-status', [BusinessApiController::class, 'verificationStatus'], $apiAuth);

$router->get('/api/v1/profile', [ProfileApiController::class, 'show'], $apiAuth);
$router->put('/api/v1/profile', [ProfileApiController::class, 'update'], $apiAuth);
$router->post('/api/v1/profile', [ProfileApiController::class, 'update'], $apiAuth); // clients without PUT
$router->post('/api/v1/profile/avatar', [ProfileApiController::class, 'uploadAvatar'], $apiAuth);
$router->delete('/api/v1/profile/avatar', [ProfileApiController::class, 'removeAvatar'], $apiAuth);

$router->get('/api/v1/addresses', [AddressApiController::class, 'index'], $apiAuth);
$router->post('/api/v1/addresses', [AddressApiController::class, 'store'], $apiAuth);
$router->put('/api/v1/addresses/{id}', [AddressApiController::class, 'update'], $apiAuth);
$router->post('/api/v1/addresses/{id}', [AddressApiController::class, 'update'], $apiAuth);
$router->delete('/api/v1/addresses/{id}', [AddressApiController::class, 'destroy'], $apiAuth);
$router->post('/api/v1/addresses/{id}/default', [AddressApiController::class, 'setDefault'], $apiAuth);

$router->get('/api/v1/categories', [CatalogApiController::class, 'categories']);
$router->get('/api/v1/categories/{id}', [CatalogApiController::class, 'categoryDetail']);
$router->get('/api/v1/products', [CatalogApiController::class, 'products']);
$router->get('/api/v1/products/search', [CatalogApiController::class, 'search']);
$router->get('/api/v1/products/market-prices', [CatalogApiController::class, 'marketPrices']);
$router->get('/api/v1/products/{id}/similar', [CatalogApiController::class, 'similar']);
$router->get('/api/v1/products/{id}/frequently-bought-together', [CatalogApiController::class, 'frequentlyBought']);
$router->get('/api/v1/products/{id}', [CatalogApiController::class, 'productDetail']);
$router->get('/api/v1/banners', [CatalogApiController::class, 'banners']);
$router->get('/api/v1/offers', [CatalogApiController::class, 'offers']);

$router->get('/api/v1/cart', [CartApiController::class, 'show'], $apiAuth);
$router->post('/api/v1/cart/items', [CartApiController::class, 'addItem'], $apiAuth);
$router->put('/api/v1/cart/items/{id}', [CartApiController::class, 'updateItem'], $apiAuth);
$router->post('/api/v1/cart/items/{id}', [CartApiController::class, 'updateItem'], $apiAuth);
$router->delete('/api/v1/cart/items/{id}', [CartApiController::class, 'removeItem'], $apiAuth);
$router->post('/api/v1/cart/coupon', [CartApiController::class, 'applyCoupon'], $apiAuth);
$router->delete('/api/v1/cart/coupon', [CartApiController::class, 'removeCoupon'], $apiAuth);

$router->get('/api/v1/wishlist', [WishlistApiController::class, 'index'], $apiAuth);
$router->post('/api/v1/wishlist', [WishlistApiController::class, 'add'], $apiAuth);
$router->delete('/api/v1/wishlist/{id}', [WishlistApiController::class, 'remove'], $apiAuth);
$router->post('/api/v1/wishlist/{id}/move-to-cart', [WishlistApiController::class, 'moveToCart'], $apiAuth);

$router->get('/api/v1/notifications', [NotificationApiController::class, 'index'], $apiAuth);
$router->post('/api/v1/notifications/read-all', [NotificationApiController::class, 'markAllRead'], $apiAuth);
$router->post('/api/v1/notifications/{id}/read', [NotificationApiController::class, 'markRead'], $apiAuth);

$router->get('/api/v1/support/faqs', [SupportApiController::class, 'faqs']);
$router->post('/api/v1/support/tickets', [SupportApiController::class, 'createTicket'], $apiAuth);
$router->get('/api/v1/support/tickets', [SupportApiController::class, 'myTickets'], $apiAuth);
$router->get('/api/v1/support/tickets/{id}', [SupportApiController::class, 'ticketDetail'], $apiAuth);

$router->post('/api/v1/bulk-enquiries', [BulkEnquiryApiController::class, 'create']);

$router->get('/api/v1/delivery-slots', [OrderApiController::class, 'deliverySlots'], $apiAuth);
$router->post('/api/v1/orders', [OrderApiController::class, 'place'], $apiAuth);
$router->get('/api/v1/orders', [OrderApiController::class, 'index'], $apiAuth);
$router->get('/api/v1/orders/{id}/invoice', [OrderApiController::class, 'invoice'], $apiAuth);
$router->post('/api/v1/orders/{id}/reorder', [OrderApiController::class, 'reorder'], $apiAuth);
$router->post('/api/v1/orders/{id}/cancel', [OrderApiController::class, 'cancel'], $apiAuth);
$router->get('/api/v1/orders/{id}', [OrderApiController::class, 'show'], $apiAuth);

// ---------------------------------------------------------------------------
// Admin panel (session auth)
// ---------------------------------------------------------------------------

// Public
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// DB connectivity check (auth required so it isn't wide open)
$router->get('/test/db', [SystemController::class, 'dbTest'], ['require_auth']);

// Authenticated modules
$router->get('/', [DashboardController::class, 'index'], [require_module('dashboard')]);
$router->get('/dashboard', [DashboardController::class, 'index'], [require_module('dashboard')]);

$router->get('/products', [ProductController::class, 'index'], [require_module('products')]);
$router->get('/products/add', [ProductController::class, 'add'], [require_module('products')]);
$router->post('/products', [ProductController::class, 'store'], [require_module('products')]);
$router->get('/products/bulk-upload', [ProductController::class, 'bulkUpload'], [require_module('products')]);
$router->post('/products/bulk-upload', [ProductController::class, 'bulkUploadStore'], [require_module('products')]);
$router->get('/products/bulk-stock', [ProductController::class, 'bulkStock'], [require_module('products')]);
$router->post('/products/bulk-stock', [ProductController::class, 'bulkStockStore'], [require_module('products')]);
$router->get('/products/templates/{type}', [ProductController::class, 'downloadTemplate'], [require_module('products')]);
$router->post('/products/bulk-delete', [ProductController::class, 'bulkDelete'], [require_module('products')]);
$router->get('/products/{id}/edit', [ProductController::class, 'edit'], [require_module('products')]);
$router->post('/products/{id}/update', [ProductController::class, 'update'], [require_module('products')]);
$router->post('/products/{id}/deactivate', [ProductController::class, 'deactivate'], [require_module('products')]);
$router->post('/products/{id}/delete', [ProductController::class, 'delete'], [require_module('products')]);
$router->post('/products/{id}/stock', [ProductController::class, 'updateStock'], [require_module('products')]);

$router->get('/categories', [CategoryController::class, 'index'], [require_module('categories')]);
$router->get('/categories/create', [CategoryController::class, 'create'], [require_module('categories')]);
$router->post('/categories', [CategoryController::class, 'store'], [require_module('categories')]);
$router->post('/categories/bulk-delete', [CategoryController::class, 'bulkDelete'], [require_module('categories')]);
$router->get('/categories/{id}/edit', [CategoryController::class, 'edit'], [require_module('categories')]);
$router->post('/categories/{id}/update', [CategoryController::class, 'update'], [require_module('categories')]);
$router->post('/categories/{id}/delete', [CategoryController::class, 'delete'], [require_module('categories')]);
$router->get('/orders', [OrderController::class, 'index'], [require_module('orders')]);
$router->get('/orders/{id}', [OrderController::class, 'show'], [require_module('orders')]);
$router->post('/orders/{id}/status', [OrderController::class, 'updateStatus'], [require_module('orders')]);
$router->post('/orders/{id}/assign', [OrderController::class, 'assign'], [require_module('orders')]);
$router->post('/orders/{id}/set-date', [OrderController::class, 'setDate'], [require_module('orders')]);

$router->get('/delivery', [DeliveryController::class, 'index'], [require_module('delivery')]);
$router->get('/delivery/{id}', [DeliveryController::class, 'show'], [require_module('delivery')]);
$router->post('/delivery/{id}/set-date', [DeliveryController::class, 'setDate'], [require_module('delivery')]);
$router->post('/delivery/{id}/out-for-delivery', [DeliveryController::class, 'outForDelivery'], [require_module('delivery')]);
$router->post('/delivery/{id}/delivered', [DeliveryController::class, 'delivered'], [require_module('delivery')]);

$router->get('/customers', [CustomerController::class, 'index'], [require_module('customers')]);
$router->get('/customers/{id}', [CustomerController::class, 'show'], [require_module('customers')]);
$router->post('/customers/{id}/approve', [CustomerController::class, 'approve'], [require_module('customers')]);
$router->post('/customers/{id}/reject', [CustomerController::class, 'reject'], [require_module('customers')]);
$router->post('/customers/{id}/toggle-block', [CustomerController::class, 'toggleBlock'], [require_module('customers')]);

$router->get('/roles', [RoleController::class, 'index'], [require_module('roles')]);
$router->get('/roles/create', [RoleController::class, 'create'], [require_module('roles')]);
$router->post('/roles', [RoleController::class, 'store'], [require_module('roles')]);
$router->get('/roles/{id}/edit', [RoleController::class, 'edit'], [require_module('roles')]);
$router->post('/roles/{id}/update', [RoleController::class, 'update'], [require_module('roles')]);
$router->post('/roles/{id}/toggle-active', [RoleController::class, 'toggleActive'], [require_module('roles')]);

$router->get('/offers', [OfferController::class, 'index'], [require_module('offers')]);
$router->get('/offers/banners/create', [OfferController::class, 'createBanner'], [require_module('offers')]);
$router->post('/offers/banners', [OfferController::class, 'storeBanner'], [require_module('offers')]);
$router->get('/offers/banners/{id}/edit', [OfferController::class, 'editBanner'], [require_module('offers')]);
$router->post('/offers/banners/{id}/update', [OfferController::class, 'updateBanner'], [require_module('offers')]);
$router->post('/offers/banners/{id}/delete', [OfferController::class, 'deleteBanner'], [require_module('offers')]);
$router->get('/offers/create', [OfferController::class, 'createOffer'], [require_module('offers')]);
$router->post('/offers', [OfferController::class, 'storeOffer'], [require_module('offers')]);
$router->get('/offers/{id}/edit', [OfferController::class, 'editOffer'], [require_module('offers')]);
$router->post('/offers/{id}/update', [OfferController::class, 'updateOffer'], [require_module('offers')]);
$router->post('/offers/{id}/delete', [OfferController::class, 'deleteOffer'], [require_module('offers')]);

$router->get('/market-prices', [MarketPriceController::class, 'index'], [require_module('market_prices')]);
$router->post('/market-prices/save', [MarketPriceController::class, 'save'], [require_module('market_prices')]);

$router->get('/support', [SupportController::class, 'index'], [require_module('support')]);
$router->get('/support/{id}', [SupportController::class, 'show'], [require_module('support')]);
$router->post('/support/{id}/reply', [SupportController::class, 'reply'], [require_module('support')]);
$router->post('/support/{id}/status', [SupportController::class, 'updateStatus'], [require_module('support')]);

$router->get('/bulk-enquiries', [BulkEnquiryController::class, 'index'], [require_module('bulk_enquiries')]);
$router->get('/bulk-enquiries/{id}', [BulkEnquiryController::class, 'show'], [require_module('bulk_enquiries')]);
$router->post('/bulk-enquiries/{id}/status', [BulkEnquiryController::class, 'updateStatus'], [require_module('bulk_enquiries')]);

$router->get('/reports', [ReportController::class, 'index'], [require_module('reports')]);
$router->get('/reports/export', [ReportController::class, 'export'], [require_module('reports')]);

$router->get('/settings', [SettingsController::class, 'index'], ['require_settings_access']);
$router->post('/settings/password', [SettingsController::class, 'updatePassword'], ['require_settings_access']);
$router->post('/settings/app', [SettingsController::class, 'updateApp'], ['require_settings_access']);
$router->post('/settings/branding', [SettingsController::class, 'updateBranding'], ['require_super_admin']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
