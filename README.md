# VeggiiCart Admin Panel

Core PHP + MySQL admin shell built on the NiceAdmin (Bootstrap 5) UI kit.

## Quick start (XAMPP)

1. Copy `app/config/config.sample.php` → `app/config/config.local.php` and set DB credentials.
2. Run migrations + seed:
   ```bash
   php scripts/migrate.php
   php scripts/seed.php
   ```
3. Ensure Apache `mod_rewrite` is enabled.
4. Open: `http://localhost/VGS/veggiicart/public/login`

### Super Admin

- Email: `admin@veggiicart.com`
- Password: `ChangeMe@123` (change before go-live)

### TEST Delivery Manager (remove before go-live)

- Email: `delivery@veggiicart.com`
- Password: `Delivery@123`
- Seed with: `php scripts/seed_orders.php`

### Useful scripts

- `php scripts/migrate.php` — apply SQL migrations
- `php scripts/seed.php` — categories, 34 products, admin user
- `php scripts/seed_orders.php` — test customers, orders, delivery manager
- `php scripts/verify_products.php` — smoke test products module
- `php scripts/verify_orders.php` — smoke test orders/delivery + stock
### Bulk templates

- `database/templates/products_bulk_upload.csv`
- `database/templates/products_bulk_stock.csv`
