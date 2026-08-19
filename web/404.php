<?php include('header.php'); ?>

<!-- =========================================
     VEGIICART 404 PAGE
========================================== -->

<main class="vc404-page">

    <section class="vc404-section">

        <div class="vc404-container">

            <div class="vc404-layout">

                <!-- LEFT VISUAL -->
                <div class="vc404-visual">

                    <div class="vc404-number">
                        <span>4</span>

                        <div class="vc404-zero">
                            <i class="fa-solid fa-basket-shopping"></i>
                        </div>

                        <span>4</span>
                    </div>

                    <div class="vc404-floating vc404-float-one">
                        <i class="fa-solid fa-carrot"></i>
                    </div>

                    <div class="vc404-floating vc404-float-two">
                        <i class="fa-solid fa-apple-whole"></i>
                    </div>

                    <div class="vc404-floating vc404-float-three">
                        <i class="fa-solid fa-leaf"></i>
                    </div>

                </div>


                <!-- RIGHT CONTENT -->
                <div class="vc404-content">

                    <span class="vc404-eyebrow">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Page Not Found
                    </span>

                    <h1>
                        Looks Like This Page
                        <span>Went Out of Stock.</span>
                    </h1>

                    <p>
                        The page you're looking for may have been moved,
                        removed or the link may be incorrect. Try searching
                        for a product or continue shopping with Vegiicart.
                    </p>


                    <!-- SEARCH -->
                    <form
                        class="vc404-search"
                        id="vc404SearchForm">

                        <div class="vc404-search-box">

                            <i class="fa-solid fa-magnifying-glass"></i>

                            <input
                                type="search"
                                id="vc404SearchInput"
                                placeholder="Search vegetables, fruits, groceries..."
                                required>

                            <button type="submit">
                                Search
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>

                        </div>

                    </form>


                    <!-- PRIMARY ACTIONS -->
                    <div class="vc404-actions">

                        <a href="index.php"
                           class="vc404-home-btn">

                            <i class="fa-solid fa-house"></i>
                            Return to Home
                        </a>

                        <a href="products.php"
                           class="vc404-products-btn">

                            Browse All Products
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </div>

            </div>


            <!-- CATEGORY SHORTCUTS -->
            <div class="vc404-categories">

                <div class="vc404-category-heading">

                    <div>
                        <span>Continue Shopping</span>
                        <h2>Browse Popular Categories</h2>
                    </div>

                    <a href="categories.php">
                        View All Categories
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>


                <div class="vc404-category-grid">

                    <a href="category.php?category=vegetables"
                       class="vc404-category-card">

                        <span>
                            <i class="fa-solid fa-carrot"></i>
                        </span>

                        <div>
                            <strong>Fresh Vegetables</strong>
                            <small>Daily fresh produce</small>
                        </div>

                        <i class="fa-solid fa-chevron-right"></i>

                    </a>


                    <a href="category.php?category=fruits"
                       class="vc404-category-card">

                        <span>
                            <i class="fa-solid fa-apple-whole"></i>
                        </span>

                        <div>
                            <strong>Fresh Fruits</strong>
                            <small>Seasonal & premium fruits</small>
                        </div>

                        <i class="fa-solid fa-chevron-right"></i>

                    </a>


                    <a href="category.php?category=grocery"
                       class="vc404-category-card">

                        <span>
                            <i class="fa-solid fa-basket-shopping"></i>
                        </span>

                        <div>
                            <strong>Daily Grocery</strong>
                            <small>Everyday essentials</small>
                        </div>

                        <i class="fa-solid fa-chevron-right"></i>

                    </a>


                    <a href="category.php?category=dairy"
                       class="vc404-category-card">

                        <span>
                            <i class="fa-solid fa-bottle-droplet"></i>
                        </span>

                        <div>
                            <strong>Dairy & Essentials</strong>
                            <small>Fresh daily necessities</small>
                        </div>

                        <i class="fa-solid fa-chevron-right"></i>

                    </a>

                </div>

            </div>


            <!-- SUPPORT -->
            <div class="vc404-support">

                <div class="vc404-support-left">

                    <span>
                        <i class="fa-solid fa-headset"></i>
                    </span>

                    <div>
                        <small>Still Need Help?</small>

                        <h3>
                            Our Support Team is Here for You
                        </h3>

                        <p>
                            If you followed a link from Vegiicart and reached
                            this page, let our support team know and we'll help
                            you find what you need.
                        </p>
                    </div>

                </div>


                <a href="contact.php"
                   class="vc404-support-btn">

                    Contact Support
                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>

        </div>

    </section>

</main>

<?php include('footer.php'); ?>