<?php include('header.php'); ?>

<section class="vc-search-page">

    <div class="vc-search-container">

        <!-- SEARCH HEADER -->
        <div class="vc-search-hero">

            <span class="vc-search-label">
                Product Search
            </span>

            <h1>
                Search Results
            </h1>

            <p>
                Find fresh fruits, vegetables, leafy greens and daily essentials
                available on Veggicart.
            </p>


            <div class="vc-search-main-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="vcSearchInput"
                    value="tomato"
                    placeholder="Search products...">

                <button
                    type="button"
                    id="vcSearchButton">

                    Search

                </button>

            </div>

        </div>


        <!-- SEARCH INFO -->
        <div class="vc-search-info">

            <div>

                <span>
                    Search results for:
                </span>

                <strong id="vcSearchKeyword">
                    “tomato”
                </strong>

            </div>


            <div class="vc-search-count">

                <strong id="vcSearchCount">
                    8
                </strong>

                Products Found

            </div>

        </div>


        <!-- TOOLBAR -->
        <div class="vc-search-toolbar">

            <button
                type="button"
                class="vc-search-filter-open"
                id="vcSearchFilterOpen">

                <i class="fa-solid fa-sliders"></i>

                Filters

            </button>


            <div class="vc-search-toolbar-text">

                Showing products matching your search

            </div>


            <div class="vc-search-sort">

                <label>
                    Sort By
                </label>

                <select id="vcSearchSort">

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


        <div class="vc-search-layout">

            <!-- FILTER SIDEBAR -->
            <aside
                class="vc-search-sidebar"
                id="vcSearchSidebar">

                <div class="vc-search-mobile-head">

                    <strong>
                        Filter Products
                    </strong>

                    <button
                        type="button"
                        id="vcSearchFilterClose">

                        <i class="fa-solid fa-xmark"></i>

                    </button>

                </div>


                <!-- CATEGORY -->
                <div class="vc-search-filter-box">

                    <div class="vc-search-filter-title">

                        <h3>
                            Categories
                        </h3>

                        <i class="fa-solid fa-layer-group"></i>

                    </div>


                    <div class="vc-search-filter-list">

                        <label class="active">

                            <input
                                type="radio"
                                name="search_category"
                                value="all"
                                checked>

                            <span>All Products</span>

                            <small>8</small>

                        </label>


                        <label>

                            <input
                                type="radio"
                                name="search_category"
                                value="vegetables">

                            <span>Vegetables</span>

                            <small>5</small>

                        </label>


                        <label>

                            <input
                                type="radio"
                                name="search_category"
                                value="fruits">

                            <span>Fruits</span>

                            <small>2</small>

                        </label>


                        <label>

                            <input
                                type="radio"
                                name="search_category"
                                value="herbs">

                            <span>Herbs</span>

                            <small>1</small>

                        </label>

                    </div>

                </div>


                <!-- PRICE -->
                <div class="vc-search-filter-box">

                    <div class="vc-search-filter-title">

                        <h3>
                            Price Range
                        </h3>

                        <i class="fa-solid fa-indian-rupee-sign"></i>

                    </div>


                    <div class="vc-search-checkboxes">

                        <label>
                            <input type="checkbox">
                            Under ₹50
                        </label>

                        <label>
                            <input type="checkbox">
                            ₹50 - ₹100
                        </label>

                        <label>
                            <input type="checkbox">
                            ₹100 - ₹200
                        </label>

                        <label>
                            <input type="checkbox">
                            Above ₹200
                        </label>

                    </div>

                </div>


                <!-- AVAILABILITY -->
                <div class="vc-search-filter-box">

                    <div class="vc-search-filter-title">

                        <h3>
                            Availability
                        </h3>

                        <i class="fa-solid fa-box-open"></i>

                    </div>


                    <div class="vc-search-checkboxes">

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


                <!-- PROMO -->
                <div class="vc-search-promo">

                    <i class="fa-solid fa-leaf"></i>

                    <h3>
                        Fresh Every Day
                    </h3>

                    <p>
                        Quality products selected carefully for your daily needs.
                    </p>

                </div>

            </aside>


            <!-- PRODUCTS -->
            <main class="vc-search-products">

                <div
                    class="vc-search-grid"
                    id="vcSearchGrid">


                    <!-- TOMATO -->
                    <article
                        class="vc-search-card"
                        data-name="Fresh Red Tomatoes"
                        data-category="vegetables"
                        data-price="60">

                        <div class="vc-search-product-image">

                            <span class="vc-search-badge">
                                20% Off
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1561136594-7f68413baa99?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Tomatoes">

                            </a>


                            <button
                                type="button"
                                class="vc-search-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-search-card-content">

                            <span class="vc-search-category">
                                Vegetables
                            </span>


                            <h3>

                                <a href="product.php">
                                    Fresh Red Tomatoes
                                </a>

                            </h3>


                            <div class="vc-search-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>
                                    4.8
                                </span>

                            </div>


                            <span class="vc-search-pack">
                                500 g
                            </span>


                            <div class="vc-search-price">

                                <strong>
                                    ₹60
                                </strong>

                                <del>
                                    ₹75
                                </del>

                            </div>


                            <div class="vc-search-stock">

                                <i class="fa-solid fa-circle-check"></i>

                                In Stock

                            </div>


                            <button
                                type="button"
                                class="vc-search-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>


                    <!-- CHERRY TOMATO -->
                    <article
                        class="vc-search-card"
                        data-name="Cherry Tomatoes"
                        data-category="vegetables"
                        data-price="90">

                        <div class="vc-search-product-image">

                            <span class="vc-search-badge vc-search-green">
                                Premium
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1561136594-7f68413baa99?auto=format&fit=crop&w=700&q=85"
                                    alt="Cherry Tomatoes">

                            </a>


                            <button
                                type="button"
                                class="vc-search-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-search-card-content">

                            <span class="vc-search-category">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Cherry Tomatoes
                                </a>
                            </h3>

                            <div class="vc-search-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>

                                <span>4.9</span>

                            </div>

                            <span class="vc-search-pack">
                                250 g
                            </span>

                            <div class="vc-search-price">
                                <strong>₹90</strong>
                                <del>₹110</del>
                            </div>

                            <div class="vc-search-stock">
                                <i class="fa-solid fa-circle-check"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-search-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>


                    <!-- CARROT -->
                    <article
                        class="vc-search-card"
                        data-name="Fresh Carrots"
                        data-category="vegetables"
                        data-price="80">

                        <div class="vc-search-product-image">

                            <span class="vc-search-badge vc-search-green">
                                Fresh
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Carrots">

                            </a>


                            <button type="button" class="vc-search-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-search-card-content">

                            <span class="vc-search-category">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Carrots
                                </a>
                            </h3>

                            <div class="vc-search-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>

                                <span>4.9</span>

                            </div>

                            <span class="vc-search-pack">
                                1 Kg
                            </span>

                            <div class="vc-search-price">

                                <strong>₹80</strong>

                                <del>₹95</del>

                            </div>

                            <div class="vc-search-stock">

                                <i class="fa-solid fa-circle-check"></i>

                                In Stock

                            </div>

                            <button type="button" class="vc-search-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>


                    <!-- POTATO -->
                    <article
                        class="vc-search-card"
                        data-name="Fresh Potatoes"
                        data-category="vegetables"
                        data-price="55">

                        <div class="vc-search-product-image">

                            <span class="vc-search-badge vc-search-green">
                                Popular
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Potatoes">

                            </a>


                            <button type="button" class="vc-search-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-search-card-content">

                            <span class="vc-search-category">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Potatoes
                                </a>
                            </h3>

                            <div class="vc-search-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>

                                <span>4.5</span>

                            </div>

                            <span class="vc-search-pack">
                                1 Kg
                            </span>

                            <div class="vc-search-price">

                                <strong>₹55</strong>

                                <del>₹65</del>

                            </div>

                            <div class="vc-search-stock">

                                <i class="fa-solid fa-circle-check"></i>

                                In Stock

                            </div>

                            <button type="button" class="vc-search-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>


                    <!-- APPLE -->
                    <article
                        class="vc-search-card"
                        data-name="Fresh Red Apples"
                        data-category="fruits"
                        data-price="180">

                        <div class="vc-search-product-image">

                            <span class="vc-search-badge">
                                Premium
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Apples">

                            </a>


                            <button type="button" class="vc-search-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-search-card-content">

                            <span class="vc-search-category">
                                Fruits
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Red Apples
                                </a>
                            </h3>

                            <div class="vc-search-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>

                                <span>4.9</span>

                            </div>

                            <span class="vc-search-pack">
                                1 Kg
                            </span>

                            <div class="vc-search-price">

                                <strong>₹180</strong>

                                <del>₹210</del>

                            </div>

                            <div class="vc-search-stock">

                                <i class="fa-solid fa-circle-check"></i>

                                In Stock

                            </div>

                            <button type="button" class="vc-search-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>

                                Add to Cart

                            </button>

                        </div>

                    </article>


                    <!-- ORANGE -->
                    <article
                        class="vc-search-card"
                        data-name="Fresh Oranges"
                        data-category="fruits"
                        data-price="120">

                        <div class="vc-search-product-image">

                            <span class="vc-search-badge vc-search-green">
                                Juicy
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1547514701-42782101795e?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Oranges">

                            </a>


                            <button type="button" class="vc-search-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-search-card-content">

                            <span class="vc-search-category">
                                Fruits
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Oranges
                                </a>
                            </h3>

                            <div class="vc-search-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>4.6</span>

                            </div>

                            <span class="vc-search-pack">
                                1 Kg
                            </span>

                            <div class="vc-search-price">
                                <strong>₹120</strong>
                            </div>

                            <div class="vc-search-stock">
                                <i class="fa-solid fa-circle-check"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-search-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>


                    <!-- SPINACH -->
                    <article
                        class="vc-search-card"
                        data-name="Fresh Spinach"
                        data-category="vegetables"
                        data-price="40">

                        <div class="vc-search-product-image">

                            <span class="vc-search-badge vc-search-green">
                                Healthy
                            </span>

                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Spinach">

                            </a>

                            <button type="button" class="vc-search-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-search-card-content">

                            <span class="vc-search-category">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Spinach
                                </a>
                            </h3>

                            <div class="vc-search-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>4.7</span>

                            </div>

                            <span class="vc-search-pack">
                                1 Bunch
                            </span>

                            <div class="vc-search-price">
                                <strong>₹40</strong>
                            </div>

                            <div class="vc-search-stock">
                                <i class="fa-solid fa-circle-check"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-search-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>


                    <!-- CORIANDER -->
                    <article
                        class="vc-search-card"
                        data-name="Fresh Coriander"
                        data-category="herbs"
                        data-price="25">

                        <div class="vc-search-product-image">

                            <span class="vc-search-badge vc-search-green">
                                Daily Fresh
                            </span>


                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1588879460618-9249e7d947d1?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Coriander">

                            </a>


                            <button type="button" class="vc-search-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-search-card-content">

                            <span class="vc-search-category">
                                Herbs
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Coriander
                                </a>
                            </h3>

                            <div class="vc-search-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>

                                <span>4.5</span>

                            </div>

                            <span class="vc-search-pack">
                                1 Bunch
                            </span>

                            <div class="vc-search-price">
                                <strong>₹25</strong>
                            </div>

                            <div class="vc-search-stock">
                                <i class="fa-solid fa-circle-check"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-search-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>

                </div>


                <!-- NO RESULTS -->
                <div
                    class="vc-search-empty"
                    id="vcSearchEmpty">

                    <span>
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>

                    <h2>
                        No products found
                    </h2>

                    <p>
                        We couldn't find a product matching your search.
                        Try another keyword or browse all products.
                    </p>

                    <a href="shop.php">

                        <i class="fa-solid fa-basket-shopping"></i>

                        Browse All Products

                    </a>

                </div>


                <!-- PAGINATION -->
                <div class="vc-search-pagination">

                    <button type="button" disabled>

                        <i class="fa-solid fa-chevron-left"></i>

                    </button>

                    <a href="#" class="active">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>

                    <button type="button">

                        <i class="fa-solid fa-chevron-right"></i>

                    </button>

                </div>

            </main>

        </div>

    </div>


    <div
        class="vc-search-overlay"
        id="vcSearchOverlay">
    </div>

</section>


<?php include('footer.php'); ?>