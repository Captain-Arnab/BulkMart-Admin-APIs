<?php include('header.php'); ?>

<section class="vc-login-page">

    <div class="vc-login-container">

        <div class="vc-login-layout">

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
                            <span><i class="fa-solid fa-leaf"></i></span>
                            <div>
                                <strong>Fresh Products</strong>
                                <small>Quality fruits and vegetables</small>
                            </div>
                        </div>

                        <div class="vc-login-benefit">
                            <span><i class="fa-solid fa-truck-fast"></i></span>
                            <div>
                                <strong>Quick Delivery</strong>
                                <small>Convenient doorstep delivery</small>
                            </div>
                        </div>

                        <div class="vc-login-benefit">
                            <span><i class="fa-solid fa-basket-shopping"></i></span>
                            <div>
                                <strong>Easy Reordering</strong>
                                <small>Access your previous orders anytime</small>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

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

                <div class="vc-login-mode-switch" role="tablist" aria-label="Login method">
                    <button type="button"
                            class="vc-login-mode is-active"
                            id="vcLoginModeEmail"
                            data-login-mode="email"
                            role="tab"
                            aria-selected="true">
                        <i class="fa-regular fa-envelope"></i>
                        Email &amp; Password
                    </button>
                    <button type="button"
                            class="vc-login-mode"
                            id="vcLoginModeMobile"
                            data-login-mode="mobile"
                            role="tab"
                            aria-selected="false">
                        <i class="fa-solid fa-mobile-screen"></i>
                        Mobile &amp; OTP
                    </button>
                </div>

                <p class="vc-login-intro" id="vcLoginIntro">
                    Enter your registered email and password to continue. No OTP needed.
                </p>

                <form action="#"
                      method="post"
                      class="vc-login-form"
                      id="vcLoginForm"
                      novalidate>

                    <input type="hidden" id="vcLoginMode" name="login_mode" value="email">

                    <div class="vc-login-field" id="vcLoginEmailField">
                        <label for="vcLoginEmail">
                            Email Address
                            <span>*</span>
                        </label>
                        <div class="vc-login-input">
                            <i class="fa-regular fa-envelope"></i>
                            <input
                                type="email"
                                id="vcLoginEmail"
                                name="email"
                                placeholder="Enter your email address"
                                autocomplete="email">
                        </div>
                    </div>

                    <div class="vc-login-field" id="vcLoginMobileField" hidden>
                        <label for="vcLoginMobile">
                            Mobile Number
                            <span>*</span>
                        </label>
                        <div class="vc-login-input">
                            <i class="fa-solid fa-phone"></i>
                            <input
                                type="tel"
                                id="vcLoginMobile"
                                name="mobile"
                                placeholder="Enter your mobile number"
                                autocomplete="tel"
                                inputmode="tel">
                        </div>
                    </div>

                    <div class="vc-login-field" id="vcLoginPasswordField">
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
                                autocomplete="current-password">
                            <button
                                type="button"
                                class="vc-password-toggle"
                                id="vcPasswordToggle"
                                aria-label="Show password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="vc-login-field" id="vcLoginOtpField" hidden>
                        <label for="vcLoginOtp">
                            OTP
                            <span>*</span>
                        </label>
                        <div class="vc-login-input">
                            <i class="fa-solid fa-key"></i>
                            <input
                                type="text"
                                id="vcLoginOtp"
                                name="otp"
                                inputmode="numeric"
                                maxlength="8"
                                placeholder="Enter OTP"
                                autocomplete="one-time-code">
                        </div>
                        <small id="vcOtpHint" class="vc-login-hint"></small>
                        <button type="button" class="vc-login-resend" id="vcLoginResendOtp" hidden>
                            Resend OTP
                        </button>
                    </div>

                    <div class="vc-login-options" id="vcLoginEmailOptions">
                        <label class="vc-remember-me">
                            <input type="checkbox" name="remember" value="1">
                            <span class="vc-checkmark"></span>
                            Remember me
                        </label>
                        <a href="forgot-password.php">Forgot Password?</a>
                    </div>

                    <button type="submit" class="vc-login-submit" id="vcLoginSubmit">
                        <span id="vcLoginSubmitText">Login to My Account</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>

                    <div class="vc-login-divider">
                        <span>New to Veggicart?</span>
                    </div>

                    <a href="register.php" class="vc-login-register">
                        <i class="fa-regular fa-user"></i>
                        Create New Account
                    </a>

                </form>

                <div class="vc-login-security">
                    <span><i class="fa-solid fa-shield-halved"></i></span>
                    <div>
                        <strong>Secure Customer Login</strong>
                        <small>Your account information is protected and kept private.</small>
                    </div>
                </div>

            </div>

        </div>

    </div>

</section>

<?php include('footer.php'); ?>
