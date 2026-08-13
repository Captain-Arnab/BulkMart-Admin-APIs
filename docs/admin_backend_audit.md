# VeggiiCart — Admin Panel + API Backend Status Audit

**Audit date:** 2026-08-13 (updated Step 14.5 — full endpoint proof)  
**Scope:** This repo (`veggiicart`) — Core PHP admin panel + customer REST API `/api/v1`.  
**Method:** Live MySQL row counts, route/controller inventory, smoke scripts (`verify_*.php`), and HTTP probes with DB side-effect checks.  
**Honesty rule:** Scaffolded-but-unproven = **PARTIALLY BUILT** or **BUILT BUT UNTESTED**, not “fully working.”

---

## Snapshot (verdict)

| Layer | Reality check |
|-------|----------------|
| **Database** | All 5 migrations applied; 26 tables present; seed/demo data is populated (not empty). |
| **Admin panel** | All listed modules are implemented and DB-wired. All admin smoke scripts PASS. Settings surface is intentionally thin. FAQ admin UI is missing. |
| **Customer API** | P1 + P2/P3 routes registered (~58). **Every documented endpoint is TESTED AND WORKING** via expanded `verify_api.php` (HTTP + DB assertions). |
| **Auth** | Admin = PHP sessions + RBAC (seed login fallback **removed**). Customer API = JWT (HS256) + refresh tokens. |
| **SMS / OTP** | Still **DEV MODE** (`sms.enabled = false`). OTP returned as `dev_otp` / logged. (Deferred — production-readiness.) |
| **Flutter `kDemoMode`** | Flutter app is **not in this repo** — cannot confirm from this codebase. |

---

## 1. DATABASE

**Source of truth:** Live DB `veggiicart` on XAMPP MySQL (`SHOW TABLES` + `COUNT(*)`), cross-checked with migrations `001`–`005`.

All migrations recorded in `migrations` table: **5 / 5**.

| Table | Exact row count (2026-08-13) | Notes |
|-------|------------------------------|--------|
| `addresses` | 9 | Seed + API test addresses |
| `admin_users` | 3 | Super Admin + TEST Delivery Manager + TEST Sub-Admin |
| `app_settings` | 7 | company/support + delivery_fee + JWT/OTP TTLs |
| `banners` | 2 | Seeded |
| `cart_items` | 1 | Live from API smoke |
| `cart_meta` | 2 | Coupon meta (P2) |
| `categories` | 4 | Seeded |
| `customer_documents` | 2 | KYC docs present |
| `customers` | 9 | Seed + OTP/API test mobiles |
| `delivery_slots` | **0** | Table exists; API synthesizes slots when empty |
| `faqs` | 5 | Seeded by migration `005` |
| `market_prices` | 5 | Seeded |
| `migrations` | 5 | Tracker |
| `notifications` | 24 | Order/KYC notifications from seeds + flows |
| `offers` | 3 | Includes coupons (e.g. GREEN10 / FLAT50 from seed) |
| `order_items` | ~194 | High — demo + smoke pollution |
| `order_status_log` | ~136 | Status history present |
| `orders` | ~72 | Includes ~55 `VC-DEMO-*` analytics seed orders + test orders |
| `otp_codes` | 5 | API OTP activity |
| `otp_rate_limits` | 2 | Rate-limit rows |
| `products` | 35 | Original seed 34 + bulk-upload test product `VG-TEST-001` |
| `refresh_tokens` | 9 | JWT refresh tokens issued |
| `role_permissions` | 4 | Sub-admin module grants |
| `support_ticket_replies` | 2 | Admin replies exist |
| `support_tickets` | 3 | Seeded |
| `wishlists` | 1 | After API smoke add |

**Schema coverage vs migrations:** Every `CREATE TABLE` from `001`–`005` exists in the live DB. No orphan expected tables missing.

---

## 2. ADMIN PANEL MODULES

Admin is **server-rendered Core PHP** (NiceAdmin), not a separate SPA. Routes in `public/index.php`; models talk to MySQL directly (not via `/api/v1`).

