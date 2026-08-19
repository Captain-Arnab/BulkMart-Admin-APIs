# VeggiiCart Admin Panel

Core PHP + MySQL admin shell built on the NiceAdmin (Bootstrap 5) UI kit.

## Quick start (XAMPP)

1. Copy `app/config/config.sample.php` → `app/config/config.local.php` and set DB credentials.
2. Run migrations + seed:
   ```bash
   php scripts/migrate.php
   php scripts/seed.php
   php scripts/seed_orders.php
   php scripts/seed_modules.php
   ```
3. Ensure Apache `mod_rewrite` is enabled.
4. Open: `http://localhost/VGS/veggiicart/public/login`

### Customer REST API (Flutter / website)

- Base: `http://localhost/VGS/veggiicart/public/api/v1`
- Docs + curl examples: [`docs/api/README.md`](docs/api/README.md)
- Postman: [`docs/api/VeggiiCart_API_v1.postman_collection.json`](docs/api/VeggiiCart_API_v1.postman_collection.json)
- Smoke test: `php scripts/verify_api.php`
- OTP SMS uses **DEV MODE** (returns `dev_otp`) until `sms.*` is configured in `config.local.php`

### ⚠️ TEST-ONLY admin accounts (local/dev — rotate or delete before go-live)

These passwords are for XAMPP seeding only. Login always requires a real `admin_users` row (no seed bypass). Demo hints on `/login` appear **only when `APP_DEBUG` is true**.

| Role | Email | Password | Seed |
|------|-------|----------|------|
| Super Admin (TEST) | `admin@veggiicart.com` | `ChangeMe@123` | `php scripts/seed.php` |
| Delivery Manager (TEST) | `delivery@veggiicart.com` | `Delivery@123` | `php scripts/seed_orders.php` |
| Sub-Admin (TEST) | `subadmin@veggiicart.com` | `SubAdmin@123` | `php scripts/seed_modules.php` (modules: products + customers) |

### Useful scripts

- `php scripts/migrate.php` — apply SQL migrations
- `php scripts/seed.php` — categories, confirmed 34-product catalog, admin user
- `php scripts/seed_orders.php` — test customers, orders, delivery manager
- `php scripts/seed_modules.php` — demo banners/offers/tickets + TEST sub-admin
- `php scripts/seed_analytics.php` — DEMO orders (VC-DEMO-*) for Dashboard/Reports charts
- `php scripts/verify_products.php` — smoke test products module
- `php scripts/verify_orders.php` — smoke test orders/delivery + stock
- `php scripts/verify_modules.php` — smoke test Step 11 modules + sub-admin gating
- `php scripts/verify_analytics.php` — smoke test Dashboard + Reports charts
- `php scripts/verify_api.php` — smoke test customer REST API core flow

### Bulk templates

- `database/templates/products_bulk_upload.csv`
- `database/templates/products_bulk_stock.csv`
