<?php include('header.php'); ?>

<!-- ================================
VEGIICART ORDER DETAILS & TRACKING
================================ -->

<section class="vc-order-page">

    <div class="vc-order-container">

        <!-- Top Breadcrumb -->
        <div class="vc-order-breadcrumb">
            <a href="index.php">
                <i class="fa-solid fa-house"></i>
                Home
            </a>

            <span>
                <i class="fa-solid fa-chevron-right"></i>
            </span>

            <a href="my-orders.php">My Orders</a>

            <span>
                <i class="fa-solid fa-chevron-right"></i>
            </span>

            <strong>Order Details</strong>
        </div>


        <!-- Order Header -->
        <div class="vc-order-head">

            <div class="vc-order-head-left">

                <span class="vc-order-label">
                    Order Details
                </span>

                <h1>
                    Order <span>#VG24081658</span>
                </h1>

                <p>
                    Placed on 11 August 2026 at 10:45 AM
                </p>

            </div>


            <div class="vc-order-head-right">

                <div class="vc-order-status">
                    <i class="fa-solid fa-circle-check"></i>
                    Out for Delivery
                </div>

                <button class="vc-invoice-btn">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    Download Invoice
                </button>

            </div>

        </div>


        <!-- =====================
        DELIVERY TRACKING
        ====================== -->

        <div class="vc-premium-card vc-tracking-card">

            <div class="vc-card-title">

                <div>
                    <span class="vc-small-heading">
                        Live Order Status
                    </span>

                    <h2>Track Your Order</h2>
                </div>

                <div class="vc-estimated-delivery">
                    <i class="fa-solid fa-truck-fast"></i>

                    <div>
                        <small>Estimated Delivery</small>
                        <strong>Today, 12:30 PM – 1:30 PM</strong>
                    </div>
                </div>

            </div>


            <div class="vc-track-progress">

                <!-- Step 1 -->
                <div class="vc-track-step completed">

                    <div class="vc-track-icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>

                    <div class="vc-track-content">
                        <strong>Order Confirmed</strong>
                        <span>11 Aug, 10:45 AM</span>
                    </div>

                </div>


                <!-- Step 2 -->
                <div class="vc-track-step completed">

                    <div class="vc-track-icon">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>

                    <div class="vc-track-content">
                        <strong>Order Packed</strong>
                        <span>11 Aug, 11:15 AM</span>
                    </div>

                </div>


                <!-- Step 3 -->
                <div class="vc-track-step active">

                    <div class="vc-track-icon">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>

                    <div class="vc-track-content">
                        <strong>Out for Delivery</strong>
                        <span>Your fresh order is on the way</span>
                    </div>

                </div>


                <!-- Step 4 -->
                <div class="vc-track-step">

                    <div class="vc-track-icon">
                        <i class="fa-solid fa-house-circle-check"></i>
                    </div>

                    <div class="vc-track-content">
                        <strong>Delivered</strong>
                        <span>Expected today</span>
                    </div>

                </div>

            </div>


            <!-- Delivery Person -->

            <div class="vc-delivery-agent">

                <div class="vc-agent-left">

                    <div class="vc-agent-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>
                        <small>Your Delivery Partner</small>
                        <h4 id="vcTrackingAgent">Assigned after dispatch</h4>

                        <span>
                            <i class="fa-solid fa-star"></i>
                            4.9 Rating
                        </span>
                    </div>

                </div>


                <div class="vc-agent-actions">

                    <a href="tel:+919999999999"
                       class="vc-agent-btn">

                        <i class="fa-solid fa-phone"></i>
                        Call

                    </a>

                    <a href="https://wa.me/919999999999"
                       target="_blank"
                       class="vc-agent-btn vc-whatsapp-btn">

                        <i class="fa-brands fa-whatsapp"></i>
                        WhatsApp

                    </a>

                </div>

            </div>

        </div>


        <!-- =====================
        MAIN ORDER GRID
        ====================== -->

        <div class="vc-order-main-grid">


            <!-- LEFT COLUMN -->

            <div class="vc-order-left">


                <!-- Products -->

                <div class="vc-premium-card">

                    <div class="vc-card-title">

                        <div>
                            <span class="vc-small-heading">
                                Your Basket
                            </span>

                            <h2>Order Items</h2>
                        </div>

                        <span class="vc-item-count">
                            4 Items
                        </span>

                    </div>


                    <div class="vc-order-products">


                        <!-- Product 1 -->

                        <div class="vc-order-product">

                            <div class="vc-product-image">

                                <img src="https://images.unsplash.com/photo-1561136594-7f68413baa99?auto=format&fit=crop&w=400&q=85"
                                     alt="Fresh Tomatoes">

                            </div>

                            <div class="vc-product-info">

                                <span class="vc-product-category">
                                    Fresh Vegetables
                                </span>

                                <h3>Farm Fresh Tomatoes</h3>

                                <div class="vc-product-meta">

                                    <span>
                                        <i class="fa-solid fa-weight-scale"></i>
                                        1 kg
                                    </span>

                                    <span>
                                        Qty: 2
                                    </span>

                                </div>

                            </div>


                            <div class="vc-product-price">

                                <span>₹70</span>
                                <strong>₹58</strong>

                            </div>

                        </div>


                        <!-- Product 2 -->

                        <div class="vc-order-product">

                            <div class="vc-product-image">

                                <img src="https://images.unsplash.com/photo-1445282768818-728615cc910a?auto=format&fit=crop&w=400&q=85"
                                     alt="Fresh Carrots">

                            </div>

                            <div class="vc-product-info">

                                <span class="vc-product-category">
                                    Fresh Vegetables
                                </span>

                                <h3>Premium Carrots</h3>

                                <div class="vc-product-meta">

                                    <span>
                                        <i class="fa-solid fa-weight-scale"></i>
                                        500 g
                                    </span>

                                    <span>
                                        Qty: 1
                                    </span>

                                </div>

                            </div>


                            <div class="vc-product-price">

                                <span>₹55</span>
                                <strong>₹45</strong>

                            </div>

                        </div>


                        <!-- Product 3 -->

                        <div class="vc-order-product">

                            <div class="vc-product-image">

                                <img src="https://images.unsplash.com/photo-1615485290382-441e4d049cb5?auto=format&fit=crop&w=400&q=85"
                                     alt="Fresh Green Vegetables">

                            </div>

                            <div class="vc-product-info">

                                <span class="vc-product-category">
                                    Green Vegetables
                                </span>

                                <h3>Fresh Green Capsicum</h3>

                                <div class="vc-product-meta">

                                    <span>
                                        <i class="fa-solid fa-weight-scale"></i>
                                        500 g
                                    </span>

                                    <span>
                                        Qty: 1
                                    </span>

                                </div>

                            </div>


                            <div class="vc-product-price">

                                <span>₹85</span>
                                <strong>₹69</strong>

                            </div>

                        </div>


                        <!-- Product 4 -->

                        <div class="vc-order-product">

                            <div class="vc-product-image">

                                <img src="https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=400&q=85"
                                     alt="Fresh Lemon">

                            </div>

                            <div class="vc-product-info">

                                <span class="vc-product-category">
                                    Fresh Produce
                                </span>

                                <h3>Farm Fresh Lemons</h3>

                                <div class="vc-product-meta">

                                    <span>
                                        <i class="fa-solid fa-weight-scale"></i>
                                        250 g
                                    </span>

                                    <span>
                                        Qty: 1
                                    </span>

                                </div>

                            </div>


                            <div class="vc-product-price">

                                <span>₹45</span>
                                <strong>₹35</strong>

                            </div>

                        </div>


                    </div>

                </div>


                <!-- Delivery Address -->

                <div class="vc-premium-card">

                    <div class="vc-card-title">

                        <div>
                            <span class="vc-small-heading">
                                Delivery Information
                            </span>

                            <h2>Delivery Address</h2>
                        </div>

                    </div>


                    <div class="vc-address-box">

                        <div class="vc-address-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>

                        <div class="vc-address-content" id="vcTrackAddress">

                            <div class="vc-address-title">

                                <h3 id="vcTrackAddrName">—</h3>

                                <span id="vcTrackAddrLabel">—</span>

                            </div>

                            <p id="vcTrackAddrText">—</p>

                        </div>

                    </div>

                </div>


            </div>


            <!-- RIGHT COLUMN -->

            <aside class="vc-order-right">


                <!-- Price Details -->

                <div class="vc-premium-card vc-summary-card">

                    <div class="vc-card-title">

                        <div>
                            <span class="vc-small-heading">
                                Payment Summary
                            </span>

                            <h2>Price Details</h2>
                        </div>

                    </div>


                    <div class="vc-price-list">

                        <div>
                            <span>Item Total</span>
                            <strong>₹252</strong>
                        </div>

                        <div>
                            <span>Delivery Charges</span>
                            <strong class="vc-free">FREE</strong>
                        </div>

                        <div>
                            <span>Handling Fee</span>
                            <strong>₹10</strong>
                        </div>

                        <div>
                            <span>Discount</span>
                            <strong class="vc-discount">− ₹30</strong>
                        </div>

                    </div>


                    <div class="vc-total-price">

                        <span>Total Amount</span>

                        <strong>₹232</strong>

                    </div>


                    <div class="vc-saving-box">

                        <i class="fa-solid fa-tags"></i>

                        <span>
                            You saved
                            <strong>₹83</strong>
                            on this order
                        </span>

                    </div>

                </div>


                <!-- Payment -->

                <div class="vc-premium-card">

                    <div class="vc-card-title">

                        <div>
                            <span class="vc-small-heading">
                                Transaction
                            </span>

                            <h2>Payment Information</h2>
                        </div>

                    </div>


                    <div class="vc-payment-info">

                        <div class="vc-payment-icon">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>

                        <div>

                            <small>Payment Method</small>

                            <h4>UPI Payment</h4>

                            <span class="vc-payment-success">
                                <i class="fa-solid fa-circle-check"></i>
                                Payment Successful
                            </span>

                        </div>

                    </div>

                </div>


                <!-- Help -->

                <div class="vc-support-card">

                    <div class="vc-support-icon">
                        <i class="fa-solid fa-headset"></i>
                    </div>

                    <h3>Need Help With Your Order?</h3>

                    <p>
                        Our Vegiicart support team is ready to help you
                        with delivery, payment or product issues.
                    </p>

                    <a href="contact.php">
                        Contact Support
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>


            </aside>

        </div>


        <!-- Bottom Actions -->

        <div class="vc-order-actions">

            <a href="shop.php"
               class="vc-continue-shopping">

                <i class="fa-solid fa-arrow-left"></i>
                Continue Shopping

            </a>


            <div>

                <button class="vc-secondary-action">
                    <i class="fa-solid fa-rotate"></i>
                    Buy Again
                </button>

                <button class="vc-primary-action">
                    <i class="fa-solid fa-headset"></i>
                    Get Help
                </button>

            </div>

        </div>


    </div>

</section>

<?php include('footer.php'); ?>