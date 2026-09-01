<?php include('header.php'); ?>

<section class="vc-category-page">

    <div class="vc-category-container">

        <!-- =========================================
             CATEGORY HERO
        ========================================== -->
        <div class="vc-category-hero">

            <div class="vc-category-hero-content">

                <span class="vc-category-label">
                    Fresh Category
                </span>

                <h1>Fresh Vegetables</h1>

                <p>
                    Shop fresh everyday vegetables carefully selected for quality,
                    freshness and convenient doorstep delivery.
                </p>

                <div class="vc-category-meta">

                    <span>
                        <i class="fa-solid fa-leaf"></i>
                        24 Products
                    </span>

                    <span>
                        <i class="fa-solid fa-truck-fast"></i>
                        Fast Delivery
                    </span>

                    <span>
                        <i class="fa-solid fa-shield-heart"></i>
                        Quality Checked
                    </span>

                </div>

            </div>


            <div class="vc-category-hero-image">

                <img
                    src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=85"
                    alt="Fresh vegetables">

            </div>

        </div>


        <!-- =========================================
             CATEGORY NAVIGATION
        ========================================== -->
        <div class="vc-category-tabs">
            <a href="category-product-listing.php" class="active">
                <i class="fa-solid fa-basket-shopping"></i>
                All Products
            </a>
        </div>


        <!-- =========================================
             TOOLBAR
        ========================================== -->
        <div class="vc-category-toolbar">

            <div class="vc-category-search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="vcCategorySearch"
                    placeholder="Search vegetables...">

            </div>


            <button
                type="button"
                class="vc-category-filter-btn"
                id="vcCategoryFilterOpen">

                <i class="fa-solid fa-sliders"></i>
                Filters

            </button>


            <div class="vc-category-results">
                Showing
                <strong id="vcCategoryResultCount">12</strong>
                products
            </div>


            <div class="vc-category-sort">

                <label>Sort By</label>

                <select id="vcCategorySort">

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



        <div class="vc-category-layout">


            <!-- =====================================
                 LEFT FILTERS
            ====================================== -->
            <aside
                class="vc-category-sidebar"
                id="vcCategorySidebar">


                <div class="vc-category-mobile-head">

                    <strong>
                        Filters
                    </strong>

                    <button
                        type="button"
                        id="vcCategoryFilterClose">

                        <i class="fa-solid fa-xmark"></i>

                    </button>

                </div>


                <!-- PRICE -->
                <div class="vc-category-filter-box">

                    <div class="vc-category-filter-title">

                        <h3>
                            Price Range
                        </h3>

                        <i class="fa-solid fa-indian-rupee-sign"></i>

                    </div>


                    <div class="vc-category-check-list">

                        <label>
                            <input
                                type="checkbox"
                                class="vc-price-filter"
                                value="0-50">

                            Under ₹50
                        </label>


                        <label>
                            <input
                                type="checkbox"
                                class="vc-price-filter"
                                value="50-100">

                            ₹50 - ₹100
                        </label>


                        <label>
                            <input
                                type="checkbox"
                                class="vc-price-filter"
                                value="100-200">

                            ₹100 - ₹200
                        </label>


                        <label>
                            <input
                                type="checkbox"
                                class="vc-price-filter"
                                value="200+">

                            Above ₹200
                        </label>

                    </div>

                </div>


                <!-- PACK SIZE -->
                <div class="vc-category-filter-box">

                    <div class="vc-category-filter-title">

                        <h3>
                            Pack Size
                        </h3>

                        <i class="fa-solid fa-weight-scale"></i>

                    </div>


                    <div class="vc-category-check-list">

                        <label>
                            <input type="checkbox">
                            250 g
                        </label>

                        <label>
                            <input type="checkbox">
                            500 g
                        </label>

                        <label>
                            <input type="checkbox">
                            1 Kg
                        </label>

                        <label>
                            <input type="checkbox">
                            2 Kg
                        </label>

                    </div>

                </div>


                <!-- AVAILABILITY -->
                <div class="vc-category-filter-box">

                    <div class="vc-category-filter-title">

                        <h3>
                            Availability
                        </h3>

                        <i class="fa-solid fa-box-open"></i>

                    </div>


                    <div class="vc-category-check-list">

                        <label>
                            <input type="checkbox" checked>
                            In Stock
                        </label>

                        <label>
                            <input type="checkbox">
                            Offers
                        </label>

                        <label>
                            <input type="checkbox">
                            Best Sellers
                        </label>

                    </div>

                </div>


                <!-- FILTER PROMO -->
                <div class="vc-category-filter-promo">

                    <span>
                        <i class="fa-solid fa-leaf"></i>
                    </span>

                    <h3>
                        Freshness Promise
                    </h3>

                    <p>
                        Carefully selected vegetables packed fresh
                        for your everyday kitchen needs.
                    </p>

                </div>

            </aside>



            <!-- =====================================
                 PRODUCTS
            ====================================== -->
            <main class="vc-category-products">

                <div
                    class="vc-category-product-grid"
                    id="vcCategoryGrid">


                    <!-- TOMATO -->
                    <article
                        class="vc-category-card"
                        data-name="Fresh Tomatoes"
                        data-price="60">

                        <div class="vc-category-product-image">

                            <span class="vc-category-product-badge">
                                20% OFF
                            </span>

                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=1000&q=85"
                                    alt="Fresh Tomatoes">

                            </a>

                            <button
                                type="button"
                                class="vc-category-wishlist">

                                <i class="fa-regular fa-heart"></i>

                            </button>

                        </div>


                        <div class="vc-category-card-content">

                            <span class="vc-category-small-label">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Red Tomatoes
                                </a>
                            </h3>


                            <div class="vc-category-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>4.8</span>

                            </div>


                            <span class="vc-category-pack">
                                500 g
                            </span>


                            <div class="vc-category-price">

                                <strong>₹60</strong>

                                <del>₹75</del>

                            </div>


                            <div class="vc-category-stock">
                                <i class="fa-solid fa-circle"></i>
                                In Stock
                            </div>


                            <button
                                type="button"
                                class="vc-category-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- CARROT -->
                    <article
                        class="vc-category-card"
                        data-name="Fresh Carrots"
                        data-price="80">

                        <div class="vc-category-product-image">

                            <span class="vc-category-product-badge vc-cat-green">
                                Fresh
                            </span>

                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Carrots">

                            </a>

                            <button type="button" class="vc-category-wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>

                        </div>


                        <div class="vc-category-card-content">

                            <span class="vc-category-small-label">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Carrots
                                </a>
                            </h3>

                            <div class="vc-category-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>

                                <span>4.9</span>

                            </div>

                            <span class="vc-category-pack">
                                1 Kg
                            </span>

                            <div class="vc-category-price">

                                <strong>₹80</strong>
                                <del>₹95</del>

                            </div>

                            <div class="vc-category-stock">
                                <i class="fa-solid fa-circle"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-category-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- POTATO -->
                    <article
                        class="vc-category-card"
                        data-name="Fresh Potatoes"
                        data-price="55">

                        <div class="vc-category-product-image">

                            <span class="vc-category-product-badge vc-cat-green">
                                Popular
                            </span>

                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Potatoes">

                            </a>

                            <button type="button" class="vc-category-wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>

                        </div>


                        <div class="vc-category-card-content">

                            <span class="vc-category-small-label">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Potatoes
                                </a>
                            </h3>

                            <div class="vc-category-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>

                                <span>4.5</span>

                            </div>

                            <span class="vc-category-pack">
                                1 Kg
                            </span>

                            <div class="vc-category-price">

                                <strong>₹55</strong>
                                <del>₹65</del>

                            </div>

                            <div class="vc-category-stock">
                                <i class="fa-solid fa-circle"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-category-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- ONION -->
                    <article
                        class="vc-category-card"
                        data-name="Fresh Onions"
                        data-price="65">

                        <div class="vc-category-product-image">

                            <span class="vc-category-product-badge">
                                Deal
                            </span>

                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1618512496248-a07fe83aa8cb?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Onions">

                            </a>

                            <button type="button" class="vc-category-wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>

                        </div>


                        <div class="vc-category-card-content">

                            <span class="vc-category-small-label">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Onions
                                </a>
                            </h3>

                            <div class="vc-category-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>

                                <span>4.8</span>

                            </div>

                            <span class="vc-category-pack">
                                1 Kg
                            </span>

                            <div class="vc-category-price">

                                <strong>₹65</strong>
                                <del>₹75</del>

                            </div>

                            <div class="vc-category-stock">
                                <i class="fa-solid fa-circle"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-category-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- BROCCOLI -->
                    <article
                        class="vc-category-card"
                        data-name="Fresh Broccoli"
                        data-price="95">

                        <div class="vc-category-product-image">

                            <span class="vc-category-product-badge vc-cat-green">
                                Healthy
                            </span>

                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1459411621453-7b03977f4bfc?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Broccoli">

                            </a>

                            <button type="button" class="vc-category-wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>

                        </div>


                        <div class="vc-category-card-content">

                            <span class="vc-category-small-label">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Broccoli
                                </a>
                            </h3>

                            <div class="vc-category-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>4.6</span>

                            </div>

                            <span class="vc-category-pack">
                                500 g
                            </span>

                            <div class="vc-category-price">

                                <strong>₹95</strong>

                            </div>

                            <div class="vc-category-stock">
                                <i class="fa-solid fa-circle"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-category-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- CAULIFLOWER -->
                    <article
                        class="vc-category-card"
                        data-name="Fresh Cauliflower"
                        data-price="70">

                        <div class="vc-category-product-image">

                            <span class="vc-category-product-badge vc-cat-green">
                                Farm Fresh
                            </span>

                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1568584711075-3d021a7c3ca3?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Cauliflower">

                            </a>

                            <button type="button" class="vc-category-wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>

                        </div>


                        <div class="vc-category-card-content">

                            <span class="vc-category-small-label">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Cauliflower
                                </a>
                            </h3>

                            <div class="vc-category-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>

                                <span>4.4</span>

                            </div>

                            <span class="vc-category-pack">
                                1 Pc
                            </span>

                            <div class="vc-category-price">

                                <strong>₹70</strong>

                            </div>

                            <div class="vc-category-stock">
                                <i class="fa-solid fa-circle"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-category-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- CAPSICUM -->
                    <article
                        class="vc-category-card"
                        data-name="Green Capsicum"
                        data-price="85">

                        <div class="vc-category-product-image">

                            <span class="vc-category-product-badge vc-cat-green">
                                Premium
                            </span>

                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?auto=format&fit=crop&w=700&q=85"
                                    alt="Green Capsicum">

                            </a>

                            <button type="button" class="vc-category-wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>

                        </div>


                        <div class="vc-category-card-content">

                            <span class="vc-category-small-label">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Green Capsicum
                                </a>
                            </h3>

                            <div class="vc-category-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>

                                <span>4.7</span>

                            </div>

                            <span class="vc-category-pack">
                                500 g
                            </span>

                            <div class="vc-category-price">

                                <strong>₹85</strong>

                            </div>

                            <div class="vc-category-stock">
                                <i class="fa-solid fa-circle"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-category-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>



                    <!-- CUCUMBER -->
                    <article
                        class="vc-category-card"
                        data-name="Fresh Cucumber"
                        data-price="45">

                        <div class="vc-category-product-image">

                            <span class="vc-category-product-badge vc-cat-green">
                                Fresh
                            </span>

                            <a href="product.php">

                                <img
                                    src="https://images.unsplash.com/photo-1604977042946-1eecc30f269e?auto=format&fit=crop&w=700&q=85"
                                    alt="Fresh Cucumber">

                            </a>

                            <button type="button" class="vc-category-wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>

                        </div>


                        <div class="vc-category-card-content">

                            <span class="vc-category-small-label">
                                Vegetables
                            </span>

                            <h3>
                                <a href="product.php">
                                    Fresh Cucumber
                                </a>
                            </h3>

                            <div class="vc-category-rating">

                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>

                                <span>4.5</span>

                            </div>

                            <span class="vc-category-pack">
                                500 g
                            </span>

                            <div class="vc-category-price">

                                <strong>₹45</strong>

                            </div>

                            <div class="vc-category-stock">
                                <i class="fa-solid fa-circle"></i>
                                In Stock
                            </div>

                            <button type="button" class="vc-category-cart-btn">

                                <i class="fa-solid fa-basket-shopping"></i>
                                Add to Cart

                            </button>

                        </div>

                    </article>

                </div>


                <!-- NO RESULTS -->
                <div
                    class="vc-category-no-results"
                    id="vcCategoryNoResults">

                    <i class="fa-solid fa-carrot"></i>

                    <h3>
                        No vegetables found
                    </h3>

                    <p>
                        Try changing your search or filters.
                    </p>

                </div>


                <!-- PAGINATION -->
                <div class="vc-category-pagination">

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
        class="vc-category-filter-overlay"
        id="vcCategoryFilterOverlay">
    </div>

</section>

<?php include('footer.php'); ?>