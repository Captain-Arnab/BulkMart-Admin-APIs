<?php include('header.php'); ?>

<main class="vc-notifications-page">

    <section class="vc-notifications-hero">
        <div class="vc-notifications-container">

            <div class="vc-notifications-hero-inner">

                <div>
                    <span class="vc-notifications-eyebrow">
                        <i class="fa-solid fa-bell"></i>
                        Your Updates
                    </span>

                    <h1>
                        Notifications
                    </h1>

                    <p>
                        Stay updated with your orders, deliveries, offers,
                        stock alerts, verification updates and support activity.
                    </p>
                </div>

                <button
                    type="button"
                    class="vc-mark-all-btn"
                    id="vcMarkAllRead">

                    <i class="fa-solid fa-check-double"></i>
                    Mark All as Read
                </button>

            </div>

        </div>
    </section>


    <section class="vc-notifications-main">
        <div class="vc-notifications-container">

            <!-- TOP SUMMARY -->
            <div class="vc-notification-summary">

                <div>
                    <span>
                        <i class="fa-solid fa-bell"></i>
                    </span>

                    <div>
                        <small>Total Notifications</small>
                        <strong id="vcTotalNotifications">8</strong>
                    </div>
                </div>


                <div>
                    <span>
                        <i class="fa-solid fa-envelope"></i>
                    </span>

                    <div>
                        <small>Unread</small>
                        <strong id="vcUnreadNotifications">5</strong>
                    </div>
                </div>


                <div>
                    <span>
                        <i class="fa-solid fa-circle-check"></i>
                    </span>

                    <div>
                        <small>Read</small>
                        <strong id="vcReadNotifications">3</strong>
                    </div>
                </div>

            </div>


            <!-- FILTERS -->
            <div class="vc-notification-filters">

                <div class="vc-filter-group">

                    <span class="vc-filter-title">
                        Categories
                    </span>

                    <div class="vc-filter-buttons">

                        <button
                            type="button"
                            class="active"
                            data-filter="all">
                            All
                        </button>

                        <button
                            type="button"
                            data-filter="orders">
                            Orders
                        </button>

                        <button
                            type="button"
                            data-filter="offers">
                            Offers
                        </button>

                        <button
                            type="button"
                            data-filter="account">
                            Account
                        </button>

                        <button
                            type="button"
                            data-filter="support">
                            Support
                        </button>

                    </div>

                </div>


                <div class="vc-filter-group vc-read-filter-group">

                    <span class="vc-filter-title">
                        Status
                    </span>

                    <div class="vc-filter-buttons">

                        <button
                            type="button"
                            class="active"
                            data-read-filter="all">
                            All
                        </button>

                        <button
                            type="button"
                            data-read-filter="unread">
                            Unread
                        </button>

                        <button
                            type="button"
                            data-read-filter="read">
                            Read
                        </button>

                    </div>

                </div>

            </div>


            <!-- NOTIFICATION LIST -->
            <div class="vc-notification-list"
                 id="vcNotificationList">


                <!-- ORDER UPDATE -->
                <article
                    class="vc-notification-item unread"
                    data-category="orders"
                    data-read="unread">

                    <div class="vc-notification-icon order">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>

                    <div class="vc-notification-content">

                        <div class="vc-notification-top">

                            <span class="vc-notification-category">
                                Order Update
                            </span>

                            <span class="vc-notification-time">
                                10 min ago
                            </span>

                        </div>

                        <h3>
                            Your order #VC1025 has been confirmed
                        </h3>

                        <p>
                            We have received your order and your fresh grocery
                            items are now being prepared.
                        </p>

                        <div class="vc-notification-actions">

                            <a href="order-details.php">
                                View Order
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>

                            <button
                                type="button"
                                class="vc-mark-read-btn">

                                <i class="fa-solid fa-check"></i>
                                Mark as Read
                            </button>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="vc-notification-delete"
                        aria-label="Delete notification">

                        <i class="fa-solid fa-trash"></i>
                    </button>

                </article>


                <!-- DELIVERY UPDATE -->
                <article
                    class="vc-notification-item unread"
                    data-category="orders"
                    data-read="unread">

                    <div class="vc-notification-icon delivery">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>

                    <div class="vc-notification-content">

                        <div class="vc-notification-top">

                            <span class="vc-notification-category">
                                Delivery Update
                            </span>

                            <span class="vc-notification-time">
                                35 min ago
                            </span>

                        </div>

                        <h3>
                            Your order is out for delivery
                        </h3>

                        <p>
                            Order #VC1019 is on the way and is expected to
                            arrive today.
                        </p>

                        <div class="vc-notification-actions">

                            <a href="order-details.php">
                                Track Delivery
                                <i class="fa-solid fa-location-arrow"></i>
                            </a>

                            <button
                                type="button"
                                class="vc-mark-read-btn">

                                <i class="fa-solid fa-check"></i>
                                Mark as Read
                            </button>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="vc-notification-delete">

                        <i class="fa-solid fa-trash"></i>
                    </button>

                </article>


                <!-- OFFER -->
                <article
                    class="vc-notification-item unread"
                    data-category="offers"
                    data-read="unread">

                    <div class="vc-notification-icon offer">
                        <i class="fa-solid fa-tags"></i>
                    </div>

                    <div class="vc-notification-content">

                        <div class="vc-notification-top">

                            <span class="vc-notification-category">
                                Special Offer
                            </span>

                            <span class="vc-notification-time">
                                1 hour ago
                            </span>

                        </div>

                        <h3>
                            Save 25% on fresh vegetables today
                        </h3>

                        <p>
                            A limited-time discount is available on selected
                            vegetables and fresh produce.
                        </p>

                        <div class="vc-notification-actions">

                            <a href="offers.php">
                                View Offer
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>

                            <button
                                type="button"
                                class="vc-mark-read-btn">

                                <i class="fa-solid fa-check"></i>
                                Mark as Read
                            </button>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="vc-notification-delete">

                        <i class="fa-solid fa-trash"></i>
                    </button>

                </article>


                <!-- STOCK -->
                <article
                    class="vc-notification-item unread"
                    data-category="offers"
                    data-read="unread">

                    <div class="vc-notification-icon stock">
                        <i class="fa-solid fa-box-open"></i>
                    </div>

                    <div class="vc-notification-content">

                        <div class="vc-notification-top">

                            <span class="vc-notification-category">
                                Stock Availability
                            </span>

                            <span class="vc-notification-time">
                                2 hours ago
                            </span>

                        </div>

                        <h3>
                            Premium Alphonso Mangoes are back in stock
                        </h3>

                        <p>
                            The product you were waiting for is available again.
                            Order before stock runs out.
                        </p>

                        <div class="vc-notification-actions">

                            <a href="product.php">
                                View Product
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>

                            <button
                                type="button"
                                class="vc-mark-read-btn">

                                <i class="fa-solid fa-check"></i>
                                Mark as Read
                            </button>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="vc-notification-delete">

                        <i class="fa-solid fa-trash"></i>
                    </button>

                </article>


                <!-- VERIFICATION -->
                <article
                    class="vc-notification-item unread"
                    data-category="account"
                    data-read="unread">

                    <div class="vc-notification-icon account">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <div class="vc-notification-content">

                        <div class="vc-notification-top">

                            <span class="vc-notification-category">
                                Verification Update
                            </span>

                            <span class="vc-notification-time">
                                3 hours ago
                            </span>

                        </div>

                        <h3>
                            Your business verification has been approved
                        </h3>

                        <p>
                            Your Vegiicart business profile is now verified and
                            eligible for business ordering features.
                        </p>

                        <div class="vc-notification-actions">

                            <a href="business-profile.php">
                                View Business Profile
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>

                            <button
                                type="button"
                                class="vc-mark-read-btn">

                                <i class="fa-solid fa-check"></i>
                                Mark as Read
                            </button>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="vc-notification-delete">

                        <i class="fa-solid fa-trash"></i>
                    </button>

                </article>


                <!-- SUPPORT -->
                <article
                    class="vc-notification-item read"
                    data-category="support"
                    data-read="read">

                    <div class="vc-notification-icon support">
                        <i class="fa-solid fa-headset"></i>
                    </div>

                    <div class="vc-notification-content">

                        <div class="vc-notification-top">

                            <span class="vc-notification-category">
                                Support Ticket
                            </span>

                            <span class="vc-notification-time">
                                Yesterday
                            </span>

                        </div>

                        <h3>
                            Support has replied to ticket #SUP-204
                        </h3>

                        <p>
                            Our support team has responded to your query about
                            your latest delivery.
                        </p>

                        <div class="vc-notification-actions">

                            <a href="support-ticket.php">
                                Open Ticket
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="vc-notification-delete">

                        <i class="fa-solid fa-trash"></i>
                    </button>

                </article>


                <!-- OFFER READ -->
                <article
                    class="vc-notification-item read"
                    data-category="offers"
                    data-read="read">

                    <div class="vc-notification-icon offer">
                        <i class="fa-solid fa-gift"></i>
                    </div>

                    <div class="vc-notification-content">

                        <div class="vc-notification-top">

                            <span class="vc-notification-category">
                                Offers
                            </span>

                            <span class="vc-notification-time">
                                2 days ago
                            </span>

                        </div>

                        <h3>
                            Use coupon FRESH15 and save more
                        </h3>

                        <p>
                            Get an extra 15% discount on eligible fresh produce
                            orders.
                        </p>

                        <div class="vc-notification-actions">

                            <a href="offers.php">
                                Explore Offers
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="vc-notification-delete">

                        <i class="fa-solid fa-trash"></i>
                    </button>

                </article>


                <!-- ORDER READ -->
                <article
                    class="vc-notification-item read"
                    data-category="orders"
                    data-read="read">

                    <div class="vc-notification-icon order">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div class="vc-notification-content">

                        <div class="vc-notification-top">

                            <span class="vc-notification-category">
                                Order Update
                            </span>

                            <span class="vc-notification-time">
                                3 days ago
                            </span>

                        </div>

                        <h3>
                            Order #VC1008 delivered successfully
                        </h3>

                        <p>
                            Your order has been delivered. Thank you for
                            shopping with Vegiicart.
                        </p>

                        <div class="vc-notification-actions">

                            <a href="order-details.php">
                                View Order
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="vc-notification-delete">

                        <i class="fa-solid fa-trash"></i>
                    </button>

                </article>

            </div>


            <!-- EMPTY STATE -->
            <div class="vc-notification-empty"
                 id="vcNotificationEmpty">

                <span>
                    <i class="fa-regular fa-bell-slash"></i>
                </span>

                <h3>No Notifications Found</h3>

                <p>
                    There are no notifications matching the selected filters.
                </p>

            </div>

        </div>
    </section>

</main>

<?php include('footer.php'); ?>