<?php include('header.php'); ?>

<section class="vc-shop-page">

    <div class="vc-shop-container">

        <!-- =========================
             SHOP HERO / HEADER
        ========================== -->
        <div class="vc-shop-head">

            <div>
                <span class="vc-shop-eyebrow">Fresh Everyday</span>

                <h1>Shop Fresh Fruits & Vegetables</h1>

                <p>
                    Discover handpicked fruits, vegetables, leafy greens
                    and daily essentials with freshness delivered to your doorstep.
                </p>
            </div>

            <div class="vc-shop-head-stat">

                <span>
                    <i class="fa-solid fa-leaf"></i>
                </span>

                <div>
                    <strong>100+ Fresh Items</strong>
                    <small>Updated regularly</small>
                </div>

            </div>

        </div>


        <!-- =========================
             TOOLBAR
        ========================== -->
        <div class="vc-shop-toolbar">

            <!-- SEARCH -->
            <div class="vc-shop-search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="vcProductSearch"
                    placeholder="Search fruits, vegetables, herbs...">

            </div>


            <!-- CATEGORY MOBILE BUTTON -->
            <button
                type="button"
                class="vc-mobile-filter-btn"
                id="vcFilterOpen">

                <i class="fa-solid fa-sliders"></i>
                Filters

            </button>


            <!-- RESULTS -->
            <div class="vc-shop-result-count">
                Showing
                <strong>12</strong>
                products
            </div>


            <!-- SORT -->
            <div class="vc-shop-sort">

                <label for="vcSortProducts">
                    Sort By
                </label>

                <select id="vcSortProducts">

                    <option value="default">
                        Recommended
                    </option>

                    <option value="low-high">
                        Price: Low to High
                    </option>

                    <option value="high-low">
                        Price: High to Low
                    </option>

                    <option value="name">
                        Name A-Z
                    </option>

                </select>

            </div>

        </div>



        <div class="vc-shop-layout">


            <!-- =========================
                 LEFT FILTER SIDEBAR
            ========================== -->
            <aside class="vc-shop-filter" id="vcShopFilter">

                <div class="vc-filter-mobile-head">

                    <strong>Filters</strong>

                    <button
                        type="button"
                        id="vcFilterClose">

                        <i class="fa-solid fa-xmark"></i>

                    </button>

                </div>


                <!-- CATEGORY -->
                <div class="vc-filter-box">

                    <div class="vc-filter-title">
                        <h3>Categories</h3>

                        <i class="fa-solid fa-layer-group"></i>
                    </div>


                    <div class="vc-category-filter">

                        <label class="active">

                            <input
                                type="radio"
                                name="category"
                                value="all"
                                checked>

                            <span class="vc-filter-radio"></span>

                            <span>All Products</span>

                            <small>12</small>

                        </label>


                        <label>

                            <input
                                type="radio"
                                name="category"
                                value="vegetables">

                            <span class="vc-filter-radio"></span>

                            <span>Vegetables</span>

                            <small>6</small>

                        </label>


                        <label>

                            <input
                                type="radio"
                                name="category"
                                value="fruits">

                            <span class="vc-filter-radio"></span>

                            <span>Fruits</span>

                            <small>3</small>

                        </label>


                        <label>

                            <input
                                type="radio"
                                name="category"
                                value="leafy">

                            <span class="vc-filter-radio"></span>

                            <span>Leafy Greens</span>

                            <small>2</small>

                        </label>


                        <label>

                            <input
                                type="radio"
                                name="category"
                                value="herbs">

                            <span class="vc-filter-radio"></span>

                            <span>Herbs</span>

                            <small>1</small>

                        </label>

                    </div>

                </div>


                <!-- PRICE FILTER -->
                <div class="vc-filter-box">

                    <div class="vc-filter-title">

                        <h3>Price Range</h3>

                        <i class="fa-solid fa-indian-rupee-sign"></i>

                    </div>


                    <div class="vc-price-options">

                        <label>
                            <input
                                type="checkbox"
                                value="0-50">

                            Under ₹50
                        </label>


                        <label>
                            <input
                                type="checkbox"
                                value="50-100">

                            ₹50 - ₹100
                        </label>


                        <label>
                            <input
                                type="checkbox"
                                value="100-200">

                            ₹100 - ₹200
                        </label>


                        <label>
                            <input
                                type="checkbox"
                                value="200+">

                            Above ₹200
                        </label>

                    </div>

                </div>


                <!-- AVAILABILITY -->
                <div class="vc-filter-box">

                    <div class="vc-filter-title">

                        <h3>Availability</h3>

                        <i class="fa-solid fa-box"></i>

                    </div>


                    <div class="vc-price-options">

                        <label>
                            <input
                                type="checkbox"
                                checked>

                            In Stock
                        </label>


                        <label>
                            <input type="checkbox">

                            Offers
                        </label>

                    </div>

                </div>


                <!-- SPECIAL INFO -->
                <div class="vc-filter-promo">

                    <span>
                        <i class="fa-solid fa-truck-fast"></i>
                    </span>

                    <h3>Fresh Delivery</h3>

                    <p>
                        Quality produce packed carefully and delivered quickly.
                    </p>

                </div>

            </aside>



            <!-- =========================
                 PRODUCT AREA
            ========================== -->
            <main class="vc-shop-products">


                <div
                    class="vc-product-grid"
                    id="vcProductGrid">


                    <!-- =========================
                         TOMATO
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Tomatoes"
                        data-category="vegetables"
                        data-price="60">

                        <div class="vc-list-image">

                            <span class="vc-list-badge">
                                20% Off
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=1000&q=85"
                                    alt="Fresh Tomatoes">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist"
                                aria-label="Add tomatoes to wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>


                            <div class="vc-list-quick-actions">

                                <a
                                    href="product.php"
                                    title="View product">

                                    <i class="fa-regular fa-eye"></i>

                                </a>

                            </div>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Vegetables
                            </span>


                            <h3>

                                <a href="product.php">
                                    Fresh Red Tomatoes
                                </a>

                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>

                                <span>
                                    4.9
                                </span>

                            </div>


                            <div class="vc-list-pack">
                                500 g
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹60
                                </strong>

                                <del>
                                    ₹75
                                </del>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         CARROT
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Carrots"
                        data-category="vegetables"
                        data-price="80">

                        <div class="vc-list-image">

                            <span class="vc-list-badge vc-badge-green">
                                Fresh
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Carrots">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Carrots
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>4.7</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Kg
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹80
                                </strong>

                                <del>
                                    ₹95
                                </del>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         POTATO
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Potatoes"
                        data-category="vegetables"
                        data-price="55">

                        <div class="vc-list-image">

                            <span class="vc-list-badge vc-badge-green">
                                Popular
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Potatoes">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Potatoes
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>

                                <span>4.5</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Kg
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹55
                                </strong>

                                <del>
                                    ₹65
                                </del>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         ONION
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Onions"
                        data-category="vegetables"
                        data-price="65">

                        <div class="vc-list-image">

                            <span class="vc-list-badge">
                                Deal
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1618512496248-a07fe83aa8cb?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Onions">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Onions
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>

                                <span>4.8</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Kg
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹65
                                </strong>

                                <del>
                                    ₹75
                                </del>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         SPINACH
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Spinach"
                        data-category="leafy"
                        data-price="40">

                        <div class="vc-list-image">

                            <span class="vc-list-badge vc-badge-green">
                                Healthy
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Spinach">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Leafy Greens
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Spinach
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>4.7</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Bunch
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹40
                                </strong>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         BOTTLE GOURD
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Bottle Gourd"
                        data-category="vegetables"
                        data-price="50">

                        <div class="vc-list-image">

                            <span class="vc-list-badge vc-badge-green">
                                Farm Fresh
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1590779033100-9f60a05a013d?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Bottle Gourd">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Bottle Gourd
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>

                                <span>4.4</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Pc
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹50
                                </strong>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         APPLE
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Apples"
                        data-category="fruits"
                        data-price="180">

                        <div class="vc-list-image">

                            <span class="vc-list-badge">
                                Premium
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Apples">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Fruits
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Red Apples
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>

                                <span>4.9</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Kg
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹180
                                </strong>

                                <del>
                                    ₹210
                                </del>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         ORANGE
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Oranges"
                        data-category="fruits"
                        data-price="120">

                        <div class="vc-list-image">

                            <span class="vc-list-badge vc-badge-green">
                                Juicy
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1547514701-42782101795e?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Oranges">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Fruits
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Oranges
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>4.6</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Kg
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹120
                                </strong>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         BANANA
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Bananas"
                        data-category="fruits"
                        data-price="70">

                        <div class="vc-list-image">

                            <span class="vc-list-badge vc-badge-green">
                                Fresh
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Bananas">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Fruits
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Bananas
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>

                                <span>4.8</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Dozen
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹70
                                </strong>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         CORIANDER
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Coriander"
                        data-category="herbs"
                        data-price="25">

                        <div class="vc-list-image">

                            <span class="vc-list-badge vc-badge-green">
                                Daily Fresh
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1588879460618-9249e7d947d1?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Coriander">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Herbs
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Coriander
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>

                                <span>4.5</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Bunch
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹25
                                </strong>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         BROCCOLI
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Broccoli"
                        data-category="vegetables"
                        data-price="95">

                        <div class="vc-list-image">

                            <span class="vc-list-badge vc-badge-green">
                                Healthy
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1459411621453-7b03977f4bfc?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Broccoli">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Broccoli
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>4.6</span>

                            </div>


                            <div class="vc-list-pack">
                                500 g
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹95
                                </strong>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- =========================
                         LETTUCE
                    ========================== -->
                    <article
                        class="vc-list-product-card"
                        data-name="Fresh Lettuce"
                        data-category="leafy"
                        data-price="60">

                        <div class="vc-list-image">

                            <span class="vc-list-badge vc-badge-green">
                                Fresh Pick
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Lettuce">

                            </a>


                            <button
                                type="button"
                                class="vc-list-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-list-content">

                            <span class="vc-list-category">
                                Leafy Greens
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Lettuce
                                </a>
                            </h3>


                            <div class="vc-list-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>

                                <span>4.4</span>

                            </div>


                            <div class="vc-list-pack">
                                1 Pc
                            </div>


                            <div class="vc-list-price">

                                <strong>
                                    ₹60
                                </strong>

                            </div>


                            <div class="vc-list-stock">

                                <i class="fa-solid fa-circle"></i>
                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-list-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>


                </div>



                <!-- NO RESULT -->
                <div
                    class="vc-no-products"
                    id="vcNoProducts">

                    <i class="fa-solid fa-basket-shopping"></i>

                    <h3>No products found</h3>

                    <p>
                        Try another keyword or choose a different category.
                    </p>

                </div>



                <!-- =========================
                     PAGINATION
                ========================== -->
                <div class="vc-shop-pagination">

                    <button type="button" disabled>

                        <i class="fa-solid fa-chevron-left"></i>

                    </button>


                    <a href="#" class="active">
                        1
                    </a>

                    <a href="#">
                        2
                    </a>

                    <a href="#">
                        3
                    </a>


                    <button type="button">

                        <i class="fa-solid fa-chevron-right"></i>

                    </button>

                </div>


            </main>


        </div>

    </div>


    <!-- MOBILE OVERLAY -->
    <div
        class="vc-filter-overlay"
        id="vcFilterOverlay">
    </div>

</section>

<?php include('footer.php'); ?>