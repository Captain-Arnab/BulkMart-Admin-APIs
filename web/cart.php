<?php include('header.php'); ?>

<section class="vc-cart-section">
    <div class="vc-cart-container">

        <div class="vc-cart-heading">
            <span class="vc-cart-badge">Your Basket</span>
            <h1>Shopping Cart</h1>
            <p>Review your fresh picks before proceeding to checkout.</p>
        </div>

        <div class="vc-cart-layout">

            <!-- LEFT: CART ITEMS -->
            <div class="vc-cart-items-wrap" id="vcCartItems">

                <!-- Cart Item 1 -->
                <div class="vc-cart-item">
                    <div class="vc-cart-product">

                        <div class="vc-cart-image">
                            <img src="https://images.unsplash.com/photo-1447175008436-054170c2e979?auto=format&fit=crop&w=500&q=80"
                                 alt="Fresh Tomatoes">
                        </div>

                        <div class="vc-cart-product-info">
                            <span class="vc-cart-category">Fresh Vegetables</span>
                            <h3>Fresh Tomatoes</h3>

                            <div class="vc-cart-meta">
                                <span>500 g</span>
                                <span class="vc-stock">In Stock</span>
                            </div>

                            <button class="vc-remove-btn">
                                <i class="fa-solid fa-trash-can"></i>
                                Remove
                            </button>
                        </div>

                    </div>

                    <div class="vc-cart-price">
                        <span>Price</span>
                        <strong>₹60</strong>
                    </div>

                    <div class="vc-cart-quantity">
                        <span>Quantity</span>

                        <div class="vc-qty-box">
                            <button type="button" class="vc-qty-minus">
                                <i class="fa-solid fa-minus"></i>
                            </button>

                            <input type="number" value="1" min="1">

                            <button type="button" class="vc-qty-plus">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="vc-cart-total">
                        <span>Total</span>
                        <strong>₹60</strong>
                    </div>

                </div>


                <!-- Cart Item 2 -->
                <div class="vc-cart-item">

                    <div class="vc-cart-product">

                        <div class="vc-cart-image">
                            <img src="https://images.unsplash.com/photo-1582515073490-39981397c445?auto=format&fit=crop&w=500&q=80"
                                 alt="Fresh Carrots">
                        </div>

                        <div class="vc-cart-product-info">
                            <span class="vc-cart-category">Farm Fresh</span>
                            <h3>Fresh Carrots</h3>

                            <div class="vc-cart-meta">
                                <span>1 Kg</span>
                                <span class="vc-stock">In Stock</span>
                            </div>

                            <button class="vc-remove-btn">
                                <i class="fa-solid fa-trash-can"></i>
                                Remove
                            </button>
                        </div>

                    </div>

                    <div class="vc-cart-price">
                        <span>Price</span>
                        <strong>₹80</strong>
                    </div>

                    <div class="vc-cart-quantity">
                        <span>Quantity</span>

                        <div class="vc-qty-box">
                            <button type="button" class="vc-qty-minus">
                                <i class="fa-solid fa-minus"></i>
                            </button>

                            <input type="number" value="2" min="1">

                            <button type="button" class="vc-qty-plus">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="vc-cart-total">
                        <span>Total</span>
                        <strong>₹160</strong>
                    </div>

                </div>


                <!-- Cart Actions -->
                <div class="vc-cart-actions">

                    <a href="shop.php" class="vc-continue-btn">
                        <i class="fa-solid fa-arrow-left"></i>
                        Continue Shopping
                    </a>

                    <button type="button" class="vc-update-btn">
                        <i class="fa-solid fa-rotate"></i>
                        Update Cart
                    </button>

                </div>

            </div>


            <!-- RIGHT: ORDER SUMMARY -->
            <aside class="vc-cart-summary">

                <div class="vc-summary-card">

                    <div class="vc-summary-header">
                        <span>
                            <i class="fa-solid fa-basket-shopping"></i>
                        </span>

                        <div>
                            <small>Order Overview</small>
                            <h2>Cart Summary</h2>
                        </div>
                    </div>


                    <div class="vc-summary-row">
                        <span>Subtotal</span>
                        <strong data-cart-subtotal>₹0</strong>
                    </div>

                    <div class="vc-summary-row">
                        <span>Delivery</span>
                        <strong class="vc-free" data-cart-delivery>FREE</strong>
                    </div>

                    <div class="vc-summary-row">
                        <span>Discount</span>
                        <strong data-cart-discount>₹0</strong>
                    </div>


                    <div class="vc-summary-divider"></div>


                    <div class="vc-summary-total">
                        <span>Total Amount</span>

                        <div>
                            <small>Including all charges</small>
                            <strong data-cart-total>₹0</strong>
                        </div>
                    </div>


                    <a href="checkout.php" class="vc-checkout-btn">
                        Proceed to Checkout
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>


                    <div class="vc-secure-checkout">
                        <i class="fa-solid fa-shield-halved"></i>

                        <div>
                            <strong>Safe & Secure Checkout</strong>
                            <span>Your payment information is protected.</span>
                        </div>
                    </div>

                </div>


                <!-- Coupon -->
                <div class="vc-coupon-card">

                    <div class="vc-coupon-title">
                        <i class="fa-solid fa-ticket"></i>

                        <div>
                            <strong>Have a coupon?</strong>
                            <span>Enter your code below</span>
                        </div>
                    </div>

                    <div class="vc-coupon-form">
                        <input type="text" placeholder="Coupon code" id="vcCartCouponInput">
                        <button type="button" id="vcCartCouponBtn">Apply</button>
                    </div>

                </div>

            </aside>

        </div>

    </div>
</section>

<?php include('footer.php'); ?>