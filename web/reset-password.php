<?php include('header.php'); ?>

<!-- =========================
     VEGIICART - RESET PASSWORD
========================= -->

<section class="vg-reset-page">

    <div class="vg-reset-container">

        <div class="vg-reset-card">

            <!-- LEFT SIDE -->
            <div class="vg-reset-info">

                <span class="vg-reset-tag">
                    Secure Account Recovery
                </span>

                <h1>Create a New Password</h1>

                <p>
                    Choose a strong new password for your Vegiicart account.
                    Make sure it is easy for you to remember but difficult
                    for others to guess.
                </p>


                <div class="vg-reset-security-list">

                    <div class="vg-reset-security-item">

                        <span>
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>

                        <div>
                            <h3>Secure Your Account</h3>
                            <p>
                                Use a unique password that you do not use
                                on other websites.
                            </p>
                        </div>

                    </div>


                    <div class="vg-reset-security-item">

                        <span>
                            <i class="fa-solid fa-key"></i>
                        </span>

                        <div>
                            <h3>Use a Strong Password</h3>
                            <p>
                                Combine uppercase, lowercase, numbers
                                and special characters.
                            </p>
                        </div>

                    </div>


                    <div class="vg-reset-security-item">

                        <span>
                            <i class="fa-solid fa-lock"></i>
                        </span>

                        <div>
                            <h3>Keep It Private</h3>
                            <p>
                                Never share your password or verification
                                code with anyone.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="vg-reset-safe-box">

                    <i class="fa-solid fa-circle-check"></i>

                    <div>
                        <strong>Secure Password Reset</strong>

                        <p>
                            Your new password will replace your old password
                            immediately after successful reset.
                        </p>
                    </div>

                </div>

            </div>


            <!-- RIGHT SIDE -->
            <div class="vg-reset-form-area">

                <div class="vg-reset-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <span class="vg-reset-small-title">
                    Final Step
                </span>

                <h2>Reset Password</h2>

                <p class="vg-reset-description">
                    Enter and confirm your new password below.
                </p>


                <!-- SUCCESS MESSAGE EXAMPLE -->
                <!--
                <div class="vg-reset-message success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>
                        Your password has been reset successfully.
                    </span>
                </div>
                -->


                <!-- ERROR MESSAGE EXAMPLE -->
                <!--
                <div class="vg-reset-message error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>
                        Passwords do not match. Please try again.
                    </span>
                </div>
                -->


                <form class="vg-reset-form"
                      action=""
                      method="post">

                    <!--
                    Use this hidden field with your PHP reset token:

                    <input type="hidden"
                           name="token"
                           value="<?php echo htmlspecialchars($_GET['token']); ?>">
                    -->


                    <!-- NEW PASSWORD -->
                    <div class="vg-reset-group">

                        <label for="newPassword">
                            New Password
                        </label>

                        <div class="vg-reset-input">

                            <i class="fa-solid fa-lock"></i>

                            <input type="password"
                                   id="newPassword"
                                   name="new_password"
                                   placeholder="Enter new password"
                                   minlength="8"
                                   required>

                            <button type="button"
                                    class="vg-password-toggle"
                                    data-target="newPassword"
                                    aria-label="Show or hide password">

                                <i class="fa-regular fa-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- PASSWORD STRENGTH -->
                    <div class="vg-password-strength">

                        <div class="vg-strength-bars">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>

                        <div class="vg-strength-text">
                            Password strength:
                            <strong id="vgStrengthText">
                                Enter Password
                            </strong>
                        </div>

                    </div>


                    <!-- PASSWORD REQUIREMENTS -->
                    <div class="vg-password-rules">

                        <span class="vg-rule"
                              id="ruleLength">

                            <i class="fa-solid fa-circle"></i>
                            At least 8 characters

                        </span>


                        <span class="vg-rule"
                              id="ruleUpper">

                            <i class="fa-solid fa-circle"></i>
                            One uppercase letter

                        </span>


                        <span class="vg-rule"
                              id="ruleNumber">

                            <i class="fa-solid fa-circle"></i>
                            One number

                        </span>


                        <span class="vg-rule"
                              id="ruleSpecial">

                            <i class="fa-solid fa-circle"></i>
                            One special character

                        </span>

                    </div>


                    <!-- CONFIRM PASSWORD -->
                    <div class="vg-reset-group">

                        <label for="confirmPassword">
                            Confirm New Password
                        </label>

                        <div class="vg-reset-input">

                            <i class="fa-solid fa-shield-halved"></i>

                            <input type="password"
                                   id="confirmPassword"
                                   name="confirm_password"
                                   placeholder="Re-enter new password"
                                   minlength="8"
                                   required>

                            <button type="button"
                                    class="vg-password-toggle"
                                    data-target="confirmPassword"
                                    aria-label="Show or hide password">

                                <i class="fa-regular fa-eye"></i>

                            </button>

                        </div>


                        <span class="vg-password-match"
                              id="vgPasswordMatch">
                        </span>

                    </div>


                    <!-- BUTTON -->
                    <button type="submit"
                            class="vg-reset-submit">

                        <i class="fa-solid fa-shield-check"></i>

                        Reset Password

                        <i class="fa-solid fa-arrow-right"></i>

                    </button>

                </form>


                <div class="vg-reset-divider">
                    <span>or</span>
                </div>


                <div class="vg-reset-login">

                    <span>
                        Remember your password?
                    </span>

                    <a href="login.php">
                        <i class="fa-solid fa-arrow-left"></i>
                        Back to Login
                    </a>

                </div>


                <div class="vg-reset-note">

                    <span>
                        <i class="fa-solid fa-circle-info"></i>
                    </span>

                    <p>
                        After resetting your password, you may need to
                        sign in again using your new password.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>

<?php include('footer.php'); ?>