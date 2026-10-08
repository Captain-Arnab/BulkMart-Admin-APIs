# VeggiiCart API — Flutter Integration Reference

**Generated:** 2026-08-13  
**Purpose:** Exact wiring facts for the Flutter app — traced from routing/auth code, not from memory.  
**Read-only discovery** (no code changes).

---

## 1. BASE URL DISCOVERY

### How requests reach the API (confirmed chain)

| Step | File | What happens |
|------|------|----------------|
| 1 | Repo-root [`.htaccess`](../../.htaccess) L1–2 | `RewriteRule ^(.*)$ public/$1 [L]` — if the vhost document root is the **project root**, every URL is internally rewritten into `/public/…`. |
| 2 | Root [`index.php`](../../index.php) L1–20 | Convenience bounce into `/public/` if someone hits the project root without rewrite. Comment L4: *“Prefer pointing the vhost/document root at `/public` in production.”* |
| 3 | [`public/.htaccess`](../../public/.htaccess) L3–9 | Non-file requests → front controller `index.php`. |
| 4 | [`public/index.php`](../../public/index.php) L34–40, L55–121 | Strips `app_base_url()` prefix from `REQUEST_URI`, then matches routes registered as `/api/v1/...`. |
| 5 | [`app/core/Router.php`](../../app/core/Router.php) L47–68 | Same base-strip + match; 404 JSON for unknown `/api/*`. |

Route paths are registered **including** the `/api/v1` prefix (e.g. `public/index.php` L55: `'/api/v1/auth/send-otp'`). There is **no** separate `routes/api.php`.

`app_base_url()` ([`app/config/app.php`](../../app/config/app.php) L75–96): when `app.base_url` is empty (current `config.local.php` L17), it auto-detects as `dirname(SCRIPT_NAME)` — e.g. `/VGS/veggiicart/public` on XAMPP, or `''` when the document root **is** `public/`.

### Confirmed base URLs

| Environment | API base URL (no trailing slash) | Why |
|-------------|----------------------------------|-----|
| **PRODUCTION (intended)** | `https://veggiicart.com/api/v1` | With docroot = `public/` **or** project-root + root `.htaccess` rewrite, the **external** path is `/api/v1/...` (the `/public` segment is not part of the client-facing URL). |
| **LOCAL (XAMPP, this repo)** | `http://localhost/VGS/veggiicart/public/api/v1` | Confirmed in [`docs/api/README.md`](README.md) L3 and live smoke/`verify_api.php` default. Htdocs is the Apache docroot; the app lives under `/VGS/veggiicart/public`. |

**Do not use** `https://veggiicart.com/public/api/v1` as the primary Flutter prod base unless production hosting is misconfigured to expose `/public` in the browser URL. The rewrite + front-controller design makes the clean path `/api/v1`.

**Sanity check after deploy:**  
`GET https://veggiicart.com/api/v1/categories` → JSON `{ "success": true, "data": { "categories": [...] }, "error": null }`

---

## 2. DIRECTORY STRUCTURE (API-relevant)

```
veggiicart/
├── .htaccess                    # rewrite → public/ (if docroot = project root)
├── index.php                    # redirect helper into /public
├── public/                      # ★ ACTUAL WEB ROOT (point domain / vhost here)
│   ├── .htaccess                # front-controller → index.php
│   ├── index.php                # registers ALL /api/v1 routes + admin routes
│   ├── assets/                  # admin UI static (not API)
│   └── uploads/                 # uploaded avatars/KYC/docs (web-accessible URLs)
├── app/                         # internal PHP (not directly URL-mapped)
│   ├── config/
│   │   ├── app.php              # app_base_url(), app_config()
│   │   ├── config.sample.php    # jwt/cors/sms defaults
│   │   ├── config.local.php     # gitignored live secrets/TTLs
│   │   └── db.php               # PDO
│   ├── core/
│   │   ├── Router.php           # method/path matcher + middleware
│   │   ├── Controller.php
│   │   └── Model.php
│   ├── middleware/
│   │   └── api_auth.php         # CORS + require_api_auth (JWT Bearer)
│   ├── controllers/api/         # one controller per resource area
│   │   ├── ApiController.php    # JSON envelope helpers
│   │   ├── AuthApiController.php
│   │   ├── BusinessApiController.php
│   │   ├── ProfileApiController.php
│   │   ├── AddressApiController.php
│   │   ├── CatalogApiController.php
│   │   ├── CartApiController.php
│   │   ├── WishlistApiController.php
│   │   ├── NotificationApiController.php
│   │   ├── SupportApiController.php
│   │   └── OrderApiController.php
│   ├── models/                  # shared with admin; API uses Customer, Product, Cart, …
│   └── services/                # JwtService, OtpService, SmsService, CheckoutService, …
├── scripts/verify_api.php       # HTTP matrix smoke test
└── docs/api/                    # this folder
```

