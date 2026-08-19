<?php include('header.php'); ?>

<!-- ========================================
     VEGIICART ORDER SUCCESS PAGE
========================================= -->

<main class="vc-success-page">

    <section class="vc-success-section">

        <div class="vc-success-container">

            <!-- SUCCESS HEADER -->
            <div class="vc-success-card">

                <div class="vc-success-icon-wrap">

                    <div class="vc-success-icon">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <span class="vc-success-ring ring-one"></span>
                    <span class="vc-success-ring ring-two"></span>

                </div>

                <span class="vc-success-label">
                    Order Confirmed
                </span>

                <h1>
                    Thank You for Your Order!
                </h1>

                <p>
                    Your order has been placed successfully. We are preparing
                    your fresh groceries and will keep you updated about the
                    delivery status.
                </p>

                <div class="vc-success-order-number">
                    <span>Order Number</span>
                    <strong id="vcSuccessOrderNo">—</strong>
                </div>

            </div>


            <!-- ORDER INFO -->
            <div class="vc-success-info-grid">

                <div class="vc-success-info-card">

                    <span class="vc-info-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </span>

                    <div>
                        <small>Order Date</small>
                        <strong id="vcSuccessOrderDate">—</strong>
                    </div>

                </div>


                <div class="vc-success-info-card">

                    <span class="vc-info-icon">
                        <i class="fa-solid fa-truck-fast"></i>
                    </span>

                    <div>
                        <small>Expected Delivery</small>
                        <strong id="vcSuccessEta">—</strong>
                    </div>

                </div>


                <div class="vc-success-info-card">

                    <span class="vc-info-icon">
                        <i class="fa-solid fa-credit-card"></i>
                    </span>

                    <div>
                        <small>Payment Method</small>
                        <strong id="vcSuccessPay">—</strong>
                    </div>

                </div>


                <div class="vc-success-info-card">

                    <span class="vc-info-icon">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </span>

                    <div>
                        <small>Total Amount</small>
                        <strong id="vcSuccessTotal">—</strong>
                    </div>

                </div>

            </div>


            <!-- MAIN GRID -->
            <div class="vc-success-main-grid">


                <!-- LEFT SIDE -->
                <div class="vc-success-left">


                    <!-- ORDER ITEMS -->
                    <div class="vc-success-box">

                        <div class="vc-success-box-head">

                            <div>
                                <span>Order Details</span>
                                <h2>Your Items</h2>
                            </div>

                            <span class="vc-item-count" id="vcSuccessItemCount">0 Items</span>

                        </div>


                        <div class="vc-success-products" id="vcSuccessItems">


                            <!-- ITEM -->
                            <div class="vc-success-product">

                                <div class="vc-success-product-image">

                                    <img
                                        src="https://images.unsplash.com/photo-1561136594-7f68413baa99?auto=format&fit=crop&w=300&q=85"
                                        alt="Fresh Tomatoes">

                                </div>

                                <div class="vc-success-product-content">

                                    <span>Fresh Vegetables</span>

                                    <h3>
                                        Farm Fresh Tomatoes
                                    </h3>

                                    <p>
                                        1 kg × ₹45
                                    </p>

                                </div>

                                <strong class="vc-success-product-price">
                                    ₹45
                                </strong>

                            </div>


                            <!-- ITEM -->
                            <div class="vc-success-product">

                                <div class="vc-success-product-image">

                                    <img
                                        src="https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?auto=format&fit=crop&w=300&q=85"
                                        alt="Bananas">

                                </div>

                                <div class="vc-success-product-content">

                                    <span>Fresh Fruits</span>

                                    <h3>
                                        Premium Bananas
                                    </h3>

                                    <p>
                                        2 dozen × ₹49
                                    </p>

                                </div>

                                <strong class="vc-success-product-price">
                                    ₹98
                                </strong>

                            </div>


                            <!-- ITEM -->
                            <div class="vc-success-product">

                                <div class="vc-success-product-image">

                                    <img
                                        src="https://images.unsplash.com/photo-1606787366850-de6330128bfc?auto=format&fit=crop&w=300&q=85"
                                        alt="Grocery Essentials">

                                </div>

                                <div class="vc-success-product-content">

                                    <span>Daily Grocery</span>

                                    <h3>
                                        Grocery Essentials Pack
                                    </h3>

                                    <p>
                                        1 pack × ₹749
                                    </p>

                                </div>

                                <strong class="vc-success-product-price">
                                    ₹749
                                </strong>

                            </div>

                        </div>


                        <!-- PRICE BREAKUP -->
                        <div class="vc-success-price-breakup">

                            <div>
                                <span>Subtotal</span>
                                <strong>₹892</strong>
                            </div>

                            <div>
                                <span>Delivery Fee</span>
                                <strong>₹55</strong>
                            </div>

                            <div>
                                <span>Discount</span>
                                <strong class="vc-success-discount">
                                    - ₹100
                                </strong>
                            </div>

                            <div>
                                <span>Taxes</span>
                                <strong>₹200</strong>
                            </div>

                            <div class="vc-success-total">
                                <span>Total Paid</span>
                                <strong>₹1,047</strong>
                            </div>

                        </div>

                    </div>


                    <!-- TRACKING -->
                    <div class="vc-success-box">

                        <div class="vc-success-box-head">

                            <div>
                                <span>Order Progress</span>
                                <h2>Track Your Order</h2>
                            </div>

                        </div>


                        <div class="vc-order-progress">


                            <div class="vc-progress-item active">

                                <div class="vc-progress-icon">
                                    <i class="fa-solid fa-check"></i>
                                </div>

                                <div>
                                    <strong>Order Placed</strong>
                                    <small>14 Aug, 03:26 PM</small>
                                </div>

                            </div>


                            <div class="vc-progress-line active"></div>


                            <div class="vc-progress-item current">

                                <div class="vc-progress-icon">
                                    <i class="fa-solid fa-basket-shopping"></i>
                                </div>

                                <div>
                                    <strong>Preparing Order</strong>
                                    <small>Your items are being packed</small>
                                </div>

                            </div>


                            <div class="vc-progress-line"></div>


                            <div class="vc-progress-item">

                                <div class="vc-progress-icon">
                                    <i class="fa-solid fa-truck"></i>
                                </div>

                                <div>
                                    <strong>Out for Delivery</strong>
                                    <small>Coming soon</small>
                                </div>

                            </div>


                            <div class="vc-progress-line"></div>


                            <div class="vc-progress-item">

                                <div class="vc-progress-icon">
                                    <i class="fa-solid fa-house"></i>
                                </div>

                                <div>
                                    <strong>Delivered</strong>
                                    <small>Estimated 15 Aug</small>
                                </div>

                            </div>

                        </div>

                    </div>


                </div>


                <!-- RIGHT SIDE -->
                <aside class="vc-success-right">


                    <!-- DELIVERY ADDRESS -->
                    <div class="vc-success-side-card">

                        <div class="vc-side-card-title">

                            <span>
                                <i class="fa-solid fa-location-dot"></i>
                            </span>

                            <h3>
                                Delivery Address
                            </h3>

                        </div>

                        <strong id="vcSuccessName">—</strong>

                        <p id="vcSuccessAddr">—</p>

                        <div class="vc-address-contact">

                            <span id="vcSuccessPhone">—</span>

                            <span id="vcSuccessEmail">—</span>

                        </div>

                    </div>


                    <!-- PAYMENT -->
                    <div class="vc-success-side-card">

                        <div class="vc-side-card-title">

                            <span>
                                <i class="fa-solid fa-wallet"></i>
                            </span>

                            <h3>
                                Payment Details
                            </h3>

                        </div>


                        <div class="vc-payment-row">

                            <span>Payment Method</span>
                            <strong>Cash on Delivery</strong>

                        </div>


                        <div class="vc-payment-row">

                            <span>Payment Status</span>

                            <strong class="vc-payment-pending">
                                Pending
                            </strong>

                        </div>


                        <div class="vc-payment-row">

                            <span>Amount</span>
                            <strong>₹1,047</strong>

                        </div>

                    </div>


                    <!-- NEED HELP -->
                    <div class="vc-success-help">

                        <div class="vc-success-help-icon">
                            <i class="fa-solid fa-headset"></i>
                        </div>

                        <h3>
                            Need Help With Your Order?
                        </h3>

                        <p>
                            Our support team is ready to assist you with
                            order, delivery or payment-related queries.
                        </p>

                        <a href="contact.php">
                            Contact Support
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </aside>

            </div>


            <!-- ACTION BUTTONS -->
            <div class="vc-success-actions">

                <a href="products.php"
                   class="vc-success-btn vc-success-btn-outline">

                    <i class="fa-solid fa-arrow-left"></i>
                    Continue Shopping

                </a>


                <a href="order-details.php"
                   class="vc-success-btn vc-success-btn-primary">

                    View Order Details
                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>


            <!-- BOTTOM MESSAGE -->
            <div class="vc-success-bottom-note">

                <span>
                    <i class="fa-solid fa-envelope-circle-check"></i>
                </span>

                <div>
                    <strong>
                        Order confirmation sent!
                    </strong>

                    <p>
                        We've sent your order confirmation and delivery
                        details to your registered email address.
                    </p>
                </div>

            </div>

        </div>

    </section>

</main>

<?php include('footer.php'); ?>