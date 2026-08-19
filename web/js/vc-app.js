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
            '<article class="vc-list-product-card" data-name="' + escapeHtml(p.name) + '" data-category="' + escapeHtml(p.category_id) + '" data-price="' + escapeHtml(p.price) + '">' +
                '<div class="vc-list-image">' +
                    (p.in_stock ? '' : '<span class="vc-list-badge">Out of stock</span>') +
                    '<a href="' + productHref(p) + '"><img src="' + escapeHtml(imgUrl(p.image_url)) + '" alt="' + escapeHtml(p.name) + '"></a>' +
                    '<button type="button" class="vc-list-wishlist" data-add-wish="' + p.id + '" aria-label="Wishlist"><i class="fa-regular fa-heart"></i></button>' +
                    '<div class="vc-list-quick-actions"><a href="' + productHref(p) + '" title="View product"><i class="fa-regular fa-eye"></i></a></div>' +
                '</div>' +
                '<div class="vc-list-content">' +
                    '<span class="vc-list-category">' + escapeHtml(p.category_name || '') + '</span>' +
                    '<h3><a href="' + productHref(p) + '">' + escapeHtml(p.name) + '</a></h3>' +
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
                        '<img src="' + escapeHtml(imgUrl(p.image_url)) + '" alt="' + escapeHtml(p.name) + '">' +
                    '</a>' +
                    '<div class="' + prefix + '-content">' +
                        '<span class="' + prefix + '-category">' + escapeHtml(p.category_name || '') + '</span>' +
                        '<h3>' + escapeHtml(p.name) + '</h3>' +
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
                    '<img src="' + escapeHtml(imgUrl(c.image_url)) + '" alt="' + escapeHtml(c.name) + '">' +
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
        var account = document.querySelector('.vc-account');
        var mobileAccount = document.querySelector('.vc-mobile-account a');
        var customer = VC.getCustomer();
        if (VC.isLoggedIn()) {
            var name = (customer && (customer.owner_name || customer.business_name || customer.mobile)) || 'My Account';
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
            timer = setInterval(function () { show(current + 1); }, 1800);
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
                    return (
                        '<article class="vgh-slide' + (i === 0 ? ' active' : '') + '">' +
                            '<img src="' + escapeHtml(imgUrl(b.image_url)) + '" alt="' + escapeHtml(b.title || 'Banner') + '">' +
                            '<div class="vgh-overlay"></div>' +
                            '<div class="vgh-container"><div class="vgh-content">' +
                                '<span class="vgh-badge"><i class="fa-solid fa-leaf"></i> Fresh Every Day</span>' +
                                '<h1>' + escapeHtml(b.title || 'Fresh Produce') + '</h1>' +
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
                            '<div class="vrc-viewed-image"><img src="' + escapeHtml(imgUrl(p.image_url)) + '" alt="' + escapeHtml(p.name) + '"></div>' +
                            '<div class="vrc-viewed-content">' +
                                '<span>' + escapeHtml(p.category_name || '') + '</span>' +
                                '<h4>' + escapeHtml(p.name) + '</h4>' +
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
                    var name = (p && p.name) || line.name;
                    var img = (p && p.image_url) || PLACEHOLDER_IMG;
                    var cat = (p && p.category_name) || '';
                    return (
                        '<article class="vrc-order-item">' +
                            '<div class="vrc-order-image">' +
                                '<img src="' + escapeHtml(imgUrl(img)) + '" alt="' + escapeHtml(name) + '">' +
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
                                '<img src="' + escapeHtml(imgUrl(c.image_url)) + '" alt="' + escapeHtml(c.name) + '">' +
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
            if (title) title.textContent = p.name;
            var cat = document.querySelector('.vc-product-breadcrumb span, .vc-product-category');
            if (cat) cat.textContent = p.category_name || '';
            var price = document.querySelector('.vc-product-price strong');
            if (price) price.textContent = money(p.price);
            renderProductGallery(p);
            var desc = document.querySelector('#description, .vc-product-tab-content, .vc-product-description');
            if (desc && p.description) {
                desc.textContent = p.description;
            }
            var addBtn = document.querySelector('.vc-product-cart, .vc-add-to-cart, button.vc-product-add');
            var qtyInput = document.getElementById('vcProductQty');
            document.querySelectorAll('.vc-product-page .vc-product-cart-btn, .vc-product-actions button').forEach(function (btn) {
                if (/cart/i.test(btn.textContent || btn.className)) {
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
        var main = document.getElementById('vcMainProductImage') || document.querySelector('.vc-product-main-image img');
        var cover = images[0] ? imgUrl(images[0].url) : PLACEHOLDER_IMG;
        if (main) {
            main.src = cover;
            main.alt = p.name;
        }
        if (gallery) {
            gallery.classList.toggle('is-single', images.length < 2);
        }
        if (!thumbs) {
            return;
        }
        thumbs.innerHTML = images.map(function (im, i) {
            var url = imgUrl(im.url);
            return (
                '<button type="button" class="vc-product-thumb' + (i === 0 ? ' active' : '') + '" data-image="' + escapeHtml(url) + '">' +
                    '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(p.name) + '">' +
                '</button>'
            );
        }).join('');
        thumbs.querySelectorAll('.vc-product-thumb').forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                thumbs.querySelectorAll('.vc-product-thumb').forEach(function (t) { t.classList.remove('active'); });
                thumb.classList.add('active');
                if (main) {
                    main.src = thumb.getAttribute('data-image') || cover;
                }
            });
        });
    }

    function bootOffers() {
        VC.offers().then(function (res) {
            var offers = (res && res.success && res.data.offers) || [];
            var host = document.querySelector('#vcCoupons, .vc-offers-grid, .vc-offers-list, main.vc-offers-page');
            var grid = document.querySelector('.vc-offers-deals, .vc-coupon-grid, #vcTodayDeals');
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
            if (grid) {
                grid.innerHTML = html;
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
                img.src = imgUrl(banners[0].image_url);
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
                if (!data.items.length) {
                    wrap.innerHTML = emptyNote('Your cart is empty.', '<a href="product.php">Continue shopping</a>');
                } else {
                    wrap.innerHTML = data.items.map(function (item) {
                        return (
                            '<div class="vc-cart-item" data-item-id="' + item.id + '">' +
                                '<div class="vc-cart-product">' +
                                    '<div class="vc-cart-image"><img src="' + escapeHtml(imgUrl(item.image_url)) + '" alt="' + escapeHtml(item.name) + '"></div>' +
                                    '<div class="vc-cart-product-info">' +
                                        '<span class="vc-cart-category">' + escapeHtml(item.category_name || '') + '</span>' +
                                        '<h3>' + escapeHtml(item.name) + '</h3>' +
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
                    }).join('');
                }
            }
            setSummaryTotals(data);
        });

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
            }
        });
    }

    function setSummaryTotals(data) {
        var rows = document.querySelectorAll('.vc-cart-summary strong, .vc-checkout-total strong, .vc-checkout-price-row strong');
        var sub = document.querySelector('.vc-cart-subtotal, .vc-summary-subtotal');
        document.querySelectorAll('.vc-cart-section .vc-cart-summary, .vc-checkout-summary').forEach(function (box) {
            var htmlNeedles = box.innerText;
        });
        var map = [
            ['.vc-cart-subtotal strong', data.subtotal],
            ['.vc-checkout-price-row:nth-of-type(1) strong', data.subtotal]
        ];
        var strongs = document.querySelectorAll('.vc-cart-totals strong, .vc-checkout-total > strong, .vc-checkout-total strong');
        if (strongs.length) {
            strongs[strongs.length - 1].textContent = money(data.total);
        }
        var discountEls = document.querySelectorAll('.vc-checkout-price-row strong');
        if (discountEls.length >= 2) {
            discountEls[0].textContent = money(data.subtotal);
            if (discountEls[1] && /discount/i.test((discountEls[1].parentElement || {}).textContent || '')) {
                discountEls[1].textContent = '-' + money(data.discount || 0);
            }
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
                    return '<div class="vc-order-product"><div class="vc-order-product-image"><img src="' +
                        escapeHtml(imgUrl(item.image_url)) + '" alt=""></div><div><strong>' + escapeHtml(item.name) +
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
                            '<a href="product-details.php?id=' + pid + '"><img src="' + escapeHtml(imgUrl(item.image_url)) + '" alt="' + escapeHtml(item.name) + '"></a>' +
                            '<button type="button" class="vc-wishlist-remove" data-wish-remove="' + item.id + '"><i class="fa-solid fa-xmark"></i></button>' +
                        '</div>' +
                        '<div class="vc-wishlist-content">' +
                            '<span class="vc-wishlist-category">' + escapeHtml(item.category_name || '') + '</span>' +
                            '<h3><a href="product-details.php?id=' + pid + '">' + escapeHtml(item.name) + '</a></h3>' +
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
            var summary = document.querySelectorAll('.vc-orders-summary strong');
            if (summary[0]) summary[0].textContent = String(orders.length);
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
            var h1 = document.querySelector('h1');
            if (h1) h1.textContent = 'Order ' + (o.order_number || '');
            var host = document.querySelector('.vc-order-details, .vc-tracking-page, section');
            if (!host) {
                return;
            }
            var items = (o.items || []).map(function (it) {
                return '<li>' + escapeHtml(it.name) + ' × ' + escapeHtml(it.quantity) + ' — ' + money(it.line_total || it.price) + '</li>';
            }).join('');
            var log = (o.status_log || o.timeline || []).map(function (s) {
                return '<li>' + escapeHtml(s.status || s.status_label || '') + ' · ' + escapeHtml(s.created_at || s.at || '') + '</li>';
            }).join('');
            var box = document.createElement('div');
            box.className = 'vc-live-order-box';
            box.innerHTML =
                '<p>Status: <strong>' + escapeHtml(o.status_label || o.status) + '</strong></p>' +
                '<p>Total: <strong>' + money(o.total) + '</strong></p>' +
                '<h3>Items</h3><ul>' + (items || '<li>No items</li>') + '</ul>' +
                (log ? '<h3>Tracking</h3><ul>' + log + '</ul>' : '') +
                (o.can_cancel ? '<button type="button" class="vc-order-btn" id="vcLiveCancel">Cancel order</button>' : '');
            host.appendChild(box);
            var btn = document.getElementById('vcLiveCancel');
            if (btn) {
                btn.addEventListener('click', function () {
                    VC.cancelOrder(id).then(function () { window.location.reload(); });
                });
            }
        });
        if (pageName() === 'order-success') {
            var msg = document.querySelector('h1, .vc-success-title');
            if (msg) {
                msg.textContent = 'Order placed successfully';
            }
        }
    }

    function bootProfile() {
        if (!requireAuth()) {
            return;
        }
        VC.profile().then(function (res) {
            if (!res || !res.success) {
                return;
            }
            var c = res.data;
            VC.setSession({ customer: c });
            var nameEl = document.querySelector('.vg-user-card h3, .vc-account-user h3');
            var emailEl = document.querySelector('.vg-user-card p, .vc-account-user p');
            if (nameEl) nameEl.textContent = c.owner_name || c.business_name || c.mobile || 'Customer';
            if (emailEl) emailEl.textContent = c.email || c.mobile || '';
            var inputs = {
                owner_name: c.owner_name,
                email: c.email,
                business_name: c.business_name,
                gst_number: c.gst_number,
                fssai_number: c.fssai_number,
                pan_number: c.pan_number
            };
            Object.keys(inputs).forEach(function (k) {
                var el = document.querySelector('[name="' + k + '"]');
                if (el && inputs[k]) el.value = inputs[k];
            });
            var form = document.querySelector('.vg-profile-form, form');
            if (form && pageName() === 'my-profile') {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var body = {};
                    ['email', 'owner_name', 'business_name', 'gst_number', 'fssai_number', 'pan_number'].forEach(function (k) {
                        var el = form.querySelector('[name="' + k + '"]');
                        if (el) body[k] = el.value.trim();
                    });
                    var nameInput = form.querySelector('input[type="text"]');
                    var emailInput = form.querySelector('input[type="email"]');
                    if (!body.owner_name && nameInput) body.owner_name = nameInput.value.trim();
                    if (!body.email && emailInput) body.email = emailInput.value.trim();
                    VC.updateProfile(body).then(function (r) {
                        if (r && r.success) {
                            VC.setSession({ customer: r.data });
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
            var host = document.querySelector('.vc-notification-list, .vc-notifications, section');
            if (!host) {
                return;
            }
            var box = document.createElement('div');
            box.className = 'vc-live-notes';
            box.innerHTML = items.length ? items.map(function (n) {
                return '<article><strong>' + escapeHtml(n.title || n.message) + '</strong><p>' +
                    escapeHtml(n.body || n.message || '') + '</p></article>';
            }).join('') : emptyNote('No notifications yet.');
            host.appendChild(box);
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
                    toast((res && res.error && res.error.message) || 'Could not submit registration.', 'error');
                    return;
                }
                var uploads = document.querySelectorAll('.vc-upload-card input[type="file"]');
                var chain = Promise.resolve();
                uploads.forEach(function (input) {
                    if (!input.files || !input.files[0]) {
                        return;
                    }
                    var label = (input.closest('.vc-upload-card') && input.closest('.vc-upload-card').querySelector('strong'));
                    var text = label ? label.textContent.toLowerCase() : '';
                    var map = {
                        gst: 'gst_certificate',
                        fssai: 'fssai_license',
                        pan: 'pan_card',
                        aadhaar: 'aadhaar_card',
                        shop: 'shop_establishment',
                        trade: 'trade_license',
                        cheque: 'cancelled_cheque',
                        business: 'business_photo',
                        owner: 'owner_photo'
                    };
                    var docType = 'business_photo';
                    Object.keys(map).forEach(function (k) {
                        if (text.indexOf(k) !== -1) {
                            docType = map[k];
                        }
                    });
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
        if (!VC.isLoggedIn()) {
            return;
        }
        VC.verificationStatus().then(function (res) {
            if (!res || !res.success) {
                return;
            }
            var status = (res.data && (res.data.kyc_status || (res.data.customer && res.data.customer.kyc_status))) || '';
            var buttons = document.querySelectorAll('[data-status-target]');
            buttons.forEach(function (b) {
                if (b.getAttribute('data-status-target') === status) {
                    b.click();
                }
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
    });
})();
