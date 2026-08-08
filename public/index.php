<?php

/**
 * Front controller — all requests dispatch through here.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/middleware/auth.php';
require_once dirname(__DIR__) . '/app/middleware/rbac.php';
require_once dirname(__DIR__) . '/app/core/Router.php';
require_once dirname(__DIR__) . '/app/core/Controller.php';
require_once dirname(__DIR__) . '/app/core/Model.php';

spl_autoload_register(static function (string $class): void {
    $map = [
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

auth_start_session();

$router = new Router();

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
$router->get('/products/{id}/edit', [ProductController::class, 'edit'], [require_module('products')]);
$router->post('/products/{id}/update', [ProductController::class, 'update'], [require_module('products')]);
$router->post('/products/{id}/deactivate', [ProductController::class, 'deactivate'], [require_module('products')]);
$router->post('/products/{id}/stock', [ProductController::class, 'updateStock'], [require_module('products')]);

$router->get('/categories', [CategoryController::class, 'index'], [require_module('categories')]);
$router->get('/categories/create', [CategoryController::class, 'create'], [require_module('categories')]);
$router->post('/categories', [CategoryController::class, 'store'], [require_module('categories')]);
$router->get('/categories/{id}/edit', [CategoryController::class, 'edit'], [require_module('categories')]);
$router->post('/categories/{id}/update', [CategoryController::class, 'update'], [require_module('categories')]);
$router->post('/categories/{id}/delete', [CategoryController::class, 'delete'], [require_module('categories')]);
$router->get('/orders', [OrderController::class, 'index'], [require_module('orders')]);
$router->get('/orders/{id}', [OrderController::class, 'show'], [require_module('orders')]);
$router->post('/orders/{id}/status', [OrderController::class, 'updateStatus'], [require_module('orders')]);
$router->post('/orders/{id}/assign', [OrderController::class, 'assign'], [require_module('orders')]);

$router->get('/delivery', [DeliveryController::class, 'index'], [require_module('delivery')]);
$router->get('/delivery/{id}', [DeliveryController::class, 'show'], [require_module('delivery')]);
$router->post('/delivery/{id}/set-date', [DeliveryController::class, 'setDate'], [require_module('delivery')]);
$router->post('/delivery/{id}/out-for-delivery', [DeliveryController::class, 'outForDelivery'], [require_module('delivery')]);
$router->post('/delivery/{id}/delivered', [DeliveryController::class, 'delivered'], [require_module('delivery')]);

$router->get('/customers', [CustomerController::class, 'index'], [require_module('customers')]);
$router->get('/roles', [RoleController::class, 'index'], [require_module('roles')]);
$router->get('/offers', [OfferController::class, 'index'], [require_module('offers')]);
$router->get('/market-prices', [MarketPriceController::class, 'index'], [require_module('market_prices')]);
$router->get('/support', [SupportController::class, 'index'], [require_module('support')]);
$router->get('/reports', [ReportController::class, 'index'], [require_module('reports')]);
$router->get('/settings', [SettingsController::class, 'index'], [require_module('settings')]);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
