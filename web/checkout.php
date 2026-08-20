<?php include('header.php'); ?>

<section class="vc-checkout-section">

    <div class="vc-checkout-container">

        <!-- PAGE HEADING -->
        <div class="vc-checkout-heading">

            <span class="vc-checkout-badge">
                Secure Checkout
            </span>

            <h1>Complete Your Order</h1>

            <p>
                Confirm contact details, pick a saved Hyderabad address, choose a delivery slot, and place your COD order.
            </p>

        </div>


        <div class="vc-checkout-layout">


            <!-- =====================================
                 LEFT SIDE
            ====================================== -->

            <div class="vc-checkout-main">


                <!-- CONTACT INFORMATION -->
                <div class="vc-checkout-card">

                    <div class="vc-checkout-card-header">

                        <span class="vc-checkout-card-icon">
                            <i class="fa-regular fa-user"></i>
                        </span>

                        <div>
                            <span>Step 01</span>
                            <h2>Contact Information</h2>
                        </div>

                    </div>


                    <div class="vc-checkout-form-grid">

                        <div class="vc-form-group">
                            <label>Full Name <span>*</span></label>

                            <div class="vc-input-wrap">
                                <i class="fa-regular fa-user"></i>

                                <input
                                    type="text"
                                    name="name"
                                    placeholder="Enter your full name"
                                    required>
                            </div>
                        </div>


                        <div class="vc-form-group">
                            <label>Phone Number <span>*</span></label>

                            <div class="vc-input-wrap">
                                <i class="fa-solid fa-phone"></i>

                                <input
                                    type="tel"
                                    name="phone"
                                    placeholder="+91 98765 43210"
                                    required>
                            </div>
                        </div>


                        <div class="vc-form-group vc-full-width">
                            <label>Email Address</label>

                            <div class="vc-input-wrap">
                                <i class="fa-regular fa-envelope"></i>

                                <input
                                    type="email"
                                    name="email"
                                    placeholder="yourname@example.com">
                            </div>
                        </div>

                    </div>

                </div>


                <!-- DELIVERY ADDRESS — saved addresses from API -->
                <div class="vc-checkout-card" id="vcCheckoutAddressCard">

                    <div class="vc-checkout-card-header">

                        <span class="vc-checkout-card-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>

                        <div>
                            <span>Step 02</span>
                            <h2>Delivery Address</h2>
                        </div>

                    </div>

                    <p class="vc-checkout-hint">
                        Use a saved Hyderabad address, or
                        <a href="manage-address.php">add one in Manage Address</a>.
                    </p>

                    <div id="vcCheckoutAddressMount" class="vc-checkout-mount"></div>

                    <div class="vc-checkout-form-grid" style="margin-top:1rem;">
                        <div class="vc-form-group vc-full-width">
                            <label>Delivery Instructions</label>
                            <textarea
                                name="instructions"
                                placeholder="Example: Please call before delivery, leave order at reception, etc."></textarea>
                        </div>
                    </div>

                </div>


                <!-- DELIVERY SLOT (preferred window — not Standard/Express fees) -->
                <div class="vc-checkout-card" id="vcCheckoutSlotCard">

                    <div class="vc-checkout-card-header">

                        <span class="vc-checkout-card-icon">
                            <i class="fa-regular fa-calendar-check"></i>
                        </span>

                        <div>
                            <span>Step 03</span>
                            <h2>Preferred Delivery Slot</h2>
                        </div>

                    </div>

                    <p class="vc-checkout-hint">
                        Pick a preferred date and time. Final timing is confirmed by our team after order review.
                    </p>

                    <div id="vcCheckoutSlotMount" class="vc-checkout-mount"></div>

                </div>


                <!-- PAYMENT — COD only -->
                <div class="vc-checkout-card" id="vcCheckoutPaymentCard">

                    <div class="vc-checkout-card-header">

                        <span class="vc-checkout-card-icon">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </span>

                        <div>
                            <span>Step 04</span>
                            <h2>Payment Method</h2>
                        </div>

                    </div>

                    <div class="vc-payment-options">

                        <label class="vc-payment-option active">

                            <input
                                type="radio"
                                name="payment"
                                value="cod"
                                checked>

                            <span class="vc-payment-radio"></span>

                            <span class="vc-payment-icon">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </span>

                            <span class="vc-payment-content">

                                <strong>Cash on Delivery</strong>

                                <small>
                                    Pay in cash when your order arrives. Currently the only available method.
                                </small>

                            </span>

                        </label>

                    </div>

                    <p class="vc-checkout-hint" style="margin-top:0.75rem;">
                        UPI and card payments are not enabled yet — COD only for now.
                    </p>

                </div>


            </div>



            <!-- =====================================
                 RIGHT SIDE ORDER SUMMARY
            ====================================== -->

            <aside class="vc-checkout-summary">


                <div class="vc-order-summary-card">


                    <div class="vc-order-summary-heading">

                        <span>
                            <i class="fa-solid fa-basket-shopping"></i>
                        </span>

                        <div>
                            <small>Your Basket</small>
                            <h2>Order Summary</h2>
                        </div>

                    </div>


                    <div class="vc-summary-line"></div>


                    <!-- COUPON -->
                    <div class="vc-checkout-coupon">

                        <input
                            type="text"
                            placeholder="Coupon code">

                        <button type="button">
                            Apply
                        </button>

                    </div>


                    <div class="vc-summary-line"></div>


                    <!-- PRICING -->
                    <div class="vc-checkout-price-row">
                        <span>Subtotal</span>
                        <strong>₹0</strong>
                    </div>


                    <div class="vc-checkout-price-row">
                        <span>Delivery</span>
                        <strong class="vc-free-text">
                            FREE
                        </strong>
                    </div>


                    <div class="vc-checkout-price-row">
                        <span>Discount</span>
                        <strong>
                            ₹0
                        </strong>
                    </div>


                    <div class="vc-summary-line"></div>


                    <div class="vc-checkout-total">

                        <div>

                            <span>
                                Total Amount
                            </span>

                            <small>
                                Inclusive of all charges · Pay on delivery
                            </small>

                        </div>


                        <strong>
                            ₹0
                        </strong>

                    </div>


                    <!-- PLACE ORDER -->
                    <button
                        type="button"
                        class="vc-place-order-btn">

                        <span>
                            Place Order
                        </span>

                        <i class="fa-solid fa-arrow-right"></i>

                    </button>


                    <!-- SECURITY -->
                    <div class="vc-checkout-security">

                        <i class="fa-solid fa-shield-halved"></i>

                        <div>

                            <strong>
                                100% Secure Checkout
                            </strong>

                            <span>
                                Your personal information is protected.
                            </span>

                        </div>

                    </div>


                </div>



                <!-- BENEFITS -->

                <div class="vc-checkout-benefits">


                    <div class="vc-checkout-benefit">

                        <span>
                            <i class="fa-solid fa-leaf"></i>
                        </span>

                        <div>
                            <strong>
                                Farm Fresh Quality
                            </strong>

                            <small>
                                Fresh produce carefully selected for you.
                            </small>
                        </div>

                    </div>


                    <div class="vc-checkout-benefit">

                        <span>
                            <i class="fa-solid fa-truck-fast"></i>
                        </span>

                        <div>
                            <strong>
                                Fast Delivery
                            </strong>

                            <small>
                                Fresh groceries delivered quickly.
                            </small>
                        </div>

                    </div>


                    <div class="vc-checkout-benefit">

                        <span>
                            <i class="fa-solid fa-headset"></i>
                        </span>

                        <div>
                            <strong>
                                Customer Support
                            </strong>

                            <small>
                                We're here whenever you need help.
                            </small>
                        </div>

                    </div>


                </div>


            </aside>


        </div>

    </div>

</section>

<?php include('footer.php'); ?>
