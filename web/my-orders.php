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
                    <strong id="vcOrdersTotal">0</strong>
                </div>
            </div>


            <div class="vc-summary-card">
                <div class="vc-summary-icon vc-processing-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>

                <div>
                    <span>Processing</span>
                    <strong id="vcOrdersProcessing">0</strong>
                </div>
            </div>


            <div class="vc-summary-card">
                <div class="vc-summary-icon vc-shipping-icon">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>

                <div>
                    <span>On The Way</span>
                    <strong id="vcOrdersOnWay">0</strong>
                </div>
            </div>


            <div class="vc-summary-card">
                <div class="vc-summary-icon vc-delivered-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <div>
                    <span>Delivered</span>
                    <strong id="vcOrdersDelivered">0</strong>
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


        <!-- Orders are rendered from GET /orders -->
        <div class="vc-no-orders" id="vcNoOrders" style="display:none;">

            <div class="vc-no-order-icon">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>

            <h2>No Orders Found</h2>

            <p>
                We couldn't find any orders matching your search.
            </p>

            <a href="product.php">
                Continue Shopping
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </div>

</section>


<?php include('footer.php'); ?>