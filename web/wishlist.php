<?php include('header.php'); ?>

<section class="vc-wishlist-page">

    <div class="vc-wishlist-container">

        <!-- PAGE HEADER -->
        <div class="vc-wishlist-head">

            <div>
                <span class="vc-wishlist-badge">Saved For Later</span>

                <h1>My Wishlist</h1>

                <p>
                    Keep your favourite fresh fruits and vegetables in one place
                    and add them to your cart whenever you're ready.
                </p>
            </div>

            <div class="vc-wishlist-head-actions">

                <span class="vc-wishlist-count">
                    <i class="fa-regular fa-heart"></i>
                    <strong id="vcWishlistCount">6</strong>
                    Items
                </span>

                <button type="button" class="vc-move-all-btn" id="vcMoveAll">
                    <i class="fa-solid fa-basket-shopping"></i>
                    Move All to Cart
                </button>

            </div>

        </div>


        <!-- WISHLIST PRODUCTS -->
        <div class="vc-wishlist-grid" id="vcWishlistGrid">


            <!-- PRODUCT 1 -->
            <article class="vc-wishlist-card">

                <div class="vc-wishlist-image">

                    <span class="vc-wishlist-offer">20% OFF</span>

                    <a href="product.php">
                        <img
                            src="https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=1000&q=85"
                            alt="Fresh Tomatoes">
                    </a>

                    <button
                        type="button"
                        class="vc-wishlist-remove"
                        aria-label="Remove product">

                        <i class="fa-solid fa-xmark"></i>

                    </button>

                </div>


                <div class="vc-wishlist-content">

                    <span class="vc-wishlist-category">
                        Vegetables
                    </span>

                    <h3>
                        <a href="product.php">
                            Fresh Red Tomatoes
                        </a>
                    </h3>


                    <div class="vc-wishlist-rating">
                        <div>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>

                        <span>4.8</span>
                    </div>


                    <div class="vc-wishlist-meta">

                        <span>
                            <i class="fa-solid fa-weight-scale"></i>
                            500 g
                        </span>

                        <span class="vc-wishlist-stock">
                            <i class="fa-solid fa-circle-check"></i>
                            In Stock
                        </span>

                    </div>


                    <div class="vc-wishlist-price">

                        <strong>₹60</strong>

                        <del>₹75</del>

                    </div>


                    <button type="button" class="vc-wishlist-cart-btn">

                        <i class="fa-solid fa-basket-shopping"></i>
                        Add to Cart

                    </button>

                </div>

            </article>


            <!-- PRODUCT 2 -->
            <article class="vc-wishlist-card">

                <div class="vc-wishlist-image">

                    <span class="vc-wishlist-offer vc-green-badge">
                        Fresh
                    </span>

                    <a href="product.php">
                        <img
                            src="https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Carrots">
                    </a>

                    <button type="button" class="vc-wishlist-remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                </div>


                <div class="vc-wishlist-content">

                    <span class="vc-wishlist-category">
                        Vegetables
                    </span>

                    <h3>
                        <a href="product.php">
                            Fresh Carrots
                        </a>
                    </h3>


                    <div class="vc-wishlist-rating">

                        <div>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>

                        <span>4.9</span>

                    </div>


                    <div class="vc-wishlist-meta">

                        <span>
                            <i class="fa-solid fa-weight-scale"></i>
                            1 Kg
                        </span>

                        <span class="vc-wishlist-stock">
                            <i class="fa-solid fa-circle-check"></i>
                            In Stock
                        </span>

                    </div>


                    <div class="vc-wishlist-price">
                        <strong>₹80</strong>
                        <del>₹95</del>
                    </div>


                    <button type="button" class="vc-wishlist-cart-btn">

                        <i class="fa-solid fa-basket-shopping"></i>
                        Add to Cart

                    </button>

                </div>

            </article>


            <!-- PRODUCT 3 -->
            <article class="vc-wishlist-card">

                <div class="vc-wishlist-image">

                    <span class="vc-wishlist-offer vc-green-badge">
                        Popular
                    </span>

                    <a href="product.php">
                        <img
                            src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Potatoes">
                    </a>

                    <button type="button" class="vc-wishlist-remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                </div>


                <div class="vc-wishlist-content">

                    <span class="vc-wishlist-category">
                        Vegetables
                    </span>

                    <h3>
                        <a href="product.php">
                            Fresh Potatoes
                        </a>
                    </h3>


                    <div class="vc-wishlist-rating">

                        <div>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-regular fa-star"></i>
                        </div>

                        <span>4.5</span>

                    </div>


                    <div class="vc-wishlist-meta">

                        <span>
                            <i class="fa-solid fa-weight-scale"></i>
                            1 Kg
                        </span>

                        <span class="vc-wishlist-stock">
                            <i class="fa-solid fa-circle-check"></i>
                            In Stock
                        </span>

                    </div>


                    <div class="vc-wishlist-price">
                        <strong>₹55</strong>
                        <del>₹65</del>
                    </div>


                    <button type="button" class="vc-wishlist-cart-btn">

                        <i class="fa-solid fa-basket-shopping"></i>
                        Add to Cart

                    </button>

                </div>

            </article>


            <!-- PRODUCT 4 -->
            <article class="vc-wishlist-card">

                <div class="vc-wishlist-image">

                    <span class="vc-wishlist-offer">
                        Premium
                    </span>

                    <a href="product.php">
                        <img
                            src="https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Apples">
                    </a>

                    <button type="button" class="vc-wishlist-remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                </div>


                <div class="vc-wishlist-content">

                    <span class="vc-wishlist-category">
                        Fruits
                    </span>

                    <h3>
                        <a href="product.php">
                            Fresh Red Apples
                        </a>
                    </h3>


                    <div class="vc-wishlist-rating">

                        <div>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>

                        <span>4.9</span>

                    </div>


                    <div class="vc-wishlist-meta">

                        <span>
                            <i class="fa-solid fa-weight-scale"></i>
                            1 Kg
                        </span>

                        <span class="vc-wishlist-stock">
                            <i class="fa-solid fa-circle-check"></i>
                            In Stock
                        </span>

                    </div>


                    <div class="vc-wishlist-price">
                        <strong>₹180</strong>
                        <del>₹210</del>
                    </div>


                    <button type="button" class="vc-wishlist-cart-btn">

                        <i class="fa-solid fa-basket-shopping"></i>
                        Add to Cart

                    </button>

                </div>

            </article>


            <!-- PRODUCT 5 -->
            <article class="vc-wishlist-card">

                <div class="vc-wishlist-image">

                    <span class="vc-wishlist-offer vc-green-badge">
                        Juicy
                    </span>

                    <a href="product.php">
                        <img
                            src="https://images.unsplash.com/photo-1547514701-42782101795e?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Oranges">
                    </a>

                    <button type="button" class="vc-wishlist-remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                </div>


                <div class="vc-wishlist-content">

                    <span class="vc-wishlist-category">
                        Fruits
                    </span>

                    <h3>
                        <a href="product.php">
                            Fresh Oranges
                        </a>
                    </h3>


                    <div class="vc-wishlist-rating">

                        <div>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>

                        <span>4.6</span>

                    </div>


                    <div class="vc-wishlist-meta">

                        <span>
                            <i class="fa-solid fa-weight-scale"></i>
                            1 Kg
                        </span>

                        <span class="vc-wishlist-stock">
                            <i class="fa-solid fa-circle-check"></i>
                            In Stock
                        </span>

                    </div>


                    <div class="vc-wishlist-price">
                        <strong>₹120</strong>
                    </div>


                    <button type="button" class="vc-wishlist-cart-btn">

                        <i class="fa-solid fa-basket-shopping"></i>
                        Add to Cart

                    </button>

                </div>

            </article>


            <!-- PRODUCT 6 -->
            <article class="vc-wishlist-card">

                <div class="vc-wishlist-image">

                    <span class="vc-wishlist-offer vc-green-badge">
                        Healthy
                    </span>

                    <a href="product.php">
                        <img
                            src="https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Spinach">
                    </a>

                    <button type="button" class="vc-wishlist-remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                </div>


                <div class="vc-wishlist-content">

                    <span class="vc-wishlist-category">
                        Leafy Greens
                    </span>

                    <h3>
                        <a href="product.php">
                            Fresh Spinach
                        </a>
                    </h3>


                    <div class="vc-wishlist-rating">

                        <div>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>

                        <span>4.7</span>

                    </div>


                    <div class="vc-wishlist-meta">

                        <span>
                            <i class="fa-solid fa-weight-scale"></i>
                            1 Bunch
                        </span>

                        <span class="vc-wishlist-stock">
                            <i class="fa-solid fa-circle-check"></i>
                            In Stock
                        </span>

                    </div>


                    <div class="vc-wishlist-price">
                        <strong>₹40</strong>
                    </div>


                    <button type="button" class="vc-wishlist-cart-btn">

                        <i class="fa-solid fa-basket-shopping"></i>
                        Add to Cart

                    </button>

                </div>

            </article>


        </div>


        <!-- ==================================
             EMPTY WISHLIST
        =================================== -->
        <div class="vc-empty-wishlist" id="vcEmptyWishlist">

            <span>
                <i class="fa-regular fa-heart"></i>
            </span>

            <h2>Your Wishlist is Empty</h2>

            <p>
                You haven't saved any fresh products yet.
                Explore Veggicart and add your favourites here.
            </p>

            <a href="shop.php">
                <i class="fa-solid fa-basket-shopping"></i>
                Start Shopping
            </a>

        </div>


        <!-- BOTTOM ACTION -->
        <div class="vc-wishlist-bottom">

            <a href="shop.php" class="vc-wishlist-continue">

                <i class="fa-solid fa-arrow-left"></i>
                Continue Shopping

            </a>

            <div>

                <i class="fa-solid fa-shield-heart"></i>

                <span>
                    Fresh products saved safely for your next order
                </span>

            </div>

        </div>

    </div>

</section>

<?php include('footer.php'); ?>