<?php include('header.php'); ?>

<!-- =========================================
     VEGIICART - ORDER DETAILS PAGE (live API)
========================================= -->

<section class="vg-order-details-page">

    <div class="vg-order-details-container">

        <div class="vg-order-details-heading">
            <div>
                <span class="vg-order-label">My Orders</span>
                <h1 id="vgOrderTitle">Order Details</h1>
                <p id="vgOrderSubtitle">
                    Track your order, view product details and manage your purchase.
                </p>
            </div>
            <a href="my-orders.php" class="vg-order-back-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Back to My Orders
            </a>
        </div>

        <!-- ORDER TOP CARD -->
        <div class="vg-order-main-card">
            <div class="vg-order-top">
                <div class="vg-order-number-box">
                    <span class="vg-order-icon">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </span>
                    <div>
                        <span>Order Number</span>
                        <h2 id="vgOrderNumber">—</h2>
                    </div>
                </div>
                <div class="vg-order-status placed" id="vgOrderStatus">
                    <i class="fa-solid fa-clock"></i>
                    <span id="vgOrderStatusText">—</span>
                </div>
            </div>

            <div class="vg-order-meta-grid">
                <div class="vg-order-meta-item">
                    <span><i class="fa-regular fa-calendar"></i></span>
                    <div>
                        <small>Order Date</small>
                        <strong id="vgOrderDate">—</strong>
                    </div>
                </div>
                <div class="vg-order-meta-item">
                    <span><i class="fa-solid fa-truck-fast"></i></span>
                    <div>
                        <small id="vgOrderEtaLabel">Expected Delivery</small>
                        <strong id="vgOrderEta">—</strong>
                    </div>
                </div>
                <div class="vg-order-meta-item">
                    <span><i class="fa-solid fa-indian-rupee-sign"></i></span>
                    <div>
                        <small>Order Total</small>
                        <strong id="vgOrderTotalMeta">—</strong>
                    </div>
                </div>
                <div class="vg-order-meta-item">
                    <span><i class="fa-solid fa-credit-card"></i></span>
                    <div>
                        <small>Payment</small>
                        <strong id="vgOrderPayMeta">—</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- TRACKING -->
        <div class="vg-order-section">
            <div class="vg-section-heading">
                <div>
                    <span class="vg-section-icon">
                        <i class="fa-solid fa-truck"></i>
                    </span>
                    <div>
                        <h2>Order Tracking</h2>
                        <p id="vgTrackCopy">Loading tracking…</p>
                    </div>
                </div>
                <span class="vg-tracking-id" id="vgTrackingId">—</span>
            </div>

            <div class="vg-tracking-wrapper" id="vgTrackingSteps">
                <div class="vg-track-step" data-step="placed">
                    <div class="vg-track-circle"><i class="fa-solid fa-check"></i></div>
                    <div class="vg-track-info">
                        <strong>Order Placed</strong>
                        <span data-date>—</span>
                        <small data-time></small>
                    </div>
                </div>
                <div class="vg-track-line" data-line="1"></div>
                <div class="vg-track-step" data-step="confirmed">
                    <div class="vg-track-circle"><i class="fa-solid fa-check"></i></div>
                    <div class="vg-track-info">
                        <strong>Confirmed</strong>
                        <span data-date>—</span>
                        <small data-time></small>
                    </div>
                </div>
                <div class="vg-track-line" data-line="2"></div>
                <div class="vg-track-step" data-step="delivery_date_set">
                    <div class="vg-track-circle"><i class="fa-solid fa-check"></i></div>
                    <div class="vg-track-info">
                        <strong>Delivery Scheduled</strong>
                        <span data-date>—</span>
                        <small data-time></small>
                    </div>
                </div>
                <div class="vg-track-line" data-line="3"></div>
                <div class="vg-track-step" data-step="out_for_delivery">
                    <div class="vg-track-circle"><i class="fa-solid fa-truck"></i></div>
                    <div class="vg-track-info">
                        <strong>Out for Delivery</strong>
                        <span data-date>—</span>
                        <small data-time></small>
                    </div>
                </div>
                <div class="vg-track-line" data-line="4"></div>
                <div class="vg-track-step" data-step="delivered">
                    <div class="vg-track-circle"><i class="fa-solid fa-house-circle-check"></i></div>
                    <div class="vg-track-info">
                        <strong>Delivered</strong>
                        <span data-date>—</span>
                        <small data-time></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="vg-order-content-grid">

            <div class="vg-order-left">

                <!-- PRODUCTS -->
                <div class="vg-order-section">
                    <div class="vg-section-heading">
                        <div>
                            <span class="vg-section-icon">
                                <i class="fa-solid fa-basket-shopping"></i>
                            </span>
                            <div>
                                <h2>Items in Your Order</h2>
                                <p id="vgItemsCopy">Loading items…</p>
                            </div>
                        </div>
                    </div>

                    <div class="vg-order-product-list" id="vgOrderItems">
                        <div class="vg-order-loading">Loading…</div>
                    </div>

                    <div class="vg-order-reorder-box">
                        <div>
                            <i class="fa-solid fa-rotate-right"></i>
                            <span>
                                <strong>Loved this order?</strong>
                                <small>Add all available items to your cart again.</small>
                            </span>
                        </div>
                        <button type="button" class="vg-reorder-btn" id="vgReorderBtn">
                            <i class="fa-solid fa-cart-plus"></i>
                            Reorder All
                        </button>
                    </div>
                </div>

                <!-- DELIVERY ADDRESS -->
                <div class="vg-order-section">
                    <div class="vg-section-heading">
                        <div>
                            <span class="vg-section-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <div>
                                <h2>Delivery Address</h2>
                                <p>Address used for this order.</p>
                            </div>
                        </div>
                    </div>
                    <div class="vg-delivery-address" id="vcOrderAddress">
                        <h3 id="vcOrderAddrName">—</h3>
                        <p id="vcOrderAddrText">—</p>
                    </div>
                </div>

                <!-- PAYMENT -->
                <div class="vg-order-section">
                    <div class="vg-section-heading">
                        <div>
                            <span class="vg-section-icon">
                                <i class="fa-solid fa-credit-card"></i>
                            </span>
                            <div>
                                <h2>Payment Information</h2>
                                <p>Payment method used for this order.</p>
                            </div>
                        </div>
                    </div>
                    <div class="vg-payment-information">
                        <div class="vg-payment-icon">
                            <i class="fa-solid fa-wallet" id="vgPayIcon"></i>
                        </div>
                        <div class="vg-payment-details">
                            <span>Payment Method</span>
                            <strong id="vgPayMethod">—</strong>
                            <small id="vgPayNote">Cash on Delivery at handover</small>
                        </div>
                        <span class="vg-payment-status" id="vgPayStatus">
                            <i class="fa-solid fa-clock"></i>
                            <span id="vgPayStatusText">Pending</span>
                        </span>
                    </div>
                </div>

            </div>

            <aside class="vg-order-right">

                <div class="vg-order-summary-card">
                    <div class="vg-summary-heading">
                        <h2>Order Summary</h2>
                        <span id="vgSummaryItemCount">0 Items</span>
                    </div>
                    <div class="vg-summary-row">
                        <span>Items Subtotal</span>
                        <strong id="vgSumSubtotal">—</strong>
                    </div>
                    <div class="vg-summary-row">
                        <span>Delivery Charge</span>
                        <strong id="vgSumFee">—</strong>
                    </div>
                    <div class="vg-summary-row discount" id="vgSumDiscountRow" hidden>
                        <span>Coupon Discount</span>
                        <strong id="vgSumDiscount">—</strong>
                    </div>
                    <div class="vg-summary-divider"></div>
                    <div class="vg-summary-total">
                        <div>
                            <span>Total Amount</span>
                            <small>Inclusive of all charges</small>
                        </div>
                        <strong id="vgSumTotal">—</strong>
                    </div>
                    <div class="vg-summary-saving" id="vgSumSaving" hidden>
                        <i class="fa-solid fa-tag"></i>
                        <span id="vgSumSavingText"></span>
                    </div>
                </div>

                <div class="vg-order-action-card">
                    <div class="vg-action-card-icon">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <h3>Order Invoice</h3>
                    <p>Download your invoice for this purchase.</p>
                    <button type="button" class="vg-invoice-btn" id="vgInvoiceBtn">
                        <i class="fa-solid fa-download"></i>
                        Download Invoice
                    </button>
                </div>

                <div class="vg-order-action-card" id="vgCancelCard" hidden>
                    <div class="vg-action-card-icon help">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <h3>Cancel Order</h3>
                    <p>Cancel before the order goes out for delivery.</p>
                    <button type="button" class="vg-support-btn" id="vgCancelBtn">
                        <i class="fa-solid fa-xmark"></i>
                        Cancel this order
                    </button>
                </div>

                <div class="vg-order-action-card">
                    <div class="vg-action-card-icon help">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <h3>Need Help?</h3>
                    <p>Have an issue with your order or delivery?</p>
                    <a href="contact.php" class="vg-support-btn">
                        <i class="fa-regular fa-comments"></i>
                        Contact Support
                    </a>
                </div>

                <div class="vg-order-small-links">
                    <a href="order-details-tracking.php" id="vgTrackLink">
                        <i class="fa-solid fa-location-crosshairs"></i>
                        Open tracking view
                    </a>
                    <a href="my-orders.php">
                        <i class="fa-solid fa-list"></i>
                        All my orders
                    </a>
                </div>

            </aside>

        </div>

    </div>

</section>

<?php include('footer.php'); ?>
