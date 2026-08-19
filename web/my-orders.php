<?php include('header.php'); ?>


<!-- ================================
     VEGIICART MY ORDERS PAGE
================================ -->

<section class="vc-orders-page">

    <div class="vc-orders-container">

        <!-- Page Heading -->
        <div class="vc-orders-heading">

            <div class="vc-orders-heading-left">
                <span class="vc-orders-small-title">
                    <i class="fa-solid fa-bag-shopping"></i>
                    Your Purchases
                </span>

                <h1>My Orders</h1>

                <p>
                    View your recent orders, track deliveries and manage
                    your purchases from one place.
                </p>
            </div>

            <div class="vc-orders-breadcrumb">
                <a href="index.php">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>My Orders</span>
            </div>

        </div>


        <!-- Order Summary -->
        <div class="vc-orders-summary">

            <div class="vc-summary-card">
                <div class="vc-summary-icon">
                    <i class="fa-solid fa-box"></i>
                </div>

                <div>
                    <span>Total Orders</span>
                    <strong>12</strong>
                </div>
            </div>


            <div class="vc-summary-card">
                <div class="vc-summary-icon vc-processing-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>

                <div>
                    <span>Processing</span>
                    <strong>02</strong>
                </div>
            </div>


            <div class="vc-summary-card">
                <div class="vc-summary-icon vc-shipping-icon">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>

                <div>
                    <span>On The Way</span>
                    <strong>01</strong>
                </div>
            </div>


            <div class="vc-summary-card">
                <div class="vc-summary-icon vc-delivered-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <div>
                    <span>Delivered</span>
                    <strong>09</strong>
                </div>
            </div>

        </div>


        <!-- Filter Area -->
        <div class="vc-orders-filter">

            <div class="vc-order-tabs">

                <button class="vc-order-tab active" data-filter="all">
                    All Orders
                </button>

                <button class="vc-order-tab" data-filter="processing">
                    Processing
                </button>

                <button class="vc-order-tab" data-filter="shipped">
                    Shipped
                </button>

                <button class="vc-order-tab" data-filter="delivered">
                    Delivered
                </button>

                <button class="vc-order-tab" data-filter="cancelled">
                    Cancelled
                </button>

            </div>


            <div class="vc-order-search">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="vcOrderSearch"
                    placeholder="Search Order ID..."
                >
            </div>

        </div>


        <!-- =======================
             ORDER CARD 1
        ======================== -->

        <article class="vc-order-card"
                 data-status="shipped"
                 data-order="#VGC10258">

            <div class="vc-order-card-header">

                <div class="vc-order-id">
                    <span>Order ID</span>
                    <strong>#VGC10258</strong>
                </div>


                <div class="vc-order-date">
                    <i class="fa-regular fa-calendar"></i>

                    <div>
                        <span>Order Date</span>
                        <strong>10 Aug 2026</strong>
                    </div>
                </div>


                <span class="vc-order-status shipped">
                    <i class="fa-solid fa-truck-fast"></i>
                    Shipped
                </span>

            </div>


            <div class="vc-order-body">

                <!-- Products -->
                <div class="vc-order-products">

                    <div class="vc-order-product">

                        <img
                            src="https://images.unsplash.com/photo-1447175008436-054170c2e979?auto=format&fit=crop&w=250&q=80"
                            alt="Fresh Carrots"
                        >

                        <div>
                            <h3>Fresh Organic Carrots</h3>
                            <p>1 Kg × 1</p>
                            <strong>₹89</strong>
                        </div>

                    </div>


                    <div class="vc-order-product">

                        <img
                            src="https://images.unsplash.com/photo-1561136594-7f68413baa99?auto=format&fit=crop&w=250&q=80"
                            alt="Fresh Tomatoes"
                        >

                        <div>
                            <h3>Farm Fresh Tomatoes</h3>
                            <p>1 Kg × 2</p>
                            <strong>₹120</strong>
                        </div>

                    </div>


                    <div class="vc-order-product">

                        <img
                            src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=250&q=80"
                            alt="Fresh Potatoes"
                        >

                        <div>
                            <h3>Fresh Potatoes</h3>
                            <p>2 Kg × 1</p>
                            <strong>₹110</strong>
                        </div>

                    </div>

                </div>


                <!-- Delivery Progress -->
                <div class="vc-order-tracking">

                    <div class="vc-tracking-top">

                        <div>
                            <span>Delivery Status</span>

                            <h4>
                                <i class="fa-solid fa-truck"></i>
                                Your order is on the way
                            </h4>
                        </div>

                        <div class="vc-delivery-date">
                            Expected Delivery
                            <strong>12 Aug 2026</strong>
                        </div>

                    </div>


                    <div class="vc-progress">

                        <div class="vc-progress-line">
                            <span class="vc-progress-active"></span>
                        </div>


                        <div class="vc-progress-step completed">
                            <div class="vc-step-icon">
                                <i class="fa-solid fa-check"></i>
                            </div>

                            <span>Confirmed</span>
                        </div>


                        <div class="vc-progress-step completed">
                            <div class="vc-step-icon">
                                <i class="fa-solid fa-check"></i>
                            </div>

                            <span>Packed</span>
                        </div>


                        <div class="vc-progress-step current">
                            <div class="vc-step-icon">
                                <i class="fa-solid fa-truck"></i>
                            </div>

                            <span>Shipped</span>
                        </div>


                        <div class="vc-progress-step">
                            <div class="vc-step-icon">
                                <i class="fa-solid fa-house"></i>
                            </div>

                            <span>Delivered</span>
                        </div>

                    </div>

                </div>

            </div>


            <div class="vc-order-footer">

                <div class="vc-order-payment">

                    <p>
                        <span>Payment</span>
                        <strong>Cash on Delivery</strong>
                    </p>

                    <p>
                        <span>Total</span>
                        <strong class="vc-order-total">₹319</strong>
                    </p>

                </div>


                <div class="vc-order-actions">

                    <a href="order-details.php"
                       class="vc-order-btn vc-btn-outline">

                        <i class="fa-regular fa-eye"></i>
                        View Details
                    </a>


                    <a href="order-tracking.php"
                       class="vc-order-btn vc-btn-green">

                        <i class="fa-solid fa-location-dot"></i>
                        Track Order
                    </a>

                </div>

            </div>

        </article>


        <!-- =======================
             ORDER CARD 2
        ======================== -->

        <article class="vc-order-card"
                 data-status="delivered"
                 data-order="#VGC10198">

            <div class="vc-order-card-header">

                <div class="vc-order-id">
                    <span>Order ID</span>
                    <strong>#VGC10198</strong>
                </div>


                <div class="vc-order-date">

                    <i class="fa-regular fa-calendar"></i>

                    <div>
                        <span>Order Date</span>
                        <strong>02 Aug 2026</strong>
                    </div>

                </div>


                <span class="vc-order-status delivered">
                    <i class="fa-solid fa-circle-check"></i>
                    Delivered
                </span>

            </div>


            <div class="vc-order-body">

                <div class="vc-order-products">

                    <div class="vc-order-product">

                        <img
                            src="https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=250&q=80"
                            alt="Fresh Lemons"
                        >

                        <div>
                            <h3>Fresh Yellow Lemons</h3>
                            <p>500 gm × 2</p>
                            <strong>₹100</strong>
                        </div>

                    </div>


                    <div class="vc-order-product">

                        <img
                            src="https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?auto=format&fit=crop&w=250&q=80"
                            alt="Fresh Mango"
                        >

                        <div>
                            <h3>Premium Fresh Mango</h3>
                            <p>1 Kg × 1</p>
                            <strong>₹199</strong>
                        </div>

                    </div>

                </div>


                <div class="vc-delivered-box">

                    <div class="vc-delivered-check">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <div>
                        <span>Delivered Successfully</span>
                        <h4>Your order was delivered on 04 Aug 2026</h4>
                    </div>

                </div>

            </div>


            <div class="vc-order-footer">

                <div class="vc-order-payment">

                    <p>
                        <span>Payment</span>
                        <strong>Online Payment</strong>
                    </p>

                    <p>
                        <span>Total</span>
                        <strong class="vc-order-total">₹299</strong>
                    </p>

                </div>


                <div class="vc-order-actions">

                    <a href="order-details.php"
                       class="vc-order-btn vc-btn-outline">

                        <i class="fa-regular fa-eye"></i>
                        View Details

                    </a>


                    <a href="#"
                       class="vc-order-btn vc-btn-light">

                        <i class="fa-solid fa-file-arrow-down"></i>
                        Invoice

                    </a>


                    <a href="#"
                       class="vc-order-btn vc-btn-green">

                        <i class="fa-solid fa-rotate"></i>
                        Buy Again

                    </a>

                </div>

            </div>

        </article>


        <!-- =======================
             ORDER CARD 3
        ======================== -->

        <article class="vc-order-card"
                 data-status="processing"
                 data-order="#VGC10271">

            <div class="vc-order-card-header">

                <div class="vc-order-id">
                    <span>Order ID</span>
                    <strong>#VGC10271</strong>
                </div>


                <div class="vc-order-date">

                    <i class="fa-regular fa-calendar"></i>

                    <div>
                        <span>Order Date</span>
                        <strong>11 Aug 2026</strong>
                    </div>

                </div>


                <span class="vc-order-status processing">

                    <i class="fa-solid fa-clock"></i>
                    Processing

                </span>

            </div>


            <div class="vc-order-body">

                <div class="vc-order-products">

                    <div class="vc-order-product">

                        <img
                            src="https://images.unsplash.com/photo-1615485500704-8e990f9900f7?auto=format&fit=crop&w=250&q=80"
                            alt="Fresh Ginger"
                        >

                        <div>
                            <h3>Fresh Ginger</h3>
                            <p>500 gm × 1</p>
                            <strong>₹79</strong>
                        </div>

                    </div>


                    <div class="vc-order-product">

                        <img
                            src="https://images.unsplash.com/photo-1615477550927-6ec1e6a36f06?auto=format&fit=crop&w=250&q=80"
                            alt="Fresh Garlic"
                        >

                        <div>
                            <h3>Premium Garlic</h3>
                            <p>500 gm × 1</p>
                            <strong>₹109</strong>
                        </div>

                    </div>

                </div>


                <div class="vc-processing-box">

                    <div class="vc-processing-animation">
                        <i class="fa-solid fa-box-open"></i>
                    </div>

                    <div>
                        <span>We're preparing your order</span>
                        <h4>Your fresh products will be packed shortly.</h4>
                    </div>

                </div>

            </div>


            <div class="vc-order-footer">

                <div class="vc-order-payment">

                    <p>
                        <span>Payment</span>
                        <strong>UPI</strong>
                    </p>

                    <p>
                        <span>Total</span>
                        <strong class="vc-order-total">₹188</strong>
                    </p>

                </div>


                <div class="vc-order-actions">

                    <a href="order-details.php"
                       class="vc-order-btn vc-btn-outline">

                        <i class="fa-regular fa-eye"></i>
                        View Details

                    </a>


                    <a href="#"
                       class="vc-order-btn vc-btn-danger">

                        <i class="fa-solid fa-xmark"></i>
                        Cancel Order

                    </a>

                </div>

            </div>

        </article>


        <!-- No Order Found -->
        <div class="vc-no-orders" id="vcNoOrders">

            <div class="vc-no-order-icon">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>

            <h2>No Orders Found</h2>

            <p>
                We couldn't find any orders matching your search.
            </p>

            <a href="products.php">
                Continue Shopping
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </div>

</section>


<?php include('footer.php'); ?>