| Path | Role |
|------|------|
| `public/` | **Public web root** — only this tree should be served by the domain. |
| `public/index.php` | Front controller; **sole** route table for `/api/v1/*` (L55–121). |
| `app/controllers/api/` | API handlers; dispatched by Router. |
| `app/middleware/api_auth.php` | CORS for `/api/*` + JWT gate. |
| `app/models/`, `app/services/` | DB + business logic reused by API (and admin). |
| `app/`, `database/`, `scripts/`, `docs/` | **Internal-only** — not meant as direct URL targets (except via `public/uploads`). |

---

## 3. FULL ENDPOINT LIST (ground truth)

**Source:** [`public/index.php`](../../public/index.php) L53–121.  
**Auth column:** `JWT` = middleware `require_api_auth` (`$apiAuth`).  
**Path below** is relative to the API base (prepend base URL from §1).

### Auth — `AuthApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| POST | `/auth/send-otp` | `sendOtp` | Public |
| POST | `/auth/resend-otp` | `resendOtp` | Public |
| POST | `/auth/verify-otp` | `verifyOtp` | Public |
| POST | `/auth/email-login` | `emailLogin` | Public |
| POST | `/auth/refresh-token` | `refreshToken` | Public |
| POST | `/auth/logout` | `logout` | Optional Bearer (body `refresh_token`) |

### Business / KYC — `BusinessApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| GET | `/business-types` | `businessTypes` | Public |
| POST | `/business/register` | `register` | JWT |
| POST | `/business/documents` | `uploadDocument` | JWT |
| GET | `/business/documents` | `listDocuments` | JWT |
| POST | `/business/resubmit` | `resubmit` | JWT |
| GET | `/business/verification-status` | `verificationStatus` | JWT |

#### `POST /business/register`

Completes business KYC registration for the authenticated customer (JWT from OTP verify). Mobile OTP remains the required signup path; password is **optional**.

**Shop-front photo is mandatory.** Upload it first via `POST /business/documents` with `document_type=shop_front_photo` (aliases: `shop_photo`, `business_photo`; stored as `business_photo`). Without it, register returns:

```json
{ "success": false, "data": null,
  "error": { "code": "SHOP_PHOTO_REQUIRED", "message": "Shop-front photo is required. Upload it via POST /business/documents with document_type=shop_front_photo before completing registration.",
             "fields": { "shop_front_photo": "Shop-front photo is required." } } }
```

(HTTP 422.) All other document types stay optional. `GET /business/verification-status` → `catalog[].required` is `true` only for `business_photo`.

**Required body fields:** `business_name`, `owner_name`, `business_type`

**Optional body fields:**

| Field | Notes |
|-------|--------|
| `email`, `gst_number`, `fssai_number`, `pan_number` | Stored when non-empty |
| `shop_address` / `address` / flat address fields | Optional address create (see controller) |
| `password` | Optional. If set, must be **≥ 6 characters**. Hashed via `Customer::setPassword` (`password_hash(..., PASSWORD_DEFAULT)`) — same as Profile change-password |
| `password_confirmation` | Required **only when** `password` is provided; must match. Alias: `confirm_password` |

If `password` is omitted (or empty), `customers.password_hash` stays `NULL` (unchanged). Email+Password login then requires setting a password later via Profile.

**Validation (422 `VALIDATION_ERROR` + `error.fields`)** when password is partially/fully provided:

