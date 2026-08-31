<?php include('header.php'); ?>

<!-- =========================================
     VEGIICART MY ACCOUNT DASHBOARD
========================================= -->

<section class="vc-account-page">

    <div class="vc-account-container">

        <!-- Page Header -->
        <div class="vc-account-header">

            <div>
                <span class="vc-account-tag">
                    <i class="fa-solid fa-user"></i>
                    Customer Account
                </span>

                <h1>My Account</h1>

                <p>
                    Manage your profile, orders, addresses, wishlist and account details.
                </p>
            </div>

            <div class="vc-account-breadcrumb">
                <a href="index.php">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>My Account</span>
            </div>

        </div>


        <div class="vc-account-layout">

            <!-- =================================
                 LEFT SIDEBAR
            ================================== -->

            <aside class="vc-account-sidebar">

                <div class="vc-account-user">

                    <div class="vc-account-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>
                        <span>Welcome Back</span>
                        <h3>—</h3>
                        <p>—</p>
                    </div>

                </div>


                <nav class="vc-account-menu">

                    <a href="account-dashboard.php" class="active">
                        <i class="fa-solid fa-table-columns"></i>
                        Dashboard
                    </a>

                    <a href="my-orders.php">
                        <i class="fa-solid fa-bag-shopping"></i>
                        My Orders
                        <span class="vc-menu-count" id="vcMenuOrderCount">0</span>
                    </a>

                    <a href="wishlist.php">
                        <i class="fa-regular fa-heart"></i>
                        Wishlist
                        <span class="vc-menu-count" id="vcMenuWishCount">0</span>
                    </a>

                    <a href="manage-address.php">
                        <i class="fa-solid fa-location-dot"></i>
                        My Addresses
                    </a>

                    <a href="my-profile.php">
                        <i class="fa-regular fa-user"></i>
                        Profile Details
                    </a>

                    <a href="change-password.php">
                        <i class="fa-solid fa-lock"></i>
                        Change Password
                    </a>

                    <a href="logout.php" class="vc-logout-link">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Logout
                    </a>

                </nav>

            </aside>


            <!-- =================================
                 DASHBOARD CONTENT
            ================================== -->

            <main class="vc-account-main">

                <!-- Welcome -->
                <div class="vc-dashboard-welcome">

                    <div class="vc-welcome-content">

                        <span id="vcDashHello">Hello 👋</span>

                        <h2>Welcome to your Vegiicart account</h2>

                        <p>
                            From here you can quickly check your latest orders,
                            manage your delivery addresses and update your account.
                        </p>

                        <a href="product.php" class="vc-shop-btn">
                            Shop Fresh Products
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                    <div class="vc-welcome-icon">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>

                </div>


                <!-- Account Stats -->
                <div class="vc-account-stats">

                    <a href="my-orders.php" class="vc-account-stat-card">

                        <div class="vc-stat-icon">
                            <i class="fa-solid fa-box"></i>
                        </div>

                        <div>
                            <span>Total Orders</span>
                            <strong id="vcDashOrderCount">0</strong>
                            <small>View all orders</small>
                        </div>

                    </a>


                    <a href="my-orders.php"
                       class="vc-account-stat-card">

                        <div class="vc-stat-icon vc-stat-shipping">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>

                        <div>
                            <span>Active Orders</span>
                            <strong id="vcDashActiveCount">0</strong>
                            <small>Track your orders</small>
                        </div>

                    </a>


                    <a href="wishlist.php"
                       class="vc-account-stat-card">

                        <div class="vc-stat-icon vc-stat-wishlist">
                            <i class="fa-solid fa-heart"></i>
                        </div>

                        <div>
                            <span>Wishlist</span>
                            <strong id="vcDashWishCount">0</strong>
                            <small>Saved products</small>
                        </div>

                    </a>


                    <a href="manage-address.php"
                       class="vc-account-stat-card">

                        <div class="vc-stat-icon vc-stat-address">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>

                        <div>
                            <span>Addresses</span>
                            <strong id="vcDashAddrCount">0</strong>
                            <small>Manage addresses</small>
                        </div>

                    </a>

                </div>


                <!-- Recent Orders -->
                <div class="vc-dashboard-section">

                    <div class="vc-section-heading">

                        <div>
                            <span>Recent Purchases</span>
                            <h2>Latest Orders</h2>
                        </div>

                        <a href="my-orders.php">
                            View All Orders
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>


                    <div class="vc-recent-orders" id="vcDashOrders">
                        <p class="vc-live-empty">Loading orders…</p>
                    </div>

                </div>


                                <!-- Bottom Grid -->
                <div class="vc-dashboard-bottom">

                    <!-- Address -->
                    <div class="vc-dashboard-panel">

                        <div class="vc-panel-heading">

                            <div class="vc-panel-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>

                            <div>
                                <span>Default Address</span>
                                <h3>Delivery Address</h3>
                            </div>

                        </div>


                        <div class="vc-address-details" id="vcDashAddress">

                            <strong>—</strong>

                            <p>No default address saved.</p>

                            <p></p>

                        </div>


                        <a href="manage-address.php"
                           class="vc-panel-link">

                            Manage Address
                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>


                    <!-- Account Details -->
                    <div class="vc-dashboard-panel">

                        <div class="vc-panel-heading">

                            <div class="vc-panel-icon">
                                <i class="fa-regular fa-user"></i>
                            </div>

                            <div>
                                <span>Personal Details</span>
                                <h3>Account Information</h3>
                            </div>

                        </div>


                        <div class="vc-account-info-row">
                            <span>Name</span>
                            <strong id="vcDashInfoName">—</strong>
                        </div>

                        <div class="vc-account-info-row">
                            <span>Email</span>
                            <strong id="vcDashInfoEmail">—</strong>
                        </div>

                        <div class="vc-account-info-row">
                            <span>Phone</span>
                            <strong id="vcDashInfoPhone">—</strong>
                        </div>


                        <a href="my-profile.php"
                           class="vc-panel-link">

                            Edit Profile
                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

            </main>

        </div>

    </div>

</section>

<?php include('footer.php'); ?>