| Module | Status | Evidence / what’s missing |
|--------|--------|---------------------------|
| **Base shell (auth, sidebar, role-gating)** | **FULLY WORKING** | Session login against `admin_users` only (seed bypass removed), RBAC sidebar, sub-admin module gates, delivery-manager home → `/delivery`. Verified by `verify_modules.php` + `verify_orders.php`. |
| **Dashboard (incl. analytics charts)** | **FULLY WORKING** | KPIs, sparklines, trend/status/category charts via `AnalyticsService` + ApexCharts. `verify_analytics.php` all PASS; not a “coming soon” stub. |
| **Products & Categories (single + bulk add, bulk stock)** | **FULLY WORKING** | CRUD, image upload, deactivate, inline stock, CSV/XLSX bulk upload + bulk stock + templates. `verify_products.php` PASS (Tomato/Potato asserted via search — list is name-sorted/paginated). |
| **Orders management** | **FULLY WORKING** | List/filters, detail, status advance, ETA, assign DM, status log. Full lifecycle verified by `verify_orders.php` (confirm → stock deduct → assign → ETA → out for delivery → delivered + COD ack; cancel restores stock). |
| **Delivery Management (role-gated)** | **FULLY WORKING** | Queue/history, assignee-scoped access, set date → out for delivery → delivered + COD mismatch ack. DM sidebar gating verified. |
| **Customers / KYC** | **FULLY WORKING** | List/filters, detail, docs, approve/reject (+ reason), block/unblock, notifications. |
| **Roles & Sub-Admins** | **FULLY WORKING** | Create/edit admin users, role_type, module checkboxes, activate/deactivate, last-super-admin guards. Sub-admin gating verified. |
| **Offers & Banners** | **FULLY WORKING** | Combined list + CRUD for banners and offers/coupons. |
| **Market Prices** | **FULLY WORKING** | Today’s prices vs catalog; bulk save. |
| **Support Tickets (admin side)** | **FULLY WORKING** | List, thread, reply, status open/in_progress/closed. |
| **Reports & Analytics** | **FULLY WORKING** | Date presets, filters, charts, top products/customers, CSV export. `verify_analytics.php` PASS. |
| **Settings** | **PARTIALLY BUILT** | **Working:** change password; edit `company_name`, `support_phone`, `support_email`. **Missing from UI:** SMS gateway, JWT secrets, delivery fee, OTP/KYC checkout flags, CORS, theme — those live only in `config.local.php`. |

**Related gap (not in your module list but real):** No admin UI to manage **FAQs** (table + customer API exist; admin CRUD does not).

---

## 3. API ENDPOINTS

**Base:** `/api/v1`  
**Canonical list:** `docs/api/README.md` + routes in `public/index.php` (Excel file was never checked into this repo; priorities reconstructed from Step 13/14 briefs + `004`/`005` migrations).

**Status legend**

| Label | Meaning |
|-------|---------|
| **TESTED AND WORKING** | Hit successfully in this audit and/or `verify_api.php` / documented Step 13 HTTP core flow |
| **BUILT BUT UNTESTED** | Real handler in code; no solid automated/HTTP proof in this audit pass |
| **NOT BUILT** | Not registered / no handler |

### P1 — Core flow (Step 13)

