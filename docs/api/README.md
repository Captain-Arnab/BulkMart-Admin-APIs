# VeggiiCart REST API v1

Base URL (XAMPP): `http://localhost/VGS/veggiicart/public/api/v1`

Envelope: `{ "success": bool, "data": {...}|null, "error": { "code", "message", "fields"? }|null }`

Auth: `Authorization: Bearer <access_token>` on protected routes.

## SMS / OTP

SMS gateway is **DEV MODE** until `sms.*` is configured — `send-otp` returns `dev_otp`.

## Cancel-order rule

Matches admin panel / `Order::canCancel`: only while status is `placed`, `confirmed`, or `delivery_date_set` (pre–out-for-delivery). Restores stock if it was already deducted on confirm.

## Full endpoint list (P1 + P2 + P3)

### Auth
| Method | Path | Auth |
|--------|------|------|
| POST | `/auth/send-otp` | no |
| POST | `/auth/resend-otp` | no |
| POST | `/auth/verify-otp` | no |
| POST | `/auth/email-login` | no |
| POST | `/auth/refresh-token` | no |
| POST | `/auth/logout` | optional |

### Business / KYC
| Method | Path | Auth |
|--------|------|------|
| GET | `/business-types` | no |
| POST | `/business/register` | yes |
| GET/POST | `/business/documents` | yes |
| POST | `/business/resubmit` | yes |
| GET | `/business/verification-status` | yes |

### Profile & addresses
| Method | Path | Auth |
|--------|------|------|
| GET/PUT | `/profile` | yes |
| POST/DELETE | `/profile/avatar` | yes |
| GET/POST | `/addresses` | yes |
| PUT/DELETE | `/addresses/{id}` | yes |
| POST | `/addresses/{id}/default` | yes |

### Catalog
| Method | Path | Auth |
|--------|------|------|
| GET | `/categories` | no |
| GET | `/categories/{id}` | no |
| GET | `/products` | no |
| GET | `/products/search` | no |
| GET | `/products/market-prices` | no |
| GET | `/products/{id}` | no |
| GET | `/products/{id}/similar` | no |
| GET | `/products/{id}/frequently-bought-together` | no |
| GET | `/banners` | no |
| GET | `/offers` | no |

### Cart & wishlist
| Method | Path | Auth |
|--------|------|------|
| GET | `/cart` | yes |
| POST | `/cart/items` | yes |
| PUT/DELETE | `/cart/items/{id}` | yes |
| POST/DELETE | `/cart/coupon` | yes |
| GET/POST | `/wishlist` | yes |
| DELETE | `/wishlist/{id}` | yes |
| POST | `/wishlist/{id}/move-to-cart` | yes |

### Checkout & orders
| Method | Path | Auth |
|--------|------|------|
| GET | `/delivery-slots` | yes |
| POST | `/orders` | yes |
| GET | `/orders` | yes |
| GET | `/orders/{id}` | yes |
| GET | `/orders/{id}/invoice` | yes (`?format=html` printable) |
| POST | `/orders/{id}/reorder` | yes |
| POST | `/orders/{id}/cancel` | yes |

### Notifications & support
| Method | Path | Auth |
|--------|------|------|
| GET | `/notifications` | yes |
| POST | `/notifications/read-all` | yes |
| POST | `/notifications/{id}/read` | yes |
| GET | `/support/faqs` | no |
| GET/POST | `/support/tickets` | yes |
| GET | `/support/tickets/{id}` | yes |

## Curl extras (P2/P3)

```bash
BASE=http://localhost/VGS/veggiicart/public/api/v1
TOKEN=...

# Coupon (seeded GREEN10 / FLAT50 after seed_modules.php)
curl -s -X POST "$BASE/cart/coupon" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"coupon_code":"GREEN10"}'

curl -s -X DELETE "$BASE/cart/coupon" -H "Authorization: Bearer $TOKEN"

# Wishlist
curl -s -X POST "$BASE/wishlist" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"product_id":1}'

# Cancel / reorder / invoice
curl -s -X POST "$BASE/orders/66/cancel" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"reason":"Ordered by mistake"}'
curl -s -X POST "$BASE/orders/66/reorder" -H "Authorization: Bearer $TOKEN"
curl -s "$BASE/orders/66/invoice"
curl -s "$BASE/orders/66/invoice?format=html" -o invoice.html

# Offers, FAQs, notifications, similar
curl -s "$BASE/offers"
curl -s "$BASE/support/faqs?q=MOQ"
curl -s "$BASE/notifications" -H "Authorization: Bearer $TOKEN"
curl -s "$BASE/products/1/similar"
curl -s "$BASE/products/market-prices"
```

Postman: `docs/api/VeggiiCart_API_v1.postman_collection.json`  
Smoke: `php scripts/verify_api.php`
