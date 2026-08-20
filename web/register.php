<?php include('header.php'); ?>

<section class="vc-signup-page">

    <div class="vc-signup-container">

        <div class="vc-signup-layout">

            <!-- =====================================
                 LEFT VISUAL PANEL
            ====================================== -->
            <div class="vc-signup-visual">

                <div class="vc-signup-overlay"></div>

                <div class="vc-signup-visual-content">

                    <span class="vc-signup-kicker">
                        Join Veggicart
                    </span>

                    <h1>
                        Create Your Fresh Shopping Account
                    </h1>

                    <p>
                        Sign up to save favourites, manage your orders,
                        track deliveries and enjoy a faster shopping experience.
                    </p>


                    <div class="vc-signup-benefits">

                        <div class="vc-signup-benefit">

                            <span>
                                <i class="fa-solid fa-leaf"></i>
                            </span>

                            <div>
                                <strong>Fresh Everyday</strong>
                                <small>Quality fruits and vegetables</small>
                            </div>

                        </div>


                        <div class="vc-signup-benefit">

                            <span>
                                <i class="fa-solid fa-heart"></i>
                            </span>

                            <div>
                                <strong>Save Your Favourites</strong>
                                <small>Build and manage your wishlist</small>
                            </div>

                        </div>


                        <div class="vc-signup-benefit">

                            <span>
                                <i class="fa-solid fa-box"></i>
                            </span>

                            <div>
                                <strong>Track Your Orders</strong>
                                <small>Stay updated from order to delivery</small>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================
                 RIGHT SIGNUP FORM
            ====================================== -->
            <div class="vc-signup-form-wrap">

                <div class="vc-signup-form-head">

                    <span class="vc-signup-icon">
                        <i class="fa-solid fa-user-plus"></i>
                    </span>

                    <div>
                        <span>New Customer</span>
                        <h2>Create Account</h2>
                    </div>

                </div>


                <p class="vc-signup-intro">
                    Fill in your details below to create your Veggicart account.
                </p>


                <form action="register-process.php"
                      method="post"
                      class="vc-signup-form">


                    <!-- NAME -->
                    <div class="vc-signup-field">

                        <label for="vcSignupName">
                            Full Name <span>*</span>
                        </label>

                        <div class="vc-signup-input">

                            <i class="fa-regular fa-user"></i>

                            <input
                                type="text"
                                id="vcSignupName"
                                name="name"
                                placeholder="Enter your full name"
                                autocomplete="name"
                                required>

                        </div>

                    </div>


                    <!-- EMAIL -->
                    <div class="vc-signup-field">

                        <label for="vcSignupEmail">
                            Email Address <span>*</span>
                        </label>

                        <div class="vc-signup-input">

                            <i class="fa-regular fa-envelope"></i>

                            <input
                                type="email"
                                id="vcSignupEmail"
                                name="email"
                                placeholder="Enter your email address"
                                autocomplete="email"
                                required>

                        </div>

                    </div>


                    <!-- PHONE -->
                    <div class="vc-signup-field">

                        <label for="vcSignupPhone">
                            Mobile Number <span>*</span>
                        </label>

                        <div class="vc-signup-input">

                            <i class="fa-solid fa-phone"></i>

                            <input
                                type="tel"
                                id="vcSignupPhone"
                                name="phone"
                                placeholder="Enter your mobile number"
                                autocomplete="tel"
                                required>

                        </div>

                    </div>


                    <!-- PASSWORD -->
                    <div class="vc-signup-field">

                        <label for="vcSignupPassword">
                            Password <span>*</span>
                        </label>

                        <div class="vc-signup-input vc-signup-password-wrap">

                            <i class="fa-solid fa-lock"></i>

                            <input
                                type="password"
                                id="vcSignupPassword"
                                name="password"
                                placeholder="Create a password"
                                autocomplete="new-password"
                                required>

                            <button
                                type="button"
                                class="vc-signup-password-toggle"
                                data-target="vcSignupPassword"
                                aria-label="Show password">

                                <i class="fa-regular fa-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- CONFIRM PASSWORD -->
                    <div class="vc-signup-field">

                        <label for="vcSignupConfirmPassword">
                            Confirm Password <span>*</span>
                        </label>

                        <div class="vc-signup-input vc-signup-password-wrap">

                            <i class="fa-solid fa-shield-halved"></i>

                            <input
                                type="password"
                                id="vcSignupConfirmPassword"
                                name="confirm_password"
                                placeholder="Confirm your password"
                                autocomplete="new-password"
                                required>

                            <button
                                type="button"
                                class="vc-signup-password-toggle"
                                data-target="vcSignupConfirmPassword"
                                aria-label="Show password">

                                <i class="fa-regular fa-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- TERMS -->
                    <label class="vc-signup-terms">

                        <input
                            type="checkbox"
                            name="terms"
                            value="1"
                            required>

                        <span class="vc-signup-checkmark"></span>

                        <span>
                            I agree to the
                            <a href="terms-and-conditions.php">Terms & Conditions</a>
                            and
                            <a href="privacy-policy.php">Privacy Policy</a>.
                        </span>

                    </label>


                    <!-- SUBMIT -->
                    <button
                        type="submit"
                        class="vc-signup-submit">

                        <span>
                            Create My Account
                        </span>

                        <i class="fa-solid fa-arrow-right"></i>

                    </button>


                    <!-- DIVIDER -->
                    <div class="vc-signup-divider">

                        <span>Already have an account?</span>

                    </div>


                    <!-- LOGIN CTA -->
                    <a href="login.php"
                       class="vc-signup-login-btn">

                        <i class="fa-solid fa-right-to-bracket"></i>

                        Login to Your Account

                    </a>

                </form>


                <!-- SECURITY -->
                <div class="vc-signup-security">

                    <span>
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>

                    <div>
                        <strong>Your information is secure</strong>

                        <small>
                            We protect your personal details and account information.
                        </small>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<?php include('footer.php'); ?>