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

                <span class="vc-success-label">Order Confirmed</span>

                <h1>Thank You for Your Order!</h1>

                <p>
                    Your order has been placed successfully. We are preparing
                    your fresh groceries and will keep you updated about the
                    delivery status.
                </p>

                <div class="vc-success-order-number">
                    <span>Order Number</span>
                    <strong id="vcSuccessOrderNo">—</strong>
                </div>

                <div class="vc-success-status-pill" id="vcSuccessStatusWrap" hidden>
                    <span>Status</span>
                    <strong id="vcSuccessStatus">—</strong>
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
                            <div class="vc-success-loading">Loading order items…</div>
                        </div>

                        <!-- PRICE BREAKUP -->
                        <div class="vc-success-price-breakup" id="vcSuccessPriceBreakup">
                            <div>
                                <span>Subtotal</span>
                                <strong id="vcSuccessSubtotal">—</strong>
                            </div>
                            <div id="vcSuccessFeeRow">
                                <span>Delivery Fee</span>
                                <strong id="vcSuccessFee">—</strong>
                            </div>
                            <div id="vcSuccessDiscountRow" hidden>
                                <span>Discount</span>
                                <strong class="vc-success-discount" id="vcSuccessDiscount">—</strong>
                            </div>
                            <div class="vc-success-total">
                                <span>Order Total</span>
                                <strong id="vcSuccessGrandTotal">—</strong>
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

                        <div class="vc-order-progress" id="vcSuccessProgress">

                            <div class="vc-progress-item" data-step="placed">
                                <div class="vc-progress-icon">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <strong>Order Placed</strong>
                                    <small data-sub>—</small>
                                </div>
                            </div>

                            <div class="vc-progress-line" data-line="1"></div>

                            <div class="vc-progress-item" data-step="preparing">
                                <div class="vc-progress-icon">
                                    <i class="fa-solid fa-basket-shopping"></i>
                                </div>
                                <div>
                                    <strong>Preparing Order</strong>
                                    <small data-sub>Waiting to start</small>
                                </div>
                            </div>

                            <div class="vc-progress-line" data-line="2"></div>

                            <div class="vc-progress-item" data-step="out_for_delivery">
                                <div class="vc-progress-icon">
                                    <i class="fa-solid fa-truck"></i>
                                </div>
                                <div>
                                    <strong>Out for Delivery</strong>
                                    <small data-sub>Coming soon</small>
                                </div>
                            </div>

                            <div class="vc-progress-line" data-line="3"></div>

                            <div class="vc-progress-item" data-step="delivered">
                                <div class="vc-progress-icon">
                                    <i class="fa-solid fa-house"></i>
                                </div>
                                <div>
                                    <strong>Delivered</strong>
                                    <small data-sub>Pending</small>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- RIGHT SIDE -->
                <aside class="vc-success-right">

                    <div class="vc-success-side-card">
                        <div class="vc-side-card-title">
                            <span><i class="fa-solid fa-location-dot"></i></span>
                            <h3>Delivery Address</h3>
                        </div>
                        <strong id="vcSuccessName">—</strong>
                        <p id="vcSuccessAddr">—</p>
                        <div class="vc-address-contact">
                            <span id="vcSuccessPhone"><i class="fa-solid fa-phone"></i> —</span>
                            <span id="vcSuccessEmail"><i class="fa-solid fa-envelope"></i> —</span>
                        </div>
                    </div>

                    <div class="vc-success-side-card">
                        <div class="vc-side-card-title">
                            <span><i class="fa-solid fa-wallet"></i></span>
                            <h3>Payment Details</h3>
                        </div>
                        <div class="vc-payment-row">
                            <span>Payment Method</span>
                            <strong id="vcSuccessPayDetail">Cash on Delivery</strong>
                        </div>
                        <div class="vc-payment-row">
                            <span>Payment Status</span>
                            <strong class="vc-payment-pending" id="vcSuccessPayStatus">Pending</strong>
                        </div>
                        <div class="vc-payment-row">
                            <span>Amount</span>
                            <strong id="vcSuccessPayAmount">—</strong>
                        </div>
                    </div>

                    <div class="vc-success-help">
                        <div class="vc-success-help-icon">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <h3>Need Help With Your Order?</h3>
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

                <a href="products.php" class="vc-success-btn vc-success-btn-outline">
                    <i class="fa-solid fa-arrow-left"></i>
                    Continue Shopping
                </a>

                <a href="order-details.php" class="vc-success-btn vc-success-btn-primary" id="vcSuccessViewDetails">
                    View Order Details
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

                <button type="button" class="vc-success-btn vc-success-btn-danger" id="vcSuccessCancel" hidden>
                    <i class="fa-solid fa-ban"></i>
                    Cancel Order
                </button>

            </div>


            <!-- BOTTOM MESSAGE -->
            <div class="vc-success-bottom-note">
                <span>
                    <i class="fa-solid fa-envelope-circle-check"></i>
                </span>
                <div>
                    <strong>Order confirmation sent!</strong>
                    <p>
                        We've sent your order confirmation and delivery
                        details to your registered mobile number.
                    </p>
                </div>
            </div>

        </div>

    </section>

</main>

<?php include('footer.php'); ?>
