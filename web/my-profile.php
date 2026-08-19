<?php include('header.php'); ?>

<!-- =========================
     VEGIICART - MY PROFILE
========================= -->

<section class="vg-profile-page">

    <div class="vg-profile-container">

        <!-- Page Heading -->
        <div class="vg-profile-heading">

            <div>
                <span class="vg-profile-label">
                    My Account
                </span>

                <h1>My Profile</h1>

                <p>
                    Manage your personal information, contact details
                    and delivery preferences.
                </p>
            </div>

            <a href="my-account.php" class="vg-back-dashboard">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Dashboard
            </a>

        </div>


        <div class="vg-profile-layout">


            <!-- =====================
                 LEFT SIDEBAR
            ====================== -->

            <aside class="vg-profile-sidebar">

                <!-- Profile Card -->
                <div class="vg-user-card">

                    <div class="vg-user-avatar">

                        <span>SM</span>

                        <button type="button"
                                class="vg-avatar-edit"
                                aria-label="Change profile photo">

                            <i class="fa-solid fa-camera"></i>

                        </button>

                    </div>

                    <h3>—</h3>

                    <p>—</p>

                    <span class="vg-verified-badge" id="vcProfileKycBadge">
                        <i class="fa-solid fa-clock"></i>
                        Pending verification
                    </span>

                </div>


                <!-- Navigation -->
                <nav class="vg-profile-nav">

                    <a href="my-account.php">

                        <span>
                            <i class="fa-solid fa-table-columns"></i>
                        </span>

                        Dashboard

                    </a>


                    <a href="my-profile.php"
                       class="active">

                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>

                        My Profile

                    </a>


                    <a href="my-orders.php">

                        <span>
                            <i class="fa-solid fa-box"></i>
                        </span>

                        My Orders

                    </a>


                    <a href="wishlist.php">

                        <span>
                            <i class="fa-regular fa-heart"></i>
                        </span>

                        Wishlist

                    </a>


                    <a href="addresses.php">

                        <span>
                            <i class="fa-solid fa-location-dot"></i>
                        </span>

                        Addresses

                    </a>


                    <a href="change-password.php">

                        <span>
                            <i class="fa-solid fa-lock"></i>
                        </span>

                        Change Password

                    </a>


                    <a href="logout.php"
                       class="vg-logout-link">

                        <span>
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </span>

                        Logout

                    </a>

                </nav>

            </aside>


            <!-- =====================
                 RIGHT CONTENT
            ====================== -->

            <main class="vg-profile-content">


                <!-- PERSONAL INFORMATION -->

                <div class="vg-profile-card">

                    <div class="vg-card-header">

                        <div>

                            <span class="vg-card-icon">
                                <i class="fa-regular fa-user"></i>
                            </span>

                            <div>
                                <h2>Personal Information</h2>

                                <p>
                                    Update your basic account details.
                                </p>
                            </div>

                        </div>

                    </div>


                    <form class="vg-profile-form">

                        <div class="vg-form-grid">

                            <div class="vg-form-group">

                                <label>
                                    First / owner name
                                </label>

                                <div class="vg-input-wrap">

                                    <i class="fa-regular fa-user"></i>

                                    <input type="text"
                                           name="owner_name"
                                           value=""
                                           placeholder="Enter full name">

                                </div>

                            </div>


                            <div class="vg-form-group">

                                <label>
                                    Business name
                                </label>

                                <div class="vg-input-wrap">

                                    <i class="fa-regular fa-user"></i>

                                    <input type="text"
                                           name="business_name"
                                           value=""
                                           placeholder="Enter business name">

                                </div>

                            </div>


                            <div class="vg-form-group">

                                <label>
                                    Email Address
                                </label>

                                <div class="vg-input-wrap">

                                    <i class="fa-regular fa-envelope"></i>

                                    <input type="email"
                                           name="email"
                                           value=""
                                           placeholder="Enter email">

                                </div>

                            </div>


                            <div class="vg-form-group">

                                <label>
                                    Mobile Number
                                </label>

                                <div class="vg-input-wrap">

                                    <i class="fa-solid fa-phone"></i>

                                    <input type="tel"
                                           name="mobile"
                                           value=""
                                           placeholder="Enter mobile number"
                                           readonly>

                                </div>

                            </div>


                            <div class="vg-form-group">

                                <label>
                                    Date of Birth
                                </label>

                                <div class="vg-input-wrap">

                                    <i class="fa-regular fa-calendar"></i>

                                    <input type="date">

                                </div>

                            </div>


                            <div class="vg-form-group">

                                <label>
                                    Gender
                                </label>

                                <div class="vg-input-wrap">

                                    <i class="fa-solid fa-venus-mars"></i>

                                    <select>

                                        <option>Select Gender</option>

                                        <option>Male</option>

                                        <option>Female</option>

                                        <option>Other</option>

                                    </select>

                                </div>

                            </div>

                        </div>


                        <div class="vg-form-actions">

                            <button type="submit"
                                    class="vg-save-btn">

                                <i class="fa-solid fa-check"></i>

                                Save Changes

                            </button>

                        </div>

                    </form>

                </div>


                <!-- DEFAULT ADDRESS -->

                <div class="vg-profile-card">

                    <div class="vg-card-header">

                        <div>

                            <span class="vg-card-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>

                            <div>

                                <h2>Default Delivery Address</h2>

                                <p>
                                    Your primary address for faster checkout.
                                </p>

                            </div>

                        </div>


                        <a href="manage-address.php"
                           class="vg-card-edit-link">

                            Manage Addresses

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>


                    <div class="vg-address-card" id="vcProfileAddressCard">
                        <p class="vc-live-empty">Loading address…</p>
                    </div>

                </div>


                <!-- ACCOUNT SECURITY -->

                <div class="vg-profile-card vg-security-card">

                    <div class="vg-card-header">

                        <div>

                            <span class="vg-card-icon">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>

                            <div>

                                <h2>Account Security</h2>

                                <p>
                                    Keep your Vegiicart account secure.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="vg-security-row">

                        <div class="vg-security-info">

                            <span>
                                <i class="fa-solid fa-key"></i>
                            </span>

                            <div>

                                <h3>Password</h3>

                                <p>
                                    Last updated a few months ago
                                </p>

                            </div>

                        </div>


                        <a href="change-password.php"
                           class="vg-outline-btn">

                            Change Password

                        </a>

                    </div>


                    <div class="vg-security-row">

                        <div class="vg-security-info">

                            <span>
                                <i class="fa-solid fa-mobile-screen-button"></i>
                            </span>

                            <div>

                                <h3>Mobile Verification</h3>

                                <p>
                                    Your mobile number is verified.
                                </p>

                            </div>

                        </div>


                        <span class="vg-status-success">

                            <i class="fa-solid fa-circle-check"></i>

                            Verified

                        </span>

                    </div>

                </div>


                <!-- QUICK ACCOUNT LINKS -->

                <div class="vg-quick-account-grid">


                    <a href="my-orders.php"
                       class="vg-quick-account-card">

                        <span class="vg-quick-icon">

                            <i class="fa-solid fa-box-open"></i>

                        </span>

                        <div>

                            <h3>My Orders</h3>

                            <p>
                                Track and manage your purchases
                            </p>

                        </div>

                        <i class="fa-solid fa-arrow-right vg-quick-arrow"></i>

                    </a>


                    <a href="wishlist.php"
                       class="vg-quick-account-card">

                        <span class="vg-quick-icon">

                            <i class="fa-regular fa-heart"></i>

                        </span>

                        <div>

                            <h3>My Wishlist</h3>

                            <p>
                                View your saved fresh products
                            </p>

                        </div>

                        <i class="fa-solid fa-arrow-right vg-quick-arrow"></i>

                    </a>


                </div>

            </main>

        </div>

    </div>

</section>

<?php include('footer.php'); ?>