- `password` — under 6 characters
- `password_confirmation` — does not match `password`

**Example (with optional password):**

```json
{
  "business_name": "Fresh Mart",
  "owner_name": "Arnab",
  "business_type": "retailer",
  "email": "shop@example.com",
  "password": "secret1",
  "password_confirmation": "secret1"
}
```

### Profile — `ProfileApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| GET | `/profile` | `show` | JWT |
| PUT | `/profile` | `update` | JWT |
| POST | `/profile` | `update` | JWT (PUT fallback) |
| POST | `/profile/avatar` | `uploadAvatar` | JWT |
| DELETE | `/profile/avatar` | `removeAvatar` | JWT |

### Addresses — `AddressApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| GET | `/addresses` | `index` | JWT |
| POST | `/addresses` | `store` | JWT |
| PUT | `/addresses/{id}` | `update` | JWT |
| POST | `/addresses/{id}` | `update` | JWT (PUT fallback) |
| DELETE | `/addresses/{id}` | `destroy` | JWT |
| POST | `/addresses/{id}/default` | `setDefault` | JWT |

### Catalog — `CatalogApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| GET | `/categories` | `categories` | Public |
| GET | `/categories/{id}` | `categoryDetail` | Public |
| GET | `/products` | `products` | Public |
| GET | `/products/search` | `search` | Public |
| GET | `/products/market-prices` | `marketPrices` | Public |
| GET | `/products/{id}/similar` | `similar` | Public |
| GET | `/products/{id}/frequently-bought-together` | `frequentlyBought` | Public |
| GET | `/products/{id}` | `productDetail` | Public |
| GET | `/banners` | `banners` | Public |
| GET | `/offers` | `offers` | Public |

#### `GET /products/{id}` product object

List/search/similar responses still return a single `image_url` (the cover/primary image) for backward compatibility.

Product **detail** adds `description`, `benefits`, `storage_tips`, and an `images` gallery array. `image_url` remains and is always the primary/cover image.

```json
{
  "id": 12,
  "category_id": 1,
  "category_name": "Green Vegetables",
  "name": "Tomato",
  "unit": "per kg",
  "moq": 1,
  "bulk_quote_threshold": 5,
  "price": 38,
  "stock": 40,
  "in_stock": true,
  "image_url": "http://localhost/VGS/veggiicart/public/uploads/products/cover.jpg",
  "item_code": "20",
  "batch_no": null,
  "grade": "A",
  "origin": null,
  "description": "Farm fresh tomatoes.",
  "benefits": "Rich in lycopene and vitamin C.",
  "storage_tips": "Keep in a cool, dry place. Refrigerate when ripe.",
  "images": [
    { "url": "http://localhost/VGS/veggiicart/public/uploads/products/cover.jpg", "is_primary": true, "sort_order": 0 },
    { "url": "http://localhost/VGS/veggiicart/public/uploads/products/side.jpg", "is_primary": false, "sort_order": 1 }
  ]
}
```

- `description`, `benefits`, and `storage_tips` are optional strings (`null` when not set).
- `images[].url` is an absolute media URL (same rules as `image_url`).
- `images[].is_primary` is `true` for exactly one cover image when any images exist.
- `images[].sort_order` is the admin gallery order (lower first).
- If a product has no `product_images` rows yet, `images` is a one-item array built from `image_url`, or `[]` if there is no image.

#### Quantity rules (MOQ multiples) — replaces the old 25/50/75/100 KG tiers

- Every quantity sent to `POST /cart/items`, `PUT /cart/items/{id}`, `POST /orders` (cart lines), `POST /orders/multi-address` (**each per-address allocation**), and `PUT /orders/{id}` must be an exact multiple of that product's `moq` (`quantity = moq × units`, units ≥ 1). Otherwise → 422 `VALIDATION_ERROR`, e.g. `Quantity for "Onion Big Size" must be a multiple of its MOQ of 20 kg (e.g. 20, 40, 60). You requested 30.`
- `moq` is on every product object (list, search, detail, similar, frequently-bought) and on cart lines.
- `bulk_quote_threshold` = `moq × 5` (also on cart lines). The stepper should allow `moq … bulk_quote_threshold` in steps of `moq`; above that, show **Get Bulk Quote** (`POST /bulk-enquiries`) instead. The server does **not** cap orders at the threshold — it is a UX cutoff only. Clients may compute `5 × moq` themselves, but prefer the field so the multiplier can change server-side (`Product::BULK_QUOTE_MOQ_MULTIPLIER`).
- `POST /orders/{id}/reorder` rounds legacy quantities **up** to the next MOQ multiple.

