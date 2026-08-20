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
        ['.vbestSlider .swiper-wrapper', '.vfreshSlider .swiper-wrapper', '.vsrSlider .swiper-wrapper'].forEach(function (sel) {
            var w = document.querySelector(sel);
            if (w) w.innerHTML = '';
        });
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
            fillSlider('.vfreshSlider .swiper-wrapper', products.slice(0, 8), 'vfresh');
            fillSlider('.vsrSlider .swiper-wrapper', products.slice(8, 16).concat(products.slice(0, 8)), 'vsr');
            initSwiper('.vbestSlider', { navigation: { nextEl: '.vbest-next', prevEl: '.vbest-prev' }, pagination: { el: '.vbest-pagination', clickable: true } });
            initSwiper('.vfreshSlider', { navigation: { nextEl: '.vfresh-next', prevEl: '.vfresh-prev' }, pagination: { el: '.vfresh-pagination', clickable: true } });
            initSwiper('.vsrSlider', { navigation: { nextEl: '.vsr-next', prevEl: '.vsr-prev' }, pagination: { el: '.vsr-pagination', clickable: true } });
        });

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
                        fillCategoryFilters(cats, selectedId);
                        renderProductGrid(grid, products);
                        bindShopFilters(grid, cats);
                        updateCategoryHero(cats, selectedId, products.length);
                    });
                }
            }

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
        var form = document.querySelector('.vc-login-form');
        if (!form) {
            return;
        }
        if (VC.isLoggedIn() && !qs('next')) {
            window.location.href = 'account-dashboard.php';
            return;
        }
        var otpWrap = document.createElement('div');
        otpWrap.className = 'vc-login-field';
        otpWrap.style.display = 'none';
        otpWrap.innerHTML =
            '<label for="vcLoginOtp">OTP <span>*</span></label>' +
            '<div class="vc-login-input"><i class="fa-solid fa-key"></i>' +
            '<input type="text" id="vcLoginOtp" inputmode="numeric" maxlength="8" placeholder="Enter OTP"></div>' +
            '<small id="vcOtpHint"></small>';
        var passField = form.querySelector('.vc-login-password-wrap');
        if (passField && passField.closest('.vc-login-field')) {
            passField.closest('.vc-login-field').after(otpWrap);
        } else {
            form.appendChild(otpWrap);
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var user = (document.getElementById('vcLoginUser') || {}).value || '';
            user = user.trim();
            var pass = (document.getElementById('vcLoginPassword') || {}).value || '';
            var otp = (document.getElementById('vcLoginOtp') || {}).value || '';
            var next = qs('next') || 'account-dashboard.php';
            var isMobile = /^[0-9+\-\s]{8,15}$/.test(user) && user.indexOf('@') === -1;

            if (isMobile) {
                if (!otp) {
                    VC.sendOtp(user).then(function (res) {
                        if (!res || !res.success) {
                            toast((res && res.error && res.error.message) || 'Could not send OTP.', 'error');
                            return;
                        }
                        otpWrap.style.display = '';
                        var hint = document.getElementById('vcOtpHint');
                        var msg = 'OTP sent to your mobile.';
                        if (res.data && res.data.dev_otp) {
                            msg += ' DEV OTP: ' + res.data.dev_otp;
                        }
                        if (hint) hint.textContent = msg;
                        toast(msg);
                    });
                    return;
                }
                VC.verifyOtp(user, otp).then(function (res) {
                    if (res && res.success) {
                        applyAuthSuccess(res.data, next);
                    } else {
                        toast((res && res.error && res.error.message) || 'Invalid OTP.', 'error');
                    }
                });
                return;
            }

            VC.emailLogin(user, pass).then(function (res) {
                if (res && res.success) {
                    applyAuthSuccess(res.data, next);
                } else {
                    toast((res && res.error && res.error.message) || 'Invalid email or password. Mobile numbers use OTP login.', 'error');
                }
            });
        });
    }

    function bootRegister() {
        var form = document.querySelector('.vc-signup-form');
        if (!form) {
            return;
        }
        var otpField = document.createElement('div');
        otpField.className = 'vc-signup-field';
        otpField.style.display = 'none';
        otpField.innerHTML =
            '<label for="vcSignupOtp">OTP sent to your mobile <span>*</span></label>' +
            '<div class="vc-signup-input"><i class="fa-solid fa-key"></i>' +
            '<input type="text" id="vcSignupOtp" inputmode="numeric" maxlength="8" placeholder="Enter OTP"></div>' +
            '<small id="vcSignupOtpHint"></small>';
        form.appendChild(otpField);

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var name = (document.getElementById('vcSignupName') || {}).value || '';
            var email = (document.getElementById('vcSignupEmail') || {}).value || '';
            var phone = (document.getElementById('vcSignupPhone') || {}).value || '';
            var otp = (document.getElementById('vcSignupOtp') || {}).value || '';
            if (!phone.trim()) {
                toast('Mobile number is required.', 'error');
                return;
            }
            if (!otp) {
                VC.sendOtp(phone.trim()).then(function (res) {
                    if (!res || !res.success) {
                        toast((res && res.error && res.error.message) || 'Could not send OTP.', 'error');
                        return;
                    }
                    otpField.style.display = '';
                    var hint = document.getElementById('vcSignupOtpHint');
                    var msg = 'OTP sent. Enter it to create your account.';
                    if (res.data && res.data.dev_otp) {
                        msg += ' DEV OTP: ' + res.data.dev_otp;
                    }
                    if (hint) hint.textContent = msg;
                    toast(msg);
                });
                return;
            }
            VC.verifyOtp(phone.trim(), otp).then(function (res) {
                if (!res || !res.success) {
                    toast((res && res.error && res.error.message) || 'Invalid OTP.', 'error');
                    return;
                }
                VC.setSession(res.data);
                return VC.updateProfile({ owner_name: name.trim(), email: email.trim() }).then(function () {
                    toast('Account created');
                    window.location.href = 'bussiness-registration.php';
                });
            });
        });
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
                var otp = window.prompt((res.data && res.data.dev_otp ? ('DEV OTP: ' + res.data.dev_otp + '\n') : '') + 'Enter the OTP sent to your mobile:');
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
        var addressCard = document.querySelectorAll('.vc-checkout-card')[1];
        var slotCard = document.querySelectorAll('.vc-checkout-card')[2];
        var selectedAddress = null;
        var selectedSlot = null;

        Promise.all([VC.cart(), VC.addresses(), VC.deliverySlots(), VC.profile()]).then(function (pack) {
            var cart = pack[0] && pack[0].success ? pack[0].data : { items: [], total: 0, subtotal: 0, discount: 0 };
            var addresses = pack[1] && pack[1].success ? pack[1].data.addresses : [];
            var slots = pack[2] && pack[2].success ? pack[2].data.slots : [];
            var profile = pack[3] && pack[3].success ? pack[3].data : VC.getCustomer();

            if (profile) {
                var nameInput = document.querySelector('.vc-checkout-form-grid input[name="name"]');
                var phoneInput = document.querySelector('.vc-checkout-form-grid input[name="phone"]');
                var emailInput = document.querySelector('.vc-checkout-form-grid input[name="email"]');
                if (nameInput && profile.owner_name) nameInput.value = profile.owner_name;
                if (phoneInput && profile.mobile) phoneInput.value = profile.mobile;
                if (emailInput && profile.email) emailInput.value = profile.email;
            }

            if (addressCard) {
                var picker = document.createElement('div');
                picker.className = 'vc-live-address-picker';
                picker.innerHTML = '<p><strong>Saved addresses</strong> <a href="manage-address.php">(manage)</a></p>' +
                    (addresses.length ? addresses.map(function (a) {
                        return '<label class="vc-live-address"><input type="radio" name="vc_address_id" value="' + a.id + '"' +
                            (a.is_default ? ' checked' : '') + '> <span><strong>' + escapeHtml(a.label || 'Address') + '</strong><br>' +
                            escapeHtml([a.line1, a.line2, a.city, a.state, a.pincode].filter(Boolean).join(', ')) + '</span></label>';
                    }).join('') : '<p>No saved address. <a href="manage-address.php">Add one</a> before placing an order.</p>');
                addressCard.appendChild(picker);
                var def = addresses.find(function (a) { return a.is_default; }) || addresses[0];
                selectedAddress = def ? def.id : null;
                picker.addEventListener('change', function (e) {
                    if (e.target.name === 'vc_address_id') {
                        selectedAddress = Number(e.target.value);
                    }
                });
            }

            if (slotCard && slots.length) {
                var slotBox = document.createElement('div');
                slotBox.className = 'vc-live-slots';
                slotBox.innerHTML = '<p><strong>Delivery slot</strong></p>' + slots.slice(0, 12).map(function (s) {
                    return '<label class="vc-live-slot"><input type="radio" name="vc_slot_id" value="' + s.id + '"> ' +
                        escapeHtml(s.date) + ' · ' + escapeHtml(s.label) + '</label>';
                }).join('');
                slotCard.appendChild(slotBox);
                slotBox.addEventListener('change', function (e) {
                    if (e.target.name === 'vc_slot_id') {
                        selectedSlot = Number(e.target.value);
                    }
                });
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
                var couponInput = couponForm.querySelector('input');
                var couponBtn = couponForm.querySelector('button');
                if (couponBtn && couponInput) {
                    couponBtn.addEventListener('click', function (e) {
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

            var placeBtn = document.querySelector('.vc-place-order-btn');
            if (placeBtn) {
                placeBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (!selectedAddress) {
                        toast('Select or add a delivery address.', 'error');
                        return;
                    }
                    if (!cart.items.length) {
                        toast('Your cart is empty.', 'error');
                        return;
                    }
                    var notes = (document.querySelector('textarea[name="instructions"]') || {}).value || '';
                    placeBtn.disabled = true;
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
                            '<div class="vc-order-id"><span>Order ID</span><strong>' + escapeHtml(o.order_number) + '</strong></div>' +
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
                if (!window.confirm('Cancel this order?')) return;
                VC.cancelOrder(c.getAttribute('data-cancel-order')).then(function (res) {
                    toast((res && res.success && res.data.message) || (res && res.error && res.error.message) || 'Updated');
                    bootOrders();
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

    function bootOrderDetails() {
        if (!requireAuth()) {
            return;
        }
        var id = qs('id');
        if (!id) {
            return;
        }
        VC.order(id).then(function (res) {
            if (!res || !res.success) {
                toast((res && res.error && res.error.message) || 'Order not found.', 'error');
                return;
            }
            var o = res.data.order;
            var cust = VC.getCustomer() || {};
            var addr = o.address || {};
            var line = addrLine(addr);
            var h1 = document.querySelector('h1');
            if (h1 && pageName() !== 'order-success') {
                h1.textContent = 'Order ' + (o.order_number || '');
            }
            setText('vcSuccessOrderNo', o.order_number ? '#' + o.order_number : '—');
            setText('vcSuccessOrderDate', formatInDate(o.placed_at));
            setText('vcSuccessEta', formatInDate(o.estimated_delivery_date));
            setText('vcSuccessPay', o.payment_method || 'Cash on Delivery');
            setText('vcSuccessTotal', money(o.total));
            setText('vcSuccessName', displayName(cust));
            setText('vcSuccessAddr', line || '—');
            setText('vcSuccessPhone', cust.mobile);
            setText('vcSuccessEmail', cust.email);
            setText('vcOrderAddrName', displayName(cust));
            setText('vcOrderAddrText', line || '—');
            setText('vcTrackAddrName', displayName(cust));
            setText('vcTrackAddrLabel', addr.label || 'Address');
            setText('vcTrackAddrText', line || '—');
            var itemsHost = document.getElementById('vcSuccessItems');
            if (itemsHost) {
                itemsHost.innerHTML = (o.items || []).map(function (it) {
                    return '<div class="vc-success-product"><div><h3>' + escapeHtml(titleCaseName(it.name)) +
                        '</h3><p>' + escapeHtml(it.quantity) + ' × ' + money(it.unit_price) +
                        '</p></div><strong>' + money(it.line_total) + '</strong></div>';
                }).join('') || emptyNote('No items');
            }
            var itemCount = document.getElementById('vcSuccessItemCount');
            if (itemCount) {
                itemCount.textContent = String((o.items || []).length) + ' Items';
            }
            var host = document.querySelector('.vc-order-details, .vc-tracking-page, .vc-success-page');
            if (!host) {
                return;
            }
            var old = host.querySelector('.vc-live-order-box');
            if (old) old.remove();
            var items = (o.items || []).map(function (it) {
                return '<li>' + escapeHtml(titleCaseName(it.name)) + ' × ' + escapeHtml(it.quantity) + ' — ' + money(it.line_total || it.unit_price) + '</li>';
            }).join('');
            var log = (o.tracking || o.status_log || o.timeline || []).map(function (s) {
                return '<li>' + escapeHtml(s.status_label || s.status || '') + ' · ' + escapeHtml(s.changed_at || s.created_at || s.at || '') + '</li>';
            }).join('');
            var box = document.createElement('div');
            box.className = 'vc-live-order-box';
            box.innerHTML =
                '<p>Status: <strong>' + escapeHtml(o.status_label || o.status) + '</strong></p>' +
                '<p>Total: <strong>' + money(o.total) + '</strong></p>' +
                '<h3>Items</h3><ul>' + (items || '<li>No items</li>') + '</ul>' +
                (log ? '<h3>Tracking</h3><ul>' + log + '</ul>' : '') +
                (o.can_cancel ? '<button type="button" class="vc-order-btn" id="vcLiveCancel">Cancel order</button>' : '');
            var mount = host.querySelector('.vc-success-container, .vc-orders-container, .vc-tracking-container') || host;
            mount.insertBefore(box, mount.firstChild);
            var btn = document.getElementById('vcLiveCancel');
            if (btn) {
                btn.addEventListener('click', function () {
                    VC.cancelOrder(id).then(function () { window.location.reload(); });
                });
            }
        });
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
                if (el && inputs[k]) el.value = inputs[k];
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
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var inputs = form.querySelectorAll('input[type="text"], input[type="tel"], select');
                var values = [];
                inputs.forEach(function (i) { values.push(i.value.trim()); });
                var type = (form.querySelector('input[name="address_type"]:checked') || {}).value || 'Home';
                var body = {
                    label: type,
                    line1: (values[4] || values[0] || ''),
                    line2: values[5] || '',
                    pincode: values[3] || '',
                    landmark: values[6] || '',
                    city: values[7] || '',
                    state: values[8] || '',
                    is_default: true
                };
                var named = {
                    line1: form.querySelector('[name="line1"]'),
                    line2: form.querySelector('[name="line2"]'),
                    city: form.querySelector('[name="city"]'),
                    state: form.querySelector('[name="state"]'),
                    pincode: form.querySelector('[name="pincode"]'),
                    landmark: form.querySelector('[name="landmark"]'),
                    label: form.querySelector('[name="label"]')
                };
                if (named.line1) {
                    body.line1 = named.line1.value.trim();
                    body.line2 = named.line2 ? named.line2.value.trim() : '';
                    body.city = named.city ? named.city.value.trim() : '';
                    body.state = named.state ? named.state.value.trim() : '';
                    body.pincode = named.pincode ? named.pincode.value.trim() : '';
                    body.landmark = named.landmark ? named.landmark.value.trim() : '';
                    body.label = named.label ? named.label.value.trim() : type;
                }
                VC.createAddress(body).then(function (res) {
                    if (res && res.success) {
                        toast('Address saved');
                        form.reset();
                        render();
                    } else {
                        toast((res && res.error && res.error.message) || 'Could not save address.', 'error');
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
            var body = {
                business_type: type,
                business_name: (document.getElementById('vcBusinessName') || {}).value || '',
                owner_name: (document.getElementById('vcOwnerName') || {}).value || '',
                email: (document.getElementById('vcEmail') || {}).value || '',
                gst_number: (document.getElementById('vcGST') || {}).value || '',
                fssai_number: (document.getElementById('vcFSSAI') || {}).value || '',
                pan_number: (document.getElementById('vcPAN') || {}).value || ''
            };
                VC.businessRegister(body).then(function (res) {
                    if (!res || !res.success) {
                        toast(apiErrorMessage(res, 'Could not submit registration.'), 'error');
                        return;
                    }
                    var cust = (res.data && res.data.customer) || VC.getCustomer() || {};
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
    });
})();
