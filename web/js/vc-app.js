/**
 * VeggiiCart website page wiring — live /api/v1 data.
 */
(function () {
    'use strict';

    var PLACEHOLDER_IMG = 'images/vegiicart-logo.jpeg';

    function pageName() {
        var body = document.body;
        if (body && body.dataset.vcPage) {
            return body.dataset.vcPage;
        }
        var file = (window.location.pathname.split('/').pop() || 'index.php');
        return file.replace(/\.php$/i, '') || 'index';
    }

    function qs(name) {
        return new URLSearchParams(window.location.search).get(name);
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /** "cabbage (small size)" → "Cabbage (Small Size)" regardless of stored casing. */
    function titleCaseName(value) {
        var s = String(value == null ? '' : value).toLocaleLowerCase('en-IN');
        return s.replace(/(^|[^A-Za-z0-9\u00C0-\u024F])([A-Za-z\u00C0-\u024F])/g, function (_, sep, ch) {
            return sep + ch.toLocaleUpperCase('en-IN');
        });
    }

    function money(n) {
        var v = Number(n);
        if (isNaN(v)) {
            v = 0;
        }
        return '₹' + (Math.round(v * 100) / 100).toFixed(0);
    }

    function imgUrl(url) {
        return url || PLACEHOLDER_IMG;
    }

    function isPdfUrl(url) {
        return /\.pdf(\?|#|$)/i.test(String(url || ''));
    }

    function catalogThumb(url, alt) {
        if (url && isPdfUrl(url)) {
            return '<span class="vc-pdf-fill" aria-hidden="true"><i class="fa-solid fa-file-pdf"></i></span>';
        }
        return '<img src="' + escapeHtml(imgUrl(url)) + '" alt="' + escapeHtml(alt || '') + '">';
    }

    function apiErrorMessage(res, fallback) {
        var fields = res && res.error && res.error.fields;
        if (fields && typeof fields === 'object') {
            var parts = Object.keys(fields).map(function (k) { return fields[k]; }).filter(Boolean);
            if (parts.length) {
                return parts.join(' ');
            }
        }
        return (res && res.error && res.error.message) || fallback || 'Something went wrong.';
    }

    function toast(message, type) {
        if (window.VC && typeof window.VC.toast === 'function' && typeof Swal !== 'undefined') {
            return window.VC.toast(message, type);
        }
        var el = document.getElementById('vcToast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'vcToast';
            el.className = 'vc-toast';
            document.body.appendChild(el);
        }
        el.className = 'vc-toast show ' + (type || 'info');
        el.textContent = message;
        clearTimeout(toast._t);
        toast._t = setTimeout(function () {
            el.classList.remove('show');
        }, 3200);
    }

    function loginUrl() {
        return 'login.php?next=' + encodeURIComponent(window.location.pathname + window.location.search);
    }

    function requireAuth() {
        if (VC.isLoggedIn()) {
            return true;
        }
        window.location.href = loginUrl();
        return false;
    }

    function productHref(p) {
        return 'product-details.php?id=' + encodeURIComponent(p.id);
    }

    function categoryHref(c) {
        return 'category-product-listing.php?id=' + encodeURIComponent(c.id);
    }

    function initSwiper(selector, extra) {
        if (typeof Swiper === 'undefined') {
            return;
        }
        var el = document.querySelector(selector);
        if (!el) {
            return;
        }
        if (el.swiper) {
            el.swiper.destroy(true, true);
        }
        var opts = Object.assign({
            loop: el.querySelectorAll('.swiper-slide').length > 3,
            speed: 500,
            spaceBetween: 18,
            grabCursor: true,
            autoplay: { delay: 1800, disableOnInteraction: false, pauseOnMouseEnter: true },
            breakpoints: {
                0: { slidesPerView: 1.2, spaceBetween: 12 },
                480: { slidesPerView: 2, spaceBetween: 12 },
                768: { slidesPerView: 3, spaceBetween: 15 },
                1100: { slidesPerView: 4, spaceBetween: 18 }
            }
        }, extra || {});
        new Swiper(selector, opts);
    }

    function bindProductActions(root) {
        (root || document).addEventListener('click', function (e) {
            var cartBtn = e.target.closest('[data-add-cart]');
            if (cartBtn) {
                e.preventDefault();
                addToCart(Number(cartBtn.getAttribute('data-add-cart')), Number(cartBtn.getAttribute('data-qty') || 0));
                return;
            }
            var wishBtn = e.target.closest('[data-add-wish]');
            if (wishBtn) {
                e.preventDefault();
                toggleWish(Number(wishBtn.getAttribute('data-add-wish')), wishBtn);
            }
        });
    }

    function addToCart(productId, qty) {
        if (!productId) {
            return;
        }
        if (!VC.isLoggedIn()) {
            toast('Please login to add items to cart.', 'error');
            window.location.href = loginUrl();
            return;
        }
        var quantity = qty && qty > 0 ? qty : 1;
        VC.product(productId).then(function (res) {
            var p = res && res.data && res.data.product;
            if (p && p.moq && quantity < p.moq) {
                quantity = p.moq;
            }
            return VC.addToCart(productId, quantity);
        }).then(function (res) {
            if (res && res.success) {
                toast('Added to cart');
                refreshHeaderCounts();
            } else {
                toast((res && res.error && res.error.message) || 'Could not add to cart.', 'error');
            }
        }).catch(function () {
            toast('Could not add to cart.', 'error');
        });
    }

    function toggleWish(productId, btn) {
        if (!VC.isLoggedIn()) {
            toast('Please login to use wishlist.', 'error');
            window.location.href = loginUrl();
            return;
        }
        VC.addWishlist(productId).then(function (res) {
            if (res && res.success) {
                if (btn) {
                    btn.classList.add('active');
                    var icon = btn.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                    }
                }
                toast('Saved to wishlist');
                refreshHeaderCounts();
            } else {
                toast((res && res.error && res.error.message) || 'Could not update wishlist.', 'error');
            }
        });
    }

    function listProductCard(p) {
        var stock = p.in_stock
            ? '<div class="vc-list-stock"><i class="fa-solid fa-circle"></i> In Stock</div>'
            : '<div class="vc-list-stock out"><i class="fa-solid fa-circle"></i> Out of Stock</div>';
        return (
            '<article class="vc-list-product-card" data-name="' + escapeHtml(titleCaseName(p.name)) + '" data-category="' + escapeHtml(p.category_id) + '" data-price="' + escapeHtml(p.price) + '">' +
                '<div class="vc-list-image">' +
                    (p.in_stock ? '' : '<span class="vc-list-badge">Out of stock</span>') +
                    '<a href="' + productHref(p) + '">' + catalogThumb(p.image_url, titleCaseName(p.name)) + '</a>' +
                    '<button type="button" class="vc-list-wishlist" data-add-wish="' + p.id + '" aria-label="Wishlist"><i class="fa-regular fa-heart"></i></button>' +
                    '<div class="vc-list-quick-actions"><a href="' + productHref(p) + '" title="View product"><i class="fa-regular fa-eye"></i></a></div>' +
                '</div>' +
                '<div class="vc-list-content">' +
                    '<span class="vc-list-category">' + escapeHtml(p.category_name || '') + '</span>' +
                    '<h3><a href="' + productHref(p) + '">' + escapeHtml(titleCaseName(p.name)) + '</a></h3>' +
                    '<div class="vc-list-pack">' + escapeHtml(p.unit || '') + (p.moq ? ' · MOQ ' + p.moq : '') + '</div>' +
                    '<div class="vc-list-price"><strong>' + money(p.price) + '</strong></div>' +
                    stock +
                    '<button type="button" class="vc-list-cart-btn" data-add-cart="' + p.id + '" data-qty="' + (p.moq || 1) + '">' +
                        '<i class="fa-solid fa-basket-shopping"></i> Add to Cart' +
                    '</button>' +
                '</div>' +
            '</article>'
        );
    }

    function sliderProductCard(p, prefix) {
        prefix = prefix || 'vbest';
        return (
            '<div class="swiper-slide">' +
                '<article class="' + prefix + '-card">' +
                    '<button class="' + prefix + '-wishlist" type="button" data-add-wish="' + p.id + '"><i class="fa-regular fa-heart"></i></button>' +
                    '<a href="' + productHref(p) + '" class="' + prefix + '-image">' +
                        catalogThumb(p.image_url, titleCaseName(p.name)) +
                    '</a>' +
                    '<div class="' + prefix + '-content">' +
                        '<span class="' + prefix + '-category">' + escapeHtml(p.category_name || '') + '</span>' +
                        '<h3>' + escapeHtml(titleCaseName(p.name)) + '</h3>' +
                        '<div class="' + prefix + '-price-row">' +
                            '<div class="' + prefix + '-price"><strong>' + money(p.price) + '</strong><span>/ ' + escapeHtml(p.unit || '') + '</span></div>' +
                            '<button class="' + prefix + '-cart-btn" type="button" data-add-cart="' + p.id + '" data-qty="' + (p.moq || 1) + '">' +
                                '<i class="fa-solid fa-cart-plus"></i>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</article>' +
            '</div>'
        );
    }

    function categoryCard(c) {
        return (
            '<a href="' + categoryHref(c) + '" class="vcat-card">' +
                '<div class="vcat-image">' +
                    catalogThumb(c.image_url, c.name) +
                    '<span class="vcat-count">' + escapeHtml(c.product_count || 0) + ' Items</span>' +
                '</div>' +
                '<div class="vcat-content">' +
                    '<div class="vcat-icon"><i class="fa-solid fa-leaf"></i></div>' +
                    '<div><h3>' + escapeHtml(c.name) + '</h3><p>Shop this category</p></div>' +
                    '<span class="vcat-arrow"><i class="fa-solid fa-arrow-right"></i></span>' +
                '</div>' +
            '</a>'
        );
    }

    function emptyNote(text, extraHtml) {
        return (
            '<div class="vc-live-empty-box">' +
                '<p class="vc-live-empty">' + escapeHtml(text) + '</p>' +
                (extraHtml || '') +
            '</div>'
        );
    }

    function setText(id, value) {
        var el = typeof id === 'string' ? document.getElementById(id) : id;
        if (el) {
            el.textContent = (value === null || value === undefined || String(value).trim() === '') ? '—' : String(value);
        }
    }

    function formatInDate(value) {
        if (!value) {
            return '—';
        }
        var d = new Date(String(value).replace(' ', 'T'));
        if (isNaN(d.getTime())) {
            return String(value);
        }
        return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function formatBizId(c) {
        if (!c || !c.id) {
            return '—';
        }
        var year = String(c.created_at || '').slice(0, 4) || String(new Date().getFullYear());
        return 'VC-BIZ-' + year + '-' + String(c.id).padStart(4, '0');
    }

    function kycUi(status) {
        var s = String(status || 'pending').toLowerCase();
        if (s === 'approved') {
            return { cls: 'approved', icon: 'fa-circle-check', title: 'Verified Business', side: 'Approved', sub: 'Business successfully verified' };
        }
        if (s === 'rejected') {
            return { cls: 'rejected', icon: 'fa-circle-xmark', title: 'Verification rejected', side: 'Rejected', sub: 'Please resubmit your documents' };
        }
        return { cls: 'pending', icon: 'fa-clock', title: 'Pending verification', side: 'Pending', sub: 'Verification in progress' };
    }

    function typeLabel(t) {
        var map = {
            retailer: 'Retail Shop',
            kirana: 'Kirana Store',
            wholesaler: 'Wholesaler',
            restaurant: 'Restaurant / Hotel',
            canteen: 'Canteen',
            other: 'Other'
        };
        return map[t] || t || '—';
    }

    function addrLine(a) {
        if (!a) {
            return '';
        }
        return [a.line1, a.line2, a.landmark, a.city, a.state, a.pincode].filter(Boolean).join(', ');
    }

    function displayName(c) {
        return (c && (c.owner_name || c.business_name || c.mobile)) || 'Customer';
    }

    function initialsFrom(c) {
        var n = String(displayName(c) || '').trim();
        if (!n || n === 'Customer') {
            return '?';
        }
        var parts = n.split(/\s+/).filter(Boolean);
        if (parts.length >= 2) {
            return (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase();
        }
        return n.slice(0, 2).toUpperCase();
    }

    function paintProfileAvatar(customer) {
        var initialsEl = document.getElementById('vgProfileAvatarInitials');
        var imgEl = document.getElementById('vgProfileAvatarImg');
        var wrap = document.getElementById('vgProfileAvatar');
        var url = customer && customer.avatar_url ? String(customer.avatar_url) : '';
        if (initialsEl) {
            initialsEl.textContent = initialsFrom(customer);
        }
        if (imgEl) {
            if (url) {
                imgEl.src = url;
                imgEl.hidden = false;
                if (wrap) wrap.classList.add('has-photo');
            } else {
                imgEl.removeAttribute('src');
                imgEl.hidden = true;
                if (wrap) wrap.classList.remove('has-photo');
            }
        }
        var dashAvatar = document.querySelector('.vc-account-avatar');
        if (dashAvatar) {
            if (url) {
                dashAvatar.innerHTML = '<img src="' + escapeHtml(url) + '" alt="">';
                dashAvatar.classList.add('has-photo');
            } else {
                dashAvatar.innerHTML = '<span>' + escapeHtml(initialsFrom(customer)) + '</span>';
                dashAvatar.classList.remove('has-photo');
            }
        }
    }

    function bindProfileAvatarUpload() {
        if (pageName() !== 'my-profile') {
            return;
        }
        var btn = document.getElementById('vgProfileAvatarBtn');
        var fileInput = document.getElementById('vgProfileAvatarFile');
        if (!btn || !fileInput || btn.getAttribute('data-vc-bound')) {
            return;
        }
        btn.setAttribute('data-vc-bound', '1');
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            fileInput.click();
        });
        fileInput.addEventListener('change', function () {
            var file = fileInput.files && fileInput.files[0];
            if (!file) {
                return;
            }
            if (!/^image\//.test(file.type)) {
                toast('Please choose an image file (JPG, PNG, or WebP).', 'error');
                fileInput.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                toast('Image must be under 5 MB.', 'error');
                fileInput.value = '';
                return;
            }
            btn.disabled = true;
            toast('Uploading photo…');
            VC.uploadAvatar(file).then(function (res) {
                btn.disabled = false;
                fileInput.value = '';
                if (res && res.success) {
                    VC.setSession({ customer: res.data });
                    paintProfileAvatar(res.data);
                    applyHeaderCustomer(res.data);
                    toast('Profile photo updated');
                } else {
                    toast((res && res.error && res.error.message) || 'Could not upload photo.', 'error');
                }
            }).catch(function () {
                btn.disabled = false;
                fileInput.value = '';
                toast('Could not upload photo.', 'error');
            });
        });
    }

    function applyHeaderCustomer(customer) {
        var account = document.querySelector('.vc-account');
        var mobileAccount = document.querySelector('.vc-mobile-account a');
        if (!VC.isLoggedIn()) {
            return;
        }
        var name = displayName(customer);
        if (account) {
            account.href = 'account-dashboard.php';
            var strong = account.querySelector('strong');
            var small = account.querySelector('small');
            if (small) small.textContent = 'Hello';
            if (strong) strong.textContent = name;
        }
        if (mobileAccount) {
            mobileAccount.href = 'account-dashboard.php';
            var mStrong = mobileAccount.querySelector('strong');
            var mSmall = mobileAccount.querySelector('small');
            if (mSmall) mSmall.textContent = 'Welcome back';
            if (mStrong) mStrong.textContent = name;
        }
    }

    var VIEW_KEY = 'vc_recent_views';

    function loadRecentViews() {
        try {
            var raw = JSON.parse(localStorage.getItem(VIEW_KEY) || '[]');
            if (!Array.isArray(raw)) {
                return [];
            }
            return raw.map(Number).filter(function (id) { return id > 0; });
        } catch (e) {
            return [];
        }
    }

    function rememberView(id) {
        id = Number(id);
        if (!id) {
            return;
        }
        var ids = loadRecentViews().filter(function (x) { return x !== id; });
        ids.unshift(id);
        try {
            localStorage.setItem(VIEW_KEY, JSON.stringify(ids.slice(0, 8)));
        } catch (e) { /* ignore quota */ }
    }

    function timeAgo(iso) {
        if (!iso) {
            return '';
        }
        var t = Date.parse(iso.replace(' ', 'T'));
        if (!t) {
            return '';
        }
        var days = Math.round((Date.now() - t) / 86400000);
        if (days <= 0) {
            return 'Today';
        }
        if (days === 1) {
            return 'Yesterday';
        }
        return days + ' days ago';
    }

    /* ---------- Header ---------- */

    function locationShortLabel(a) {
        if (!a) {
            return 'Select Location';
        }
        var primary = String(a.label || a.city || a.landmark || a.line1 || '').trim();
        var secondary = String(a.pincode || a.city || '').trim();
        if (primary && secondary && primary.toLowerCase() !== secondary.toLowerCase()) {
            return primary + ' · ' + secondary;
        }
        return primary || secondary || 'Select Location';
    }

    function paintHeaderLocation(address) {
        var label = locationShortLabel(address);
        var desktop = document.getElementById('vcHeaderLocationLabel');
        var mobile = document.getElementById('vcMobileLocationLabel');
        if (desktop) {
            desktop.textContent = label;
            desktop.title = address ? addrLine(address) : 'Select delivery location';
        }
        if (mobile) {
            mobile.textContent = label;
            mobile.title = address ? addrLine(address) : 'Select delivery location';
        }
        var wrap = document.getElementById('vcLocationWrap');
        if (wrap) {
            wrap.classList.toggle('has-location', !!address);
        }
    }

    function refreshHeaderLocation() {
        if (!VC.isLoggedIn()) {
            paintHeaderLocation(null);
            return Promise.resolve(null);
        }
        return VC.addresses().then(function (res) {
            var list = (res && res.success && res.data && res.data.addresses) || [];
            var chosen = list.find(function (a) { return a.is_default; }) || list[0] || null;
            paintHeaderLocation(chosen);
            return chosen;
        }).catch(function () {
            paintHeaderLocation(null);
            return null;
        });
    }

    function locationOptionHtml(a) {
        return (
            '<button type="button" class="vc-location-option' + (a.is_default ? ' is-active' : '') + '" data-loc-id="' + a.id + '" role="option" aria-selected="' + (a.is_default ? 'true' : 'false') + '">' +
                '<i class="fa-solid fa-location-dot" aria-hidden="true"></i>' +
                '<div>' +
                    '<strong>' + escapeHtml(a.label || 'Address') + '</strong>' +
                    '<span>' + escapeHtml(addrLine(a)) + '</span>' +
                    (a.is_default ? '<em>Default delivery</em>' : '') +
                '</div>' +
            '</button>'
        );
    }

    function fillLocationLists(list) {
        var html;
        if (!list.length) {
            html = '<p class="vc-location-dropdown-empty">No saved address yet. Add one to set your delivery location.</p>';
        } else {
            html = list.map(locationOptionHtml).join('');
        }
        var desktopList = document.getElementById('vcLocationDropdownList');
        var mobileList = document.getElementById('vcMobileLocationList');
        if (desktopList) {
            desktopList.innerHTML = html;
        }
        if (mobileList) {
            mobileList.innerHTML = html;
        }
    }

    function closeHeaderLocationDropdown() {
        var wrap = document.getElementById('vcLocationWrap');
        var dropdown = document.getElementById('vcLocationDropdown');
        var btn = document.getElementById('vcHeaderLocation');
        if (wrap) {
            wrap.classList.remove('is-open');
        }
        if (dropdown) {
            dropdown.hidden = true;
        }
        if (btn) {
            btn.setAttribute('aria-expanded', 'false');
        }
        var mobileBtn = document.getElementById('vcMobileLocation');
        var mobilePanel = document.getElementById('vcMobileLocationPanel');
        if (mobileBtn) {
            mobileBtn.setAttribute('aria-expanded', 'false');
        }
        if (mobilePanel) {
            mobilePanel.hidden = true;
        }
    }

    function openDesktopLocationDropdown() {
        var wrap = document.getElementById('vcLocationWrap');
        var dropdown = document.getElementById('vcLocationDropdown');
        var btn = document.getElementById('vcHeaderLocation');
        if (!dropdown || !btn) {
            return;
        }
        wrap && wrap.classList.add('is-open');
        dropdown.hidden = false;
        btn.setAttribute('aria-expanded', 'true');
        loadLocationDropdownContent();
    }

    function openMobileLocationPanel() {
        var mobileBtn = document.getElementById('vcMobileLocation');
        var mobilePanel = document.getElementById('vcMobileLocationPanel');
        if (!mobilePanel || !mobileBtn) {
            return;
        }
        mobilePanel.hidden = false;
        mobileBtn.setAttribute('aria-expanded', 'true');
        loadLocationDropdownContent();
    }

    function loadLocationDropdownContent() {
        var desktopList = document.getElementById('vcLocationDropdownList');
        var mobileList = document.getElementById('vcMobileLocationList');
        var loading = '<p class="vc-location-dropdown-loading">Loading addresses…</p>';
        if (desktopList) {
            desktopList.innerHTML = loading;
        }
        if (mobileList) {
            mobileList.innerHTML = loading;
        }

        if (!VC.isLoggedIn()) {
            var guestHtml = '<p class="vc-location-dropdown-empty">Please <a href="' + escapeHtml(loginUrl()) + '">login</a> to choose a delivery address.</p>';
            if (desktopList) {
                desktopList.innerHTML = guestHtml;
            }
            if (mobileList) {
                mobileList.innerHTML = guestHtml;
            }
            return;
        }

        VC.addresses().then(function (res) {
            var list = (res && res.success && res.data && res.data.addresses) || [];
            fillLocationLists(list);
            var chosen = list.find(function (a) { return a.is_default; }) || list[0] || null;
            paintHeaderLocation(chosen);
        }).catch(function () {
            var err = '<p class="vc-location-dropdown-empty">Could not load addresses. Please try again.</p>';
            if (desktopList) {
                desktopList.innerHTML = err;
            }
            if (mobileList) {
                mobileList.innerHTML = err;
            }
        });
    }

    function selectHeaderLocation(id) {
        if (!VC.isLoggedIn()) {
            window.location.href = loginUrl();
            return;
        }
        var buttons = document.querySelectorAll('.vc-location-option[data-loc-id="' + id + '"]');
        buttons.forEach(function (b) { b.disabled = true; });
        VC.addresses().then(function (res) {
            var list = (res && res.success && res.data && res.data.addresses) || [];
            var chosen = list.find(function (a) { return String(a.id) === String(id); });
            if (!chosen) {
                buttons.forEach(function (b) { b.disabled = false; });
                toast('Address not found.', 'error');
                return null;
            }
            if (chosen.is_default) {
                paintHeaderLocation(chosen);
                fillLocationLists(list);
                closeHeaderLocationDropdown();
                toast('Delivery location selected');
                return null;
            }
            return VC.defaultAddress(id).then(function (setRes) {
                if (setRes && setRes.success) {
                    var updated = (setRes.data && setRes.data.address) || chosen;
                    paintHeaderLocation(updated);
                    return VC.addresses().then(function (again) {
                        var next = (again && again.success && again.data && again.data.addresses) || [];
                        fillLocationLists(next);
                        closeHeaderLocationDropdown();
                        toast('Delivery location updated');
                    });
                }
                buttons.forEach(function (b) { b.disabled = false; });
                toast((setRes && setRes.error && setRes.error.message) || 'Could not update location.', 'error');
                return null;
            });
        }).catch(function () {
            buttons.forEach(function (b) { b.disabled = false; });
            toast('Could not update location.', 'error');
        });
    }

    function bindHeaderLocation() {
        if (document.documentElement.getAttribute('data-vc-loc-bound') === '1') {
            return;
        }
        document.documentElement.setAttribute('data-vc-loc-bound', '1');

        document.addEventListener('click', function (e) {
            var option = e.target.closest('[data-loc-id]');
            if (option && (option.closest('#vcLocationDropdown') || option.closest('#vcMobileLocationPanel'))) {
                e.preventDefault();
                e.stopPropagation();
                selectHeaderLocation(option.getAttribute('data-loc-id'));
                return;
            }

            var desktopBtn = e.target.closest('#vcHeaderLocation');
            if (desktopBtn) {
                e.preventDefault();
                e.stopPropagation();
                var dropdown = document.getElementById('vcLocationDropdown');
                var isOpen = dropdown && !dropdown.hidden;
                if (isOpen) {
                    closeHeaderLocationDropdown();
                } else {
                    closeHeaderLocationDropdown();
                    openDesktopLocationDropdown();
                }
                return;
            }

            var mobileBtn = e.target.closest('#vcMobileLocation');
            if (mobileBtn) {
                e.preventDefault();
                e.stopPropagation();
                var panel = document.getElementById('vcMobileLocationPanel');
                var open = panel && !panel.hidden;
                if (open) {
                    closeHeaderLocationDropdown();
                } else {
                    openMobileLocationPanel();
                }
                return;
            }

            if (!e.target.closest('#vcLocationWrap') && !e.target.closest('#vcMobileLocationPanel') && !e.target.closest('#vcMobileLocation')) {
                closeHeaderLocationDropdown();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeHeaderLocationDropdown();
            }
        });
    }

    function refreshHeaderCounts() {
        var cartEl = document.querySelector('.vc-action[href="cart.php"] .vc-count, #vcHeaderCartCount');
        var wishEl = document.querySelector('.vc-action[href="wishlist.php"] .vc-count, #vcHeaderWishlistCount');
        if (!VC.isLoggedIn()) {
            if (cartEl) cartEl.textContent = '0';
            if (wishEl) wishEl.textContent = '0';
            return;
        }
        VC.cart().then(function (res) {
            if (cartEl && res && res.success) {
                cartEl.textContent = String(res.data.item_count || 0);
            }
        });
        VC.wishlist().then(function (res) {
            if (wishEl && res && res.success) {
                wishEl.textContent = String(res.data.count || 0);
            }
        });
    }

    function bootHeader() {
        applyHeaderCustomer(VC.getCustomer());
        if (VC.isLoggedIn()) {
            VC.profile().then(function (res) {
                if (res && res.success) {
                    VC.setSession({ customer: res.data });
                    applyHeaderCustomer(res.data);
                }
            });
        }

        document.querySelectorAll('.vc-search, .vc-mobile-search').forEach(function (form) {
            form.setAttribute('action', 'product-search.php');
            form.setAttribute('method', 'get');
            var input = form.querySelector('input[type="search"], input[name="search"], input');
            if (input) {
                input.setAttribute('name', 'q');
            }
        });

        var offers = document.querySelectorAll('a[href="offers.php"]');
        offers.forEach(function (a) { a.setAttribute('href', 'offer.php'); });

        VC.categories().then(function (res) {
            if (!res || !res.success) {
                return;
            }
            var cats = (res.data && res.data.categories) || [];
            var drop = document.getElementById('vcCategoryDropdown');
            if (drop) {
                drop.innerHTML = cats.map(function (c) {
                    return '<a href="' + categoryHref(c) + '"><i class="fa-solid fa-leaf"></i> ' + escapeHtml(c.name) + '</a>';
                }).join('') || '<a href="category.php">All Categories</a>';
            }
            var menu = document.querySelector('.vc-desktop-menu');
            if (menu) {
                var extras = menu.querySelectorAll('a[href="contact.php"], a[href="about.php"]');
                var contactHtml = '<a href="about.php">About Us</a>';
                menu.innerHTML =
                    '<a href="index.php"' + (pageName() === 'index' ? ' class="active"' : '') + '>Home</a>' +
                    cats.slice(0, 6).map(function (c) {
                        return '<a href="' + categoryHref(c) + '">' + escapeHtml(c.name) + '</a>';
                    }).join('') +
                    '<a href="offer.php">Offers</a>' +
                    contactHtml;
            }
            var mobileNav = document.querySelector('.vc-mobile-nav');
            if (mobileNav) {
                mobileNav.innerHTML =
                    '<a href="index.php"><i class="fa-solid fa-house"></i> Home</a>' +
                    cats.map(function (c) {
                        return '<a href="' + categoryHref(c) + '"><i class="fa-solid fa-leaf"></i> ' + escapeHtml(c.name) + '</a>';
                    }).join('') +
                    '<a href="offer.php"><i class="fa-solid fa-tags"></i> Offers</a>' +
                    '<a href="wishlist.php"><i class="fa-regular fa-heart"></i> Wishlist</a>' +
                    '<a href="about.php"><i class="fa-regular fa-envelope"></i> About Us</a>';
            }
        });

        bindHeaderLocation();
        refreshHeaderLocation();
        refreshHeaderCounts();
        bindProductActions(document);
    }

    /* ---------- Home ---------- */

    function initHeroSlider() {
        var slider = document.getElementById('vghSlider');
        if (!slider) {
            return;
        }
        var slides = slider.querySelectorAll('.vgh-slide');
        var dots = slider.querySelectorAll('.vgh-dot');
        var nextButton = slider.querySelector('.vgh-next');
        var prevButton = slider.querySelector('.vgh-prev');
        var current = 0;
        var timer;
        function show(index) {
            if (!slides.length) return;
            if (index >= slides.length) index = 0;
            if (index < 0) index = slides.length - 1;
            slides.forEach(function (s) { s.classList.remove('active'); });
            dots.forEach(function (d) { d.classList.remove('active'); });
            slides[index].classList.add('active');
            if (dots[index]) dots[index].classList.add('active');
            current = index;
        }
        function start() {
            stop();
            timer = setInterval(function () { show(current + 1); }, 6000);
        }
        function stop() {
            if (timer) clearInterval(timer);
        }
        if (nextButton) nextButton.onclick = function () { show(current + 1); start(); };
        if (prevButton) prevButton.onclick = function () { show(current - 1); start(); };
        dots.forEach(function (dot) {
            dot.onclick = function () {
                show(parseInt(dot.getAttribute('data-slide'), 10) || 0);
                start();
            };
        });
        show(0);
        start();
    }

    function bootHome() {
        var slidesWrap = document.querySelector('#vghSlider .vgh-slides');
        if (slidesWrap) {
            slidesWrap.innerHTML = '';
        }
        ['.vbestSlider .swiper-wrapper'].forEach(function (sel) {
            var w = document.querySelector(sel);
            if (w) w.innerHTML = '';
        });
        var catRows = document.getElementById('vcCategoryProductRows');
        if (catRows) {
            catRows.innerHTML = '';
        }
        var dotsWrap = document.querySelector('#vghSlider .vgh-dots, #vghSlider .vgh-pagination, .vgh-dots');
        if (!dotsWrap) {
            dotsWrap = document.querySelector('#vghSlider') && document.querySelector('#vghSlider').querySelector('[class*="dot"]');
        }
        var dotsParent = document.querySelector('#vghSlider .vgh-dots') || document.querySelector('#vghSlider > .vgh-dots') || (function () {
            var nodes = document.querySelectorAll('#vghSlider > div');
            return nodes[nodes.length - 1];
        })();

        VC.banners().then(function (res) {
            var banners = (res && res.success && res.data.banners) || [];
            if (slidesWrap && banners.length) {
                slidesWrap.innerHTML = banners.map(function (b, i) {
                    var heading = b.title ? '<h1>' + escapeHtml(b.title) + '</h1>' : '';
                    var desc = b.description ? '<p class="vgh-lead">' + escapeHtml(b.description) + '</p>' : '';
                    var media = (b.image_url && isPdfUrl(b.image_url))
                        ? '<iframe class="vgh-slide-pdf" src="' + escapeHtml(imgUrl(b.image_url)) + '#toolbar=0" title="' + escapeHtml(b.title || 'Banner') + '"></iframe>'
                        : '<img src="' + escapeHtml(imgUrl(b.image_url)) + '" alt="' + escapeHtml(b.title || 'Banner') + '">';
                    return (
                        '<article class="vgh-slide' + (i === 0 ? ' active' : '') + '">' +
                            media +
                            '<div class="vgh-overlay"></div>' +
                            '<div class="vgh-container"><div class="vgh-content">' +
                                '<span class="vgh-badge"><i class="fa-solid fa-leaf"></i> Fresh Every Day</span>' +
                                heading +
                                desc +
                                '<div class="vgh-actions">' +
                                    '<a href="' + escapeHtml(b.link || 'product.php') + '" class="vgh-primary-btn">Shop Now <i class="fa-solid fa-arrow-right"></i></a>' +
                                '</div>' +
                            '</div></div>' +
                        '</article>'
                    );
                }).join('');
                var dotsBox = document.querySelector('#vghSlider .vgh-dots');
                if (dotsBox) {
                    dotsBox.innerHTML = banners.map(function (_, i) {
                        return '<button class="vgh-dot' + (i === 0 ? ' active' : '') + '" type="button" data-slide="' + i + '" aria-label="Slide ' + (i + 1) + '"></button>';
                    }).join('');
                }
            }
        }).finally(function () {
            initHeroSlider();
        });

        VC.categories().then(function (res) {
            var cats = (res && res.success && res.data.categories) || [];
            var grid = document.querySelector('.vcat-grid');
            if (grid) {
                grid.id = grid.id || 'vcHomeCategories';
                grid.innerHTML = cats.length ? cats.map(categoryCard).join('') : emptyNote('No categories yet.');
            }
            var viewAll = document.querySelector('.vcat-view-all');
            if (viewAll) {
                viewAll.setAttribute('href', 'category.php');
            }
        });

        VC.products({ per_page: 16 }).then(function (res) {
            var products = (res && res.success && res.data.products) || [];
            fillSlider('.vbestSlider .swiper-wrapper', products.slice(0, 8), 'vbest');
            initSwiper('.vbestSlider', { navigation: { nextEl: '.vbest-next', prevEl: '.vbest-prev' }, pagination: { el: '.vbest-pagination', clickable: true } });
        });

        renderHomeCategoryRows();

        document.querySelectorAll('a[href="shop.php"], a[href="products.php"]').forEach(function (a) {
            a.setAttribute('href', 'product.php');
        });
        document.querySelectorAll('a[href="offers.php"]').forEach(function (a) {
            a.setAttribute('href', 'offer.php');
        });
        document.querySelectorAll('a[href="fruits.php"], a[href="vegetables.php"], a[href="bulk-orders.php"]').forEach(function (a) {
            a.setAttribute('href', 'category.php');
        });

        bootHomeContinue();
    }

    function categoryProductRowHtml(cat, products, uid) {
        var slides = products.map(function (p) { return sliderProductCard(p, 'vfresh'); }).join('');
        return (
            '<section class="vfresh-section vc-home-cat-row" data-category-id="' + escapeHtml(cat.id) + '">' +
                '<div class="vfresh-container">' +
                    '<div class="vfresh-heading">' +
                        '<div>' +
                            '<span class="vfresh-subtitle"><i class="fa-solid fa-leaf"></i> Shop by Category</span>' +
                            '<h2>' + escapeHtml(titleCaseName(cat.name)) + ' <span>Products</span></h2>' +
                            '<p>Fresh picks from ' + escapeHtml(titleCaseName(cat.name)) + ' — quality produce for bulk and everyday orders.</p>' +
                        '</div>' +
                        '<div class="vfresh-heading-actions">' +
                            '<button class="vfresh-nav ' + uid + '-prev" type="button" aria-label="Previous">' +
                                '<i class="fa-solid fa-chevron-left"></i>' +
                            '</button>' +
                            '<button class="vfresh-nav ' + uid + '-next" type="button" aria-label="Next">' +
                                '<i class="fa-solid fa-chevron-right"></i>' +
                            '</button>' +
                            '<a href="' + categoryHref(cat) + '" class="vfresh-view-all">' +
                                'View All <i class="fa-solid fa-arrow-right"></i>' +
                            '</a>' +
                        '</div>' +
                    '</div>' +
                    '<div class="swiper vfreshSlider ' + uid + '-slider">' +
                        '<div class="swiper-wrapper">' + slides + '</div>' +
                        '<div class="swiper-pagination ' + uid + '-pagination"></div>' +
                    '</div>' +
                '</div>' +
            '</section>'
        );
    }

    function renderHomeCategoryRows() {
        var host = document.getElementById('vcCategoryProductRows');
        if (!host) {
            return;
        }
        host.innerHTML = '<div class="vfresh-section"><div class="vfresh-container">' + emptyNote('Loading categories…') + '</div></div>';

        VC.categories().then(function (res) {
            var cats = (res && res.success && res.data.categories) || [];
            cats = cats.filter(function (c) { return Number(c.product_count || 0) > 0; });
            if (!cats.length) {
                host.innerHTML = '';
                return null;
            }
            return Promise.all(cats.map(function (c) {
                return VC.products({ category_id: c.id, per_page: 12 }).then(function (pr) {
                    return {
                        cat: c,
                        products: (pr && pr.success && pr.data.products) || []
                    };
                });
            }));
        }).then(function (rows) {
            if (!rows) {
                return;
            }
            rows = rows.filter(function (r) { return r.products.length > 0; });
            if (!rows.length) {
                host.innerHTML = '';
                return;
            }
            host.innerHTML = rows.map(function (r) {
                return categoryProductRowHtml(r.cat, r.products, 'vcatrow-' + r.cat.id);
            }).join('');
            rows.forEach(function (r) {
                var uid = 'vcatrow-' + r.cat.id;
                initSwiper('.' + uid + '-slider', {
                    navigation: { nextEl: '.' + uid + '-next', prevEl: '.' + uid + '-prev' },
                    pagination: { el: '.' + uid + '-pagination', clickable: true }
                });
            });
        }).catch(function () {
            host.innerHTML = '';
        });
    }

    function bootHomeContinue() {
        var section = document.getElementById('vcRecentSection') || document.querySelector('.vrc-section');
        if (!section) {
            return;
        }
        section.hidden = true;
        var heading = document.getElementById('vcRecentHeading');
        var main = document.getElementById('vcRecentMain');
        var viewedArea = document.getElementById('vcRecentViewedArea');
        var orderList = document.getElementById('vcRecentOrders');
        var viewedGrid = document.getElementById('vcRecentViewed');
        var viewIds = loadRecentViews();

        var ordersPromise = VC.isLoggedIn()
            ? VC.orders({ per_page: 5 })
            : Promise.resolve(null);

        Promise.all([
            ordersPromise,
            viewIds.length ? VC.products({ per_page: 50 }) : Promise.resolve(null)
        ]).then(function (pack) {
            var orderRes = pack[0];
            var prodRes = pack[1];
            var orders = (orderRes && orderRes.success && orderRes.data.orders) || [];
            var hasOrders = VC.isLoggedIn() && orders.length > 0;
            var catalog = (prodRes && prodRes.success && prodRes.data.products) || [];
            var byId = {};
            catalog.forEach(function (p) { byId[p.id] = p; });
            var viewed = viewIds.map(function (id) { return byId[id]; }).filter(Boolean);
            var hasViews = viewed.length > 0;

            if (heading) heading.hidden = !hasOrders;
            if (main) main.hidden = !hasOrders;
            if (viewedArea) viewedArea.hidden = !hasViews;

            if (hasViews && viewedGrid) {
                viewedGrid.innerHTML = viewed.map(function (p) {
                    return (
                        '<a href="' + productHref(p) + '" class="vrc-viewed-card">' +
                            '<div class="vrc-viewed-image">' + catalogThumb(p.image_url, titleCaseName(p.name)) + '</div>' +
                            '<div class="vrc-viewed-content">' +
                                '<span>' + escapeHtml(p.category_name || '') + '</span>' +
                                '<h4>' + escapeHtml(titleCaseName(p.name)) + '</h4>' +
                                '<div><strong>' + money(p.price) + '</strong><small> / ' + escapeHtml(p.unit || '') + '</small></div>' +
                            '</div>' +
                            '<span class="vrc-viewed-arrow"><i class="fa-solid fa-arrow-right"></i></span>' +
                        '</a>'
                    );
                }).join('');
            }

            if (!hasOrders) {
                if (hasViews) {
                    section.hidden = false;
                }
                return;
            }

            var first = orders[0];
            VC.order(first.id).then(function (detailRes) {
                var order = (detailRes && detailRes.success && detailRes.data.order) || first;
                var items = order.items || [];
                var unique = [];
                var seen = {};
                items.forEach(function (line) {
                    if (!line.product_id || seen[line.product_id]) {
                        return;
                    }
                    seen[line.product_id] = true;
                    unique.push(line);
                });
                unique = unique.slice(0, 4);
                return Promise.all(unique.map(function (line) {
                    return VC.product(line.product_id).then(function (pr) {
                        var p = pr && pr.success && pr.data.product;
                        return {
                            line: line,
                            product: p,
                            when: timeAgo(order.placed_at)
                        };
                    }).catch(function () {
                        return { line: line, product: null, when: timeAgo(order.placed_at) };
                    });
                }));
            }).then(function (rows) {
                if (!orderList || !rows || !rows.length) {
                    if (main) main.hidden = true;
                    if (heading) heading.hidden = true;
                    section.hidden = !hasViews;
                    return;
                }
                orderList.innerHTML = rows.map(function (row) {
                    var line = row.line;
                    var p = row.product;
                    var name = titleCaseName((p && p.name) || line.name);
                    var img = (p && p.image_url) || PLACEHOLDER_IMG;
                    var cat = (p && p.category_name) || '';
                    return (
                        '<article class="vrc-order-item">' +
                            '<div class="vrc-order-image">' +
                                catalogThumb(img, name) +
                                (row.when ? '<span>' + escapeHtml(row.when) + '</span>' : '') +
                            '</div>' +
                            '<div class="vrc-order-content">' +
                                '<small>' + escapeHtml(cat) + '</small>' +
                                '<h4>' + escapeHtml(name) + '</h4>' +
                                '<div class="vrc-order-meta">' +
                                    '<span><i class="fa-solid fa-box"></i> ' + escapeHtml(line.quantity) + ' ' + escapeHtml(line.unit || '') + '</span>' +
                                    '<strong>' + money(line.unit_price) + '</strong>' +
                                '</div>' +
                            '</div>' +
                            '<button type="button" class="vrc-reorder-btn" data-add-cart="' + line.product_id + '" data-qty="' + (line.quantity || 1) + '">' +
                                '<i class="fa-solid fa-rotate-right"></i><span>Reorder</span>' +
                            '</button>' +
                        '</article>'
                    );
                }).join('');
                var hist = section.querySelector('.vrc-box-head a');
                if (hist) hist.setAttribute('href', 'my-orders.php');
                section.hidden = false;
            }).catch(function () {
                if (main) main.hidden = true;
                if (heading) heading.hidden = true;
                section.hidden = !hasViews;
            });
        }).catch(function () {
            section.hidden = true;
        });
    }

    function fillSlider(selector, products, prefix) {
        var wrap = document.querySelector(selector);
        if (!wrap) {
            return;
        }
        wrap.innerHTML = products.length
            ? products.map(function (p) { return sliderProductCard(p, prefix); }).join('')
            : '<div class="swiper-slide">' + emptyNote('No products yet.') + '</div>';
    }

    /* ---------- Catalog pages ---------- */

    function renderProductGrid(grid, products) {
        if (!grid) {
            return;
        }
        grid.innerHTML = products.length
            ? products.map(listProductCard).join('')
            : emptyNote('No products found.', '<a href="product.php">Browse all products</a>');
        var countEl = document.querySelector('.vc-shop-result-count strong, #vcSearchCount, #vcCategoryResultCount');
        if (countEl) {
            countEl.textContent = String(products.length);
        }
        var noEl = document.getElementById('vcNoProducts') || document.getElementById('vcSearchEmpty') || document.getElementById('vcCategoryNoResults');
        if (noEl) {
            noEl.style.display = products.length ? 'none' : 'block';
        }
    }

    function categoryTabIcon(name) {
        var n = String(name || '').toLowerCase();
        if (/fruit/.test(n)) return 'fa-apple-whole';
        if (/herb|leafy|leaf|green/.test(n)) return 'fa-seedling';
        if (/root/.test(n)) return 'fa-carrot';
        if (/veg/.test(n)) return 'fa-carrot';
        return 'fa-leaf';
    }

    function fillCategoryTabs(cats, selectedId) {
        var tabs = document.querySelector('.vc-category-tabs');
        if (!tabs) {
            return;
        }
        var allActive = !selectedId;
        var html = '<a href="category-product-listing.php"' + (allActive ? ' class="active"' : '') + '>' +
            '<i class="fa-solid fa-basket-shopping"></i> All Products</a>';
        html += cats.map(function (c) {
            var sel = String(c.id) === String(selectedId);
            return '<a href="' + categoryHref(c) + '"' + (sel ? ' class="active"' : '') + '>' +
                '<i class="fa-solid ' + categoryTabIcon(c.name) + '"></i> ' + escapeHtml(c.name) + '</a>';
        }).join('');
        tabs.innerHTML = html;
    }

    function fillCategoryFilters(cats, selectedId) {
        var box = document.querySelector('.vc-category-filter, .vc-search-filter-list');
        if (!box) {
            return;
        }
        var all = '<label class="' + (!selectedId ? 'active' : '') + '">' +
            '<input type="radio" name="category" value=""' + (!selectedId ? ' checked' : '') + '>' +
            '<span class="vc-filter-radio"></span><span>All Products</span></label>';
        box.innerHTML = all + cats.map(function (c) {
            var sel = String(c.id) === String(selectedId);
            return '<label class="' + (sel ? 'active' : '') + '">' +
                '<input type="radio" name="category" value="' + c.id + '"' + (sel ? ' checked' : '') + '>' +
                '<span class="vc-filter-radio"></span><span>' + escapeHtml(c.name) + '</span>' +
                '<small>' + escapeHtml(c.product_count || 0) + '</small></label>';
        }).join('');
    }

    function bootShop(kind) {
        var grid = document.getElementById('vcProductGrid') ||
            document.getElementById('vcCategoryGrid') ||
            document.getElementById('vcSearchGrid');
        if (grid) {
            grid.innerHTML = emptyNote('Loading products…');
        }
        var selectedId = qs('id') || qs('category_id') || qs('cat') || '';
        var q = qs('q') || qs('search') || '';
        var searchInput = document.getElementById('vcProductSearch') || document.getElementById('vcSearchInput') || document.getElementById('vcCategorySearch');
        if (searchInput && q) {
            searchInput.value = q;
        }

        Promise.all([VC.categories(), VC.products({
            q: q,
            category_id: /^\d+$/.test(String(selectedId)) ? selectedId : '',
            per_page: 50
        })]).then(function (pack) {
            var catRes = pack[0];
            var prodRes = pack[1];
            var cats = (catRes && catRes.success && catRes.data.categories) || [];
            var products = (prodRes && prodRes.success && prodRes.data.products) || [];

            if (selectedId && !/^\d+$/.test(String(selectedId))) {
                var needle = String(selectedId).toLowerCase();
                var match = cats.find(function (c) {
                    return String(c.name).toLowerCase().indexOf(needle) !== -1;
                });
                if (match) {
                    selectedId = match.id;
                    return VC.products({ q: q, category_id: selectedId, per_page: 50 }).then(function (r2) {
                        products = (r2 && r2.success && r2.data.products) || [];
                        fillCategoryTabs(cats, selectedId);
                        fillCategoryFilters(cats, selectedId);
                        renderProductGrid(grid, products);
                        bindShopFilters(grid, cats);
                        updateCategoryHero(cats, selectedId, products.length);
                    });
                }
            }

            fillCategoryTabs(cats, selectedId);
            fillCategoryFilters(cats, selectedId);
            renderProductGrid(grid, products);
            bindShopFilters(grid, cats);
            updateCategoryHero(cats, selectedId, products.length);

            var kw = document.getElementById('vcSearchKeyword');
            if (kw) {
                kw.textContent = q ? '“' + q + '”' : '“All Products”';
            }
        });
    }

    function updateCategoryHero(cats, selectedId, count) {
        if (!selectedId) {
            return;
        }
        var cat = cats.find(function (c) { return String(c.id) === String(selectedId); });
        if (!cat) {
            return;
        }
        var h1 = document.querySelector('.vc-category-hero h1, .vc-category-page h1');
        if (h1) {
            h1.textContent = cat.name;
        }
        var meta = document.querySelector('.vc-category-meta span');
        if (meta) {
            meta.innerHTML = '<i class="fa-solid fa-leaf"></i> ' + count + ' Products';
        }
    }

    function bindShopFilters(grid, cats) {
        var searchInput = document.getElementById('vcProductSearch') || document.getElementById('vcSearchInput') || document.getElementById('vcCategorySearch');
        var sort = document.getElementById('vcSortProducts') || document.getElementById('vcSearchSort') || document.getElementById('vcCategorySort');
        var timer = null;

        function reload() {
            var q = searchInput ? searchInput.value.trim() : '';
            var checked = document.querySelector('.vc-category-filter input:checked, .vc-search-filter-list input:checked');
            var categoryId = checked ? checked.value : '';
            VC.products({ q: q, category_id: categoryId, per_page: 50 }).then(function (res) {
                var products = (res && res.success && res.data.products) || [];
                if (sort) {
                    products = sortProducts(products, sort.value);
                }
                renderProductGrid(grid, products);
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(reload, 300);
            });
        }
        document.querySelectorAll('.vc-category-filter input, .vc-search-filter-list input').forEach(function (input) {
            input.addEventListener('change', function () {
                document.querySelectorAll('.vc-category-filter label, .vc-search-filter-list label').forEach(function (l) {
                    l.classList.remove('active');
                });
                if (input.closest('label')) {
                    input.closest('label').classList.add('active');
                }
                reload();
            });
        });
        if (sort) {
            sort.addEventListener('change', reload);
        }
        var searchBtn = document.getElementById('vcSearchButton');
        if (searchBtn) {
            searchBtn.addEventListener('click', reload);
        }
    }

    function sortProducts(products, value) {
        var list = products.slice();
        if (value === 'low-high') {
            list.sort(function (a, b) { return a.price - b.price; });
        } else if (value === 'high-low') {
            list.sort(function (a, b) { return b.price - a.price; });
        } else if (value === 'name') {
            list.sort(function (a, b) { return String(a.name).localeCompare(b.name); });
        }
        return list;
    }

    function bootCategoryIndex() {
        var grid = document.querySelector('.vc-category-main-grid');
        if (!grid) {
            return;
        }
        VC.categories().then(function (res) {
            var cats = (res && res.success && res.data.categories) || [];
            grid.innerHTML = cats.map(function (c) {
                return (
                    '<article class="vc-category-main-card">' +
                        '<a href="' + categoryHref(c) + '">' +
                            '<div class="vc-category-main-image">' +
                                catalogThumb(c.image_url, c.name) +
                                '<span>' + escapeHtml(c.product_count || 0) + ' Products</span>' +
                            '</div>' +
                            '<div class="vc-category-main-content">' +
                                '<span class="vc-category-main-icon"><i class="fa-solid fa-leaf"></i></span>' +
                                '<div><small>Shop by type</small><h3>' + escapeHtml(c.name) + '</h3>' +
                                '<p>Fresh produce for bulk and daily orders.</p></div>' +
                                '<span class="vc-category-main-arrow"><i class="fa-solid fa-arrow-right"></i></span>' +
                            '</div>' +
                        '</a>' +
                    '</article>'
                );
            }).join('') || emptyNote('No categories yet.');
        });
    }

    function isKgProductUnit(unit) {
        var u = String(unit || '').toLowerCase();
        if (!u) {
            return true;
        }
        if (/(bunch|dozen|piece|pcs|pack|bag|box|tray|bundle|leaf|leaves)/.test(u)) {
            return false;
        }
        return /kg|kilo|kilogram/.test(u) || u === 'per kg' || u.indexOf('per kg') !== -1;
    }

    function qtyTiersForProduct(p) {
        var base = [25, 50, 75, 100];
        var moq = Number(p && p.moq) || 1;
        var filtered = base.filter(function (q) { return q >= moq; });
        if (!filtered.length) {
            filtered = [Math.max(moq, 100)];
        }
        return filtered;
    }

    function openBulkEnquiryModal(product) {
        var modal = document.getElementById('vcBulkEnquiryModal');
        if (!modal) {
            return;
        }
        var form = document.getElementById('vcBulkEnquiryForm');
        var err = document.getElementById('vcBulkFormError');
        var ok = document.getElementById('vcBulkFormSuccess');
        if (form) {
            form.reset();
        }
        if (err) {
            err.hidden = true;
            err.textContent = '';
        }
        if (ok) {
            ok.hidden = true;
        }
        var idInput = document.getElementById('vcBulkProductId');
        var labelInput = document.getElementById('vcBulkProductLabel');
        if (idInput) {
            idInput.value = product && product.id ? String(product.id) : '';
        }
        if (labelInput) {
            labelInput.value = product
                ? (titleCaseName(product.name) + (product.unit ? ' (' + product.unit + ')' : ''))
                : '';
        }
        var cust = VC.getCustomer && VC.getCustomer();
        if (cust && form) {
            var nameEl = form.querySelector('[name="name"]');
            var bizEl = form.querySelector('[name="business_name"]');
            var mobEl = form.querySelector('[name="mobile"]');
            if (nameEl && !nameEl.value) {
                nameEl.value = cust.owner_name || cust.name || '';
            }
            if (bizEl && !bizEl.value) {
                bizEl.value = cust.business_name || '';
            }
            if (mobEl && !mobEl.value) {
                mobEl.value = cust.mobile || '';
            }
        }
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('vc-modal-open');
    }

    function closeBulkEnquiryModal() {
        var modal = document.getElementById('vcBulkEnquiryModal');
        if (!modal) {
            return;
        }
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('vc-modal-open');
    }

    function bindBulkEnquiryForm() {
        var modal = document.getElementById('vcBulkEnquiryModal');
        if (!modal || modal.dataset.bound === '1') {
            return;
        }
        modal.dataset.bound = '1';
        var closeBtn = document.getElementById('vcBulkModalClose');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeBulkEnquiryModal);
        }
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeBulkEnquiryModal();
            }
        });
        var form = document.getElementById('vcBulkEnquiryForm');
        if (!form) {
            return;
        }
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var err = document.getElementById('vcBulkFormError');
            var ok = document.getElementById('vcBulkFormSuccess');
            var btn = document.getElementById('vcBulkSubmitBtn');
            if (err) {
                err.hidden = true;
                err.textContent = '';
            }
            if (ok) {
                ok.hidden = true;
            }
            var fd = new FormData(form);
            var productIdRaw = String(fd.get('product_id') || '').trim();
            var payload = {
                name: String(fd.get('name') || '').trim(),
                business_name: String(fd.get('business_name') || '').trim(),
                mobile: String(fd.get('mobile') || '').trim(),
                product_id: productIdRaw ? Number(productIdRaw) : null,
                required_quantity: String(fd.get('required_quantity') || '').trim(),
                delivery_location: String(fd.get('delivery_location') || '').trim(),
                pincode: String(fd.get('pincode') || '').trim(),
                preferred_delivery_date: String(fd.get('preferred_delivery_date') || '').trim() || null,
                additional_requirement: String(fd.get('additional_requirement') || '').trim() || null
            };
            if (btn) {
                btn.disabled = true;
            }
            VC.submitBulkEnquiry(payload).then(function (res) {
                if (btn) {
                    btn.disabled = false;
                }
                if (res && res.success) {
                    if (ok) {
                        ok.hidden = false;
                        ok.textContent = (res.data && res.data.message) ||
                            'Thanks — our team will contact you within 24 hours.';
                    }
                    toast('Enquiry submitted');
                    setTimeout(function () {
                        closeBulkEnquiryModal();
                    }, 1800);
                    return;
                }
                var msg = apiErrorMessage(res, 'Could not submit enquiry.');
                if (err) {
                    err.hidden = false;
                    err.textContent = msg;
                }
                toast(msg, 'error');
            }).catch(function () {
                if (btn) {
                    btn.disabled = false;
                }
                if (err) {
                    err.hidden = false;
                    err.textContent = 'Could not submit enquiry.';
                }
                toast('Could not submit enquiry.', 'error');
            });
        });
    }

    function setupProductQuantityUI(p) {
        var qtyInput = document.getElementById('vcProductQty');
        var tierBlock = document.getElementById('vcQtyTierBlock');
        var tierWrap = document.getElementById('vcQtyTiers');
        var legacyBox = document.getElementById('vcLegacyQtyBox');
        var lineTotal = document.getElementById('vcQtyLineTotal');
        var purchase = document.querySelector('.vc-product-purchase');
        var hint = document.getElementById('vcQtyTierHint');
        var unitPrice = Number(p.price) || 0;
        var useKgTiers = isKgProductUnit(p.unit);

        function syncLineTotal(qty) {
            if (!lineTotal) {
                return;
            }
            if (!qty || qty < 1) {
                lineTotal.hidden = true;
                return;
            }
            lineTotal.hidden = false;
            lineTotal.textContent = 'Line total: ' + money(unitPrice * qty) +
                ' for ' + qty + (useKgTiers ? ' KG' : (' ' + (p.unit || 'units')));
        }

        function setQty(qty) {
            qty = Number(qty) || 1;
            if (qtyInput) {
                qtyInput.value = String(qty);
            }
            syncLineTotal(qty);
        }

        if (useKgTiers) {
            var tiers = qtyTiersForProduct(p);
            if (tierWrap) {
                tierWrap.hidden = false;
                tierWrap.innerHTML = tiers.map(function (q, i) {
                    return '<button type="button" class="vc-weight-btn' + (i === 0 ? ' active' : '') +
                        '" data-qty="' + q + '">' + q + ' KG</button>';
                }).join('');
                tierWrap.querySelectorAll('.vc-weight-btn').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        tierWrap.querySelectorAll('.vc-weight-btn').forEach(function (b) {
                            b.classList.remove('active');
                        });
                        btn.classList.add('active');
                        setQty(btn.getAttribute('data-qty'));
                    });
                });
            }
            if (legacyBox) {
                legacyBox.hidden = true;
            }
            if (purchase) {
                purchase.classList.add('vc-purchase-tiers-only');
            }
            if (hint) {
                hint.textContent = 'Fixed bulk packs in KG' +
                    (Number(p.moq) > 25 ? ' · MOQ ' + p.moq + ' KG' : '');
            }
            if (tierBlock) {
                tierBlock.hidden = false;
            }
            setQty(tiers[0]);
        } else {
            if (tierBlock) {
                // Keep bulk quote CTA; hide KG chips for bunch/dozen/etc.
                if (tierWrap) {
                    tierWrap.innerHTML = '';
                    tierWrap.hidden = true;
                }
                if (hint) {
                    hint.textContent = 'Sold ' + (p.unit || 'per unit') +
                        ' — use quantity below, or request a bulk quote for large orders.';
                }
                var quoteLabel = document.querySelector('.vc-bulk-quote-row > span');
                if (quoteLabel) {
                    quoteLabel.textContent = 'Need a larger bulk order?';
                }
            }
            if (legacyBox) {
                legacyBox.hidden = false;
            }
            if (purchase) {
                purchase.classList.remove('vc-purchase-tiers-only');
            }
            var moq = Math.max(1, Number(p.moq) || 1);
            if (qtyInput) {
                qtyInput.min = String(moq);
                qtyInput.value = String(moq);
            }
            setQty(moq);

            var minus = document.getElementById('vcQtyMinus');
            var plus = document.getElementById('vcQtyPlus');
            if (minus && !minus.dataset.bound) {
                minus.dataset.bound = '1';
                minus.addEventListener('click', function () {
                    var v = Math.max(moq, (Number(qtyInput && qtyInput.value) || moq) - 1);
                    setQty(v);
                });
            }
            if (plus && !plus.dataset.bound) {
                plus.dataset.bound = '1';
                plus.addEventListener('click', function () {
                    var v = (Number(qtyInput && qtyInput.value) || moq) + 1;
                    setQty(v);
                });
            }
            if (qtyInput && !qtyInput.dataset.bound) {
                qtyInput.dataset.bound = '1';
                qtyInput.addEventListener('change', function () {
                    var v = Math.max(moq, Number(qtyInput.value) || moq);
                    setQty(v);
                });
            }
        }

        var bulkBtn = document.getElementById('vcBulkQuoteBtn');
        if (bulkBtn && !bulkBtn.dataset.bound) {
            bulkBtn.dataset.bound = '1';
            bulkBtn.addEventListener('click', function () {
                openBulkEnquiryModal(p);
            });
        }
        bindBulkEnquiryForm();
    }

    function bootProductDetails() {
        var id = qs('id');
        if (!id) {
            return;
        }
        VC.product(id).then(function (res) {
            if (!res || !res.success) {
                toast((res && res.error && res.error.message) || 'Product not found.', 'error');
                return;
            }
            var p = res.data.product;
            rememberView(p.id);
            var title = document.querySelector('.vc-product-info h1, .vc-product-title, .vc-product-page h1');
            if (title) title.textContent = titleCaseName(p.name);
            var cat = document.querySelector('.vc-product-breadcrumb span, .vc-product-category');
            if (cat) cat.textContent = p.category_name || '';
            var price = document.querySelector('.vc-product-price strong');
            if (price) {
                price.textContent = money(p.price);
            }
            var unitLabel = document.querySelector('.vc-product-price small, .vc-product-unit');
            if (unitLabel && p.unit) {
                unitLabel.textContent = '/ ' + p.unit;
            }
            renderProductGallery(p);
            var desc = document.querySelector('#description, .vc-product-tab-content, .vc-product-description');
            if (desc && p.description) {
                desc.textContent = p.description;
            }

            setupProductQuantityUI(p);

            var qtyInput = document.getElementById('vcProductQty');
            var addBtn = document.getElementById('vcAddCartBtn') ||
                document.querySelector('.vc-add-cart-btn, .vc-product-cart, .vc-add-to-cart, button.vc-product-add');
            if (addBtn && !addBtn.dataset.bound) {
                addBtn.dataset.bound = '1';
                addBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var qty = qtyInput ? Number(qtyInput.value) : (p.moq || 1);
                    addToCart(p.id, qty);
                });
            }
            document.querySelectorAll('.vc-product-page .vc-product-cart-btn, .vc-product-actions button').forEach(function (btn) {
                if (btn.dataset.bound === '1') {
                    return;
                }
                if (/cart/i.test(btn.textContent || btn.className)) {
                    btn.dataset.bound = '1';
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        var qty = qtyInput ? Number(qtyInput.value) : (p.moq || 1);
                        addToCart(p.id, qty);
                    });
                }
            });
            var wish = document.querySelector('.vc-product-wishlist');
            if (wish) {
                wish.setAttribute('data-add-wish', p.id);
            }
            var crumbs = document.querySelectorAll('.vc-product-breadcrumb a');
            if (crumbs[1]) crumbs[1].href = 'product.php';
            if (crumbs[2]) {
                crumbs[2].href = 'category-product-listing.php?id=' + p.category_id;
                crumbs[2].textContent = p.category_name || 'Category';
            }
            var badge = document.querySelector('.vc-product-discount');
            if (badge) {
                badge.style.display = 'none';
            }
        });
        VC.similar(id).then(function (res) {
            var products = (res && res.success && res.data.products) || [];
            var wrap = document.querySelector('.vc-related-grid, .vc-similar-grid, .vbestSlider .swiper-wrapper');
            if (!wrap) {
                return;
            }
            if (!products.length) {
                wrap.innerHTML = '';
                var relatedBlock = wrap.closest('.vc-related-products');
                if (relatedBlock) {
                    relatedBlock.style.display = 'none';
                }
                return;
            }
            if (wrap.classList.contains('swiper-wrapper')) {
                wrap.innerHTML = products.map(function (p) { return sliderProductCard(p, 'vbest'); }).join('');
            } else {
                wrap.innerHTML = products.map(listProductCard).join('');
            }
        });
    }

    function showMainMedia(url, alt) {
        var main = document.getElementById('vcMainProductImage') || document.querySelector('.vc-product-main-image img');
        var pdf = document.getElementById('vcMainProductPdf');
        var src = imgUrl(url);
        var pdfFile = !!(url && isPdfUrl(url));
        if (pdf) {
            if (pdfFile) {
                pdf.src = src;
                pdf.hidden = false;
                if (main) {
                    main.hidden = true;
                }
            } else {
                pdf.hidden = true;
                pdf.removeAttribute('src');
                if (main) {
                    main.hidden = false;
                    main.src = src;
                    main.alt = alt || '';
                }
            }
            return;
        }
        if (main) {
            main.src = pdfFile ? PLACEHOLDER_IMG : src;
            main.alt = alt || '';
        }
    }

    function renderProductGallery(p) {
        var images = [];
        if (p.images && p.images.length) {
            images = p.images.slice().sort(function (a, b) {
                if (a.is_primary === b.is_primary) {
                    return (a.sort_order || 0) - (b.sort_order || 0);
                }
                return a.is_primary ? -1 : 1;
            });
        } else if (p.image_url) {
            images = [{ url: p.image_url, is_primary: true, sort_order: 0 }];
        }
        var gallery = document.querySelector('.vc-product-gallery');
        var thumbs = document.getElementById('vcProductThumbs') || document.querySelector('.vc-product-thumbnails');
        var coverUrl = images[0] ? images[0].url : null;
        showMainMedia(coverUrl, titleCaseName(p.name));
        if (gallery) {
            gallery.classList.toggle('is-single', images.length < 2);
        }
        if (!thumbs) {
            return;
        }
        thumbs.innerHTML = images.map(function (im, i) {
            var url = imgUrl(im.url);
            var pdf = isPdfUrl(im.url);
            var inner = pdf
                ? '<span class="vc-pdf-fill"><i class="fa-solid fa-file-pdf"></i></span>'
                : '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(titleCaseName(p.name)) + '">';
            return (
                '<button type="button" class="vc-product-thumb' + (i === 0 ? ' active' : '') + '" data-image="' + escapeHtml(im.url || '') + '">' +
                    inner +
                '</button>'
            );
        }).join('');
        thumbs.querySelectorAll('.vc-product-thumb').forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                thumbs.querySelectorAll('.vc-product-thumb').forEach(function (t) { t.classList.remove('active'); });
                thumb.classList.add('active');
                showMainMedia(thumb.getAttribute('data-image') || coverUrl, titleCaseName(p.name));
            });
        });
    }

    function bootOffers() {
        VC.offers().then(function (res) {
            var offers = (res && res.success && res.data.offers) || [];
            var host = document.querySelector('#vcCoupons, .vc-offers-grid, .vc-offers-list, main.vc-offers-page');
            var grids = document.querySelectorAll('.vc-deals-grid, .vc-coupons-grid, .vc-offers-deals, .vc-coupon-grid');
            var html = offers.length ? offers.map(function (o) {
                var value = o.discount_type === 'percent'
                    ? o.discount_value + '% OFF'
                    : money(o.discount_value) + ' OFF';
                return (
                    '<article class="vc-offer-live-card">' +
                        '<strong>' + escapeHtml(o.title) + '</strong>' +
                        '<span class="vc-offer-live-value">' + escapeHtml(value) + '</span>' +
                        (o.coupon_code ? '<code>' + escapeHtml(o.coupon_code) + '</code>' : '') +
                        (o.category_name ? '<small>' + escapeHtml(o.category_name) + '</small>' : '') +
                        (o.valid_till ? '<small>Valid till ' + escapeHtml(o.valid_till) + '</small>' : '') +
                    '</article>'
                );
            }).join('') : emptyNote('No offers right now.');
            if (grids.length) {
                grids.forEach(function (grid) { grid.innerHTML = html; });
            } else if (host) {
                var box = document.createElement('section');
                box.className = 'vc-offers-container vc-offer-live-wrap';
                box.innerHTML = '<h2>Current Offers</h2><div class="vc-offer-live-grid">' + html + '</div>';
                host.appendChild(box);
            }
        });
        VC.banners().then(function (res) {
            var banners = (res && res.success && res.data.banners) || [];
            var img = document.querySelector('.vc-offers-hero-image img');
            if (img && banners[0]) {
                if (isPdfUrl(banners[0].image_url)) {
                    img.src = PLACEHOLDER_IMG;
                } else {
                    img.src = imgUrl(banners[0].image_url);
                }
            }
        });
    }

    /* ---------- Auth ---------- */

    function applyAuthSuccess(data, next) {
        VC.setSession(data);
        toast('Logged in successfully');
        window.location.href = next || 'account-dashboard.php';
    }

    function bootLogin() {
        var form = document.getElementById('vcLoginForm') || document.querySelector('.vc-login-form');
        if (!form) {
            return;
        }
        if (VC.isLoggedIn() && !qs('next')) {
            window.location.href = 'account-dashboard.php';
            return;
        }

        var modeInput = document.getElementById('vcLoginMode');
        var emailField = document.getElementById('vcLoginEmailField');
        var mobileField = document.getElementById('vcLoginMobileField');
        var passwordField = document.getElementById('vcLoginPasswordField');
        var otpField = document.getElementById('vcLoginOtpField');
        var emailOptions = document.getElementById('vcLoginEmailOptions');
        var intro = document.getElementById('vcLoginIntro');
        var submitText = document.getElementById('vcLoginSubmitText');
        var resendBtn = document.getElementById('vcLoginResendOtp');
        var otpHint = document.getElementById('vcOtpHint');
        var emailInput = document.getElementById('vcLoginEmail');
        var mobileInput = document.getElementById('vcLoginMobile');
        var passwordInput = document.getElementById('vcLoginPassword');
        var otpInput = document.getElementById('vcLoginOtp');
        var otpSent = false;

        function currentMode() {
            return (modeInput && modeInput.value === 'mobile') ? 'mobile' : 'email';
        }

        function setDisabled(el, disabled) {
            if (!el) return;
            el.disabled = !!disabled;
            if (disabled) {
                el.removeAttribute('required');
            }
        }

        function setMode(mode) {
            mode = mode === 'mobile' ? 'mobile' : 'email';
            if (modeInput) {
                modeInput.value = mode;
            }
            document.querySelectorAll('[data-login-mode]').forEach(function (btn) {
                var active = btn.getAttribute('data-login-mode') === mode;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            if (emailField) emailField.hidden = mode !== 'email';
            if (mobileField) mobileField.hidden = mode !== 'mobile';
            if (passwordField) passwordField.hidden = mode !== 'email';
            if (emailOptions) emailOptions.hidden = mode !== 'email';

            // Strict field isolation: inactive mode inputs are disabled so they
            // cannot be autofilled/submitted, and cannot create mobile+password paths.
            setDisabled(emailInput, mode !== 'email');
            setDisabled(passwordInput, mode !== 'email');
            setDisabled(mobileInput, mode !== 'mobile');
            setDisabled(otpInput, mode !== 'mobile');

            if (mode === 'email') {
                otpSent = false;
                if (otpField) otpField.hidden = true;
                if (resendBtn) resendBtn.hidden = true;
                if (otpHint) otpHint.textContent = '';
                if (otpInput) otpInput.value = '';
                if (mobileInput) mobileInput.value = '';
            } else {
                if (passwordInput) passwordInput.value = '';
                if (emailInput) emailInput.value = '';
                if (otpField) otpField.hidden = !otpSent;
                if (resendBtn) resendBtn.hidden = !otpSent;
                setDisabled(otpInput, !otpSent);
            }

            if (intro) {
                intro.textContent = mode === 'mobile'
                    ? 'Enter your registered mobile number. We will send an OTP — no password needed.'
                    : 'Enter your registered email and password to continue. No OTP needed.';
            }
            if (submitText) {
                if (mode === 'mobile') {
                    submitText.textContent = otpSent ? 'Verify OTP & Login' : 'Send OTP';
                } else {
                    submitText.textContent = 'Login to My Account';
                }
            }
        }

        document.querySelectorAll('[data-login-mode]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                otpSent = false;
                setMode(btn.getAttribute('data-login-mode'));
            });
        });
        setMode(currentMode());

        function sendMobileOtp() {
            var mobile = (mobileInput && mobileInput.value || '').trim();
            if (!mobile) {
                toast('Enter your mobile number.', 'error');
                return Promise.resolve(false);
            }
            if (mobile.indexOf('@') !== -1) {
                toast('Use the Email & Password tab for email login.', 'error');
                return Promise.resolve(false);
            }
            return VC.sendOtp(mobile).then(function (res) {
                if (!res || !res.success) {
                    toast(apiErrorMessage(res, 'Could not send OTP.'), 'error');
                    return false;
                }
                otpSent = true;
                setMode('mobile');
                if (otpField) otpField.hidden = false;
                if (resendBtn) resendBtn.hidden = false;
                setDisabled(otpInput, false);
                var msg = 'OTP sent to your mobile.';
                if (res.data && res.data.dev_otp) {
                    msg += ' DEV OTP: ' + res.data.dev_otp;
                }
                if (otpHint) otpHint.textContent = msg;
                toast(msg);
                if (otpInput) otpInput.focus();
                return true;
            });
        }

        if (resendBtn) {
            resendBtn.addEventListener('click', function () {
                sendMobileOtp();
            });
        }

        var toggle = document.getElementById('vcPasswordToggle');
        if (toggle && !toggle.getAttribute('data-vc-bound')) {
            toggle.setAttribute('data-vc-bound', '1');
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (!passwordInput || passwordInput.disabled) {
                    return;
                }
                var show = passwordInput.type === 'password';
                passwordInput.type = show ? 'text' : 'password';
                var icon = toggle.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', !show);
                    icon.classList.toggle('fa-eye-slash', show);
                } else {
                    toggle.innerHTML = show
                        ? '<i class="fa-regular fa-eye-slash"></i>'
                        : '<i class="fa-regular fa-eye"></i>';
                }
                toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var next = qs('next') || 'account-dashboard.php';
            var mode = currentMode();

            // Mobile mode → OTP endpoints ONLY (never password / email-login)
            if (mode === 'mobile') {
                var mobile = (mobileInput && mobileInput.value || '').trim();
                var otp = (otpInput && otpInput.value || '').trim();
                if (!mobile) {
                    toast('Enter your mobile number.', 'error');
                    return;
                }
                if (mobile.indexOf('@') !== -1) {
                    toast('Use the Email & Password tab for email login.', 'error');
                    return;
                }
                if (!otpSent || !otp) {
                    sendMobileOtp();
                    return;
                }
                VC.verifyOtp(mobile, otp).then(function (res) {
                    if (res && res.success) {
                        applyAuthSuccess(res.data, next);
                    } else {
                        toast(apiErrorMessage(res, 'Invalid OTP.'), 'error');
                    }
                });
                return;
            }

            // Email mode → /auth/email-login ONLY (never OTP / mobile)
            var email = (emailInput && emailInput.value || '').trim();
            var pass = (passwordInput && passwordInput.value || '');
            if (!email) {
                toast('Enter your email address.', 'error');
                return;
            }
            if (email.indexOf('@') === -1) {
                toast('Enter a valid email address. For phone login, use the Mobile & OTP tab.', 'error');
                return;
            }
            if (!pass) {
                toast('Enter your password.', 'error');
                return;
            }
            VC.emailLogin(email, pass).then(function (res) {
                if (res && res.success) {
                    applyAuthSuccess(res.data, next);
                    return;
                }
                var code = res && res.error && res.error.code;
                var msg = apiErrorMessage(res, 'Invalid email or password.');
                if (code === 'PASSWORD_NOT_SET') {
                    toast(msg, 'error');
                    return;
                }
                if (code === 'INVALID_LOGIN_METHOD') {
                    toast(msg, 'error');
                    return;
                }
                toast(msg, 'error');
            });
        });
    }

    function bootRegister() {
        var form = document.getElementById('vcSignupRegistrationForm');
        if (!form) {
            // Legacy simplified signup form fallback removed — redirect if old markup appears
            var legacy = document.querySelector('.vc-signup-form');
            if (legacy && !document.getElementById('vcSignupRegistrationForm')) {
                return;
            }
            return;
        }

        var step = 1;
        var total = 5;
        var otpSent = false;
        var otpVerified = VC.isLoggedIn();
        var pinOk = false;

        var nextBtn = document.getElementById('vcSignupNext');
        var prevBtn = document.getElementById('vcSignupPrev');
        var nextText = document.getElementById('vcSignupNextText');
        var stepText = document.getElementById('vcSignupStepText');
        var pctText = document.getElementById('vcSignupProgressPct');
        var bar = document.getElementById('vcSignupProgressBar');
        var otpWrap = document.getElementById('vcSignupOtpWrap');
        var otpHint = document.getElementById('vcSignupOtpHint');
        var resendBtn = document.getElementById('vcSignupResendOtp');
        var sameAddr = document.getElementById('vcSignupSameAddress');
        var deliveryWrap = document.getElementById('vcSignupDeliveryWrap');
        var pinHint = document.getElementById('vcSignupPinHint');

        var docNameMap = {
            gst_certificate: 'gst_certificate',
            fssai_document: 'fssai_license',
            shop_registration: 'shop_establishment',
            msme_certificate: 'msme_certificate',
            trade_licence: 'trade_license',
            pan_card: 'pan_card',
            aadhaar_card: 'aadhaar_card',
            shop_photo: 'business_photo',
            business_card: 'owner_photo'
        };

        function val(id) {
            return String((document.getElementById(id) || {}).value || '').trim();
        }

        function setHint(el, msg, ok) {
            if (!el) return;
            el.textContent = msg || '';
            el.style.color = ok ? '#1a7f37' : (msg ? '#b42318' : '');
        }

        function showStep(n) {
            step = n;
            form.querySelectorAll('[data-signup-step]').forEach(function (el) {
                el.classList.toggle('active', Number(el.getAttribute('data-signup-step')) === step);
            });
            document.querySelectorAll('#vcSignupStepBar .vc-signup-step').forEach(function (btn) {
                var s = Number(btn.getAttribute('data-goto-step'));
                btn.classList.toggle('is-active', s === step);
                btn.classList.toggle('is-done', s < step);
            });
            var pct = Math.round((step / total) * 100);
            if (stepText) stepText.textContent = String(step);
            if (pctText) pctText.textContent = pct + '%';
            if (bar) bar.style.width = pct + '%';
            if (prevBtn) prevBtn.hidden = step === 1;
            if (nextText) {
                if (step === 1) nextText.textContent = otpVerified ? 'Continue' : (otpSent ? 'Verify OTP' : 'Send OTP');
                else if (step === 5) nextText.textContent = 'Submit Application';
                else nextText.textContent = 'Continue';
            }
            if (step === 5) buildReview();
            window.scrollTo({ top: Math.max(0, form.getBoundingClientRect().top + window.scrollY - 80), behavior: 'smooth' });
        }

        function buildReview() {
            setText('vcReviewMobile', val('vcSignupPhone') || ((VC.getCustomer() || {}).mobile || '—'));
            var type = (form.querySelector('input[name="business_type"]:checked') || {}).value || '—';
            setText('vcReviewType', type);
            setText('vcReviewBusiness', val('vcSignupBusinessName') || '—');
            setText('vcReviewOwner', val('vcSignupOwnerName') || '—');
            setText('vcReviewEmail', val('vcSignupEmail') || '—');
            setText('vcReviewPassword', val('vcSignupPassword') ? 'Set (for Email & Password login)' : 'Not set (optional)');
            var shop = val('vcSignupShopAddress');
            var delivery = (sameAddr && sameAddr.checked) ? shop : val('vcSignupDeliveryAddress');
            var addr = [shop, delivery !== shop ? ('Delivery: ' + delivery) : '', val('vcSignupLandmark'), val('vcSignupCity'), val('vcSignupState'), val('vcSignupPincode')]
                .filter(Boolean).join(', ');
            setText('vcReviewAddress', addr || '—');
            var docs = [];
            form.querySelectorAll('.vc-upload-card input[type="file"]').forEach(function (input) {
                if (input.files && input.files[0]) {
                    docs.push((input.closest('.vc-upload-card').querySelector('strong') || {}).textContent || input.name);
                }
            });
            setText('vcReviewDocs', docs.length ? docs.join(', ') : 'None selected (optional)');
        }

        function sendOtp() {
            var mobile = val('vcSignupPhone');
            if (!/^\d{10}$/.test(mobile.replace(/\D/g, '')) && !/^[0-9+\-\s]{8,15}$/.test(mobile)) {
                toast('Enter a valid mobile number.', 'error');
                return Promise.resolve(false);
            }
            return VC.sendOtp(mobile, { purpose: 'register' }).then(function (res) {
                if (!res || !res.success) {
                    var code = res && res.error && res.error.code;
                    var msg = (res && res.error && res.error.message) || 'Could not send OTP.';
                    if (code === 'MOBILE_ALREADY_REGISTERED') {
                        toast(msg, 'error');
                        setTimeout(function () { window.location.href = 'login.php'; }, 1800);
                        return false;
                    }
                    toast(msg, 'error');
                    return false;
                }
                otpSent = true;
                if (otpWrap) otpWrap.hidden = false;
                if (resendBtn) resendBtn.hidden = false;
                var msg = 'OTP sent to your mobile.';
                if (res.data && res.data.dev_otp) msg += ' DEV OTP: ' + res.data.dev_otp;
                if (otpHint) otpHint.textContent = msg;
                toast(msg);
                showStep(1);
                return true;
            });
        }

        function verifyOtp() {
            var mobile = val('vcSignupPhone');
            var otp = val('vcSignupOtp');
            if (!otp) {
                toast('Enter the OTP.', 'error');
                return Promise.resolve(false);
            }
            return VC.verifyOtp(mobile, otp, { purpose: 'register' }).then(function (res) {
                if (!res || !res.success) {
                    var code = res && res.error && res.error.code;
                    var msg = (res && res.error && res.error.message) || 'Invalid OTP.';
                    if (code === 'MOBILE_ALREADY_REGISTERED') {
                        toast(msg, 'error');
                        setTimeout(function () { window.location.href = 'login.php'; }, 1800);
                        return false;
                    }
                    toast(msg, 'error');
                    return false;
                }
                VC.setSession(res.data);
                otpVerified = true;
                toast('Mobile verified');
                showStep(2);
                return true;
            });
        }

        function validateStep(n) {
            if (n === 1) {
                if (otpVerified) return true;
                if (!otpSent) {
                    sendOtp();
                    return false;
                }
                verifyOtp();
                return false;
            }
            if (n === 2) {
                if (!otpVerified && !VC.isLoggedIn()) {
                    toast('Please verify your mobile first.', 'error');
                    showStep(1);
                    return false;
                }
                var type = form.querySelector('input[name="business_type"]:checked');
                var err = document.getElementById('vcSignupBizTypeError');
                if (!type) {
                    if (err) err.hidden = false;
                    toast('Select a business type.', 'error');
                    return false;
                }
                if (err) err.hidden = true;
                if (!val('vcSignupBusinessName') || !val('vcSignupOwnerName')) {
                    toast('Business name and owner name are required.', 'error');
                    return false;
                }
                var pass = val('vcSignupPassword');
                var passConfirm = val('vcSignupPasswordConfirm');
                if (pass || passConfirm) {
                    if (pass.length < 6) {
                        toast('Password must be at least 6 characters.', 'error');
                        return false;
                    }
                    if (pass !== passConfirm) {
                        toast('Password confirmation does not match.', 'error');
                        return false;
                    }
                }
                return true;
            }
            if (n === 3) {
                if (!val('vcSignupShopAddress') || !val('vcSignupCity') || !val('vcSignupState') || !val('vcSignupPincode')) {
                    toast('Please complete the required address fields.', 'error');
                    return false;
                }
                if (!(sameAddr && sameAddr.checked) && !val('vcSignupDeliveryAddress')) {
                    toast('Enter delivery address or choose Same as shop address.', 'error');
                    return false;
                }
                if (!/^\d{6}$/.test(val('vcSignupPincode'))) {
                    toast('Enter a valid 6-digit pincode.', 'error');
                    return false;
                }
                if (!pinOk) {
                    return VC.checkPincode(val('vcSignupPincode')).then(function (res) {
                        if (res && res.success && res.data && res.data.serviceable) {
                            pinOk = true;
                            if (res.data.city) document.getElementById('vcSignupCity').value = res.data.city;
                            if (res.data.state) document.getElementById('vcSignupState').value = res.data.state;
                            setHint(pinHint, '✓ We deliver here', true);
                            showStep(4);
                            return false;
                        }
                        setHint(pinHint, '✗ Not serviceable in this area yet', false);
                        toast("We currently deliver only within Hyderabad — this pincode isn't serviceable yet", 'error');
                        return false;
                    });
                }
                return true;
            }
            if (n === 5) {
                var terms = document.getElementById('vcSignupTerms');
                if (!terms || !terms.checked) {
                    toast('Please accept Terms & Privacy Policy.', 'error');
                    return false;
                }
                return true;
            }
            return true;
        }

        function submitRegistration() {
            if (!VC.isLoggedIn()) {
                toast('Please verify mobile OTP first.', 'error');
                showStep(1);
                return;
            }
            if (!validateStep(5)) return;

            var type = (form.querySelector('input[name="business_type"]:checked') || {}).value || '';
            var shop = val('vcSignupShopAddress');
            var delivery = (sameAddr && sameAddr.checked) ? shop : val('vcSignupDeliveryAddress');
            var body = {
                business_type: type,
                business_name: val('vcSignupBusinessName'),
                owner_name: val('vcSignupOwnerName'),
                email: val('vcSignupEmail'),
                gst_number: val('vcSignupGST'),
                fssai_number: val('vcSignupFSSAI'),
                pan_number: val('vcSignupPAN'),
                shop_address: shop,
                delivery_address: delivery,
                city: val('vcSignupCity'),
                state: val('vcSignupState'),
                pincode: val('vcSignupPincode'),
                landmark: val('vcSignupLandmark')
            };
            var regPass = val('vcSignupPassword');
            if (regPass) {
                body.password = regPass;
                body.password_confirmation = val('vcSignupPasswordConfirm');
            }

            if (nextBtn) nextBtn.disabled = true;
            VC.businessRegister(body).then(function (res) {
                if (!res || !res.success) {
                    if (nextBtn) nextBtn.disabled = false;
                    toast(apiErrorMessage(res, 'Could not submit registration.'), 'error');
                    return null;
                }
                if (res.data && res.data.customer) {
                    VC.setSession({ customer: res.data.customer });
                }
                var uploads = form.querySelectorAll('.vc-upload-card input[type="file"]');
                var chain = Promise.resolve();
                uploads.forEach(function (input) {
                    if (!input.files || !input.files[0]) return;
                    var docType = docNameMap[input.name] || input.name;
                    chain = chain.then(function () {
                        return VC.uploadDocument(docType, input.files[0]).then(function (up) {
                            if (!up || !up.success) {
                                toast((up && up.error && up.error.message) || ('Could not upload ' + input.name), 'error');
                            }
                        });
                    });
                });
                return chain.then(function () {
                    var kyc = (res.data && (res.data.kyc_status || (res.data.customer && res.data.customer.kyc_status))) || 'pending';
                    toast(kyc === 'approved' ? 'Registration approved. Welcome!' : 'Application submitted for review.');
                    window.location.href = 'verification-status.php';
                });
            }).catch(function () {
                if (nextBtn) nextBtn.disabled = false;
                toast('Could not submit registration.', 'error');
            });
        }

        if (sameAddr) {
            sameAddr.addEventListener('change', function () {
                if (deliveryWrap) deliveryWrap.hidden = !!sameAddr.checked;
            });
            if (deliveryWrap) deliveryWrap.hidden = !!sameAddr.checked;
        }

        var pinInput = document.getElementById('vcSignupPincode');
        if (pinInput) {
            pinInput.addEventListener('blur', function () {
                var pin = val('vcSignupPincode');
                pinOk = false;
                if (!/^\d{6}$/.test(pin)) {
                    setHint(pinHint, pin ? 'Enter a valid 6-digit pincode.' : '', false);
                    return;
                }
                VC.checkPincode(pin).then(function (res) {
                    if (res && res.success && res.data && res.data.serviceable) {
                        pinOk = true;
                        if (res.data.city) document.getElementById('vcSignupCity').value = res.data.city;
                        if (res.data.state) document.getElementById('vcSignupState').value = res.data.state;
                        setHint(pinHint, '✓ We deliver here', true);
                    } else {
                        setHint(pinHint, '✗ Not serviceable in this area yet', false);
                    }
                });
            });
        }

        form.querySelectorAll('.vc-upload-card input[type="file"]').forEach(function (input) {
            input.addEventListener('change', function () {
                var nameEl = input.closest('.vc-upload-card') && input.closest('.vc-upload-card').querySelector('.vc-file-name');
                if (nameEl) {
                    nameEl.textContent = (input.files && input.files[0]) ? input.files[0].name : 'No file selected';
                }
            });
        });

        if (resendBtn) {
            resendBtn.addEventListener('click', function () { sendOtp(); });
        }

        form.querySelectorAll('.vc-password-toggle[data-target]').forEach(function (btn) {
            if (btn.getAttribute('data-vc-bound') === '1') return;
            btn.setAttribute('data-vc-bound', '1');
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var input = document.getElementById(btn.getAttribute('data-target') || '');
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                var icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', !show);
                    icon.classList.toggle('fa-eye-slash', show);
                }
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (step > 1) {
                    showStep(step - 1);
                    return;
                }
                // Step 1: leave wizard (Back was visible but did nothing before)
                window.location.href = 'index.php';
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (step === 5) {
                    submitRegistration();
                    return;
                }
                var ok = validateStep(step);
                if (ok === true) {
                    showStep(step + 1);
                } else if (ok && typeof ok.then === 'function') {
                    ok.then(function () { /* step advanced inside promise when needed */ });
                }
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (step === 5) submitRegistration();
            else if (nextBtn) nextBtn.click();
        });

        if (VC.isLoggedIn()) {
            otpVerified = true;
            var cust = VC.getCustomer() || {};
            if (cust.registration_complete) {
                window.location.href = 'account-dashboard.php';
                return;
            }
            if (cust.mobile && document.getElementById('vcSignupPhone')) {
                document.getElementById('vcSignupPhone').value = cust.mobile;
            }
            if (cust.email && document.getElementById('vcSignupEmail')) {
                document.getElementById('vcSignupEmail').value = cust.email;
            }
            if (cust.owner_name && document.getElementById('vcSignupOwnerName')) {
                document.getElementById('vcSignupOwnerName').value = cust.owner_name;
            }
            if (cust.business_name && document.getElementById('vcSignupBusinessName')) {
                document.getElementById('vcSignupBusinessName').value = cust.business_name;
            }
            if (otpWrap) otpWrap.hidden = true;
            showStep(2);
        } else {
            showStep(1);
        }
    }

    function bootForgot() {
        var form = document.querySelector('form');
        if (!form || pageName() !== 'forgot-password') {
            return;
        }
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var input = form.querySelector('input[type="tel"], input[type="text"], input[name="mobile"], input');
            var mobile = input ? input.value.trim() : '';
            if (!mobile) {
                toast('Enter your registered mobile number.', 'error');
                return;
            }
            VC.sendOtp(mobile).then(function (res) {
                if (!res || !res.success) {
                    toast((res && res.error && res.error.message) || 'Could not send OTP.', 'error');
                    return;
                }
                var promptFn = window.VC && typeof window.VC.prompt === 'function'
                    ? window.VC.prompt
                    : function (msg) { return Promise.resolve(window.prompt(msg)); };
                promptFn('Enter the OTP sent to your mobile:', {
                    title: 'Verify OTP',
                    placeholder: '6-digit OTP',
                    devOtp: res.data && res.data.dev_otp ? res.data.dev_otp : ''
                }).then(function (otp) {
                    if (!otp) {
                        return;
                    }
                    VC.verifyOtp(mobile, otp).then(function (v) {
                        if (v && v.success) {
                            applyAuthSuccess(v.data, 'account-dashboard.php');
                        } else {
                            toast((v && v.error && v.error.message) || 'Invalid OTP.', 'error');
                        }
                    });
                });
            });
        });
    }

    /* ---------- Cart / checkout ---------- */

    function bootCart() {
        if (!requireAuth()) {
            return;
        }
        var wrap = document.querySelector('.vc-cart-items-wrap');
        if (wrap) {
            wrap.innerHTML = emptyNote('Loading cart…');
        }
        VC.cart().then(function (res) {
            if (!res || !res.success) {
                toast((res && res.error && res.error.message) || 'Could not load cart.', 'error');
                return;
            }
            var data = res.data;
            if (wrap) {
                var actionsHtml =
                    '<div class="vc-cart-actions">' +
                        '<a href="product.php" class="vc-continue-btn">' +
                            '<i class="fa-solid fa-arrow-left"></i> Continue Shopping' +
                        '</a>' +
                        '<button type="button" class="vc-update-btn" id="vcUpdateCartBtn">' +
                            '<i class="fa-solid fa-rotate"></i> Update Cart' +
                        '</button>' +
                    '</div>';
                if (!data.items.length) {
                    wrap.innerHTML = emptyNote('Your cart is empty.', '<a href="product.php">Continue shopping</a>');
                } else {
                    wrap.innerHTML = data.items.map(function (item) {
                        return (
                            '<div class="vc-cart-item" data-item-id="' + item.id + '">' +
                                '<div class="vc-cart-product">' +
                                    '<div class="vc-cart-image">' + catalogThumb(item.image_url, titleCaseName(item.name)) + '</div>' +
                                    '<div class="vc-cart-product-info">' +
                                        '<span class="vc-cart-category">' + escapeHtml(item.category_name || '') + '</span>' +
                                        '<h3>' + escapeHtml(titleCaseName(item.name)) + '</h3>' +
                                        '<div class="vc-cart-meta"><span>' + escapeHtml(item.unit || '') + '</span>' +
                                            '<span class="vc-stock">' + (item.in_stock ? 'In Stock' : 'Out of Stock') + '</span></div>' +
                                        '<button class="vc-remove-btn" type="button" data-remove-item="' + item.id + '"><i class="fa-solid fa-trash-can"></i> Remove</button>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="vc-cart-price"><span>Price</span><strong>' + money(item.price) + '</strong></div>' +
                                '<div class="vc-cart-quantity"><span>Quantity</span>' +
                                    '<div class="vc-qty-box">' +
                                        '<button type="button" class="vc-qty-minus" data-qty-delta="-1" data-item-id="' + item.id + '" data-qty="' + item.quantity + '"><i class="fa-solid fa-minus"></i></button>' +
                                        '<input type="number" value="' + item.quantity + '" min="' + (item.moq || 1) + '" data-item-id="' + item.id + '">' +
                                        '<button type="button" class="vc-qty-plus" data-qty-delta="1" data-item-id="' + item.id + '" data-qty="' + item.quantity + '"><i class="fa-solid fa-plus"></i></button>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="vc-cart-total"><span>Total</span><strong>' + money(item.line_total) + '</strong></div>' +
                            '</div>'
                        );
                    }).join('') + actionsHtml;
                }
            }
            setSummaryTotals(data);
        });

        if (!bootCart._bound) {
            bootCart._bound = true;
            document.addEventListener('click', function (e) {
                var rm = e.target.closest('[data-remove-item]');
                if (rm) {
                    VC.removeCartItem(rm.getAttribute('data-remove-item')).then(function () { bootCart(); refreshHeaderCounts(); });
                    return;
                }
                var deltaBtn = e.target.closest('[data-qty-delta]');
                if (deltaBtn) {
                    var next = Number(deltaBtn.getAttribute('data-qty')) + Number(deltaBtn.getAttribute('data-qty-delta'));
                    if (next < 1) {
                        return;
                    }
                    VC.updateCartItem(deltaBtn.getAttribute('data-item-id'), next).then(function (res) {
                        if (!res || !res.success) {
                            toast((res && res.error && res.error.message) || 'Could not update quantity.', 'error');
                        }
                        bootCart();
                    });
                    return;
                }
                if (e.target.closest('#vcUpdateCartBtn')) {
                    bootCart();
                    toast('Cart updated');
                    return;
                }
                if (e.target.closest('#vcCartCouponBtn')) {
                    var input = document.getElementById('vcCartCouponInput');
                    var code = input ? String(input.value || '').trim() : '';
                    if (!code) {
                        toast('Enter a coupon code.', 'error');
                        return;
                    }
                    VC.applyCoupon(code).then(function (res) {
                        if (res && res.success) {
                            toast('Coupon applied');
                            bootCart();
                        } else {
                            toast((res && res.error && res.error.message) || 'Invalid coupon.', 'error');
                        }
                    });
                }
            });
        }
    }

    function setSummaryTotals(data) {
        data = data || {};
        var subtotal = Number(data.subtotal) || 0;
        var discount = Number(data.discount) || 0;
        var delivery = Number(data.delivery_fee != null ? data.delivery_fee : data.delivery) || 0;
        var total = data.total != null ? Number(data.total) : Math.max(0, subtotal - discount + delivery);

        var subEl = document.querySelector('[data-cart-subtotal]');
        if (subEl) {
            subEl.textContent = money(subtotal);
        }

        var discountEl = document.querySelector('[data-cart-discount]');
        if (discountEl) {
            discountEl.textContent = discount > 0 ? '- ' + money(discount) : money(0);
        }

        var deliveryEl = document.querySelector('[data-cart-delivery]');
        if (deliveryEl) {
            deliveryEl.textContent = delivery > 0 ? money(delivery) : 'FREE';
            deliveryEl.classList.toggle('vc-free', delivery <= 0);
        }

        var totalEl = document.querySelector('[data-cart-total]');
        if (totalEl) {
            totalEl.textContent = money(total);
        }

        var checkoutRows = document.querySelectorAll('.vc-checkout-price-row');
        checkoutRows.forEach(function (row) {
            var label = String((row.querySelector('span') || {}).textContent || '').toLowerCase();
            var strong = row.querySelector('strong');
            if (!strong) {
                return;
            }
            if (label.indexOf('subtotal') !== -1) {
                strong.textContent = money(subtotal);
            } else if (label.indexOf('discount') !== -1) {
                strong.textContent = discount > 0 ? '-' + money(discount) : money(0);
            } else if (label.indexOf('delivery') !== -1) {
                strong.textContent = delivery > 0 ? money(delivery) : 'FREE';
                strong.classList.toggle('vc-free-text', delivery <= 0);
            }
        });

        var strongs = document.querySelectorAll('.vc-cart-totals strong, .vc-checkout-total > strong, .vc-checkout-total strong');
        if (strongs.length) {
            strongs[strongs.length - 1].textContent = money(total);
        }
    }

    function bootCheckout() {
        if (!requireAuth()) {
            return;
        }
        var addressCard = document.getElementById('vcCheckoutAddressCard')
            || document.querySelectorAll('.vc-checkout-card')[1];
        var slotCard = document.getElementById('vcCheckoutSlotCard')
            || document.querySelectorAll('.vc-checkout-card')[2];
        var addressMount = document.getElementById('vcCheckoutAddressMount') || addressCard;
        var slotMount = document.getElementById('vcCheckoutSlotMount') || slotCard;
        var selectedAddress = null;
        var selectedSlot = null;
        var deliveryMode = 'single';
        var selectedMultiIds = [];
        var cartRef = { items: [], total: 0, subtotal: 0, discount: 0 };
        var addressesRef = [];
        var slotsRef = [];
        var placeBtn = document.querySelector('.vc-place-order-btn');

        function addrServiceable(a) {
            return a && a.serviceable !== false;
        }

        function slotLabel(id) {
            var s = slotsRef.find(function (x) { return Number(x.id) === Number(id); });
            return s ? (s.date + ' · ' + s.label) : '';
        }

        function renderAllocationUI(host) {
            var box = host.querySelector('#vcMultiAlloc');
            if (!box) {
                return;
            }
            if (deliveryMode !== 'split' || selectedMultiIds.length < 2) {
                box.innerHTML = selectedMultiIds.length < 2
                    ? '<div class="vc-checkout-empty-note"><i class="fa-solid fa-diagram-project"></i><p>Select <strong>at least 2 addresses</strong> above, then allocate each product’s quantity across them.</p></div>'
                    : '';
                updatePlaceEnabled();
                return;
            }
            var addrs = selectedMultiIds.map(function (id) {
                return addressesRef.find(function (a) { return Number(a.id) === Number(id); });
            }).filter(Boolean);

            var slotsHtml = addrs.map(function (a) {
                return '<div class="vc-multi-slot-row" data-addr-slot="' + a.id + '">' +
                    '<span class="vc-multi-slot-label">' + escapeHtml(a.label || 'Address') + '</span>' +
                    '<select class="vc-multi-slot-select" data-slot-for="' + a.id + '">' +
                    '<option value="">Any / later</option>' +
                    slotsRef.slice(0, 12).map(function (s) {
                        return '<option value="' + s.id + '">' + escapeHtml(s.date + ' · ' + s.label) + '</option>';
                    }).join('') +
                    '</select></div>';
            }).join('');

            var rows = (cartRef.items || []).map(function (item) {
                var pid = item.product_id || item.id;
                var totalQty = Number(item.quantity) || 0;
                var inputs = addrs.map(function (a) {
                    return '<label class="vc-alloc-cell"><span>' + escapeHtml(a.label || ('#' + a.id)) + '</span>' +
                        '<input type="number" min="0" step="0.01" class="vc-alloc-input" data-pid="' + pid +
                        '" data-aid="' + a.id + '" value="0"></label>';
                }).join('');
                return '<div class="vc-alloc-row" data-pid="' + pid + '" data-total="' + totalQty + '">' +
                    '<div class="vc-alloc-head"><strong>' + escapeHtml(titleCaseName(item.name)) +
                    '</strong> <span class="vc-alloc-need-wrap">Cart total: <b class="vc-alloc-need">' + totalQty + '</b></span>' +
                    ' <span class="vc-alloc-status" data-status-for="' + pid + '">Allocate fully</span></div>' +
                    '<div class="vc-alloc-inputs">' + inputs + '</div></div>';
            }).join('');

            box.innerHTML =
                '<div class="vc-multi-panel">' +
                    '<div class="vc-multi-panel-title">Slot per location</div>' +
                    '<div class="vc-multi-slots">' + slotsHtml + '</div>' +
                '</div>' +
                '<div class="vc-multi-panel">' +
                    '<div class="vc-multi-panel-title">Allocate quantities</div>' +
                    '<p class="vc-multi-panel-sub">Each product’s inputs must add up exactly to the cart total.</p>' +
                    '<div class="vc-multi-alloc-list">' + rows + '</div>' +
                '</div>';

            box.querySelectorAll('.vc-alloc-input').forEach(function (inp) {
                inp.addEventListener('input', validateAllocations);
            });
            validateAllocations();
        }

        function validateAllocations() {
            var box = document.getElementById('vcMultiAlloc');
            if (!box || deliveryMode !== 'split') {
                updatePlaceEnabled();
                return true;
            }
            var ok = true;
            box.querySelectorAll('.vc-alloc-row').forEach(function (row) {
                var need = Number(row.getAttribute('data-total')) || 0;
                var pid = row.getAttribute('data-pid');
                var sum = 0;
                row.querySelectorAll('.vc-alloc-input').forEach(function (inp) {
                    sum += Number(inp.value) || 0;
                });
                sum = Math.round(sum * 100) / 100;
                need = Math.round(need * 100) / 100;
                var status = box.querySelector('[data-status-for="' + pid + '"]');
                var match = Math.abs(sum - need) < 0.001;
                if (!match) ok = false;
                if (status) {
                    status.textContent = match ? '✓ Fully allocated' : ('Allocated ' + sum + ' / ' + need);
                    status.style.color = match ? '#1a7f37' : '#b42318';
                }
            });
            updatePlaceEnabled(ok);
            return ok;
        }

        function updatePlaceEnabled(allocOk) {
            if (!placeBtn) return;
            if (deliveryMode === 'split') {
                var can = selectedMultiIds.length >= 2 && cartRef.items.length > 0 && allocOk !== false && validateAllocationsSilent();
                placeBtn.disabled = !can;
                placeBtn.style.opacity = can ? '1' : '0.55';
            } else {
                placeBtn.disabled = false;
                placeBtn.style.opacity = '1';
            }
        }

        function validateAllocationsSilent() {
            var box = document.getElementById('vcMultiAlloc');
            if (!box) return false;
            var ok = true;
            box.querySelectorAll('.vc-alloc-row').forEach(function (row) {
                var need = Math.round((Number(row.getAttribute('data-total')) || 0) * 100) / 100;
                var sum = 0;
                row.querySelectorAll('.vc-alloc-input').forEach(function (inp) {
                    sum += Number(inp.value) || 0;
                });
                if (Math.abs(Math.round(sum * 100) / 100 - need) > 0.001) ok = false;
            });
            return ok && (cartRef.items || []).length > 0;
        }

        function buildMultiPayload(notes) {
            var box = document.getElementById('vcMultiAlloc');
            var blocks = selectedMultiIds.map(function (aid) {
                var items = [];
                (cartRef.items || []).forEach(function (item) {
                    var pid = item.product_id || item.id;
                    var inp = box.querySelector('.vc-alloc-input[data-pid="' + pid + '"][data-aid="' + aid + '"]');
                    var qty = inp ? Number(inp.value) || 0 : 0;
                    if (qty > 0) {
                        items.push({ product_id: Number(pid), quantity: qty });
                    }
                });
                var slotSel = box.querySelector('.vc-multi-slot-select[data-slot-for="' + aid + '"]');
                var slotId = slotSel && slotSel.value ? Number(slotSel.value) : null;
                var blockNotes = notes || '';
                if (slotId) {
                    var sl = slotLabel(slotId);
                    if (sl) blockNotes = (blockNotes ? blockNotes + ' — ' : '') + 'Slot: ' + sl;
                }
                return {
                    address_id: Number(aid),
                    delivery_slot_id: slotId,
                    notes: blockNotes,
                    items: items
                };
            });
            return { addresses: blocks };
        }

        Promise.all([VC.cart(), VC.addresses(), VC.deliverySlots(), VC.profile()]).then(function (pack) {
            var cart = pack[0] && pack[0].success ? pack[0].data : { items: [], total: 0, subtotal: 0, discount: 0 };
            var addresses = pack[1] && pack[1].success ? pack[1].data.addresses : [];
            var slots = pack[2] && pack[2].success ? pack[2].data.slots : [];
            var profile = pack[3] && pack[3].success ? pack[3].data : VC.getCustomer();
            cartRef = cart;
            addressesRef = addresses;
            slotsRef = slots;

            if (profile) {
                var nameInput = document.querySelector('.vc-checkout-form-grid input[name="name"]');
                var phoneInput = document.querySelector('.vc-checkout-form-grid input[name="phone"]');
                var emailInput = document.querySelector('.vc-checkout-form-grid input[name="email"]');
                if (nameInput && profile.owner_name) nameInput.value = profile.owner_name;
                if (phoneInput && profile.mobile) phoneInput.value = profile.mobile;
                if (emailInput && profile.email) emailInput.value = profile.email;
            }

            if (addressMount) {
                addressMount.querySelectorAll('.vc-live-address-picker, .vc-delivery-mode, .vc-multi-alloc, #vcMultiAlloc').forEach(function (n) { n.remove(); });

                var modeBox = document.createElement('div');
                modeBox.className = 'vc-delivery-mode';
                modeBox.innerHTML =
                    '<div class="vc-mode-label">How should we deliver?</div>' +
                    '<div class="vc-mode-toggle" role="radiogroup" aria-label="Delivery mode">' +
                        '<button type="button" class="vc-mode-option is-active" data-mode="single">' +
                            '<span class="vc-mode-radio" aria-hidden="true"></span>' +
                            '<span class="vc-mode-copy"><strong>One address</strong><small>Send the full cart to a single location</small></span>' +
                        '</button>' +
                        '<button type="button" class="vc-mode-option" data-mode="split">' +
                            '<span class="vc-mode-radio" aria-hidden="true"></span>' +
                            '<span class="vc-mode-copy"><strong>Split addresses</strong><small>Divide quantities across 2+ locations</small></span>' +
                        '</button>' +
                    '</div>';
                addressMount.appendChild(modeBox);

                var picker = document.createElement('div');
                picker.className = 'vc-live-address-picker';
                picker.id = 'vcAddressPicker';
                addressMount.appendChild(picker);

                var multiHost = document.createElement('div');
                multiHost.id = 'vcMultiAlloc';
                multiHost.className = 'vc-multi-alloc';
                multiHost.style.display = 'none';
                addressMount.appendChild(multiHost);

                function syncModeButtons() {
                    modeBox.querySelectorAll('.vc-mode-option').forEach(function (btn) {
                        btn.classList.toggle('is-active', btn.getAttribute('data-mode') === deliveryMode);
                    });
                }

                function addressCardHtml(a, inputHtml) {
                    return (
                        '<label class="vc-addr-choice">' +
                            inputHtml +
                            '<span class="vc-addr-choice-mark" aria-hidden="true"></span>' +
                            '<span class="vc-addr-choice-body">' +
                                '<span class="vc-addr-choice-top">' +
                                    '<strong>' + escapeHtml(a.label || 'Address') + '</strong>' +
                                    (a.is_default ? '<em class="vc-addr-default">Default</em>' : '') +
                                '</span>' +
                                '<span class="vc-addr-choice-line">' +
                                    escapeHtml([a.line1, a.line2, a.city, a.state, a.pincode].filter(Boolean).join(', ')) +
                                '</span>' +
                            '</span>' +
                        '</label>'
                    );
                }

                function paintAddresses() {
                    var serviceable = addresses.filter(addrServiceable);
                    var blocked = addresses.filter(function (a) { return !addrServiceable(a); });
                    syncModeButtons();

                    if (deliveryMode === 'single') {
                        multiHost.style.display = 'none';
                        if (slotCard) slotCard.style.display = '';

                        if (!serviceable.length) {
                            picker.innerHTML =
                                '<div class="vc-checkout-empty">' +
                                    '<div class="vc-checkout-empty-icon"><i class="fa-solid fa-location-dot"></i></div>' +
                                    '<strong>No delivery address yet</strong>' +
                                    '<p>Add a Hyderabad address to continue checkout.</p>' +
                                    '<a class="vc-checkout-empty-btn" href="manage-address.php">Add address</a>' +
                                '</div>' +
                                (blocked.length
                                    ? '<div class="vc-addr-warn">Some saved addresses are outside our Hyderabad service area and can’t be used.</div>'
                                    : '');
                            selectedAddress = null;
                            return;
                        }

                        picker.innerHTML =
                            '<div class="vc-addr-picker-head">' +
                                '<strong>Saved addresses</strong>' +
                                '<a href="manage-address.php">Manage</a>' +
                            '</div>' +
                            '<div class="vc-addr-choice-list">' +
                                serviceable.map(function (a) {
                                    return addressCardHtml(
                                        a,
                                        '<input type="radio" name="vc_address_id" value="' + a.id + '"' + (a.is_default ? ' checked' : '') + '>'
                                    );
                                }).join('') +
                            '</div>' +
                            (blocked.length
                                ? '<div class="vc-addr-warn">Not serviceable: ' +
                                    blocked.map(function (a) {
                                        return escapeHtml((a.label || 'Address') + ' (' + (a.pincode || '') + ')');
                                    }).join(', ') +
                                  '</div>'
                                : '');

                        var def = serviceable.find(function (a) { return a.is_default; }) || serviceable[0];
                        selectedAddress = def ? def.id : null;
                        if (def) {
                            var radio = picker.querySelector('input[value="' + def.id + '"]');
                            if (radio) radio.checked = true;
                        }
                        picker.querySelectorAll('.vc-addr-choice').forEach(function (lab) {
                            lab.classList.toggle('is-selected', !!(lab.querySelector('input') && lab.querySelector('input').checked));
                        });
                    } else {
                        if (slotCard) slotCard.style.display = 'none';
                        multiHost.style.display = '';

                        if (serviceable.length < 2) {
                            picker.innerHTML =
                                '<div class="vc-checkout-empty">' +
                                    '<div class="vc-checkout-empty-icon"><i class="fa-solid fa-map-location-dot"></i></div>' +
                                    '<strong>Need 2+ Hyderabad addresses</strong>' +
                                    '<p>Split delivery needs at least two saved serviceable addresses.</p>' +
                                    '<a class="vc-checkout-empty-btn" href="manage-address.php">Add / manage addresses</a>' +
                                '</div>';
                            selectedMultiIds = [];
                            renderAllocationUI(addressMount);
                            return;
                        }

                        picker.innerHTML =
                            '<div class="vc-addr-picker-head">' +
                                '<strong>Select 2 or more addresses</strong>' +
                                '<a href="manage-address.php">Manage</a>' +
                            '</div>' +
                            '<div class="vc-addr-choice-list">' +
                                serviceable.map(function (a) {
                                    var checked = selectedMultiIds.indexOf(Number(a.id)) !== -1 ? ' checked' : '';
                                    return addressCardHtml(
                                        a,
                                        '<input type="checkbox" name="vc_multi_address" value="' + a.id + '"' + checked + '>'
                                    );
                                }).join('') +
                            '</div>';
                        picker.querySelectorAll('.vc-addr-choice').forEach(function (lab) {
                            lab.classList.toggle('is-selected', !!(lab.querySelector('input') && lab.querySelector('input').checked));
                        });
                        renderAllocationUI(addressMount);
                    }
                }

                paintAddresses();

                modeBox.addEventListener('click', function (e) {
                    var btn = e.target.closest('.vc-mode-option');
                    if (!btn || !modeBox.contains(btn)) return;
                    e.preventDefault();
                    var next = btn.getAttribute('data-mode');
                    if (!next || next === deliveryMode) return;
                    deliveryMode = next;
                    selectedMultiIds = [];
                    paintAddresses();
                    updatePlaceEnabled();
                });

                picker.addEventListener('change', function (e) {
                    if (e.target.name === 'vc_address_id') {
                        selectedAddress = Number(e.target.value);
                        picker.querySelectorAll('.vc-addr-choice').forEach(function (lab) {
                            lab.classList.toggle('is-selected', lab.querySelector('input') && lab.querySelector('input').checked);
                        });
                    }
                    if (e.target.name === 'vc_multi_address') {
                        selectedMultiIds = Array.prototype.slice.call(
                            picker.querySelectorAll('input[name="vc_multi_address"]:checked')
                        ).map(function (el) { return Number(el.value); });
                        picker.querySelectorAll('.vc-addr-choice').forEach(function (lab) {
                            lab.classList.toggle('is-selected', lab.querySelector('input') && lab.querySelector('input').checked);
                        });
                        renderAllocationUI(addressMount);
                    }
                });
            }

            if (slotMount && slots.length) {
                slotMount.querySelectorAll('.vc-live-slots').forEach(function (n) { n.remove(); });
                var slotBox = document.createElement('div');
                slotBox.className = 'vc-live-slots';
                slotBox.innerHTML = '<p><strong>Available slots</strong></p>' + slots.slice(0, 12).map(function (s) {
                    return '<label class="vc-live-slot"><input type="radio" name="vc_slot_id" value="' + s.id + '"> ' +
                        escapeHtml(s.date) + ' · ' + escapeHtml(s.label) + '</label>';
                }).join('');
                slotMount.appendChild(slotBox);
                slotBox.addEventListener('change', function (e) {
                    if (e.target.name === 'vc_slot_id') {
                        selectedSlot = Number(e.target.value);
                        slotBox.querySelectorAll('.vc-live-slot').forEach(function (lab) {
                            lab.classList.toggle('is-selected', lab.querySelector('input') && lab.querySelector('input').checked);
                        });
                    }
                });
            } else if (slotMount) {
                slotMount.innerHTML = '<p class="vc-checkout-hint">No delivery slots available right now. You can still place the order — we will confirm timing.</p>';
            }

            var summary = document.querySelector('.vc-order-summary-card');
            if (summary) {
                var productsHtml = cart.items.map(function (item) {
                    return '<div class="vc-order-product"><div class="vc-order-product-image">' +
                        catalogThumb(item.image_url, titleCaseName(item.name)) +
                        '</div><div><strong>' + escapeHtml(titleCaseName(item.name)) +
                        '</strong><small>' + item.quantity + ' × ' + money(item.price) + '</small></div></div>';
                }).join('') || emptyNote('Cart is empty.');
                var existingProducts = summary.querySelectorAll('.vc-order-product');
                existingProducts.forEach(function (n) { n.remove(); });
                var heading = summary.querySelector('.vc-order-summary-heading');
                if (heading) {
                    heading.insertAdjacentHTML('afterend', productsHtml);
                }
            }
            setSummaryTotals(cart);

            var couponForm = document.querySelector('.vc-checkout-coupon');
            if (couponForm) {
                var couponBtn = couponForm.querySelector('button');
                var couponInput = couponForm.querySelector('input');
                if (couponBtn && couponInput) {
                    var freshCouponBtn = couponBtn.cloneNode(true);
                    couponBtn.parentNode.replaceChild(freshCouponBtn, couponBtn);
                    freshCouponBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        VC.applyCoupon(couponInput.value.trim()).then(function (res) {
                            if (res && res.success) {
                                toast('Coupon applied');
                                bootCheckout();
                            } else {
                                toast((res && res.error && res.error.message) || 'Invalid coupon.', 'error');
                            }
                        });
                    });
                }
            }

            placeBtn = document.querySelector('.vc-place-order-btn');
            if (placeBtn) {
                var freshPlace = placeBtn.cloneNode(true);
                placeBtn.parentNode.replaceChild(freshPlace, placeBtn);
                placeBtn = freshPlace;
                placeBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (!cartRef.items.length) {
                        toast('Your cart is empty.', 'error');
                        return;
                    }
                    var notes = (document.querySelector('textarea[name="instructions"]') || {}).value || '';
                    placeBtn.disabled = true;

                    if (deliveryMode === 'split') {
                        if (selectedMultiIds.length < 2) {
                            placeBtn.disabled = false;
                            toast('Select at least two addresses.', 'error');
                            return;
                        }
                        if (!validateAllocations()) {
                            placeBtn.disabled = false;
                            toast('Allocate each product fully across addresses.', 'error');
                            return;
                        }
                        VC.placeMultiAddressOrder(buildMultiPayload(notes)).then(function (res) {
                            placeBtn.disabled = false;
                            if (res && res.success) {
                                var batchId = res.data.batch_id;
                                var orders = res.data.orders || [];
                                var first = orders[0];
                                var ids = orders.map(function (o) { return o.order_id; }).join(',');
                                window.location.href = 'order-success.php?batch=' + encodeURIComponent(batchId) +
                                    '&ids=' + encodeURIComponent(ids) +
                                    (first ? '&id=' + encodeURIComponent(first.order_id) : '');
                            } else {
                                toast((res && res.error && res.error.message) || 'Could not place orders.', 'error');
                            }
                        }).catch(function () {
                            placeBtn.disabled = false;
                            toast('Could not place orders.', 'error');
                        });
                        return;
                    }

                    if (!selectedAddress) {
                        placeBtn.disabled = false;
                        toast('Select or add a delivery address.', 'error');
                        return;
                    }
                    var chosen = addressesRef.find(function (a) { return Number(a.id) === Number(selectedAddress); });
                    if (chosen && !addrServiceable(chosen)) {
                        placeBtn.disabled = false;
                        toast("We currently deliver only within Hyderabad — this pincode isn't serviceable yet", 'error');
                        return;
                    }
                    VC.placeOrder({
                        address_id: selectedAddress,
                        delivery_slot_id: selectedSlot,
                        notes: notes
                    }).then(function (res) {
                        placeBtn.disabled = false;
                        if (res && res.success) {
                            var order = res.data.order;
                            window.location.href = 'order-success.php?id=' + encodeURIComponent(order.id);
                        } else {
                            toast((res && res.error && res.error.message) || 'Could not place order.', 'error');
                        }
                    }).catch(function () {
                        placeBtn.disabled = false;
                        toast('Could not place order.', 'error');
                    });
                });
            }
            updatePlaceEnabled();
        });
    }

    /* ---------- Account ---------- */

    function bootWishlist() {
        if (!requireAuth()) {
            return;
        }
        var grid = document.getElementById('vcWishlistGrid');
        if (grid) {
            grid.innerHTML = emptyNote('Loading wishlist…');
        }
        VC.wishlist().then(function (res) {
            var items = (res && res.success && res.data.items) || [];
            var count = document.getElementById('vcWishlistCount');
            if (count) count.textContent = String(items.length);
            if (!grid) {
                return;
            }
            if (!items.length) {
                grid.innerHTML = emptyNote('Your wishlist is empty.', '<a href="product.php">Browse products</a>');
                var moveAll = document.getElementById('vcMoveAll');
                if (moveAll) {
                    moveAll.style.display = 'none';
                }
                return;
            }
            grid.innerHTML = items.map(function (item) {
                var pid = item.product_id || item.id;
                return (
                    '<article class="vc-wishlist-card">' +
                        '<div class="vc-wishlist-image">' +
                            '<a href="product-details.php?id=' + pid + '">' + catalogThumb(item.image_url, titleCaseName(item.name)) + '</a>' +
                            '<button type="button" class="vc-wishlist-remove" data-wish-remove="' + item.id + '"><i class="fa-solid fa-xmark"></i></button>' +
                        '</div>' +
                        '<div class="vc-wishlist-content">' +
                            '<span class="vc-wishlist-category">' + escapeHtml(item.category_name || '') + '</span>' +
                            '<h3><a href="product-details.php?id=' + pid + '">' + escapeHtml(titleCaseName(item.name)) + '</a></h3>' +
                            '<div class="vc-list-price"><strong>' + money(item.price) + '</strong></div>' +
                            '<button type="button" class="vc-list-cart-btn" data-wish-cart="' + item.id + '">Move to Cart</button>' +
                        '</div>' +
                    '</article>'
                );
            }).join('');
        });
        document.addEventListener('click', function (e) {
            var rm = e.target.closest('[data-wish-remove]');
            if (rm) {
                VC.removeWishlist(rm.getAttribute('data-wish-remove')).then(function () { bootWishlist(); refreshHeaderCounts(); });
            }
            var mv = e.target.closest('[data-wish-cart]');
            if (mv) {
                VC.moveWishlistToCart(mv.getAttribute('data-wish-cart')).then(function (res) {
                    if (res && res.success) {
                        toast('Moved to cart');
                        bootWishlist();
                        refreshHeaderCounts();
                    } else {
                        toast((res && res.error && res.error.message) || 'Could not move to cart.', 'error');
                    }
                });
            }
        });
        var moveAll = document.getElementById('vcMoveAll');
        if (moveAll) {
            moveAll.addEventListener('click', function () {
                VC.wishlist().then(function (res) {
                    var items = (res && res.success && res.data.items) || [];
                    var chain = Promise.resolve();
                    items.forEach(function (item) {
                        chain = chain.then(function () { return VC.moveWishlistToCart(item.id); });
                    });
                    chain.then(function () { bootWishlist(); refreshHeaderCounts(); toast('Moved to cart'); });
                });
            });
        }
    }

    function bootOrders() {
        if (!requireAuth()) {
            return;
        }
        document.querySelectorAll('.vc-order-card').forEach(function (n) { n.remove(); });
        VC.orders({ per_page: 50 }).then(function (res) {
            var orders = (res && res.success && res.data.orders) || [];
            var first = document.querySelector('.vc-order-card');
            var parent = first ? first.parentElement : document.querySelector('.vc-orders-container');
            document.querySelectorAll('.vc-order-card').forEach(function (n) { n.remove(); });
            var no = document.getElementById('vcNoOrders');
            var processing = orders.filter(function (o) {
                return ['pending', 'confirmed', 'processing', 'packed'].indexOf(String(o.status).toLowerCase()) !== -1;
            }).length;
            var onWay = orders.filter(function (o) {
                return ['shipped', 'out_for_delivery'].indexOf(String(o.status).toLowerCase()) !== -1;
            }).length;
            var delivered = orders.filter(function (o) {
                return String(o.status).toLowerCase() === 'delivered';
            }).length;
            setText('vcOrdersTotal', orders.length);
            setText('vcOrdersProcessing', processing);
            setText('vcOrdersOnWay', onWay);
            setText('vcOrdersDelivered', delivered);
            if (!orders.length) {
                if (no) {
                    no.style.display = 'block';
                    var copy = no.querySelector('p');
                    if (copy) {
                        copy.textContent = 'You have not placed an order yet. Start with fresh produce for your business.';
                    }
                    var shop = no.querySelector('a');
                    if (shop) {
                        shop.setAttribute('href', 'product.php');
                    }
                }
                return;
            }
            if (no) no.style.display = 'none';
            var html = orders.map(function (o) {
                var st = String(o.status || '').toLowerCase();
                return (
                    '<article class="vc-order-card" data-status="' + escapeHtml(st) + '" data-order="' + escapeHtml(o.order_number) + '">' +
                        '<div class="vc-order-card-header">' +
                            '<div class="vc-order-id"><span>Order ID</span><strong>' + escapeHtml(o.order_number) + '</strong>' +
                            (o.is_multi_location || o.batch_id
                                ? '<div class="vc-batch-badge" style="font-size:0.78rem;color:#0b6bcb;margin-top:0.25rem;">Part of a multi-location order</div>'
                                : '') +
                            '</div>' +
                            '<div class="vc-order-date"><i class="fa-regular fa-calendar"></i><div><span>Order Date</span><strong>' +
                                escapeHtml((o.placed_at || '').slice(0, 10)) + '</strong></div></div>' +
                            '<span class="vc-order-status ' + escapeHtml(st) + '">' + escapeHtml(o.status_label || o.status) + '</span>' +
                        '</div>' +
                        '<div class="vc-order-footer">' +
                            '<div class="vc-order-payment"><span>Total</span><strong class="vc-order-total">' + money(o.total) + '</strong></div>' +
                            '<div class="vc-order-actions">' +
                                '<a href="order-details.php?id=' + o.id + '" class="vc-order-btn vc-btn-outline">View Details</a>' +
                                '<a href="order-details-tracking.php?id=' + o.id + '" class="vc-order-btn vc-btn-green">Track</a>' +
                                (o.can_cancel ? '<button type="button" class="vc-order-btn vc-btn-outline" data-cancel-order="' + o.id + '">Cancel</button>' : '') +
                                '<button type="button" class="vc-order-btn vc-btn-outline" data-reorder="' + o.id + '">Reorder</button>' +
                            '</div>' +
                        '</div>' +
                    '</article>'
                );
            }).join('');
            if (no) {
                no.insertAdjacentHTML('beforebegin', html);
            } else if (parent) {
                parent.insertAdjacentHTML('beforeend', html);
            }
        });
        document.addEventListener('click', function (e) {
            var c = e.target.closest('[data-cancel-order]');
            if (c) {
                var confirmFn = window.VC && typeof window.VC.confirm === 'function'
                    ? window.VC.confirm
                    : function (msg) { return Promise.resolve(window.confirm(msg)); };
                confirmFn('Cancel this order?', {
                    title: 'Cancel order',
                    danger: true,
                    confirmText: 'Yes, cancel order'
                }).then(function (ok) {
                    if (!ok) {
                        return;
                    }
                    VC.cancelOrder(c.getAttribute('data-cancel-order')).then(function (res) {
                        toast((res && res.success && res.data.message) || (res && res.error && res.error.message) || 'Updated');
                        bootOrders();
                    });
                });
            }
            var r = e.target.closest('[data-reorder]');
            if (r) {
                VC.reorder(r.getAttribute('data-reorder')).then(function (res) {
                    if (res && res.success) {
                        toast('Items added to cart');
                        window.location.href = 'cart.php';
                    } else {
                        toast((res && res.error && res.error.message) || 'Could not reorder.', 'error');
                    }
                });
            }
        });
    }

    function formatInDateTime(value) {
        if (!value) {
            return '—';
        }
        var d = new Date(String(value).replace(' ', 'T'));
        if (isNaN(d.getTime())) {
            return String(value);
        }
        return d.toLocaleString('en-IN', {
            day: 'numeric',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function qtyLabel(qty) {
        var n = Number(qty);
        if (isNaN(n)) {
            return String(qty == null ? '' : qty);
        }
        if (Math.abs(n - Math.round(n)) < 0.001) {
            return String(Math.round(n));
        }
        return String(Math.round(n * 100) / 100);
    }

    function fillSuccessProgress(order) {
        var root = document.getElementById('vcSuccessProgress');
        if (!root) {
            return;
        }
        var status = String(order.status || 'placed').toLowerCase();
        var tracking = order.tracking || [];
        var byStatus = {};
        tracking.forEach(function (row) {
            if (row && row.status) {
                byStatus[String(row.status).toLowerCase()] = row;
            }
        });
        var rank = {
            placed: 1,
            confirmed: 2,
            delivery_date_set: 2,
            out_for_delivery: 3,
            delivered: 4,
            cancelled: 0
        };
        var current = rank[status] || 1;
        var steps = [
            {
                key: 'placed',
                doneAt: current >= 1,
                current: current === 1 && status !== 'cancelled',
                sub: formatInDateTime(order.placed_at || (byStatus.placed && byStatus.placed.changed_at))
            },
            {
                key: 'preparing',
                doneAt: current > 2,
                current: current === 2,
                sub: current >= 2
                    ? (byStatus.confirmed || byStatus.delivery_date_set
                        ? formatInDateTime((byStatus.confirmed || byStatus.delivery_date_set).changed_at)
                        : 'Your items are being packed')
                    : 'Waiting to start'
            },
            {
                key: 'out_for_delivery',
                doneAt: current > 3,
                current: current === 3,
                sub: current >= 3
                    ? formatInDateTime(byStatus.out_for_delivery && byStatus.out_for_delivery.changed_at)
                    : 'Coming soon'
            },
            {
                key: 'delivered',
                doneAt: current >= 4,
                current: current === 4,
                sub: current >= 4
                    ? formatInDateTime(order.delivered_at || (byStatus.delivered && byStatus.delivered.changed_at))
                    : (order.estimated_delivery_date
                        ? 'Estimated ' + formatInDate(order.estimated_delivery_date)
                        : 'Pending')
            }
        ];

        steps.forEach(function (step, idx) {
            var el = root.querySelector('[data-step="' + step.key + '"]');
            if (!el) {
                return;
            }
            el.classList.remove('active', 'current');
            if (step.doneAt) {
                el.classList.add('active');
            } else if (step.current) {
                el.classList.add('current');
            }
            var sub = el.querySelector('[data-sub]');
            if (sub) {
                sub.textContent = step.sub || '—';
            }
            var line = root.querySelector('[data-line="' + (idx + 1) + '"]');
            if (line) {
                line.classList.toggle('active', current > idx + 1);
            }
        });
    }

    function fillSuccessPage(order, cust, line) {
        var payLabel = order.payment_method || 'Cash on Delivery';
        if (/^cod$/i.test(String(payLabel).trim())) {
            payLabel = 'Cash on Delivery';
        }

        setText('vcSuccessOrderNo', order.order_number ? '#' + order.order_number : '—');
        setText('vcSuccessOrderDate', formatInDate(order.placed_at));
        setText('vcSuccessEta', formatInDate(order.estimated_delivery_date));
        setText('vcSuccessPay', payLabel === 'Cash on Delivery' ? 'COD' : payLabel);
        setText('vcSuccessTotal', money(order.total));
        setText('vcSuccessName', displayName(cust) || (order.address && order.address.label) || '—');
        setText('vcSuccessAddr', line || '—');

        var phoneEl = document.getElementById('vcSuccessPhone');
        if (phoneEl) {
            phoneEl.innerHTML = '<i class="fa-solid fa-phone"></i> ' + escapeHtml(cust.mobile || '—');
        }
        var emailEl = document.getElementById('vcSuccessEmail');
        if (emailEl) {
            emailEl.innerHTML = '<i class="fa-solid fa-envelope"></i> ' + escapeHtml(cust.email || '—');
        }

        setText('vcSuccessPayDetail', payLabel);
        setText('vcSuccessPayAmount', money(order.total));
        var payStatus = document.getElementById('vcSuccessPayStatus');
        if (payStatus) {
            var delivered = String(order.status || '').toLowerCase() === 'delivered';
            payStatus.textContent = delivered ? 'Collected' : 'Pending';
            payStatus.className = delivered ? 'vc-payment-paid' : 'vc-payment-pending';
        }

        var statusWrap = document.getElementById('vcSuccessStatusWrap');
        var statusEl = document.getElementById('vcSuccessStatus');
        if (statusWrap && statusEl) {
            statusEl.textContent = order.status_label || order.status || '—';
            statusWrap.hidden = false;
        }

        var itemsHost = document.getElementById('vcSuccessItems');
        if (itemsHost) {
            var rows = order.items || [];
            itemsHost.innerHTML = rows.length ? rows.map(function (it) {
                var name = titleCaseName(it.name);
                var qty = qtyLabel(it.quantity);
                var unitBit = it.unit ? ' ' + escapeHtml(it.unit) : '';
                return '<div class="vc-success-product">' +
                    '<div class="vc-success-product-image">' +
                    '<span class="vc-success-product-fallback"><i class="fa-solid fa-leaf"></i></span>' +
                    '</div>' +
                    '<div class="vc-success-product-content">' +
                    '<span>Fresh Produce</span>' +
                    '<h3>' + escapeHtml(name) + '</h3>' +
                    '<p>' + escapeHtml(qty) + unitBit + ' × ' + money(it.unit_price) + '</p>' +
                    '</div>' +
                    '<strong class="vc-success-product-price">' + money(it.line_total) + '</strong>' +
                    '</div>';
            }).join('') : '<div class="vc-success-loading">No items in this order.</div>';
        }

        var itemCount = document.getElementById('vcSuccessItemCount');
        if (itemCount) {
            var n = (order.items || []).length;
            itemCount.textContent = n + (n === 1 ? ' Item' : ' Items');
        }

        setText('vcSuccessSubtotal', money(order.subtotal));
        setText('vcSuccessFee', money(order.delivery_fee));
        setText('vcSuccessGrandTotal', money(order.total));
        var disc = Number(order.discount_amount || 0);
        var discRow = document.getElementById('vcSuccessDiscountRow');
        if (discRow) {
            if (disc > 0) {
                discRow.hidden = false;
                setText('vcSuccessDiscount', '- ' + money(disc));
            } else {
                discRow.hidden = true;
            }
        }

        fillSuccessProgress(order);

        var viewBtn = document.getElementById('vcSuccessViewDetails');
        if (viewBtn && order.id) {
            viewBtn.href = 'order-details.php?id=' + encodeURIComponent(order.id);
        }

        var cancelBtn = document.getElementById('vcSuccessCancel');
        if (cancelBtn) {
            if (order.can_cancel) {
                cancelBtn.hidden = false;
                cancelBtn.onclick = function () {
                    if (!window.confirm('Cancel this order?')) {
                        return;
                    }
                    VC.cancelOrder(order.id).then(function (res) {
                        if (res && res.success) {
                            toast('Order cancelled.', 'success');
                            window.location.reload();
                        } else {
                            toast((res && res.error && res.error.message) || 'Could not cancel order.', 'error');
                        }
                    });
                };
            } else {
                cancelBtn.hidden = true;
            }
        }
    }

    function orderStatusUi(status) {
        var s = String(status || 'placed').toLowerCase();
        var map = {
            placed: { cls: 'placed', icon: 'fa-clock' },
            confirmed: { cls: 'confirmed', icon: 'fa-circle-check' },
            delivery_date_set: { cls: 'scheduled', icon: 'fa-calendar-check' },
            out_for_delivery: { cls: 'out', icon: 'fa-truck-fast' },
            delivered: { cls: 'delivered', icon: 'fa-circle-check' },
            cancelled: { cls: 'cancelled', icon: 'fa-ban' }
        };
        return map[s] || map.placed;
    }

    function splitDateTime(value) {
        if (!value) {
            return { date: '—', time: '' };
        }
        var d = new Date(String(value).replace(' ', 'T'));
        if (isNaN(d.getTime())) {
            return { date: String(value), time: '' };
        }
        return {
            date: d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }),
            time: d.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' })
        };
    }

    function fillOrderDetailsTracking(order) {
        var root = document.getElementById('vgTrackingSteps');
        if (!root) {
            return;
        }
        var status = String(order.status || 'placed').toLowerCase();
        var rank = {
            placed: 1,
            confirmed: 2,
            delivery_date_set: 3,
            out_for_delivery: 4,
            delivered: 5,
            cancelled: 0
        };
        var current = rank[status] || 1;
        var byStatus = {};
        (order.tracking || []).forEach(function (row) {
            if (row && row.status) {
                byStatus[String(row.status).toLowerCase()] = row;
            }
        });

        var steps = ['placed', 'confirmed', 'delivery_date_set', 'out_for_delivery', 'delivered'];
        steps.forEach(function (key, idx) {
            var el = root.querySelector('[data-step="' + key + '"]');
            if (!el) {
                return;
            }
            var stepRank = idx + 1;
            el.classList.toggle('completed', current >= stepRank && status !== 'cancelled');
            el.classList.toggle('current', current === stepRank && status !== 'cancelled');
            var stamp = byStatus[key] && byStatus[key].changed_at;
            if (key === 'placed' && !stamp) {
                stamp = order.placed_at;
            }
            if (key === 'delivered' && !stamp) {
                stamp = order.delivered_at;
            }
            var parts = splitDateTime(stamp);
            var dateEl = el.querySelector('[data-date]');
            var timeEl = el.querySelector('[data-time]');
            if (dateEl) {
                dateEl.textContent = stamp ? parts.date : (current >= stepRank ? 'Done' : 'Pending');
            }
            if (timeEl) {
                timeEl.textContent = stamp ? parts.time : '';
            }
            var line = root.querySelector('[data-line="' + stepRank + '"]');
            if (line) {
                line.classList.toggle('completed', current > stepRank && status !== 'cancelled');
            }
        });

        var copy = document.getElementById('vgTrackCopy');
        if (copy) {
            if (status === 'cancelled') {
                copy.textContent = 'This order was cancelled.';
            } else if (status === 'delivered') {
                copy.textContent = 'Your order has been successfully delivered.';
            } else if (status === 'out_for_delivery') {
                copy.textContent = 'Your order is out for delivery and will reach you today.';
            } else {
                copy.textContent = 'We will keep this timeline updated as your order progresses.';
            }
        }
        setText('vgTrackingId', order.order_number ? ('Order: ' + order.order_number) : '—');
    }

    function fillOrderDetailsPage(order, cust, line) {
        var payLabel = order.payment_method || 'Cash on Delivery';
        if (/^cod$/i.test(String(payLabel).trim())) {
            payLabel = 'Cash on Delivery';
        }
        var status = String(order.status || 'placed').toLowerCase();
        var ui = orderStatusUi(status);

        setText('vgOrderTitle', 'Order ' + (order.order_number || ''));
        setText('vgOrderNumber', order.order_number ? '#' + order.order_number : '—');
        setText('vgOrderStatusText', order.status_label || status);
        var statusEl = document.getElementById('vgOrderStatus');
        if (statusEl) {
            statusEl.className = 'vg-order-status ' + ui.cls;
            statusEl.innerHTML = '<i class="fa-solid ' + ui.icon + '"></i><span id="vgOrderStatusText">' +
                escapeHtml(order.status_label || status) + '</span>';
        }

        setText('vgOrderDate', formatInDate(order.placed_at));
        var etaLabel = document.getElementById('vgOrderEtaLabel');
        if (etaLabel) {
            etaLabel.textContent = status === 'delivered' ? 'Delivered On' : 'Expected Delivery';
        }
        setText(
            'vgOrderEta',
            status === 'delivered'
                ? formatInDate(order.delivered_at)
                : formatInDate(order.estimated_delivery_date)
        );
        setText('vgOrderTotalMeta', money(order.total));
        setText('vgOrderPayMeta', payLabel === 'Cash on Delivery' ? 'COD' : payLabel);

        fillOrderDetailsTracking(order);

        var items = order.items || [];
        var itemsHost = document.getElementById('vgOrderItems');
        if (itemsHost) {
            itemsHost.innerHTML = items.length ? items.map(function (it) {
                var name = titleCaseName(it.name);
                var qty = qtyLabel(it.quantity);
                var unit = it.unit ? String(it.unit) : '';
                var buyHref = it.product_id ? ('product.php?id=' + encodeURIComponent(it.product_id)) : 'products.php';
                return '<div class="vg-order-product">' +
                    '<div class="vg-product-image"><span class="vg-product-fallback"><i class="fa-solid fa-leaf"></i></span></div>' +
                    '<div class="vg-product-info">' +
                    '<span class="vg-product-category">Fresh Produce</span>' +
                    '<h3>' + escapeHtml(name) + '</h3>' +
                    '<div class="vg-product-meta">' +
                    (unit ? '<span>' + escapeHtml(unit) + '</span>' : '') +
                    '<span>Qty: ' + escapeHtml(qty) + '</span>' +
                    '</div></div>' +
                    '<div class="vg-product-price">' +
                    '<small>' + money(it.unit_price) + ' × ' + escapeHtml(qty) + '</small>' +
                    '<strong>' + money(it.line_total) + '</strong>' +
                    '<a href="' + buyHref + '">Buy Again</a>' +
                    '</div></div>';
            }).join('') : '<div class="vg-order-loading">No items in this order.</div>';
        }
        var n = items.length;
        setText('vgItemsCopy', n + (n === 1 ? ' item in this order.' : ' items in this order.'));
        setText('vgSummaryItemCount', n + (n === 1 ? ' Item' : ' Items'));

        setText('vcOrderAddrName', displayName(cust) || (order.address && order.address.label) || '—');
        setText('vcOrderAddrText', line || '—');

        setText('vgPayMethod', payLabel);
        setText('vgPayNote', payLabel === 'Cash on Delivery'
            ? 'Pay cash to the delivery partner at handover'
            : 'Payment recorded for this order');
        var paid = status === 'delivered';
        setText('vgPayStatusText', paid ? 'Collected' : 'Pending');
        var payStatus = document.getElementById('vgPayStatus');
        if (payStatus) {
            payStatus.classList.toggle('paid', paid);
            payStatus.innerHTML = '<i class="fa-solid ' + (paid ? 'fa-circle-check' : 'fa-clock') +
                '"></i><span id="vgPayStatusText">' + (paid ? 'Collected' : 'Pending') + '</span>';
        }

        setText('vgSumSubtotal', money(order.subtotal));
        setText('vgSumFee', money(order.delivery_fee));
        setText('vgSumTotal', money(order.total));
        var disc = Number(order.discount_amount || 0);
        var discRow = document.getElementById('vgSumDiscountRow');
        var saveEl = document.getElementById('vgSumSaving');
        if (discRow) {
            if (disc > 0) {
                discRow.hidden = false;
                setText('vgSumDiscount', '- ' + money(disc));
                if (saveEl) {
                    saveEl.hidden = false;
                    setText('vgSumSavingText', 'You saved ' + money(disc) + ' on this order');
                }
            } else {
                discRow.hidden = true;
                if (saveEl) saveEl.hidden = true;
            }
        }

        var trackLink = document.getElementById('vgTrackLink');
        if (trackLink && order.id) {
            trackLink.href = 'order-details-tracking.php?id=' + encodeURIComponent(order.id);
        }

        var reorderBtn = document.getElementById('vgReorderBtn');
        if (reorderBtn) {
            reorderBtn.onclick = function () {
                VC.reorder(order.id).then(function (res) {
                    if (res && res.success) {
                        toast('Items added to cart.', 'success');
                        window.location.href = 'cart.php';
                    } else {
                        toast((res && res.error && res.error.message) || 'Could not reorder.', 'error');
                    }
                });
            };
        }

        var cancelCard = document.getElementById('vgCancelCard');
        var cancelBtn = document.getElementById('vgCancelBtn');
        if (cancelCard && cancelBtn) {
            if (order.can_cancel) {
                cancelCard.hidden = false;
                cancelBtn.onclick = function () {
                    if (!window.confirm('Cancel this order?')) {
                        return;
                    }
                    VC.cancelOrder(order.id).then(function (res) {
                        if (res && res.success) {
                            toast('Order cancelled.', 'success');
                            window.location.reload();
                        } else {
                            toast((res && res.error && res.error.message) || 'Could not cancel.', 'error');
                        }
                    });
                };
            } else {
                cancelCard.hidden = true;
            }
        }

        var invoiceBtn = document.getElementById('vgInvoiceBtn');
        var invoiceViewBtn = document.getElementById('vgInvoiceViewBtn');

        function openInvoiceBlob(pack, mode) {
            if (!pack || !pack.success || !pack.blob) {
                toast('Unable to load invoice.', 'error');
                return;
            }
            var type = pack.contentType || pack.blob.type || '';
            if (type.indexOf('application/json') !== -1) {
                toast('Unable to load invoice.', 'error');
                return;
            }
            var filename = pack.filename || ('invoice-' + (order.order_number || order.id) + (mode === 'pdf' ? '.pdf' : '.html'));
            var url = URL.createObjectURL(pack.blob);
            if (mode === 'view') {
                var win = window.open(url, '_blank');
                if (!win) {
                    toast('Please allow pop-ups to view the invoice.', 'error');
                }
                setTimeout(function () { URL.revokeObjectURL(url); }, 60000);
                return;
            }
            var a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
            toast('Invoice PDF downloaded');
        }

        if (invoiceViewBtn) {
            invoiceViewBtn.onclick = function () {
                invoiceViewBtn.disabled = true;
                VC.invoiceFile(order.id, 'html').then(function (pack) {
                    invoiceViewBtn.disabled = false;
                    openInvoiceBlob(pack, 'view');
                }).catch(function () {
                    invoiceViewBtn.disabled = false;
                    toast('Unable to load invoice.', 'error');
                });
            };
        }

        if (invoiceBtn) {
            invoiceBtn.onclick = function () {
                invoiceBtn.disabled = true;
                VC.invoiceFile(order.id, 'pdf').then(function (pack) {
                    invoiceBtn.disabled = false;
                    openInvoiceBlob(pack, 'pdf');
                }).catch(function () {
                    invoiceBtn.disabled = false;
                    toast('Unable to download invoice PDF.', 'error');
                });
            };
        }
    }

    function bootOrderDetails() {
        if (!requireAuth()) {
            return;
        }
        var id = qs('id');
        if (!id) {
            return;
        }
        var page = pageName();
        var isSuccess = page === 'order-success';
        var isDetails = page === 'order-details';
        var isTracking = page === 'order-details-tracking';

        VC.order(id).then(function (res) {
            if (!res || !res.success) {
                toast((res && res.error && res.error.message) || 'Order not found.', 'error');
                return;
            }
            var o = res.data.order;
            var cust = VC.getCustomer() || {};
            var addr = o.address || {};
            var line = addrLine(addr);
            var batchParam = qs('batch');
            var idsParam = qs('ids');

            if (isSuccess) {
                var h1 = document.querySelector('.vc-success-card h1');
                if (batchParam || o.batch_id || o.is_multi_location) {
                    var successLabel = document.querySelector('.vc-success-label');
                    if (successLabel) successLabel.textContent = 'Multi-location order confirmed';
                    if (h1) h1.textContent = 'Orders placed across your locations';
                    var successCopy = document.querySelector('.vc-success-card > p');
                    var idList = (idsParam || '').split(',').filter(Boolean);
                    var count = idList.length || 2;
                    if (successCopy) {
                        successCopy.textContent = count + ' orders were placed across ' + count +
                            ' locations in one checkout. Each location is fulfilled separately.';
                    }
                    var orderNoWrap = document.querySelector('.vc-success-order-number');
                    if (orderNoWrap && idList.length) {
                        Promise.all(idList.map(function (oid) { return VC.order(oid); })).then(function (packs) {
                            var nums = packs.filter(function (p) { return p && p.success; }).map(function (p) {
                                return p.data.order.order_number;
                            });
                            if (nums.length) {
                                orderNoWrap.innerHTML = '<span>' + nums.length + ' order numbers</span><strong>' +
                                    escapeHtml(nums.map(function (n) { return '#' + n; }).join(', ')) + '</strong>';
                            }
                        });
                    }
                }
                fillSuccessPage(o, cust, line);
                return;
            }

            if (isDetails) {
                fillOrderDetailsPage(o, cust, line);
                return;
            }

            if (isTracking) {
                fillTrackingPage(o, cust, line);
                return;
            }

            // Fallback for any residual legacy markup
            var h1Legacy = document.querySelector('h1');
            if (h1Legacy) {
                h1Legacy.textContent = 'Order ' + (o.order_number || '');
            }
            setText('vcOrderAddrName', displayName(cust));
            setText('vcOrderAddrText', line || '—');
            setText('vcTrackAddrName', displayName(cust));
            setText('vcTrackAddrLabel', addr.label || 'Address');
            setText('vcTrackAddrText', line || '—');
        });
    }

    function fillTrackingPage(order, cust, line) {
        var payLabel = order.payment_method || 'Cash on Delivery';
        if (/^cod$/i.test(String(payLabel).trim())) {
            payLabel = 'Cash on Delivery';
        }
        var status = String(order.status || 'placed').toLowerCase();
        var ui = orderStatusUi(status);

        setText('vcTrackOrderNo', order.order_number ? '#' + order.order_number : '—');
        setText('vcTrackPlaced', 'Placed on ' + formatInDateTime(order.placed_at));
        setText('vcTrackStatusText', order.status_label || status);
        var badge = document.getElementById('vcTrackStatusBadge');
        if (badge) {
            badge.innerHTML = '<i class="fa-solid ' + ui.icon + '"></i><span id="vcTrackStatusText">' +
                escapeHtml(order.status_label || status) + '</span>';
        }

        var etaLabel = document.getElementById('vcTrackEtaLabel');
        if (etaLabel) {
            etaLabel.textContent = status === 'delivered' ? 'Delivered On' : 'Expected Delivery';
        }
        setText(
            'vcTrackEta',
            status === 'delivered'
                ? formatInDate(order.delivered_at)
                : formatInDate(order.estimated_delivery_date)
        );

        var detailsLink = document.getElementById('vcTrackDetailsLink');
        if (detailsLink && order.id) {
            detailsLink.href = 'order-details.php?id=' + encodeURIComponent(order.id);
        }

        setText('vcTrackAddrName', displayName(cust) || (order.address && order.address.label) || '—');
        setText('vcTrackAddrLabel', (order.address && order.address.label) || 'Address');
        setText('vcTrackAddrText', line || '—');

        var items = order.items || [];
        setText('vcTrackItemCount', items.length + (items.length === 1 ? ' Item' : ' Items'));
        var productsHost = document.getElementById('vcTrackItems');
        if (productsHost) {
            productsHost.innerHTML = items.length ? items.map(function (it) {
                return '<div class="vc-order-product">' +
                    '<div class="vc-product-image"><span class="vg-product-fallback"><i class="fa-solid fa-leaf"></i></span></div>' +
                    '<div class="vc-product-info">' +
                    '<span class="vc-product-category">Fresh Produce</span>' +
                    '<h3>' + escapeHtml(titleCaseName(it.name)) + '</h3>' +
                    '<div class="vc-product-meta"><span>Qty: ' + escapeHtml(qtyLabel(it.quantity)) +
                    (it.unit ? ' ' + escapeHtml(it.unit) : '') + '</span></div></div>' +
                    '<div class="vc-product-price"><strong>' + money(it.line_total) + '</strong></div></div>';
            }).join('') : '<p>No items in this order.</p>';
        }

        setText('vcTrackSubtotal', money(order.subtotal));
        setText('vcTrackFee', Number(order.delivery_fee || 0) === 0 ? 'FREE' : money(order.delivery_fee));
        setText('vcTrackTotal', money(order.total));
        var disc = Number(order.discount_amount || 0);
        var discRow = document.getElementById('vcTrackDiscountRow');
        if (discRow) {
            if (disc > 0) {
                discRow.hidden = false;
                setText('vcTrackDiscount', '− ' + money(disc));
            } else {
                discRow.hidden = true;
            }
        }

        setText('vcTrackPayMethod', payLabel);
        var paid = status === 'delivered';
        var payStatus = document.getElementById('vcTrackPayStatus');
        if (payStatus) {
            payStatus.innerHTML = '<i class="fa-solid ' + (paid ? 'fa-circle-check' : 'fa-clock') +
                '"></i><span id="vcTrackPayStatusText">' +
                (paid ? 'Collected' : 'Pending') + '</span>';
        }

        var root = document.getElementById('vcTrackProgress');
        if (root) {
            var rank = {
                placed: 1,
                confirmed: 2,
                delivery_date_set: 2,
                out_for_delivery: 3,
                delivered: 4,
                cancelled: 0
            };
            var current = rank[status] || 1;
            var byStatus = {};
            (order.tracking || []).forEach(function (row) {
                if (row && row.status) {
                    byStatus[String(row.status).toLowerCase()] = row;
                }
            });
            [
                { key: 'placed', sub: formatInDateTime(order.placed_at || (byStatus.placed && byStatus.placed.changed_at)) },
                {
                    key: 'preparing',
                    sub: current >= 2
                        ? formatInDateTime((byStatus.confirmed || byStatus.delivery_date_set || {}).changed_at) || 'In progress'
                        : 'Waiting'
                },
                {
                    key: 'out_for_delivery',
                    sub: current >= 3
                        ? formatInDateTime(byStatus.out_for_delivery && byStatus.out_for_delivery.changed_at) || 'On the way'
                        : 'Coming soon'
                },
                {
                    key: 'delivered',
                    sub: current >= 4
                        ? formatInDateTime(order.delivered_at || (byStatus.delivered && byStatus.delivered.changed_at))
                        : (order.estimated_delivery_date ? ('Est. ' + formatInDate(order.estimated_delivery_date)) : 'Pending')
                }
            ].forEach(function (step, idx) {
                var el = root.querySelector('[data-step="' + step.key + '"]');
                if (!el) {
                    return;
                }
                var stepNum = idx + 1;
                el.classList.remove('completed', 'active');
                if (current > stepNum) {
                    el.classList.add('completed');
                } else if (current === stepNum) {
                    el.classList.add('active');
                }
                var sub = el.querySelector('[data-sub]');
                if (sub) {
                    sub.textContent = step.sub || '—';
                }
            });
        }

        var reorderBtn = document.getElementById('vcTrackReorderBtn');
        if (reorderBtn) {
            reorderBtn.onclick = function () {
                VC.reorder(order.id).then(function (res) {
                    if (res && res.success) {
                        toast('Items added to cart.', 'success');
                        window.location.href = 'cart.php';
                    } else {
                        toast((res && res.error && res.error.message) || 'Could not reorder.', 'error');
                    }
                });
            };
        }
    }

    function fillBusinessProfile(c, verify, addresses) {
        var kyc = (verify && verify.kyc_status) || c.kyc_status || 'pending';
        var ui = kycUi(kyc);
        var bizId = formatBizId(c);
        var loc = '';
        var def = (addresses || []).find(function (a) { return a.is_default; }) || (addresses || [])[0];
        if (def) {
            loc = [def.city, def.state].filter(Boolean).join(', ') || addrLine(def);
        }
        setText('vcBpType', typeLabel(c.business_type));
        setText('vcBpName', c.business_name);
        setText('vcBpId', bizId);
        setText('vcBpOwner', c.owner_name);
        setText('vcBpMobile', c.mobile);
        setText('vcBpEmail', c.email);
        setText('vcBpLocation', loc);
        setText('vcBpDetailName', c.business_name);
        setText('vcBpDetailType', typeLabel(c.business_type));
        setText('vcBpDetailOwner', c.owner_name);
        setText('vcBpDetailMobile', c.mobile);
        setText('vcBpDetailEmail', c.email);
        setText('vcBpGst', c.gst_number);
        setText('vcBpFssai', c.fssai_number);
        setText('vcBpPan', c.pan_number);
        setText('vcBpAddrName', c.business_name || c.owner_name);
        setText('vcBpAddrText', def ? addrLine(def) : 'No address saved yet.');
        setText('vcBpVerifyTitle', ui.side);
        setText('vcBpVerifySub', ui.sub);
        setText('vcBpAppId', bizId);
        setText('vcBpSubmitted', formatInDate(c.created_at));
        setText('vcBpKycRaw', kyc);
        var badge = document.getElementById('vcBpStatusBadge');
        var statusText = document.getElementById('vcBpStatusText');
        if (badge) {
            badge.className = 'vc-bp-status ' + ui.cls;
            badge.innerHTML = '<i class="fa-solid ' + ui.icon + '"></i> <span id="vcBpStatusText">' + escapeHtml(ui.title) + '</span>';
        } else if (statusText) {
            statusText.textContent = ui.title;
        }
        var box = document.getElementById('vcBpVerifyBox');
        if (box) {
            box.className = 'vc-bp-verification-badge ' + ui.cls;
            var icon = box.querySelector('i');
            if (icon) icon.className = 'fa-solid ' + ui.icon;
        }
        if (ui.cls === 'approved') {
            setText('vcBpBenefitTitle', 'Business account active');
            setText('vcBpBenefitCopy', 'Your verified business account gives you access to bulk ordering and eligible business pricing.');
        } else if (ui.cls === 'rejected') {
            setText('vcBpBenefitTitle', 'Verification rejected');
            setText('vcBpBenefitCopy', verify && verify.kyc_rejection_reason ? verify.kyc_rejection_reason : 'Please update your documents and resubmit.');
        } else {
            setText('vcBpBenefitTitle', 'Verification pending');
            setText('vcBpBenefitCopy', 'Complete verification to unlock bulk ordering and eligible business pricing.');
        }
        var docs = (verify && verify.documents) || [];
        var catalog = (verify && verify.catalog) || [];
        var byType = {};
        docs.forEach(function (d) { byType[d.document_type || d.type] = d; });
        var expected = catalog.length ? catalog : Object.keys(byType).map(function (k) { return { key: k, label: k }; });
        var uploaded = expected.filter(function (row) { return !!byType[row.key]; }).length;
        var missing = expected.length - uploaded;
        setText('vcBpDocVerified', kyc === 'approved' ? uploaded : 0);
        setText('vcBpDocReview', kyc === 'pending' ? uploaded : 0);
        setText('vcBpDocExpired', missing);
        setText('vcBpDocRejected', kyc === 'rejected' ? uploaded : 0);
        var host = document.getElementById('vcBpDocuments');
        if (host) {
            host.innerHTML = expected.length ? expected.map(function (row) {
                var doc = byType[row.key];
                var st = !doc ? 'missing' : (kyc === 'approved' ? 'verified' : (kyc === 'rejected' ? 'rejected' : 'reviewing'));
                var stText = !doc ? 'Not uploaded' : (kyc === 'approved' ? 'Verified' : (kyc === 'rejected' ? 'Needs review' : 'Uploaded'));
                return '<div class="vc-bp-document"><div class="vc-bp-doc-info"><div><strong>' +
                    escapeHtml(row.label || row.key) + '</strong><small>' +
                    escapeHtml(doc ? formatInDate(doc.uploaded_at) : 'Not uploaded yet') +
                    '</small></div></div><span class="vc-bp-doc-status ' + st + '">' + stText + '</span></div>';
            }).join('') : emptyNote('No documents in catalog yet.');
        }
    }

    function bootProfile() {
        if (!requireAuth()) {
            return;
        }
        Promise.all([
            VC.profile(),
            VC.addresses(),
            VC.orders({ per_page: 20 }),
            VC.wishlist(),
            pageName() === 'bussiness-profile' ? VC.verificationStatus() : Promise.resolve(null)
        ]).then(function (pack) {
            var res = pack[0];
            if (!res || !res.success) {
                return;
            }
            var c = res.data;
            VC.setSession({ customer: c });
            applyHeaderCustomer(c);
            paintProfileAvatar(c);
            bindProfileAvatarUpload();
            var nameEl = document.querySelector('.vg-user-card h3, .vc-account-user h3');
            var emailEl = document.querySelector('.vg-user-card p, .vc-account-user p');
            if (nameEl) nameEl.textContent = displayName(c);
            if (emailEl) emailEl.textContent = c.email || c.mobile || '';
            var hello = document.getElementById('vcDashHello');
            if (hello) hello.textContent = 'Hello ' + displayName(c) + ' 👋';
            var inputs = {
                owner_name: c.owner_name,
                email: c.email,
                business_name: c.business_name,
                gst_number: c.gst_number,
                fssai_number: c.fssai_number,
                pan_number: c.pan_number,
                mobile: c.mobile
            };
            Object.keys(inputs).forEach(function (k) {
                var el = document.querySelector('[name="' + k + '"]');
                if (el) el.value = inputs[k] || '';
            });
            var kyc = kycUi(c.kyc_status);
            var kycBadge = document.getElementById('vcProfileKycBadge');
            if (kycBadge) {
                kycBadge.innerHTML = '<i class="fa-solid ' + kyc.icon + '"></i> ' + escapeHtml(kyc.title);
            }
            var addresses = (pack[1] && pack[1].success && pack[1].data.addresses) || [];
            var def = addresses.find(function (a) { return a.is_default; }) || addresses[0];
            var addrCard = document.getElementById('vcProfileAddressCard');
            if (addrCard) {
                addrCard.innerHTML = def
                    ? '<h3>' + escapeHtml(displayName(c)) + '</h3><p>' + escapeHtml(addrLine(def)) + '</p>'
                    : emptyNote('No saved address yet.', '<a href="manage-address.php">Add an address</a>');
            }
            var dashAddr = document.getElementById('vcDashAddress');
            if (dashAddr) {
                dashAddr.innerHTML = def
                    ? '<strong>' + escapeHtml(displayName(c)) + '</strong><p>' + escapeHtml(addrLine(def)) +
                        '</p><p><i class="fa-solid fa-phone"></i> ' + escapeHtml(c.mobile || '') + '</p>'
                    : '<strong>—</strong><p>No default address saved.</p>';
            }
            setText('vcDashInfoName', displayName(c));
            setText('vcDashInfoEmail', c.email);
            setText('vcDashInfoPhone', c.mobile);
            var orders = (pack[2] && pack[2].success && pack[2].data.orders) || [];
            var wish = (pack[3] && pack[3].success && pack[3].data.items) || [];
            var active = orders.filter(function (o) {
                return ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'out_for_delivery'].indexOf(String(o.status).toLowerCase()) !== -1;
            }).length;
            setText('vcDashOrderCount', orders.length);
            setText('vcDashActiveCount', active);
            setText('vcDashWishCount', wish.length);
            setText('vcDashAddrCount', addresses.length);
            setText('vcMenuOrderCount', orders.length);
            setText('vcMenuWishCount', wish.length);
            var dashOrders = document.getElementById('vcDashOrders');
            if (dashOrders) {
                dashOrders.innerHTML = orders.length ? orders.slice(0, 5).map(function (o) {
                    return '<div class="vc-recent-order"><div class="vc-recent-order-info"><span class="vc-order-number">' +
                        escapeHtml(o.order_number || '') + '</span><h3>' + escapeHtml(o.status_label || o.status) +
                        '</h3><p>' + escapeHtml(formatInDate(o.placed_at)) + '</p></div>' +
                        '<div class="vc-recent-order-price"><span>Total</span><strong>' + money(o.total) +
                        '</strong></div><span class="vc-dashboard-status ' + escapeHtml(String(o.status || '')) + '">' +
                        escapeHtml(o.status_label || o.status) + '</span>' +
                        '<a href="order-details.php?id=' + o.id + '" class="vc-view-order">View Order</a></div>';
                }).join('') : emptyNote('No orders yet.', '<a href="product.php">Shop products</a>');
            }
            if (pageName() === 'bussiness-profile') {
                var vres = pack[4];
                fillBusinessProfile(c, (vres && vres.success && vres.data) || { kyc_status: c.kyc_status, documents: [], catalog: [] }, addresses);
            }
            var form = document.querySelector('.vg-profile-form');
            if (form && pageName() === 'my-profile' && !form.getAttribute('data-vc-bound')) {
                form.setAttribute('data-vc-bound', '1');
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var body = {};
                    ['email', 'owner_name', 'business_name', 'gst_number', 'fssai_number', 'pan_number'].forEach(function (k) {
                        var el = form.querySelector('[name="' + k + '"]');
                        if (el) body[k] = el.value.trim();
                    });
                    VC.updateProfile(body).then(function (r) {
                        if (r && r.success) {
                            VC.setSession({ customer: r.data });
                            applyHeaderCustomer(r.data);
                            toast('Profile updated');
                        } else {
                            toast((r && r.error && r.error.message) || 'Could not update profile.', 'error');
                        }
                    });
                });
            }
        });
        var logoutLinks = document.querySelectorAll('a[href="logout.php"], a[href*="logout"]');
        logoutLinks.forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                VC.logout().then(function () {
                    window.location.href = 'index.php';
                });
            });
        });
    }

    function bootAddresses() {
        if (!requireAuth()) {
            return;
        }
        var grid = document.querySelector('.vg-saved-address-grid');
        function render() {
            VC.addresses().then(function (res) {
                var list = (res && res.success && res.data.addresses) || [];
                var countEl = document.getElementById('vgSavedAddressCount');
                var defaultEl = document.getElementById('vgDefaultAddressLabel');
                if (countEl) {
                    countEl.textContent = String(list.length);
                }
                if (defaultEl) {
                    var def = list.find(function (a) { return a.is_default; }) || list[0];
                    defaultEl.textContent = def ? (def.label || 'Address') : 'None';
                }
                refreshHeaderLocation();
                if (!grid) {
                    return;
                }
                if (!list.length) {
                    grid.innerHTML = emptyNote('No saved addresses yet.', '<span>Add your first delivery address using the form.</span>');
                    return;
                }
                grid.innerHTML = list.map(function (a) {
                    return (
                        '<article class="vg-address-card' + (a.is_default ? ' vg-default-address' : '') + '">' +
                            '<div class="vg-address-card-top"><div class="vg-address-card-tags">' +
                                '<span class="vg-address-type">' + escapeHtml(a.label || 'Address') + '</span>' +
                                (a.is_default ? '<span class="vg-default-label">Default</span>' : '') +
                            '</div>' +
                            '<div class="vg-address-menu">' +
                                (!a.is_default ? '<button type="button" class="vg-address-action" data-default-addr="' + a.id + '">Default</button>' : '') +
                                '<button type="button" class="vg-address-action delete" data-del-addr="' + a.id + '"><i class="fa-solid fa-trash"></i></button>' +
                            '</div></div>' +
                            '<div class="vg-address-details"><p>' +
                                escapeHtml([a.line1, a.line2, a.landmark, a.city, a.state, a.pincode].filter(Boolean).join(', ')) +
                            '</p></div></article>'
                    );
                }).join('');
            });
        }
        render();
        document.addEventListener('click', function (e) {
            var d = e.target.closest('[data-del-addr]');
            if (d) {
                VC.deleteAddress(d.getAttribute('data-del-addr')).then(function () { render(); toast('Address removed'); });
            }
            var def = e.target.closest('[data-default-addr]');
            if (def) {
                VC.defaultAddress(def.getAttribute('data-default-addr')).then(function () { render(); toast('Default address updated'); });
            }
        });
        var form = document.querySelector('#vgNewAddressForm form, .vg-address-form');
        if (form) {
            var pinHint = document.getElementById('vgPincodeHint');
            var pinHidden = document.getElementById('vgPincodeValue') || form.querySelector('[name="pincode"]');
            var pinSearch = document.getElementById('vgPincodeSearch');
            var pinDropdown = document.getElementById('vgPincodeDropdown');
            var pinWrap = document.getElementById('vgPincodeSelect');
            var cityInput = form.querySelector('[name="city"]');
            var stateInput = form.querySelector('[name="state"]');
            var allPins = [];
            var pinOk = false;

            function setPinHint(msg, ok) {
                pinOk = !!ok;
                if (!pinHint) return;
                pinHint.textContent = msg || '';
                pinHint.className = 'vg-pincode-hint' + (ok ? ' is-ok' : (msg ? ' is-bad' : ''));
            }

            function selectPin(pin, city, state) {
                if (pinHidden) pinHidden.value = pin;
                if (pinSearch) pinSearch.value = pin + (city ? ' · ' + city : '');
                if (cityInput) cityInput.value = city || 'Hyderabad';
                if (stateInput) stateInput.value = state || 'Telangana';
                setPinHint('✓ We deliver here' + (city ? ' (' + city + ')' : ''), true);
                closePinDropdown();
            }

            function clearPinSelection() {
                if (pinHidden) pinHidden.value = '';
                setPinHint('', false);
            }

            function openPinDropdown() {
                if (!pinDropdown) return;
                pinDropdown.hidden = false;
                if (pinSearch) pinSearch.setAttribute('aria-expanded', 'true');
                if (pinWrap) pinWrap.classList.add('is-open');
            }

            function closePinDropdown() {
                if (!pinDropdown) return;
                pinDropdown.hidden = true;
                if (pinSearch) pinSearch.setAttribute('aria-expanded', 'false');
                if (pinWrap) pinWrap.classList.remove('is-open');
            }

            function renderPinOptions(filter) {
                if (!pinDropdown) return;
                var q = String(filter || '').trim().toLowerCase();
                // If display value is "500001 · Hyderabad", search by digits only when typing new query
                var raw = q.replace(/[^\d]/g, '');
                var list = allPins.filter(function (row) {
                    if (!q) return true;
                    var pin = String(row.pincode || '');
                    var city = String(row.city || '').toLowerCase();
                    var state = String(row.state || '').toLowerCase();
                    if (raw && pin.indexOf(raw) !== -1) return true;
                    return pin.indexOf(q) !== -1 || city.indexOf(q) !== -1 || state.indexOf(q) !== -1;
                });
                if (!list.length) {
                    pinDropdown.innerHTML = '<div class="vg-pincode-empty">No matching Hyderabad pincode</div>';
                    openPinDropdown();
                    return;
                }
                pinDropdown.innerHTML = list.slice(0, 80).map(function (row) {
                    var selected = pinHidden && pinHidden.value === row.pincode ? ' is-selected' : '';
                    return '<button type="button" class="vg-pincode-option' + selected + '" data-pin="' +
                        escapeHtml(row.pincode) + '" data-city="' + escapeHtml(row.city || 'Hyderabad') +
                        '" data-state="' + escapeHtml(row.state || 'Telangana') + '" role="option">' +
                        '<strong>' + escapeHtml(row.pincode) + '</strong>' +
                        '<span>' + escapeHtml((row.city || 'Hyderabad') + ', ' + (row.state || 'Telangana')) + '</span></button>';
                }).join('');
                openPinDropdown();
            }

            if (pinWrap && pinSearch && pinDropdown) {
                VC.serviceablePincodes().then(function (res) {
                    allPins = (res && res.success && res.data.pincodes) || [];
                    if (!allPins.length) {
                        setPinHint('No serviceable pincodes available right now.', false);
                    }
                });

                pinSearch.addEventListener('focus', function () {
                    // Show full list when focusing with empty / selected display
                    var q = pinHidden && pinHidden.value && pinSearch.value.indexOf(pinHidden.value) === 0
                        ? ''
                        : pinSearch.value;
                    renderPinOptions(q);
                });

                pinSearch.addEventListener('input', function () {
                    clearPinSelection();
                    renderPinOptions(pinSearch.value);
                });

                pinDropdown.addEventListener('click', function (e) {
                    var btn = e.target.closest('.vg-pincode-option');
                    if (!btn) return;
                    selectPin(btn.getAttribute('data-pin'), btn.getAttribute('data-city'), btn.getAttribute('data-state'));
                });

                document.addEventListener('click', function (e) {
                    if (pinWrap && !pinWrap.contains(e.target)) {
                        closePinDropdown();
                        // Restore display if a pin was already chosen
                        if (pinHidden && pinHidden.value && pinSearch) {
                            var match = allPins.find(function (r) { return r.pincode === pinHidden.value; });
                            pinSearch.value = pinHidden.value + (match ? ' · ' + (match.city || 'Hyderabad') : '');
                        }
                    }
                });
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var type = (form.querySelector('input[name="address_type"]:checked') || {}).value || 'Home';
                var defaultBox = form.querySelector('input[name="is_default"], input[type="checkbox"]');
                var body = {
                    label: type,
                    line1: '',
                    line2: '',
                    pincode: pinHidden ? pinHidden.value.trim() : '',
                    landmark: '',
                    city: (cityInput && cityInput.value.trim()) || 'Hyderabad',
                    state: (stateInput && stateInput.value.trim()) || 'Telangana',
                    is_default: !defaultBox || defaultBox.checked
                };
                var named = {
                    line1: form.querySelector('[name="line1"]'),
                    line2: form.querySelector('[name="line2"]'),
                    landmark: form.querySelector('[name="landmark"]'),
                    label: form.querySelector('[name="label"]')
                };
                if (named.line1) {
                    body.line1 = named.line1.value.trim();
                    body.line2 = named.line2 ? named.line2.value.trim() : '';
                    body.landmark = named.landmark ? named.landmark.value.trim() : '';
                    body.label = named.label ? named.label.value.trim() : type;
                }
                if (!/^\d{6}$/.test(body.pincode)) {
                    toast('Please search and select a Hyderabad PIN code.', 'error');
                    setPinHint('Select a PIN code from the list.', false);
                    if (pinSearch) pinSearch.focus();
                    return;
                }
                function saveAddress() {
                    VC.createAddress(body).then(function (res) {
                        if (res && res.success) {
                            toast('Address saved');
                            form.reset();
                            if (cityInput) cityInput.value = 'Hyderabad';
                            if (stateInput) stateInput.value = 'Telangana';
                            if (pinHidden) pinHidden.value = '';
                            if (pinSearch) pinSearch.value = '';
                            setPinHint('', false);
                            pinOk = false;
                            render();
                        } else {
                            toast((res && res.error && res.error.message) || 'Could not save address.', 'error');
                        }
                    });
                }
                if (pinOk) {
                    saveAddress();
                    return;
                }
                VC.checkPincode(body.pincode).then(function (res) {
                    if (res && res.success && res.data && res.data.serviceable) {
                        pinOk = true;
                        setPinHint('✓ We deliver here', true);
                        if (cityInput && res.data.city) cityInput.value = res.data.city;
                        if (stateInput && res.data.state) stateInput.value = res.data.state;
                        saveAddress();
                    } else {
                        setPinHint('✗ Not serviceable in this area yet', false);
                        toast("We currently deliver only within Hyderabad — this pincode isn't serviceable yet", 'error');
                    }
                });
            });
        }
    }

    function bootNotifications() {
        if (!requireAuth()) {
            return;
        }
        VC.notifications().then(function (res) {
            var items = (res && res.success && (res.data.notifications || res.data.items)) || [];
            var unread = items.filter(function (n) { return !n.read_at && !n.is_read; }).length;
            setText('vcTotalNotifications', items.length);
            setText('vcUnreadNotifications', unread);
            setText('vcReadNotifications', items.length - unread);
            var host = document.getElementById('vcNotificationList') || document.querySelector('.vc-notification-list');
            if (!host) {
                return;
            }
            host.innerHTML = items.length ? items.map(function (n) {
                var read = n.read_at || n.is_read;
                return '<article class="vc-notification-item ' + (read ? 'read' : 'unread') + '">' +
                    '<div class="vc-notification-content"><strong>' + escapeHtml(n.title || n.message || 'Update') +
                    '</strong><p>' + escapeHtml(n.body || n.message || '') + '</p></div></article>';
            }).join('') : emptyNote('No notifications yet.');
        });
    }

    function bootBusiness() {
        if (!requireAuth()) {
            return;
        }
        var form = document.getElementById('vcBusinessRegistrationForm');
        if (!form) {
            return;
        }
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var type = (document.querySelector('input[name="business_type"]:checked') || {}).value || '';
            var shop = (document.getElementById('vcShopAddress') || {}).value || '';
            var same = document.getElementById('vcSameAddress');
            var delivery = (same && same.checked)
                ? shop
                : ((document.getElementById('vcDeliveryAddress') || {}).value || '');
            var body = {
                business_type: type,
                business_name: (document.getElementById('vcBusinessName') || {}).value || '',
                owner_name: (document.getElementById('vcOwnerName') || {}).value || '',
                email: (document.getElementById('vcEmail') || {}).value || '',
                gst_number: (document.getElementById('vcGST') || {}).value || '',
                fssai_number: (document.getElementById('vcFSSAI') || {}).value || '',
                pan_number: (document.getElementById('vcPAN') || {}).value || '',
                shop_address: String(shop).trim(),
                delivery_address: String(delivery).trim(),
                city: (document.getElementById('vcCity') || {}).value || '',
                state: (document.getElementById('vcState') || {}).value || '',
                pincode: (document.getElementById('vcPincode') || {}).value || '',
                landmark: (document.getElementById('vcLandmark') || {}).value || ''
            };
            VC.businessRegister(body).then(function (res) {
                if (!res || !res.success) {
                    toast(apiErrorMessage(res, 'Could not submit registration.'), 'error');
                    return;
                }
                var cust = (res.data && res.data.customer) || VC.getCustomer() || {};
                if (res.data && res.data.customer) {
                    VC.setSession({ customer: res.data.customer });
                }
                setText('vcRegAppId', formatBizId(cust));
                var uploads = document.querySelectorAll('.vc-upload-card input[type="file"]');
                var chain = Promise.resolve();
                var nameMap = {
                    gst_certificate: 'gst_certificate',
                    fssai_document: 'fssai_license',
                    shop_registration: 'shop_establishment',
                    msme_certificate: 'msme_certificate',
                    trade_licence: 'trade_license',
                    pan_card: 'pan_card',
                    aadhaar_card: 'aadhaar_card',
                    shop_photo: 'business_photo',
                    business_card: 'owner_photo'
                };
                uploads.forEach(function (input) {
                    if (!input.files || !input.files[0]) {
                        return;
                    }
                    var docType = nameMap[input.name] || input.name;
                    chain = chain.then(function () { return VC.uploadDocument(docType, input.files[0]); });
                });
                chain.then(function () {
                    var success = document.getElementById('vcRegistrationSuccess');
                    if (success) success.classList.add('show');
                    toast('Registration submitted');
                    setTimeout(function () { window.location.href = 'verification-status.php'; }, 1200);
                });
            });
        }, true);
    }

    function bootVerification() {
        if (!requireAuth()) {
            return;
        }
        function setText(id, value) {
            var el = document.getElementById(id);
            if (el) {
                el.textContent = value || '—';
            }
        }
        function formatDate(value) {
            if (!value) {
                return '—';
            }
            var d = new Date(String(value).replace(' ', 'T'));
            if (isNaN(d.getTime())) {
                return String(value);
            }
            return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
        }
        function fileNameFromUrl(url) {
            var path = String(url || '').split('?')[0];
            var name = path.split('/').pop() || 'document';
            try {
                return decodeURIComponent(name);
            } catch (e) {
                return name;
            }
        }
        function showStatusPanel(status) {
            var map = { pending: 'vcStatusPending', approved: 'vcStatusApproved', rejected: 'vcStatusRejected' };
            var id = map[status] || 'vcStatusPending';
            document.querySelectorAll('.vc-verification-status-panel').forEach(function (panel) {
                panel.classList.toggle('active', panel.id === id);
            });
        }
        function isImageUrl(url) {
            return /\.(jpe?g|png|gif|webp|avif|bmp)(\?|#|$)/i.test(String(url || ''));
        }
        function openDocPreview(url, label) {
            var box = document.getElementById('vcDocLightbox');
            var body = document.getElementById('vcDocLightboxBody');
            if (!box || !body || !url) {
                return;
            }
            if (isPdfUrl(url)) {
                body.innerHTML = '<iframe src="' + escapeHtml(url) + '" title="' + escapeHtml(label || 'Document') + '"></iframe>';
            } else if (isImageUrl(url)) {
                body.innerHTML = '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(label || 'Document') + '">';
            } else {
                body.innerHTML = '<a href="' + escapeHtml(url) + '" target="_blank" rel="noopener">Open file</a>';
            }
            box.hidden = false;
        }
        function bindLightbox() {
            var box = document.getElementById('vcDocLightbox');
            var close = document.getElementById('vcDocLightboxClose');
            if (close) {
                close.addEventListener('click', function () { if (box) box.hidden = true; });
            }
            if (box) {
                box.addEventListener('click', function (e) {
                    if (e.target === box) {
                        box.hidden = true;
                    }
                });
            }
        }
        function previewInner(url, label) {
            if (!url) {
                return '<span class="vc-doc-preview-empty"><i class="fa-solid fa-cloud-arrow-up"></i></span>';
            }
            if (isImageUrl(url)) {
                return '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(label || '') + '">';
            }
            if (isPdfUrl(url)) {
                return '<span class="vc-doc-preview-empty is-file"><i class="fa-solid fa-file-pdf"></i></span>';
            }
            return '<span class="vc-doc-preview-empty is-file"><i class="fa-solid fa-file"></i></span>';
        }
        function renderDocs(docs, kyc, catalog) {
            var grid = document.getElementById('vcPendingDocGrid');
            var total = document.getElementById('vcPendingDocTotal');
            var rejected = document.getElementById('vcRejectedDocList');
            var list = docs || [];
            var byType = {};
            list.forEach(function (doc) {
                byType[doc.document_type] = doc;
            });
            var expected = (catalog || []).filter(function (row) {
                return row.key !== 'cancelled_cheque';
            });
            if (!expected.length) {
                expected = [
                    { key: 'gst_certificate', label: 'GST Certificate' },
                    { key: 'fssai_license', label: 'FSSAI Licence' },
                    { key: 'shop_establishment', label: 'Shop Registration' },
                    { key: 'msme_certificate', label: 'MSME Certificate' },
                    { key: 'trade_license', label: 'Trade Licence' },
                    { key: 'pan_card', label: 'PAN Card' },
                    { key: 'aadhaar_card', label: 'Aadhaar Card' },
                    { key: 'business_photo', label: 'Shop-front Photo' },
                    { key: 'owner_photo', label: 'Business Visiting Card' }
                ];
            }
            var uploadedCount = expected.filter(function (row) { return !!byType[row.key]; }).length;
            if (total) {
                total.textContent = uploadedCount + ' of ' + expected.length + ' uploaded';
            }
            function cardHtml(row, doc) {
                var uploaded = !!doc;
                var url = doc && doc.file_url;
                var badge = !uploaded ? 'missing' : (kyc === 'approved' ? 'verified' : (kyc === 'rejected' ? 'rejected' : 'reviewing'));
                var badgeText = !uploaded ? 'Not uploaded' : (kyc === 'approved' ? 'Verified' : (kyc === 'rejected' ? 'Needs review' : 'Uploaded'));
                var previewBtn = uploaded && url
                    ? '<button type="button" class="vc-doc-view" data-preview="' + escapeHtml(url) + '" data-label="' + escapeHtml(row.label) + '"><i class="fa-regular fa-eye"></i> Preview</button>'
                    : '';
                return (
                    '<article class="vc-doc-card' + (uploaded ? ' is-uploaded' : ' is-missing') + '">' +
                        '<div class="vc-doc-preview"' + (uploaded && url ? ' data-preview="' + escapeHtml(url) + '" data-label="' + escapeHtml(row.label) + '" role="button" tabindex="0"' : '') + '>' + previewInner(url, row.label) + '</div>' +
                        '<div class="vc-doc-meta">' +
                            '<strong>' + escapeHtml(row.label) + '</strong>' +
                            '<small>' + (uploaded ? escapeHtml(fileNameFromUrl(url)) : 'Not uploaded yet') + '</small>' +
                            '<span class="vc-doc-status ' + badge + '">' + badgeText + '</span>' +
                        '</div>' +
                        previewBtn +
                    '</article>'
                );
            }
            var html = expected.map(function (row) { return cardHtml(row, byType[row.key]); }).join('');
            if (grid) {
                grid.innerHTML = html;
                grid.querySelectorAll('[data-preview]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        openDocPreview(btn.getAttribute('data-preview'), btn.getAttribute('data-label'));
                    });
                });
            }
            if (rejected) {
                rejected.innerHTML = expected.map(function (row) { return cardHtml(row, byType[row.key]); }).join('');
                rejected.querySelectorAll('[data-preview]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        openDocPreview(btn.getAttribute('data-preview'), btn.getAttribute('data-label'));
                    });
                });
            }
        }

        bindLightbox();
        VC.verificationStatus().then(function (res) {
            if (!res || !res.success) {
                return;
            }
            var data = res.data || {};
            var customer = data.customer || {};
            var status = data.kyc_status || customer.kyc_status || 'pending';
            var docs = data.documents || [];
            var sess = (VC.getCustomer && VC.getCustomer()) || {};
            var id = customer.id || sess.id;
            var year = (customer.created_at || '').slice(0, 4) || String(new Date().getFullYear());
            setText('vcAppNumber', id ? ('VC-BIZ-' + year + '-' + String(id).padStart(4, '0')) : '—');
            setText('vcSubmittedDate', formatDate(customer.created_at || customer.updated_at));
            setText('vcBizNameLive', customer.business_name);
            setText('vcOwnerNameLive', customer.owner_name);
            setText('vcApprovedBizName', customer.business_name);
            setText('vcApprovedBizMeta', [typeLabel(customer.business_type), customer.owner_name].filter(Boolean).join(' · '));
            setText('vcApprovedEyebrow', status === 'approved' ? 'Verified Business' : 'Business');
            setText('vcJourneySubmitted', formatDate(customer.created_at || customer.updated_at));
            if (data.kyc_rejection_reason) {
                setText('vcRejectReason', data.kyc_rejection_reason);
            }
            var copy = document.getElementById('vcPendingReviewCopy');
            if (copy) {
                copy.textContent = docs.length
                    ? 'Your application has been received. Our team is reviewing your business information and uploaded documents.'
                    : 'Your application has been received. Our team is reviewing your business information. You have not uploaded any documents yet.';
            }
            showStatusPanel(status);
            renderDocs(docs, status, data.catalog || []);
        });
    }

    /* ---------- FAQ & Support ---------- */

    function bootFaqs() {
        var listEl = document.getElementById('vcFaqList');
        var filtersEl = document.getElementById('vcFaqFilters');
        var searchEl = document.getElementById('vcFaqSearch');
        if (!listEl) {
            return;
        }

        var allFaqs = [];
        var activeCategory = 'all';
        var query = '';

        function render() {
            var filtered = allFaqs.filter(function (f) {
                if (activeCategory !== 'all' && String(f.category || '') !== activeCategory) {
                    return false;
                }
                if (!query) {
                    return true;
                }
                var hay = (String(f.question || '') + ' ' + String(f.answer || '') + ' ' + String(f.category || '')).toLowerCase();
                return hay.indexOf(query) !== -1;
            });

            if (!filtered.length) {
                listEl.innerHTML = '<p class="vc-help-empty">No FAQs matched your search.</p>';
                return;
            }

            listEl.innerHTML = filtered.map(function (f, idx) {
                return (
                    '<article class="vc-faq-item' + (idx === 0 ? ' is-open' : '') + '">' +
                        '<button type="button" class="vc-faq-q" aria-expanded="' + (idx === 0 ? 'true' : 'false') + '">' +
                            '<span>' + escapeHtml(f.question || '') + '</span>' +
                            '<i class="fa-solid fa-chevron-down" aria-hidden="true"></i>' +
                        '</button>' +
                        '<div class="vc-faq-a">' +
                            (f.category ? '<em class="vc-faq-cat">' + escapeHtml(f.category) + '</em>' : '') +
                            '<p>' + escapeHtml(f.answer || '') + '</p>' +
                        '</div>' +
                    '</article>'
                );
            }).join('');
        }

        function renderFilters(categories) {
            if (!filtersEl) {
                return;
            }
            var cats = ['all'].concat(categories);
            filtersEl.innerHTML = cats.map(function (c) {
                var label = c === 'all' ? 'All' : c;
                return '<button type="button" class="vc-help-chip' + (activeCategory === c ? ' is-active' : '') + '" data-faq-cat="' + escapeHtml(c) + '">' + escapeHtml(label) + '</button>';
            }).join('');
        }

        listEl.addEventListener('click', function (e) {
            var btn = e.target.closest('.vc-faq-q');
            if (!btn) {
                return;
            }
            var item = btn.closest('.vc-faq-item');
            if (!item) {
                return;
            }
            var open = item.classList.contains('is-open');
            listEl.querySelectorAll('.vc-faq-item.is-open').forEach(function (el) {
                el.classList.remove('is-open');
                var q = el.querySelector('.vc-faq-q');
                if (q) q.setAttribute('aria-expanded', 'false');
            });
            if (!open) {
                item.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });

        if (filtersEl) {
            filtersEl.addEventListener('click', function (e) {
                var chip = e.target.closest('[data-faq-cat]');
                if (!chip) {
                    return;
                }
                activeCategory = chip.getAttribute('data-faq-cat') || 'all';
                filtersEl.querySelectorAll('.vc-help-chip').forEach(function (el) {
                    el.classList.toggle('is-active', el.getAttribute('data-faq-cat') === activeCategory);
                });
                render();
            });
        }

        if (searchEl) {
            searchEl.addEventListener('input', function () {
                query = String(searchEl.value || '').trim().toLowerCase();
                render();
            });
        }

        VC.faqs().then(function (res) {
            allFaqs = (res && res.success && res.data && res.data.faqs) || [];
            var categories = [];
            allFaqs.forEach(function (f) {
                var c = String(f.category || '').trim();
                if (c && categories.indexOf(c) === -1) {
                    categories.push(c);
                }
            });
            renderFilters(categories);
            render();
        }).catch(function () {
            listEl.innerHTML = '<p class="vc-help-empty">Could not load FAQs right now. Please try again later.</p>';
        });
    }

    function bootSupport() {
        var form = document.getElementById('vcSupportTicketForm');
        var guest = document.getElementById('vcSupportGuestNote');
        var ticketsEl = document.getElementById('vcSupportTickets');
        var submitBtn = document.getElementById('vcSupportSubmitBtn');

        function renderTickets(list) {
            if (!ticketsEl) {
                return;
            }
            if (!list.length) {
                ticketsEl.innerHTML = '<p class="vc-help-empty">No support tickets yet.</p>';
                return;
            }
            ticketsEl.innerHTML = list.map(function (t) {
                var status = String(t.status || 'open').replace(/_/g, ' ');
                return (
                    '<article class="vc-support-ticket">' +
                        '<div class="vc-support-ticket-top">' +
                            '<strong>#' + escapeHtml(t.id) + ' · ' + escapeHtml(t.subject_type || 'Support') + '</strong>' +
                            '<span class="vc-support-status">' + escapeHtml(status) + '</span>' +
                        '</div>' +
                        '<p>' + escapeHtml(t.description || '') + '</p>' +
                        (t.created_at ? '<small>' + escapeHtml(t.created_at) + '</small>' : '') +
                    '</article>'
                );
            }).join('');
        }

        if (!VC.isLoggedIn()) {
            if (guest) guest.hidden = false;
            if (form) form.hidden = true;
            if (ticketsEl) {
                ticketsEl.innerHTML = '<p class="vc-help-empty">Log in to view your support tickets.</p>';
            }
            return;
        }

        if (guest) guest.hidden = true;
        if (form) form.hidden = false;

        VC.supportTickets({ per_page: 10 }).then(function (res) {
            var list = (res && res.success && res.data && res.data.tickets) || [];
            renderTickets(list);
        }).catch(function () {
            if (ticketsEl) {
                ticketsEl.innerHTML = '<p class="vc-help-empty">Could not load tickets.</p>';
            }
        });

        if (!form) {
            return;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var fd = new FormData(form);
            var body = {
                subject_type: String(fd.get('subject_type') || '').trim(),
                description: String(fd.get('description') || '').trim(),
                related_order_id: String(fd.get('related_order_id') || '').trim() || null
            };
            if (!body.subject_type || !body.description) {
                toast('Please fill subject and description.', 'error');
                return;
            }
            if (body.related_order_id) {
                body.related_order_id = Number(body.related_order_id);
            } else {
                delete body.related_order_id;
            }
            if (submitBtn) submitBtn.disabled = true;
            VC.createSupportTicket(body).then(function (res) {
                if (submitBtn) submitBtn.disabled = false;
                if (res && res.success) {
                    toast('Support ticket submitted');
                    form.reset();
                    return VC.supportTickets({ per_page: 10 }).then(function (again) {
                        renderTickets((again && again.success && again.data && again.data.tickets) || []);
                    });
                }
                toast(apiErrorMessage(res, 'Could not submit ticket.'), 'error');
            }).catch(function () {
                if (submitBtn) submitBtn.disabled = false;
                toast('Could not submit ticket.', 'error');
            });
        });
    }

    function bootChangePassword() {
        if (!requireAuth()) {
            return;
        }
        var form = document.getElementById('vcChangePasswordForm');
        if (!form) {
            return;
        }
        var btn = document.getElementById('vcChangePasswordBtn');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var current = ((document.getElementById('vcCurrentPassword') || {}).value || '');
            var password = ((document.getElementById('vcNewPassword') || {}).value || '');
            var confirm = ((document.getElementById('vcConfirmPassword') || {}).value || '');
            if (!password || password.length < 6) {
                toast('New password must be at least 6 characters.', 'error');
                return;
            }
            if (password !== confirm) {
                toast('Password confirmation does not match.', 'error');
                return;
            }
            if (btn) btn.disabled = true;
            VC.changePassword({
                current_password: current,
                password: password,
                password_confirmation: confirm
            }).then(function (res) {
                if (btn) btn.disabled = false;
                if (res && res.success) {
                    toast('Password updated. You can now login with Email & Password.');
                    form.reset();
                    return;
                }
                toast(apiErrorMessage(res, 'Could not update password.'), 'error');
            }).catch(function () {
                if (btn) btn.disabled = false;
                toast('Could not update password.', 'error');
            });
        });
    }

    /* ---------- Boot ---------- */

    document.addEventListener('DOMContentLoaded', function () {
        bootHeader();
        var page = pageName();
        if (page === 'index') bootHome();
        if (page === 'product' || page === 'category-product-listing' || page === 'product-search') bootShop(page);
        if (page === 'category') bootCategoryIndex();
        if (page === 'product-details') bootProductDetails();
        if (page === 'offer') bootOffers();
        if (page === 'login') bootLogin();
        if (page === 'register') bootRegister();
        if (page === 'forgot-password') bootForgot();
        if (page === 'cart') bootCart();
        if (page === 'checkout') bootCheckout();
        if (page === 'wishlist') bootWishlist();
        if (page === 'my-orders') bootOrders();
        if (page === 'order-details' || page === 'order-details-tracking' || page === 'order-success') bootOrderDetails();
        if (page === 'my-profile' || page === 'account-dashboard' || page === 'bussiness-profile') bootProfile();
        if (page === 'manage-address') bootAddresses();
        if (page === 'notification') bootNotifications();
        if (page === 'bussiness-registration') bootBusiness();
        if (page === 'verification-status') bootVerification();
        if (page === 'faq') bootFaqs();
        if (page === 'support') bootSupport();
        if (page === 'change-password') bootChangePassword();
    });
})();