| Method | Path | Status |
|--------|------|--------|
| POST | `/auth/send-otp` | **TESTED AND WORKING** |
| POST | `/auth/resend-otp` | **TESTED AND WORKING** |
| POST | `/auth/verify-otp` | **TESTED AND WORKING** |
| POST | `/auth/email-login` | **TESTED AND WORKING** |
| POST | `/auth/refresh-token` | **TESTED AND WORKING** (rotation + DB revoke asserted) |
| POST | `/auth/logout` | **TESTED AND WORKING** (refresh revoked in DB) |
| GET | `/business-types` | **TESTED AND WORKING** |
| POST | `/business/register` | **TESTED AND WORKING** (KYC pending in DB) |
| POST | `/business/documents` | **TESTED AND WORKING** (row + file) |
| GET | `/business/verification-status` | **TESTED AND WORKING** |
| GET | `/profile` | **TESTED AND WORKING** |
| PUT/POST | `/profile` | **TESTED AND WORKING** (DB owner_name) |
| GET | `/addresses` | **TESTED AND WORKING** |
| POST | `/addresses` | **TESTED AND WORKING** |
| PUT/POST | `/addresses/{id}` | **TESTED AND WORKING** |
| DELETE | `/addresses/{id}` | **TESTED AND WORKING** (row removed) |
| POST | `/addresses/{id}/default` | **TESTED AND WORKING** (`is_default=1`) |
| GET | `/categories` | **TESTED AND WORKING** |
| GET | `/products` | **TESTED AND WORKING** |
| GET | `/products/search` | **TESTED AND WORKING** |
| GET | `/products/{id}` | **TESTED AND WORKING** |
| GET | `/banners` | **TESTED AND WORKING** |
| GET | `/cart` | **TESTED AND WORKING** |
| POST | `/cart/items` | **TESTED AND WORKING** |
| PUT/POST | `/cart/items/{id}` | **TESTED AND WORKING** (qty in DB) |
| DELETE | `/cart/items/{id}` | **TESTED AND WORKING** (row removed) |
| GET | `/delivery-slots` | **TESTED AND WORKING** (synthetic when table empty) |
| POST | `/orders` | **TESTED AND WORKING** |
| GET | `/orders` | **TESTED AND WORKING** |
| GET | `/orders/{id}` | **TESTED AND WORKING** |

### P2 / P3 — Remaining (Step 14)

| Method | Path | Status |
|--------|------|--------|
| GET | `/business/documents` | **TESTED AND WORKING** |
| POST | `/business/resubmit` | **TESTED AND WORKING** (rejected → pending) |
| POST | `/profile/avatar` | **TESTED AND WORKING** |
| DELETE | `/profile/avatar` | **TESTED AND WORKING** (avatar cleared) |
| POST | `/cart/coupon` | **TESTED AND WORKING** (FLAT50 → `cart_meta`) |
| DELETE | `/cart/coupon` | **TESTED AND WORKING** (`cart_meta` cleared) |
| GET | `/wishlist` | **TESTED AND WORKING** |
| POST | `/wishlist` | **TESTED AND WORKING** |
| DELETE | `/wishlist/{id}` | **TESTED AND WORKING** |
| POST | `/wishlist/{id}/move-to-cart` | **TESTED AND WORKING** (wishlist removed + cart row) |
| GET | `/orders/{id}/invoice` | **TESTED AND WORKING** |
| POST | `/orders/{id}/reorder` | **TESTED AND WORKING** (cart populated) |
| POST | `/orders/{id}/cancel` | **TESTED AND WORKING** (status + stock restore after confirm) |
| GET | `/notifications` | **TESTED AND WORKING** |
| POST | `/notifications/read-all` | **TESTED AND WORKING** (unread=0) |
| POST | `/notifications/{id}/read` | **TESTED AND WORKING** |
| GET | `/offers` | **TESTED AND WORKING** |
| GET | `/support/faqs` | **TESTED AND WORKING** |
| POST | `/support/tickets` | **TESTED AND WORKING** |
| GET | `/support/tickets` | **TESTED AND WORKING** |
| GET | `/support/tickets/{id}` | **TESTED AND WORKING** |
| GET | `/categories/{id}` | **TESTED AND WORKING** |
| GET | `/products/market-prices` | **TESTED AND WORKING** |
| GET | `/products/{id}/similar` | **TESTED AND WORKING** |
| GET | `/products/{id}/frequently-bought-together` | **TESTED AND WORKING** |

**NOT BUILT:** None of the documented P1/P2/P3 customer endpoints above are missing from the router.

**Coverage:** `scripts/verify_api.php` now runs a full HTTP matrix against `VERIFY_API_BASE` (default local XAMPP) and asserts DB side-effects for writes (cancel/stock, coupon clear, address delete, refresh revoke, etc.).

---

## 4. AUTH & SECURITY

