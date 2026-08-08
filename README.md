# VeggiiCart Admin Panel

Core PHP + MySQL admin shell built on the NiceAdmin (Bootstrap 5) UI kit.

## Quick start (XAMPP)

1. Copy `app/config/config.sample.php` → `app/config/config.local.php` and set DB credentials (a starter `config.local.php` is already present for local XAMPP).
2. Create an empty MySQL database named `veggiicart` (tables come later with the schema).
3. Ensure Apache `mod_rewrite` is enabled and `AllowOverride All` for `htdocs`.
4. Open: `http://localhost/VGS/veggiicart/public/login`

### TEMP Super Admin (change before go-live)

- Email: `admin@veggiicart.com`
- Username: `admin`
- Password: `ChangeMe@123`

### DB connectivity check

After login: `http://localhost/VGS/veggiicart/public/test/db`

## Structure

See project folders under `app/` (controllers, models, views, middleware, core) and `public/` (front controller + assets).
