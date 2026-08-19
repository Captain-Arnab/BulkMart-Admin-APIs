<?php include('header.php'); ?>

<section class="vc-product-page">

    <div class="vc-product-container">

        <!-- BREADCRUMB -->
        <div class="vc-product-breadcrumb">
            <a href="index.php">Home</a>
            <i class="fa-solid fa-chevron-right"></i>

            <a href="shop.php">Shop</a>
            <i class="fa-solid fa-chevron-right"></i>

            <a href="vegetables.php">Vegetables</a>
            <i class="fa-solid fa-chevron-right"></i>

            <span>Fresh Tomatoes</span>
        </div>


        <!-- =========================================
             MAIN PRODUCT AREA
        ========================================== -->
        <div class="vc-product-main">


            <!-- =====================================
                 LEFT PRODUCT GALLERY
            ====================================== -->
            <div class="vc-product-gallery">


                <!-- THUMBNAILS -->
                <div class="vc-product-thumbnails" id="vcProductThumbs"></div>


                <!-- MAIN IMAGE -->
                <div class="vc-product-main-image">

                    <span class="vc-product-discount">
                        20% OFF
                    </span>


                    <button
                        type="button"
                        class="vc-product-wishlist"
                        aria-label="Add to wishlist">

                        <i class="fa-regular fa-heart"></i>

                    </button>


                    <img
                        id="vcMainProductImage"
                        src="images/vegiicart-logo.jpeg"
                        alt="Product image">

                </div>

            </div>



            <!-- =====================================
                 RIGHT PRODUCT INFORMATION
            ====================================== -->
            <div class="vc-product-info">


                <span class="vc-product-category">
                    Fresh Vegetables
                </span>


                <h1>
                    Farm Fresh Red Tomatoes
                </h1>


                <!-- RATING -->
                <div class="vc-product-rating">

                    <div class="vc-stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>

                    <span>
                        4.8
                    </span>

                    <a href="#vcReviews">
                        124 Reviews
                    </a>

                </div>


                <!-- PRICE -->
                <div class="vc-product-price">

                    <strong id="vcProductPrice">
                        ₹60
                    </strong>

                    <del>
                        ₹75
                    </del>

                    <span>
                        Save ₹15
                    </span>

                </div>


                <!-- DESCRIPTION -->
                <p class="vc-product-short-description">
                    Naturally fresh, juicy and carefully selected red tomatoes
                    sourced for everyday cooking. Perfect for salads, curries,
                    soups, sauces and healthy homemade meals.
                </p>


                <!-- STOCK -->
                <div class="vc-product-stock">

                    <span>
                        <i class="fa-solid fa-circle-check"></i>
                        In Stock
                    </span>

                    <small>
                        Fresh stock available for delivery
                    </small>

                </div>


                <!-- =====================================
                     WEIGHT SELECTION
                ====================================== -->
                <div class="vc-product-option">

                    <div class="vc-product-option-title">

                        <strong>
                            Select Weight
                        </strong>

                        <span>
                            Choose your pack size
                        </span>

                    </div>


                    <div class="vc-weight-options">

                        <button
                            type="button"
                            class="vc-weight-btn"
                            data-price="35">

                            250 g

                        </button>


                        <button
                            type="button"
                            class="vc-weight-btn active"
                            data-price="60">

                            500 g

                        </button>


                        <button
                            type="button"
                            class="vc-weight-btn"
                            data-price="110">

                            1 Kg

                        </button>


                        <button
                            type="button"
                            class="vc-weight-btn"
                            data-price="205">

                            2 Kg

                        </button>

                    </div>

                </div>



                <!-- =====================================
                     QUANTITY + CART
                ====================================== -->
                <div class="vc-product-purchase">


                    <div class="vc-product-qty">

                        <button
                            type="button"
                            id="vcQtyMinus">

                            <i class="fa-solid fa-minus"></i>

                        </button>


                        <input
                            type="number"
                            id="vcProductQty"
                            value="1"
                            min="1">


                        <button
                            type="button"
                            id="vcQtyPlus">

                            <i class="fa-solid fa-plus"></i>

                        </button>

                    </div>



                    <button
                        type="button"
                        class="vc-add-cart-btn">

                        <i class="fa-solid fa-basket-shopping"></i>

                        <span>
                            Add to Cart
                        </span>

                    </button>

                </div>



                <!-- BUY NOW -->
                <a
                    href="checkout.php"
                    class="vc-buy-now-btn">

                    Buy Now

                    <i class="fa-solid fa-arrow-right"></i>

                </a>



                <!-- =====================================
                     PRODUCT FEATURES
                ====================================== -->
                <div class="vc-product-benefits">


                    <div class="vc-product-benefit">

                        <span>
                            <i class="fa-solid fa-leaf"></i>
                        </span>

                        <div>
                            <strong>
                                Farm Fresh
                            </strong>

                            <small>
                                Carefully selected produce
                            </small>
                        </div>

                    </div>



                    <div class="vc-product-benefit">

                        <span>
                            <i class="fa-solid fa-truck-fast"></i>
                        </span>

                        <div>
                            <strong>
                                Quick Delivery
                            </strong>

                            <small>
                                Delivered fresh to your door
                            </small>
                        </div>

                    </div>



                    <div class="vc-product-benefit">

                        <span>
                            <i class="fa-solid fa-shield-heart"></i>
                        </span>

                        <div>
                            <strong>
                                Quality Checked
                            </strong>

                            <small>
                                Freshness you can trust
                            </small>
                        </div>

                    </div>

                </div>



                <!-- =====================================
                     DELIVERY PINCODE
                ====================================== -->
                <div class="vc-delivery-check">


                    <div class="vc-delivery-check-title">

                        <i class="fa-solid fa-location-dot"></i>

                        <div>

                            <strong>
                                Check Delivery Availability
                            </strong>

                            <span>
                                Enter your PIN code
                            </span>

                        </div>

                    </div>


                    <div class="vc-pincode-form">

                        <input
                            type="text"
                            maxlength="6"
                            placeholder="Enter PIN code">


                        <button type="button">
                            Check
                        </button>

                    </div>

                </div>


            </div>

        </div>



        <!-- =========================================
             PRODUCT DETAILS
        ========================================== -->
        <div class="vc-product-details">


            <!-- TAB BUTTONS -->
            <div class="vc-product-tabs">

                <button
                    type="button"
                    class="vc-product-tab active"
                    data-tab="description">

                    Description

                </button>


                <button
                    type="button"
                    class="vc-product-tab"
                    data-tab="benefits">

                    Benefits

                </button>


                <button
                    type="button"
                    class="vc-product-tab"
                    data-tab="storage">

                    Storage Tips

                </button>


                <button
                    type="button"
                    class="vc-product-tab"
                    data-tab="reviews">

                    Reviews

                </button>

            </div>



            <!-- =====================================
                 DESCRIPTION
            ====================================== -->
            <div
                class="vc-product-tab-content active"
                id="description">


                <div class="vc-product-description-layout">


                    <div>

                        <span class="vc-section-tag">
                            About This Product
                        </span>


                        <h2>
                            Freshness You Can Taste
                        </h2>


                        <p>
                            Our farm fresh tomatoes are carefully selected
                            for their colour, texture, freshness and natural
                            flavour.
                        </p>


                        <p>
                            They are ideal for Indian curries, salads,
                            soups, sauces, chutneys and everyday home cooking.
                            Veggicart aims to make fresh fruits and vegetables
                            easily available with convenient doorstep delivery.
                        </p>

                    </div>



                    <div class="vc-product-specifications">

                        <div>
                            <span>Category</span>
                            <strong>Vegetables</strong>
                        </div>


                        <div>
                            <span>Product</span>
                            <strong>Fresh Tomato</strong>
                        </div>


                        <div>
                            <span>Quality</span>
                            <strong>Premium Fresh</strong>
                        </div>


                        <div>
                            <span>Available Size</span>
                            <strong>250 g – 2 Kg</strong>
                        </div>


                        <div>
                            <span>Storage</span>
                            <strong>Cool & Dry Place</strong>
                        </div>

                    </div>


                </div>

            </div>



            <!-- =====================================
                 BENEFITS TAB
            ====================================== -->
            <div
                class="vc-product-tab-content"
                id="benefits">


                <div class="vc-product-tab-grid">


                    <div class="vc-info-card">

                        <i class="fa-solid fa-heart-pulse"></i>

                        <h3>
                            Healthy Choice
                        </h3>

                        <p>
                            A fresh vegetable perfect for balanced
                            everyday meals and home cooking.
                        </p>

                    </div>



                    <div class="vc-info-card">

                        <i class="fa-solid fa-seedling"></i>

                        <h3>
                            Fresh Produce
                        </h3>

                        <p>
                            Carefully selected to maintain good
                            freshness, texture and appearance.
                        </p>

                    </div>



                    <div class="vc-info-card">

                        <i class="fa-solid fa-utensils"></i>

                        <h3>
                            Versatile Cooking
                        </h3>

                        <p>
                            Suitable for salads, curries, sauces,
                            soups, chutneys and many recipes.
                        </p>

                    </div>


                </div>

            </div>



            <!-- =====================================
                 STORAGE
            ====================================== -->
            <div
                class="vc-product-tab-content"
                id="storage">


                <div class="vc-storage-box">

                    <i class="fa-solid fa-box-open"></i>

                    <div>

                        <h3>
                            Storage Recommendation
                        </h3>


                        <p>
                            Store tomatoes in a clean, cool and dry place.
                            Once fully ripe, refrigeration can help preserve
                            freshness for a longer period.
                        </p>

                    </div>

                </div>

            </div>



            <!-- =====================================
                 REVIEWS
            ====================================== -->
            <div
                class="vc-product-tab-content"
                id="reviews">


                <div class="vc-review-summary">

                    <div>

                        <strong>
                            4.8
                        </strong>


                        <div class="vc-stars">

                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>

                        </div>


                        <span>
                            Based on 124 customer reviews
                        </span>

                    </div>


                    <button type="button">
                        Write a Review
                    </button>


                </div>

            </div>


        </div>



        <!-- =========================================
             RELATED PRODUCTS
        ========================================== -->
        <div class="vc-related-products">


            <div class="vc-related-heading">

                <div>

                    <span>
                        You May Also Like
                    </span>


                    <h2>
                        More Fresh Picks
                    </h2>

                </div>


                <a href="shop.php">

                    View All Products

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>



            <div class="vc-related-grid">


                <!-- =================================
                     CARROT
                ================================== -->
                <article class="vc-related-card">

                    <div class="vc-related-image">

                        <span>
                            Fresh
                        </span>

                        <img
                            src="https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Carrots">


                        <button
                            type="button"
                            aria-label="Add carrot to wishlist">

                            <i class="fa-regular fa-heart"></i>

                        </button>

                    </div>


                    <div class="vc-related-content">

                        <small>
                            Vegetables
                        </small>


                        <h3>
                            Fresh Carrots
                        </h3>


                        <div class="vc-related-bottom">

                            <strong>
                                ₹80 / Kg
                            </strong>


                            <a href="product.php">
                                <i class="fa-solid fa-plus"></i>
                            </a>

                        </div>

                    </div>

                </article>



                <!-- =================================
                     POTATO
                ================================== -->
                <article class="vc-related-card">

                    <div class="vc-related-image">

                        <span>
                            Popular
                        </span>


                        <img
                            src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Potatoes">


                        <button
                            type="button"
                            aria-label="Add potato to wishlist">

                            <i class="fa-regular fa-heart"></i>

                        </button>

                    </div>


                    <div class="vc-related-content">

                        <small>
                            Vegetables
                        </small>


                        <h3>
                            Fresh Potatoes
                        </h3>


                        <div class="vc-related-bottom">

                            <strong>
                                ₹55 / Kg
                            </strong>


                            <a href="product.php">
                                <i class="fa-solid fa-plus"></i>
                            </a>

                        </div>

                    </div>

                </article>



                <!-- =================================
                     ONION
                ================================== -->
                <article class="vc-related-card">

                    <div class="vc-related-image">

                        <span>
                            Daily Essential
                        </span>


                        <img
                            src="https://images.unsplash.com/photo-1618512496248-a07fe83aa8cb?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Onions">


                        <button
                            type="button"
                            aria-label="Add onion to wishlist">

                            <i class="fa-regular fa-heart"></i>

                        </button>

                    </div>


                    <div class="vc-related-content">

                        <small>
                            Vegetables
                        </small>


                        <h3>
                            Fresh Onions
                        </h3>


                        <div class="vc-related-bottom">

                            <strong>
                                ₹65 / Kg
                            </strong>


                            <a href="product.php">
                                <i class="fa-solid fa-plus"></i>
                            </a>

                        </div>

                    </div>

                </article>



                <!-- =================================
                     SPINACH
                ================================== -->
                <article class="vc-related-card">

                    <div class="vc-related-image">

                        <span>
                            Healthy
                        </span>


                        <img
                            src="https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Spinach">


                        <button
                            type="button"
                            aria-label="Add spinach to wishlist">

                            <i class="fa-regular fa-heart"></i>

                        </button>

                    </div>


                    <div class="vc-related-content">

                        <small>
                            Leafy Greens
                        </small>


                        <h3>
                            Fresh Spinach
                        </h3>


                        <div class="vc-related-bottom">

                            <strong>
                                ₹40 / Bunch
                            </strong>


                            <a href="product.php">
                                <i class="fa-solid fa-plus"></i>
                            </a>

                        </div>

                    </div>

                </article>


            </div>

        </div>


    </div>

</section>

<?php include('footer.php'); ?>