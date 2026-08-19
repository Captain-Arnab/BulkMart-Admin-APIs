<?php include('header.php'); ?>

<section class="vc-login-page">

    <div class="vc-login-container">

        <div class="vc-login-layout">

            <!-- =====================================
                 LEFT BRAND / BENEFIT PANEL
            ====================================== -->
            <div class="vc-login-visual">

                <div class="vc-login-visual-overlay"></div>

                <div class="vc-login-visual-content">

                    <span class="vc-login-kicker">
                        Welcome Back
                    </span>

                    <h1>
                        Fresh Shopping Starts Here
                    </h1>

                    <p>
                        Login to manage your orders, save favourite products,
                        track deliveries and enjoy a faster Veggicart shopping experience.
                    </p>


                    <div class="vc-login-benefits">

                        <div class="vc-login-benefit">

                            <span>
                                <i class="fa-solid fa-leaf"></i>
                            </span>

                            <div>
                                <strong>Fresh Products</strong>
                                <small>Quality fruits and vegetables</small>
                            </div>

                        </div>


                        <div class="vc-login-benefit">

                            <span>
                                <i class="fa-solid fa-truck-fast"></i>
                            </span>

                            <div>
                                <strong>Quick Delivery</strong>
                                <small>Convenient doorstep delivery</small>
                            </div>

                        </div>


                        <div class="vc-login-benefit">

                            <span>
                                <i class="fa-solid fa-basket-shopping"></i>
                            </span>

                            <div>
                                <strong>Easy Reordering</strong>
                                <small>Access your previous orders anytime</small>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================
                 RIGHT LOGIN FORM
            ====================================== -->
            <div class="vc-login-form-wrap">

                <div class="vc-login-form-head">

                    <span class="vc-login-icon">
                        <i class="fa-regular fa-user"></i>
                    </span>

                    <div>
                        <span>Customer Account</span>
                        <h2>Login to Veggicart</h2>
                    </div>

                </div>


                <p class="vc-login-intro">
                    Enter your registered email address or mobile number
                    and password to continue.
                </p>


                <form action="login-process.php"
                      method="post"
                      class="vc-login-form">


                    <!-- EMAIL / PHONE -->
                    <div class="vc-login-field">

                        <label for="vcLoginUser">
                            Email Address / Mobile Number
                            <span>*</span>
                        </label>

                        <div class="vc-login-input">

                            <i class="fa-regular fa-user"></i>

                            <input
                                type="text"
                                id="vcLoginUser"
                                name="username"
                                placeholder="Enter email or mobile number"
                                autocomplete="username"
                                required>

                        </div>

                    </div>


                    <!-- PASSWORD -->
                    <div class="vc-login-field">

                        <label for="vcLoginPassword">
                            Password
                            <span>*</span>
                        </label>

                        <div class="vc-login-input vc-login-password-wrap">

                            <i class="fa-solid fa-lock"></i>

                            <input
                                type="password"
                                id="vcLoginPassword"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required>


                            <button
                                type="button"
                                class="vc-password-toggle"
                                id="vcPasswordToggle"
                                aria-label="Show password">

                                <i class="fa-regular fa-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- REMEMBER + FORGOT -->
                    <div class="vc-login-options">

                        <label class="vc-remember-me">

                            <input
                                type="checkbox"
                                name="remember"
                                value="1">

                            <span class="vc-checkmark"></span>

                            Remember me

                        </label>


                        <a href="forgot-password.php">
                            Forgot Password?
                        </a>

                    </div>


                    <!-- LOGIN -->
                    <button
                        type="submit"
                        class="vc-login-submit">

                        <span>
                            Login to My Account
                        </span>

                        <i class="fa-solid fa-arrow-right"></i>

                    </button>


                    <!-- DIVIDER -->
                    <div class="vc-login-divider">

                        <span>New to Veggicart?</span>

                    </div>


                    <!-- REGISTER -->
                    <a
                        href="register.php"
                        class="vc-login-register">

                        <i class="fa-regular fa-user"></i>

                        Create New Account

                    </a>


                </form>


                <!-- SECURITY -->
                <div class="vc-login-security">

                    <span>
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>

                    <div>
                        <strong>Secure Customer Login</strong>

                        <small>
                            Your account information is protected and kept private.
                        </small>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<?php include('footer.php'); ?>