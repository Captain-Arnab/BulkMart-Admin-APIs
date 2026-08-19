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
            <div class="vc-notification-list" id="vcNotificationList">
                <p class="vc-live-empty">Loading notifications…</p>
            </div>

        </div>
    </section>

</main>

<?php include('footer.php'); ?>