### Cart — `CartApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| GET | `/cart` | `show` | JWT |
| POST | `/cart/items` | `addItem` | JWT |
| PUT | `/cart/items/{id}` | `updateItem` | JWT |
| POST | `/cart/items/{id}` | `updateItem` | JWT (PUT fallback) |
| DELETE | `/cart/items/{id}` | `removeItem` | JWT |
| POST | `/cart/coupon` | `applyCoupon` | JWT |
| DELETE | `/cart/coupon` | `removeCoupon` | JWT |

### Wishlist — `WishlistApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| GET | `/wishlist` | `index` | JWT |
| POST | `/wishlist` | `add` | JWT |
| DELETE | `/wishlist/{id}` | `remove` | JWT |
| POST | `/wishlist/{id}/move-to-cart` | `moveToCart` | JWT |

### Notifications — `NotificationApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| GET | `/notifications` | `index` | JWT |
| POST | `/notifications/read-all` | `markAllRead` | JWT |
| POST | `/notifications/{id}/read` | `markRead` | JWT |

### Support — `SupportApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| GET | `/support/faqs` | `faqs` | Public |
| POST | `/support/tickets` | `createTicket` | JWT |
| GET | `/support/tickets` | `myTickets` | JWT |
| GET | `/support/tickets/{id}` | `ticketDetail` | JWT |

### Orders / checkout — `OrderApiController`

| Method | Path | Handler | Auth |
|--------|------|---------|------|
| GET | `/delivery-slots` | `deliverySlots` | JWT |
| POST | `/orders` | `place` | JWT |
| GET | `/orders` | `index` | JWT |
| GET | `/orders/{id}/invoice` | `invoice` | JWT |
| POST | `/orders/{id}/reorder` | `reorder` | JWT |
| POST | `/orders/{id}/cancel` | `cancel` | JWT |
| GET | `/orders/{id}` | `show` | JWT |
| PUT | `/orders/{id}` | `update` (edit: max 2×, within 2 min) | JWT |
| POST | `/orders/{id}` | `update` | JWT (PUT fallback) |

**Registered route count:** **60** (including POST aliases for PUT).

#### `PUT /orders/{id}` — customer order edit (time-limited, max 2 edits)

Body: `{ "items": [ { "product_id": 61, "quantity": 5 }, { "product_id": 84, "quantity": 60 } ] }` — the **full** new item list (replaces all lines; products may be added/removed; each quantity must be an MOQ multiple; no duplicate `product_id`; empty list → 422, cancel instead).

**Edit rule** (constants in `Order`: `ORDER_EDIT_WINDOW_SECONDS = 120`, `ORDER_EDIT_MAX_COUNT = 2`). An order is editable only when **all** hold:

1. `edit_count < 2`
2. server now − `placed_at` ≤ 120 seconds (server clock only — never the device clock)
3. `status` is `placed` or `confirmed`

Errors (all 422, checked in this order, under a row lock so concurrent edits cannot both pass):

| Code | When | Message (example) |
|------|------|-------------------|
| `EDIT_LIMIT_REACHED` | `edit_count >= 2` | "This order has already been edited 2 times, which is the maximum. It cannot be modified again. You can still cancel it if it has not been dispatched." |
| `EDIT_WINDOW_EXPIRED` | more than 120 s since `placed_at` | "The 2-minute edit window for this order has ended. Orders can only be edited within 2 minutes of placing them. You can still cancel it if it has not been dispatched." |
| `VALIDATION_ERROR` | status no longer editable, or invalid items / MOQ / stock / coupon | server message |

