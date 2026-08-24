<?php include('header.php'); ?>

<!-- ================================
VEGIICART ORDER DETAILS & TRACKING (live API)
================================ -->

<section class="vc-order-page">

    <div class="vc-order-container">

        <div class="vc-order-breadcrumb">
            <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
            <span><i class="fa-solid fa-chevron-right"></i></span>
            <a href="my-orders.php">My Orders</a>
            <span><i class="fa-solid fa-chevron-right"></i></span>
            <strong>Order Details</strong>
        </div>

        <div class="vc-order-head">
            <div class="vc-order-head-left">
                <span class="vc-order-label">Order Details</span>
                <h1>Order <span id="vcTrackOrderNo">—</span></h1>
                <p id="vcTrackPlaced">Loading…</p>
            </div>
            <div class="vc-order-head-right">
                <div class="vc-order-status" id="vcTrackStatusBadge">
                    <i class="fa-solid fa-clock"></i>
                    <span id="vcTrackStatusText">—</span>
                </div>
                <a href="order-details.php" class="vc-invoice-btn" id="vcTrackDetailsLink">
                    <i class="fa-solid fa-file-lines"></i>
                    Full Order Details
                </a>
            </div>
        </div>

        <div class="vc-premium-card vc-tracking-card">
            <div class="vc-card-title">
                <div>
                    <span class="vc-small-heading">Live Order Status</span>
                    <h2>Track Your Order</h2>
                </div>
                <div class="vc-estimated-delivery">
                    <i class="fa-solid fa-truck-fast"></i>
                    <div>
                        <small id="vcTrackEtaLabel">Expected Delivery</small>
                        <strong id="vcTrackEta">—</strong>
                    </div>
                </div>
            </div>

            <div class="vc-track-progress" id="vcTrackProgress">
                <div class="vc-track-step" data-step="placed">
                    <div class="vc-track-icon"><i class="fa-solid fa-receipt"></i></div>
                    <div class="vc-track-content">
                        <strong>Order Placed</strong>
                        <span data-sub>—</span>
                    </div>
                </div>
                <div class="vc-track-step" data-step="preparing">
                    <div class="vc-track-icon"><i class="fa-solid fa-basket-shopping"></i></div>
                    <div class="vc-track-content">
                        <strong>Preparing</strong>
                        <span data-sub>Waiting</span>
                    </div>
                </div>
                <div class="vc-track-step" data-step="out_for_delivery">
                    <div class="vc-track-icon"><i class="fa-solid fa-truck-fast"></i></div>
                    <div class="vc-track-content">
                        <strong>Out for Delivery</strong>
                        <span data-sub>Coming soon</span>
                    </div>
                </div>
                <div class="vc-track-step" data-step="delivered">
                    <div class="vc-track-icon"><i class="fa-solid fa-house-circle-check"></i></div>
                    <div class="vc-track-content">
                        <strong>Delivered</strong>
                        <span data-sub>Pending</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="vc-order-main-grid">

            <div class="vc-order-left">

                <div class="vc-premium-card">
                    <div class="vc-card-title">
                        <div>
                            <span class="vc-small-heading">Your Basket</span>
                            <h2>Order Items</h2>
                        </div>
                        <span class="vc-item-count" id="vcTrackItemCount">0 Items</span>
                    </div>
                    <div class="vc-order-products" id="vcTrackItems">
                        <p>Loading items…</p>
                    </div>
                </div>

                <div class="vc-premium-card">
                    <div class="vc-card-title">
                        <div>
                            <span class="vc-small-heading">Delivery Information</span>
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

            <aside class="vc-order-right">

                <div class="vc-premium-card vc-summary-card">
                    <div class="vc-card-title">
                        <div>
                            <span class="vc-small-heading">Payment Summary</span>
                            <h2>Price Details</h2>
                        </div>
                    </div>
                    <div class="vc-price-list">
                        <div>
                            <span>Item Total</span>
                            <strong id="vcTrackSubtotal">—</strong>
                        </div>
                        <div>
                            <span>Delivery Charges</span>
                            <strong id="vcTrackFee">—</strong>
                        </div>
                        <div id="vcTrackDiscountRow" hidden>
                            <span>Discount</span>
                            <strong class="vc-discount" id="vcTrackDiscount">—</strong>
                        </div>
                    </div>
                    <div class="vc-total-price">
                        <span>Total Amount</span>
                        <strong id="vcTrackTotal">—</strong>
                    </div>
                </div>

                <div class="vc-premium-card">
                    <div class="vc-card-title">
                        <div>
                            <span class="vc-small-heading">Transaction</span>
                            <h2>Payment Information</h2>
                        </div>
                    </div>
                    <div class="vc-payment-info">
                        <div class="vc-payment-icon">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <small>Payment Method</small>
                            <h4 id="vcTrackPayMethod">—</h4>
                            <span class="vc-payment-success" id="vcTrackPayStatus">
                                <i class="fa-solid fa-clock"></i>
                                <span id="vcTrackPayStatusText">Pending</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="vc-support-card">
                    <div class="vc-support-icon">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <h3>Need Help With Your Order?</h3>
                    <p>
                        Our VeggiiCart support team is ready to help you
                        with delivery, payment or product issues.
                    </p>
                    <a href="contact.php">
                        Contact Support
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            </aside>

        </div>

        <div class="vc-order-actions">
            <a href="products.php" class="vc-continue-shopping">
                <i class="fa-solid fa-arrow-left"></i>
                Continue Shopping
            </a>
            <div>
                <button type="button" class="vc-secondary-action" id="vcTrackReorderBtn">
                    <i class="fa-solid fa-rotate"></i>
                    Buy Again
                </button>
                <a href="contact.php" class="vc-primary-action">
                    <i class="fa-solid fa-headset"></i>
                    Get Help
                </a>
            </div>
        </div>

    </div>

</section>

<?php include('footer.php'); ?>
