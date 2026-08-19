<?php include('header.php'); ?>


<!-- =========================
     VEGIICART - FORGOT PASSWORD
========================= -->

<section class="vg-forgot-page">

    <div class="vg-forgot-container">

        <div class="vg-forgot-card">

            <!-- LEFT SIDE -->
            <div class="vg-forgot-info">

                <span class="vg-forgot-tag">
                    Account Recovery
                </span>

                <h1>Forgot Your Password?</h1>

                <p>
                    No problem. Enter your registered email address or
                    mobile number and we’ll help you regain access to
                    your Vegiicart account.
                </p>

                <div class="vg-forgot-features">

                    <div class="vg-forgot-feature">
                        <span>
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>

                        <div>
                            <h3>Secure Recovery</h3>
                            <p>
                                Your account recovery information is handled securely.
                            </p>
                        </div>
                    </div>


                    <div class="vg-forgot-feature">
                        <span>
                            <i class="fa-solid fa-envelope-circle-check"></i>
                        </span>

                        <div>
                            <h3>Quick Verification</h3>
                            <p>
                                Receive a password reset link or verification code.
                            </p>
                        </div>
                    </div>


                    <div class="vg-forgot-feature">
                        <span>
                            <i class="fa-solid fa-lock"></i>
                        </span>

                        <div>
                            <h3>Create a New Password</h3>
                            <p>
                                Reset your password and securely continue shopping.
                            </p>
                        </div>
                    </div>

                </div>


                <div class="vg-forgot-help">

                    <i class="fa-solid fa-headset"></i>

                    <div>
                        <span>Need help?</span>
                        <p>
                            Contact Vegiicart customer support for account assistance.
                        </p>
                    </div>

                </div>

            </div>


            <!-- RIGHT SIDE -->
            <div class="vg-forgot-form-area">

                <div class="vg-forgot-icon">
                    <i class="fa-solid fa-key"></i>
                </div>

                <span class="vg-form-small-title">
                    Password Assistance
                </span>

                <h2>Reset Password</h2>

                <p class="vg-form-description">
                    Enter the email address or mobile number associated
                    with your account.
                </p>


                <!-- SUCCESS MESSAGE EXAMPLE -->
                <!--
                <div class="vg-forgot-message success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>
                        Password reset instructions have been sent successfully.
                    </span>
                </div>
                -->


                <!-- ERROR MESSAGE EXAMPLE -->
                <!--
                <div class="vg-forgot-message error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>
                        We couldn't find an account with these details.
                    </span>
                </div>
                -->


                <form class="vg-forgot-form"
                      action=""
                      method="post">

                    <div class="vg-forgot-group">

                        <label for="forgotAccount">
                            Email Address or Mobile Number
                        </label>

                        <div class="vg-forgot-input">

                            <i class="fa-regular fa-envelope"></i>

                            <input type="text"
                                   id="forgotAccount"
                                   name="account"
                                   placeholder="Enter email or mobile number"
                                   required>

                        </div>

                    </div>


                    <button type="submit"
                            class="vg-reset-btn">

                        Send Reset Link

                        <i class="fa-solid fa-arrow-right"></i>

                    </button>

                </form>


                <div class="vg-forgot-divider">

                    <span>or</span>

                </div>


                <div class="vg-login-return">

                    <span>
                        Remember your password?
                    </span>

                    <a href="login.php">
                        <i class="fa-solid fa-arrow-left"></i>
                        Back to Login
                    </a>

                </div>


                <div class="vg-security-note">

                    <span>
                        <i class="fa-solid fa-shield-check"></i>
                    </span>

                    <p>
                        For your security, Vegiicart will never ask you
                        to share your password or OTP with anyone.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<?php include('footer.php'); ?>