| Question | Answer |
|----------|--------|
| **Is JWT auth implemented for the API?** | **Yes.** Custom HS256 in `app/services/JwtService.php`. Access JWT + opaque refresh token hashed in `refresh_tokens`. Protected routes use `require_api_auth` (`Authorization: Bearer …`). |
| **Admin auth** | Separate: PHP session + `rbac_can` / `require_module`. Not JWT. |
| **Is `kDemoMode` still true/false in Flutter?** | **Unknown — Flutter app not present in this workspace.** No `*.dart` / `pubspec.yaml` / `kDemoMode` references under `veggiicart`. Confirm in the Flutter repo separately. |
| **SMS gateway connected?** | **No — DEV MODE.** `config.local.php` has `sms.enabled => false`, empty `api_key` / `endpoint`. OTP logged to `storage/logs/sms_dev.log` and returned as `dev_otp`. Even if enabled, `SmsService` still has a **TODO** placeholder HTTP call (Msg91/Fast2SMS not wired to DLT templates). |
| **KYC gate on checkout** | `checkout.require_kyc_approved => false` in local config — customers can place orders without approved KYC. |
| **CORS** | `allowed_origins => ['*']` in local config — fine for local, not production-safe. |
| **Secrets / go-live leftovers** | TEST-ONLY seed passwords remain for local seeding (marked in README + login hints behind `APP_DEBUG` only). Seed login **bypass removed**. Rotate before production. |

---

## 5. KNOWN GAPS (broken / stubbed / “for now”)

1. **SMS still DEV MODE** — production OTP and order-status SMS will not reach phones until a real gateway + DLT templates are wired. *(Deferred — production-readiness.)*
2. **`SmsService` TODO** — enabling `sms.enabled` alone is not enough; provider payload is still a placeholder.
3. **`delivery_slots` table empty** — API falls back to synthesized morning/afternoon/evening windows (usable shortcut; no real capacity booking against DB rows).
4. **KYC not enforced at checkout** — `require_kyc_approved = false` locally. *(Deferred.)*
5. **Settings UI incomplete** — only password + 3 company fields; operational config is file-based. *(Deferred.)*
6. **No FAQ admin CRUD** — FAQs are seeded/SQL-only; customers can read via API.
7. **Demo/test pollution** — many `VC-DEMO-*` / `VC-TEST-*` / API smoke orders; wishlist/cart/OTP leftovers from testing.
8. **Postman gaps** — several write endpoints missing from the collection vs router (covered by `verify_api.php` instead).
9. **API Endpoints Excel** — never checked into repo; priorities inferred from Step 13/14 prompts.
10. **Payment** — COD only; no online payment gateway.
11. **Flutter `kDemoMode`** — cannot be audited from this backend repo.
12. **Unused leftover** — `app/views/shared/placeholder.php` (“Coming soon”) is dead code, not linked.

**Resolved in Step 14.5:** `verify_orders.php` SmsService autoload; full API untested matrix; Tomato/Potato assert; `attempt_seed_login()` removed; TEST passwords clearly marked.

---

## Smoke script scorecard (Step 14.5)

| Script | Result |
|--------|--------|
| `php scripts/verify_api.php` | **PASS** — full HTTP matrix + DB side-effects; SMS DEV MODE confirmed |
| `php scripts/verify_modules.php` | **PASS** |
| `php scripts/verify_analytics.php` | **PASS** |
| `php scripts/verify_products.php` | **PASS** |
| `php scripts/verify_orders.php` | **PASS** — full lifecycle + cancel stock restore |

---

## Recommended next priorities

1. Wire Flutter app to this API; confirm/clear `kDemoMode`.  
2. Wire real SMS + turn off `dev_otp` in non-debug.  
3. Decide KYC gate (`require_kyc_approved`) before production.  
4. Seed real `delivery_slots` or document synthetic slots as permanent.  
5. Expand Settings UI / rotate TEST admin passwords for go-live.  
6. Optional: sync Postman collection with full write coverage.  

---

*Updated after Step 14.5 (verify fix + full endpoint proof). Client apps (Flutter/website UI) remain out of scope except where noted.*
