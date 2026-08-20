/**
 * VeggiiCart website → /public/api/v1 client.
 * Same envelope + Bearer JWT pattern as the Flutter app.
 * Never talks to the database; only /api/v1/*.
 */
(function (global) {
    'use strict';

    var TOKEN_KEY = 'vc_access_token';
    var REFRESH_KEY = 'vc_refresh_token';
    var CUSTOMER_KEY = 'vc_customer';

    function detectApiBase() {
        var path = window.location.pathname || '/';
        var dir = path.replace(/\/[^/]*\.[a-zA-Z0-9]+$/, '');
        dir = dir.replace(/\/$/, '');
        if (dir.slice(-4) === '/web') {
            dir = dir.slice(0, -4);
        }
        return window.location.origin + dir + '/public/api/v1';
    }

    var API_BASE = detectApiBase();

    function getToken() {
        return localStorage.getItem(TOKEN_KEY) || '';
    }

    function getRefresh() {
        return localStorage.getItem(REFRESH_KEY) || '';
    }

    function getCustomer() {
        try {
            var raw = localStorage.getItem(CUSTOMER_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    function setSession(data) {
        if (!data) {
            return;
        }
        if (data.access_token) {
            localStorage.setItem(TOKEN_KEY, data.access_token);
        }
        if (data.refresh_token) {
            localStorage.setItem(REFRESH_KEY, data.refresh_token);
        }
        if (data.customer) {
            localStorage.setItem(CUSTOMER_KEY, JSON.stringify(data.customer));
        }
    }

    function clearSession() {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(REFRESH_KEY);
        localStorage.removeItem(CUSTOMER_KEY);
    }

    function isLoggedIn() {
        return getToken() !== '';
    }

    var refreshInFlight = null;

    function refreshAccessToken() {
        if (refreshInFlight) {
            return refreshInFlight;
        }
        var refresh = getRefresh();
        if (!refresh) {
            return Promise.resolve(false);
        }
        refreshInFlight = fetch(API_BASE + '/auth/refresh-token', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ refresh_token: refresh })
        })
            .then(function (res) { return res.json().then(function (json) { return { res: res, json: json }; }); })
            .then(function (pack) {
                refreshInFlight = null;
                if (pack.json && pack.json.success && pack.json.data) {
                    setSession(pack.json.data);
                    return true;
                }
                clearSession();
                return false;
            })
            .catch(function () {
                refreshInFlight = null;
                return false;
            });
        return refreshInFlight;
    }

    function request(method, path, body, options) {
        options = options || {};
        var headers = {
            'Accept': 'application/json'
        };
        if (!options.multipart) {
            headers['Content-Type'] = 'application/json';
        }
        var token = getToken();
        if (token) {
            headers.Authorization = 'Bearer ' + token;
        }

        var init = { method: method, headers: headers };
        if (options.multipart) {
            init.body = body;
        } else if (body !== undefined && body !== null && method !== 'GET') {
            init.body = JSON.stringify(body);
        }

        var url = API_BASE + path;
        if (method === 'GET' && body && typeof body === 'object') {
            var qs = Object.keys(body)
                .filter(function (k) { return body[k] !== undefined && body[k] !== null && body[k] !== ''; })
                .map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(body[k]); })
                .join('&');
            if (qs) {
                url += (path.indexOf('?') >= 0 ? '&' : '?') + qs;
            }
        }

        return fetch(url, init).then(function (res) {
            return res.json().catch(function () {
                return { success: false, data: null, error: { code: 'INVALID_JSON', message: 'Invalid response.' } };
            }).then(function (json) {
                return { res: res, json: json };
            });
        }).then(function (pack) {
            var code = pack.json && pack.json.error && pack.json.error.code;
            if (pack.res.status === 401 && code === 'UNAUTHORIZED' && !options._retried) {
                return refreshAccessToken().then(function (ok) {
                    if (!ok) {
                        return pack.json;
                    }
                    options._retried = true;
                    return request(method, path, body, options);
                });
            }
            return pack.json;
        });
    }

    var api = {
        base: API_BASE,
        getToken: getToken,
        getCustomer: getCustomer,
        setSession: setSession,
        clearSession: clearSession,
        isLoggedIn: isLoggedIn,

        get: function (path, query) { return request('GET', path, query); },
        post: function (path, body) { return request('POST', path, body); },
        put: function (path, body) { return request('PUT', path, body); },
        del: function (path, body) { return request('DELETE', path, body); },

        sendOtp: function (mobile) { return request('POST', '/auth/send-otp', { mobile: mobile }); },
        verifyOtp: function (mobile, otp) { return request('POST', '/auth/verify-otp', { mobile: mobile, otp: otp }); },
        emailLogin: function (email, password) { return request('POST', '/auth/email-login', { email: email, password: password }); },
        logout: function () {
            var refresh = getRefresh();
            return request('POST', '/auth/logout', { refresh_token: refresh }).finally(function () {
                clearSession();
            });
        },

        categories: function () { return request('GET', '/categories'); },
        category: function (id, query) { return request('GET', '/categories/' + id, query); },
        products: function (query) { return request('GET', '/products', query); },
        product: function (id) { return request('GET', '/products/' + id); },
        search: function (query) { return request('GET', '/products/search', query); },
        similar: function (id) { return request('GET', '/products/' + id + '/similar'); },
        frequentlyBought: function (id) { return request('GET', '/products/' + id + '/frequently-bought-together'); },
        banners: function () { return request('GET', '/banners'); },
        offers: function () { return request('GET', '/offers'); },

        cart: function () { return request('GET', '/cart'); },
        addToCart: function (productId, quantity) {
            return request('POST', '/cart/items', { product_id: productId, quantity: quantity });
        },
        updateCartItem: function (id, quantity) {
            return request('PUT', '/cart/items/' + id, { quantity: quantity });
        },
        removeCartItem: function (id) { return request('DELETE', '/cart/items/' + id); },
        applyCoupon: function (code) { return request('POST', '/cart/coupon', { coupon_code: code }); },
        removeCoupon: function () { return request('DELETE', '/cart/coupon'); },

        wishlist: function () { return request('GET', '/wishlist'); },
        addWishlist: function (productId) { return request('POST', '/wishlist', { product_id: productId }); },
        removeWishlist: function (id) { return request('DELETE', '/wishlist/' + id); },
        moveWishlistToCart: function (id) { return request('POST', '/wishlist/' + id + '/move-to-cart'); },

        profile: function () { return request('GET', '/profile'); },
        updateProfile: function (body) { return request('PUT', '/profile', body); },

        addresses: function () { return request('GET', '/addresses'); },
        createAddress: function (body) { return request('POST', '/addresses', body); },
        updateAddress: function (id, body) { return request('PUT', '/addresses/' + id, body); },
        deleteAddress: function (id) { return request('DELETE', '/addresses/' + id); },
        defaultAddress: function (id) { return request('POST', '/addresses/' + id + '/default'); },

        orders: function (query) { return request('GET', '/orders', query); },
        order: function (id) { return request('GET', '/orders/' + id); },
        placeOrder: function (body) { return request('POST', '/orders', body); },
        cancelOrder: function (id, reason) { return request('POST', '/orders/' + id + '/cancel', { reason: reason || '' }); },
        reorder: function (id) { return request('POST', '/orders/' + id + '/reorder'); },
        deliverySlots: function () { return request('GET', '/delivery-slots'); },

        notifications: function () { return request('GET', '/notifications'); },
        markNotificationRead: function (id) { return request('POST', '/notifications/' + id + '/read'); },
        markAllNotificationsRead: function () { return request('POST', '/notifications/read-all'); },

        businessTypes: function () { return request('GET', '/business-types'); },
        businessRegister: function (body) { return request('POST', '/business/register', body); },
        verificationStatus: function () { return request('GET', '/business/verification-status'); },
        uploadDocument: function (documentType, file) {
            var fd = new FormData();
            fd.append('document_type', documentType);
            fd.append('file', file);
            return request('POST', '/business/documents', fd, { multipart: true });
        },
        documents: function () { return request('GET', '/business/documents'); },
        resubmit: function () { return request('POST', '/business/resubmit', {}); },

        submitBulkEnquiry: function (body) { return request('POST', '/bulk-enquiries', body); }
    };

    global.VC = api;
})(window);