**Edit fields on every order object** (`GET /orders`, `GET /orders/{id}`, place / edit / cancel responses), all computed with the rule above:

| Field | Type | Meaning |
|-------|------|---------|
| `can_edit` | bool | Show the Edit button only when `true` |
| `edit_count` | int | Successful edits so far |
| `edit_remaining` | int | `max(0, 2 − edit_count)` |
| `edit_max_count` | int | `2` |
| `edit_window_seconds` | int | `120` |
| `edit_expires_at` | ISO 8601 | `placed_at + 120 s`, e.g. `2026-10-08T13:02:14+05:30` |
| `edit_seconds_left` | int | Seconds until `edit_expires_at` while `can_edit` is true, else `0` |
| `server_time` | ISO 8601 | Server clock when the response was built |

**Countdown on clients:** start from `edit_seconds_left` (or `edit_expires_at − server_time`) and count down with a local monotonic timer; do **not** compare `edit_expires_at` against the device clock. Hide the Edit button at zero; the server rejects late edits with `EDIT_WINDOW_EXPIRED` regardless.

- Each successful edit increments `edit_count` by 1.
- Lines are re-priced at **current** catalog prices; `delivery_fee` is kept; an order coupon is re-evaluated (edit rejected if it would make the coupon ineligible/expired).
- Stock: `placed` → validated only (deducted on confirm, as usual). `confirmed` → old quantities restored, new ones validated and deducted, atomically.
- Cart and cart coupon are untouched. **Cancellation is independent of `edit_count` and the edit window** — `can_cancel` is status-based only (`placed`, `confirmed`, `delivery_date_set`); an edited order can still be cancelled and stock is restored for the edited quantities.
- Success: 200 `{ "message": "...", "order": { ...same shape as GET /orders/{id} } }`. `message` is "Order updated. You can edit this order 1 more time(s) within the remaining time." after the first edit, and "Your order has been confirmed. This order cannot be modified again." after the second.

**Timezone:** PHP runs in `APP_TIMEZONE` (`Asia/Kolkata` by default, `app.timezone` in `config.local.php`) and every DB connection sets the MySQL session `time_zone` to the same offset, so `placed_at` (written with `NOW()`) and PHP's `time()` refer to the same clock.

### vs `verify_api.php` coverage

| Status | Routes |
|--------|--------|
| **Hit in current `scripts/verify_api.php`** | Auth (all 6), business-types/register/documents GET+POST/resubmit, profile PUT+POST+avatar, addresses POST/PUT/DELETE/default, products/search + products/{id}, cart add/update/delete/coupon, wishlist add/delete/move-to-cart, orders place/invoice/cancel/reorder, notifications read + read-all, support tickets POST + GET/{id} |
| **Registered but not in current `verify_api.php` HTTP matrix** (were probed in earlier audit smokes / are read-only GETs) | `GET /profile`, `GET /addresses`, `GET /categories`, `GET /categories/{id}`, `GET /products`, `GET /products/market-prices`, `GET /products/{id}/similar`, `GET /products/{id}/frequently-bought-together`, `GET /banners`, `GET /offers`, `GET /cart`, `GET /wishlist`, `GET /notifications`, `GET /support/faqs`, `GET /support/tickets`, `GET /delivery-slots`, `GET /orders`, `GET /orders/{id}`, `GET /business/verification-status`, POST aliases `POST /addresses/{id}`, `POST /cart/items/{id}`, invoice `?format=html` |
| **In verify but not a separate route** | N/A |

---

## 4. AUTH INTEGRATION DETAILS

### Header format

Confirmed in [`app/middleware/api_auth.php`](../../app/middleware/api_auth.php) L42–55:

```http
Authorization: Bearer <access_token>
```

- Case-insensitive `Bearer` + whitespace + token (`preg_match('/^Bearer\s+(\S+)$/i'`).
- Also checks `REDIRECT_HTTP_AUTHORIZATION` and `apache_request_headers()` if CGI strips the header (L42–51).

### Token TTLs

