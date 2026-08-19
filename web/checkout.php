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
                Enter your delivery details and review your fresh Veggicart order before placing it.
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


                <!-- DELIVERY ADDRESS -->
                <div class="vc-checkout-card">

                    <div class="vc-checkout-card-header">

                        <span class="vc-checkout-card-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>

                        <div>
                            <span>Step 02</span>
                            <h2>Delivery Address</h2>
                        </div>

                    </div>


                    <div class="vc-checkout-form-grid">

                        <div class="vc-form-group vc-full-width">

                            <label>Address <span>*</span></label>

                            <div class="vc-input-wrap">
                                <i class="fa-solid fa-house"></i>

                                <input
                                    type="text"
                                    name="address"
                                    placeholder="House number, building, street"
                                    required>
                            </div>

                        </div>


                        <div class="vc-form-group vc-full-width">

                            <label>Apartment / Landmark</label>

                            <div class="vc-input-wrap">
                                <i class="fa-solid fa-map-pin"></i>

                                <input
                                    type="text"
                                    name="landmark"
                                    placeholder="Apartment, landmark or nearby location">
                            </div>

                        </div>


                        <div class="vc-form-group">

                            <label>City <span>*</span></label>

                            <div class="vc-input-wrap">
                                <i class="fa-solid fa-city"></i>

                                <input
                                    type="text"
                                    name="city"
                                    placeholder="Enter city"
                                    required>
                            </div>

                        </div>


                        <div class="vc-form-group">

                            <label>State <span>*</span></label>

                            <div class="vc-input-wrap">

                                <i class="fa-solid fa-location-arrow"></i>

                                <select name="state" required>

                                    <option value="">Select State</option>

                                    <option>Uttarakhand</option>
                                    <option>Uttar Pradesh</option>
                                    <option>Delhi</option>
                                    <option>Haryana</option>
                                    <option>Punjab</option>
                                    <option>Rajasthan</option>
                                    <option>Maharashtra</option>

                                </select>

                            </div>

                        </div>


                        <div class="vc-form-group">

                            <label>PIN Code <span>*</span></label>

                            <div class="vc-input-wrap">

                                <i class="fa-solid fa-location-crosshairs"></i>

                                <input
                                    type="text"
                                    name="pincode"
                                    placeholder="Enter PIN code"
                                    maxlength="6"
                                    required>

                            </div>

                        </div>


                        <div class="vc-form-group">

                            <label>Address Type</label>

                            <div class="vc-input-wrap">

                                <i class="fa-solid fa-building"></i>

                                <select name="address_type">

                                    <option>Home</option>
                                    <option>Office</option>
                                    <option>Other</option>

                                </select>

                            </div>

                        </div>


                        <div class="vc-form-group vc-full-width">

                            <label>Delivery Instructions</label>

                            <textarea
                                name="instructions"
                                placeholder="Example: Please call before delivery, leave order at reception, etc."></textarea>

                        </div>

                    </div>

                </div>


                <!-- DELIVERY METHOD -->
                <div class="vc-checkout-card">

                    <div class="vc-checkout-card-header">

                        <span class="vc-checkout-card-icon">
                            <i class="fa-solid fa-truck-fast"></i>
                        </span>

                        <div>
                            <span>Step 03</span>
                            <h2>Delivery Method</h2>
                        </div>

                    </div>


                    <div class="vc-delivery-options">


                        <label class="vc-delivery-option active">

                            <input
                                type="radio"
                                name="delivery"
                                value="standard"
                                checked>

                            <span class="vc-option-radio"></span>


                            <span class="vc-delivery-icon">
                                <i class="fa-solid fa-truck"></i>
                            </span>


                            <span class="vc-delivery-content">

                                <strong>Standard Delivery</strong>

                                <small>
                                    Delivery within 2–4 hours
                                </small>

                            </span>


                            <span class="vc-delivery-price">
                                FREE
                            </span>

                        </label>


                        <label class="vc-delivery-option">

                            <input
                                type="radio"
                                name="delivery"
                                value="express">

                            <span class="vc-option-radio"></span>


                            <span class="vc-delivery-icon">
                                <i class="fa-solid fa-bolt"></i>
                            </span>


                            <span class="vc-delivery-content">

                                <strong>Express Delivery</strong>

                                <small>
                                    Priority delivery within 60–90 minutes
                                </small>

                            </span>


                            <span class="vc-delivery-price">
                                ₹49
                            </span>

                        </label>

                    </div>

                </div>


                <!-- PAYMENT -->
                <div class="vc-checkout-card">

                    <div class="vc-checkout-card-header">

                        <span class="vc-checkout-card-icon">
                            <i class="fa-regular fa-credit-card"></i>
                        </span>

                        <div>
                            <span>Step 04</span>
                            <h2>Payment Method</h2>
                        </div>

                    </div>


                    <div class="vc-payment-options">


                        <!-- COD -->
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
                                    Pay when your fresh order arrives
                                </small>

                            </span>

                        </label>


                        <!-- UPI -->
                        <label class="vc-payment-option">

                            <input
                                type="radio"
                                name="payment"
                                value="upi">

                            <span class="vc-payment-radio"></span>


                            <span class="vc-payment-icon">
                                <i class="fa-solid fa-mobile-screen-button"></i>
                            </span>


                            <span class="vc-payment-content">

                                <strong>UPI Payment</strong>

                                <small>
                                    Google Pay, PhonePe, Paytm & other UPI apps
                                </small>

                            </span>

                        </label>


                        <!-- CARD -->
                        <label class="vc-payment-option">

                            <input
                                type="radio"
                                name="payment"
                                value="card">

                            <span class="vc-payment-radio"></span>


                            <span class="vc-payment-icon">
                                <i class="fa-regular fa-credit-card"></i>
                            </span>


                            <span class="vc-payment-content">

                                <strong>Debit / Credit Card</strong>

                                <small>
                                    Secure online card payment
                                </small>

                            </span>

                        </label>

                    </div>

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


                    <!-- PRODUCT -->
                    <div class="vc-order-product">

                        <div class="vc-order-product-image">

                            <img
                                src="https://images.unsplash.com/photo-1447175008436-054170c2e979?auto=format&fit=crop&w=400&q=80"
                                alt="Fresh Tomato">

                            <span>1</span>

                        </div>


                        <div class="vc-order-product-content">

                            <h3>Fresh Tomatoes</h3>

                            <small>
                                500 g
                            </small>

                        </div>


                        <strong>
                            ₹60
                        </strong>

                    </div>


                    <!-- PRODUCT -->
                    <div class="vc-order-product">

                        <div class="vc-order-product-image">

                            <img
                                src="https://images.unsplash.com/photo-1582515073490-39981397c445?auto=format&fit=crop&w=400&q=80"
                                alt="Fresh Carrot">

                            <span>2</span>

                        </div>


                        <div class="vc-order-product-content">

                            <h3>Fresh Carrots</h3>

                            <small>
                                1 Kg
                            </small>

                        </div>


                        <strong>
                            ₹160
                        </strong>

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
                        <strong>₹220</strong>
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
                            -₹20
                        </strong>
                    </div>


                    <div class="vc-summary-line"></div>


                    <div class="vc-checkout-total">

                        <div>

                            <span>
                                Total Amount
                            </span>

                            <small>
                                Inclusive of all charges
                            </small>

                        </div>


                        <strong>
                            ₹200
                        </strong>

                    </div>


                    <!-- PLACE ORDER -->
                    <button
                        type="submit"
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