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
                        <h3>Rahul Sharma</h3>
                        <p>rahul@example.com</p>
                    </div>

                </div>


                <nav class="vc-account-menu">

                    <a href="my-account.php" class="active">
                        <i class="fa-solid fa-table-columns"></i>
                        Dashboard
                    </a>

                    <a href="my-orders.php">
                        <i class="fa-solid fa-bag-shopping"></i>
                        My Orders
                        <span class="vc-menu-count">12</span>
                    </a>

                    <a href="wishlist.php">
                        <i class="fa-regular fa-heart"></i>
                        Wishlist
                        <span class="vc-menu-count">5</span>
                    </a>

                    <a href="addresses.php">
                        <i class="fa-solid fa-location-dot"></i>
                        My Addresses
                    </a>

                    <a href="profile.php">
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

                        <span>Hello Rahul 👋</span>

                        <h2>Welcome to your Vegiicart account</h2>

                        <p>
                            From here you can quickly check your latest orders,
                            manage your delivery addresses and update your account.
                        </p>

                        <a href="products.php" class="vc-shop-btn">
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
                            <strong>12</strong>
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
                            <strong>03</strong>
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
                            <strong>05</strong>
                            <small>Saved products</small>
                        </div>

                    </a>


                    <a href="addresses.php"
                       class="vc-account-stat-card">

                        <div class="vc-stat-icon vc-stat-address">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>

                        <div>
                            <span>Addresses</span>
                            <strong>02</strong>
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


                    <div class="vc-recent-orders">

                        <!-- Order -->
                        <div class="vc-recent-order">

                            <div class="vc-order-product-image">

                                <img
                                    src="https://images.unsplash.com/photo-1447175008436-054170c2e979?auto=format&fit=crop&w=220&q=80"
                                    alt="Fresh vegetables"
                                >

                            </div>

                            <div class="vc-recent-order-info">

                                <span class="vc-order-number">
                                    Order #VGC10258
                                </span>

                                <h3>Fresh Vegetable Combo</h3>

                                <p>
                                    3 Items • 10 Aug 2026
                                </p>

                            </div>

                            <div class="vc-recent-order-price">
                                <span>Total</span>
                                <strong>₹319</strong>
                            </div>

                            <span class="vc-dashboard-status shipped">
                                Shipped
                            </span>

                            <a href="order-details.php"
                               class="vc-view-order">

                                View Order

                            </a>

                        </div>


                        <!-- Order -->
                        <div class="vc-recent-order">

                            <div class="vc-order-product-image">

                                <img
                                    src="https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?auto=format&fit=crop&w=220&q=80"
                                    alt="Fresh fruits"
                                >

                            </div>

                            <div class="vc-recent-order-info">

                                <span class="vc-order-number">
                                    Order #VGC10198
                                </span>

                                <h3>Premium Fruit Basket</h3>

                                <p>
                                    4 Items • 02 Aug 2026
                                </p>

                            </div>

                            <div class="vc-recent-order-price">
                                <span>Total</span>
                                <strong>₹599</strong>
                            </div>

                            <span class="vc-dashboard-status delivered">
                                Delivered
                            </span>

                            <a href="order-details.php"
                               class="vc-view-order">

                                View Order

                            </a>

                        </div>


                        <!-- Order -->
                        <div class="vc-recent-order">

                            <div class="vc-order-product-image">

                                <img
                                    src="https://images.unsplash.com/photo-1615485500704-8e990f9900f7?auto=format&fit=crop&w=220&q=80"
                                    alt="Fresh grocery products"
                                >

                            </div>

                            <div class="vc-recent-order-info">

                                <span class="vc-order-number">
                                    Order #VGC10271
                                </span>

                                <h3>Daily Kitchen Essentials</h3>

                                <p>
                                    5 Items • 11 Aug 2026
                                </p>

                            </div>

                            <div class="vc-recent-order-price">
                                <span>Total</span>
                                <strong>₹428</strong>
                            </div>

                            <span class="vc-dashboard-status processing">
                                Processing
                            </span>

                            <a href="order-details.php"
                               class="vc-view-order">

                                View Order

                            </a>

                        </div>

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


                        <div class="vc-address-details">

                            <strong>Rahul Sharma</strong>

                            <p>
                                24 Green Park Road,<br>
                                Rishikesh, Uttarakhand 249201
                            </p>

                            <p>
                                <i class="fa-solid fa-phone"></i>
                                +91 98765 43210
                            </p>

                        </div>


                        <a href="addresses.php"
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
                            <strong>Rahul Sharma</strong>
                        </div>

                        <div class="vc-account-info-row">
                            <span>Email</span>
                            <strong>rahul@example.com</strong>
                        </div>

                        <div class="vc-account-info-row">
                            <span>Phone</span>
                            <strong>+91 98765 43210</strong>
                        </div>


                        <a href="profile.php"
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