| Token | Duration | Source |
|-------|----------|--------|
| **Access JWT** | **3600 s (1 hour)** | `config.local.php` `jwt.access_ttl` L22; mirrored in `app_settings.jwt_access_ttl_seconds`; used in `JwtService::issueAccessToken` L10 and returned as `expires_in` |
| **Refresh token** | **2592000 s (30 days)** | `config.local.php` `jwt.refresh_ttl` L23; `RefreshToken::store` uses this TTL (`AuthApiController::issueTokens` L184) |

Access token payload (`JwtService` L12–17): `sub` (customer id), `type: "access"`, `iat`, `exp`.

Refresh token is an **opaque** 64-char hex string (`bin2hex(random_bytes(32))`), **not** a JWT — stored hashed in `refresh_tokens`.

### `POST /auth/verify-otp`

**Request** (`AuthApiController::verifyOtp` L53–55):

```json
{ "mobile": "9876500001", "otp": "123456" }
```

**Success response** (`issueTokens` L193–200 → envelope):

```json
{
  "success": true,
  "data": {
    "access_token": "<jwt>",
    "refresh_token": "<64-hex>",
    "token_type": "Bearer",
    "expires_in": 3600,
    "is_new_user": true,
    "customer": {
      "id": 1,
      "mobile": "9876500001",
      "email": null,
      "business_name": "...",
      "owner_name": "...",
      "business_type": "...",
      "gst_number": null,
      "fssai_number": null,
      "pan_number": null,
      "avatar_url": null,
      "kyc_status": "pending",
      "kyc_rejection_reason": null,
      "is_blocked": false,
      "registration_complete": false
    }
  },
  "error": null
}
```

Customer fields from `Customer::publicProfile` ([`app/models/Customer.php`](../../app/models/Customer.php) L137–155).

### `POST /auth/refresh-token`

**Request** (L129–132):

```json
{ "refresh_token": "<current refresh>" }
```

**Success:** same shape as verify-otp `data` via `issueTokens` (new access + **rotated** refresh; old refresh revoked).  
**Failure:** HTTP **401**, `error.code = "UNAUTHORIZED"` (L137, L142).

### Expired / invalid access token (Dio interceptor)

From `require_api_auth` ([`api_auth.php`](../../app/middleware/api_auth.php) L53–74) via `ApiController::abort`:

| Situation | HTTP | `error.code` | `error.message` |
|-----------|------|--------------|-----------------|
| Missing/malformed `Authorization` | **401** | `UNAUTHORIZED` | `Missing or invalid Authorization header.` |
| Bad signature / wrong type / **expired JWT** | **401** | `UNAUTHORIZED` | `Invalid or expired access token.` |
| Valid JWT but `sub` missing/0 | **401** | `UNAUTHORIZED` | `Invalid or expired access token.` |
| Customer row gone | **401** | `UNAUTHORIZED` | `Customer not found.` |
| Customer blocked | **403** | `FORBIDDEN` | `Your account has been blocked. Contact support.` |

**Interceptor rule:** treat **401 + `error.code == "UNAUTHORIZED"`** as “refresh or re-login”. Do **not** treat **403 FORBIDDEN** as token expiry (account blocked). Validation failures are **422** with `VALIDATION_ERROR` / other codes — unrelated to auth refresh.

Body shape on auth abort (L32–36 of `ApiController.php`):

```json
{
  "success": false,
  "data": null,
  "error": { "code": "UNAUTHORIZED", "message": "Invalid or expired access token." }
}
```

---

## 5. RESPONSE ENVELOPE

**Standard** — all JSON API responses go through `ApiController::envelope` ([`ApiController.php`](../../app/controllers/api/ApiController.php) L40–49):

```json
{
  "success": true|false,
  "data": { ... } | null,
  "error": null | { "code": "...", "message": "...", "fields"?: { ... } }
}
```

- Success: `error` is JSON `null`.
- Failure: `data` is `null`; optional `error.fields` on validation (`validationError` L22–26).
- `Content-Type: application/json; charset=utf-8`.

**Known deviation**

| Endpoint | Behavior |
|----------|----------|
| `GET /orders/{id}/invoice?format=html` (or `pdf`) | Returns **raw HTML** (`Content-Type: text/html`), **not** the JSON envelope — `OrderApiController::renderInvoiceHtml` L333. Default (no query / `format=json`) stays JSON: `{ success, data: { invoice: {...} }, error }`. |

Router 404 for unknown API paths also uses the same envelope (`Router.php` L103–109, code `NOT_FOUND`).

CORS preflight `OPTIONS` returns **204** empty body (`api_auth.php` L31–34) — not the envelope.

---

## 6. CORS / NETWORK NOTES

### CORS (current)

[`api_apply_cors`](../../app/middleware/api_auth.php) L6–34 + [`config.local.php`](../../app/config/config.local.php) L36–38 / sample L38–44:

- **Current:** `cors.allowed_origins = ['*']` — reflects request `Origin` when present, else `*`.
- Allows methods: `GET, POST, PUT, PATCH, DELETE, OPTIONS`.
- Allows headers: `Authorization, Content-Type, Accept, X-Requested-With`.
- Sets `Access-Control-Allow-Credentials: true`.

**Production flag:** sample config says replace `*` before production. Once tightened to explicit origins, Flutter **web** and any website origins (e.g. `https://app.veggiicart.com`, `https://veggiicart.com`) **must** be listed or browsers will block. Native iOS/Android Dio calls are unaffected by CORS.

### Content-Type expectations

| Call type | Expectation |
|-----------|-------------|
| Most JSON writes | `Content-Type: application/json` + JSON body (`ApiController::jsonBody` L53–63). |
| Multipart uploads | **Do not** force JSON. Use `multipart/form-data`: |
| → Avatar | field name `avatar` **or** `file` (`ProfileApiController` L47–51) |
| → KYC document | fields `document_type` + `file` (`BusinessApiController` L94–99); images/PDF ≤ 5MB |
| Method overrides | Router accepts `X-HTTP-Method-Override` or `_method` on POST (`Router.php` L50–56) for PUT/DELETE if needed |

### Other

- OTP DEV MODE may return `dev_otp` / `dev_mode` in `send-otp` data until SMS is live — strip/ignore in production builds.
- Absolute media URLs are built from request host (`ApiController::absoluteMedia` L101–112).

---

## 7. QUICK COPY-PASTE BLOCK (Flutter)

```dart
/// VeggiiCart API — from docs/api/integration_reference.md (do not guess).
const String kApiBaseUrlProd = 'https://veggiicart.com/api/v1';
const String kApiBaseUrlLocal = 'http://localhost/VGS/veggiicart/public/api/v1';

/// Prefix only — full header value is '$kAuthHeaderPrefix$accessToken'
const String kAuthHeaderPrefix = 'Bearer ';

/// Access JWT lifetime from server config (seconds).
const int kAccessTokenTtlSeconds = 3600;

/// Refresh token lifetime from server config (seconds).
const int kRefreshTokenTtlSeconds = 2592000;
```

**Dio interceptor tip:** on response `statusCode == 401` and `data['error']['code'] == 'UNAUTHORIZED'`, call `POST /auth/refresh-token` with `{ "refresh_token": ... }`, then retry; if refresh also 401, force logout/OTP again.

### Sanity-check summary

- **Total registered `/api/v1` routes:** **58**
- **Auth required (JWT Bearer):** **40**
- **Public (no JWT middleware):** **18** (includes logout with optional Bearer; includes all catalog + FAQs + auth OTP/login/refresh)

---

### Cite checklist (double-check these if anything looks off)

| Fact | Citation |
|------|----------|
| Route table | `public/index.php` L55–121 |
| Root rewrite to public | `.htaccess` L1–2 |
| Front controller | `public/.htaccess` L8–9 |
| Prefer docroot = public | root `index.php` L4 |
| JWT header parse | `app/middleware/api_auth.php` L53–60 |
| Envelope | `app/controllers/api/ApiController.php` L40–49 |
| Token issue shape | `AuthApiController.php` L193–200 |
| Access/refresh TTL | `config.local.php` L21–23; `JwtService.php` L10 |
| Invoice HTML deviation | `OrderApiController.php` L279–283, L333 |
| Local base URL docs | `docs/api/README.md` L3 |
