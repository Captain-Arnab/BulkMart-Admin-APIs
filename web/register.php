<?php
include('header.php');

$vcBizTypes = [
    ['Retail Shop', 'fa-shop'],
    ['Kirana Store', 'fa-basket-shopping'],
    ['Supermarket', 'fa-cart-shopping'],
    ['Hotel', 'fa-hotel'],
    ['Restaurant', 'fa-utensils'],
    ['Catering Service', 'fa-bowl-food'],
    ['Hostel', 'fa-bed'],
    ['Hospital', 'fa-hospital'],
    ['Corporate Pantry', 'fa-building'],
    ['Juice Shop', 'fa-glass-water'],
    ['Vendor/Reseller', 'fa-truck-field'],
    ['Other', 'fa-ellipsis'],
];

$vcDocTypes = [
    ['gst_certificate', 'GST Certificate', 'fa-file-invoice'],
    ['fssai_document', 'FSSAI Licence', 'fa-certificate'],
    ['shop_registration', 'Shop Registration', 'fa-shop'],
    ['msme_certificate', 'MSME Certificate', 'fa-building-circle-check'],
    ['trade_licence', 'Trade Licence', 'fa-file-signature'],
    ['pan_card', 'PAN Card', 'fa-id-card'],
    ['aadhaar_card', 'Aadhaar Card', 'fa-address-card'],
    ['shop_photo', 'Shop-front Photo', 'fa-camera'],
    ['business_card', 'Business Visiting Card', 'fa-address-book'],
];
?>

<section class="vc-signup-page vc-signup-wizard-page">
    <div class="vc-signup-container">
        <div class="vc-signup-layout vc-signup-layout-wide">

            <div class="vc-signup-visual">
                <div class="vc-signup-overlay"></div>
                <div class="vc-signup-visual-content">
                    <span class="vc-signup-kicker">Join Veggicart</span>
                    <h1>Create Your Fresh Shopping Account</h1>
                    <p>
                        Register your business the same way as the Veggicart app —
                        verify mobile, add shop details, address and documents.
                    </p>
                    <div class="vc-signup-benefits">
                        <div class="vc-signup-benefit">
                            <span><i class="fa-solid fa-mobile-screen"></i></span>
                            <div>
                                <strong>OTP verification</strong>
                                <small>Secure mobile-first signup</small>
                            </div>
                        </div>
                        <div class="vc-signup-benefit">
                            <span><i class="fa-solid fa-store"></i></span>
                            <div>
                                <strong>Business profile</strong>
                                <small>Same fields as the mobile app</small>
                            </div>
                        </div>
                        <div class="vc-signup-benefit">
                            <span><i class="fa-solid fa-file-shield"></i></span>
                            <div>
                                <strong>Optional documents</strong>
                                <small>Upload now or later</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="vc-signup-form-wrap vc-signup-wizard-wrap">
                <div class="vc-signup-form-head">
                    <span class="vc-signup-icon"><i class="fa-solid fa-user-plus"></i></span>
                    <div>
                        <span>Business Registration</span>
                        <h2>Create Account</h2>
                    </div>
                </div>

                <div class="vc-signup-steps" id="vcSignupStepBar" aria-label="Registration progress">
                    <button type="button" class="vc-signup-step is-active" data-goto-step="1"><span>1</span>Mobile</button>
                    <button type="button" class="vc-signup-step" data-goto-step="2"><span>2</span>Business</button>
                    <button type="button" class="vc-signup-step" data-goto-step="3"><span>3</span>Address</button>
                    <button type="button" class="vc-signup-step" data-goto-step="4"><span>4</span>Documents</button>
                    <button type="button" class="vc-signup-step" data-goto-step="5"><span>5</span>Review</button>
                </div>

                <div class="vc-mobile-progress vc-signup-progress">
                    <div class="vc-progress-meta">
                        <span>Step <strong id="vcSignupStepText">1</strong> of 5</span>
                        <strong id="vcSignupProgressPct">20%</strong>
                    </div>
                    <div class="vc-progress-track"><span id="vcSignupProgressBar"></span></div>
                </div>

                <form id="vcSignupRegistrationForm" class="vc-signup-form vc-registration-main" novalidate enctype="multipart/form-data">

                    <!-- STEP 1: MOBILE OTP -->
                    <div class="vc-form-step active" data-signup-step="1">
                        <div class="vc-step-heading">
                            <span>Step 01</span>
                            <h2>Verify your mobile number</h2>
                            <p>We will send a one-time password. No account password is required to register.</p>
                        </div>

                        <div class="vc-field">
                            <label for="vcSignupPhone">Mobile Number <em>*</em></label>
                            <div class="vc-input-wrap">
                                <i class="fa-solid fa-phone"></i>
                                <input type="tel" id="vcSignupPhone" name="mobile" placeholder="10-digit mobile number" autocomplete="tel" inputmode="tel" required>
                            </div>
                        </div>

                        <div class="vc-field" id="vcSignupOtpWrap" hidden>
                            <label for="vcSignupOtp">OTP <em>*</em></label>
                            <div class="vc-input-wrap">
                                <i class="fa-solid fa-key"></i>
                                <input type="text" id="vcSignupOtp" name="otp" maxlength="8" inputmode="numeric" placeholder="Enter OTP" autocomplete="one-time-code">
                            </div>
                            <small id="vcSignupOtpHint" class="vc-login-hint"></small>
                            <button type="button" class="vc-login-resend" id="vcSignupResendOtp" hidden>Resend OTP</button>
                        </div>
                    </div>

                    <!-- STEP 2: BUSINESS INFO -->
                    <div class="vc-form-step" data-signup-step="2">
                        <div class="vc-step-heading">
                            <span>Step 02</span>
                            <h2>Business information</h2>
                            <p>Tell us about your shop. GST, FSSAI, PAN and password are optional.</p>
                        </div>

                        <div class="vc-business-types" id="vcSignupBizTypes">
                            <?php foreach ($vcBizTypes as [$label, $icon]): ?>
                                <label class="vc-business-type-card">
                                    <input type="radio" name="business_type" value="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>">
                                    <span class="vc-type-icon"><i class="fa-solid <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>"></i></span>
                                    <strong><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></strong>
                                    <span class="vc-type-check"><i class="fa-solid fa-check"></i></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="vc-field-error" id="vcSignupBizTypeError" hidden>Please select a business type.</p>

                        <div class="vc-form-grid">
                            <div class="vc-field">
                                <label for="vcSignupBusinessName">Business / Shop Name <em>*</em></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-solid fa-store"></i>
                                    <input type="text" id="vcSignupBusinessName" name="business_name" required placeholder="Your shop or business name">
                                </div>
                            </div>
                            <div class="vc-field">
                                <label for="vcSignupOwnerName">Owner Name <em>*</em></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-regular fa-user"></i>
                                    <input type="text" id="vcSignupOwnerName" name="owner_name" required placeholder="Owner full name">
                                </div>
                            </div>
                            <div class="vc-field vc-field-full">
                                <label for="vcSignupEmail">Email <span>Optional</span></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-regular fa-envelope"></i>
                                    <input type="email" id="vcSignupEmail" name="email" placeholder="business@email.com" autocomplete="email">
                                </div>
                            </div>
                            <div class="vc-field">
                                <label for="vcSignupPassword">
                                    Password
                                    <span>Optional — lets you log in with Email &amp; Password later</span>
                                </label>
                                <div class="vc-input-wrap vc-password-field">
                                    <i class="fa-solid fa-lock"></i>
                                    <input
                                        type="password"
                                        id="vcSignupPassword"
                                        name="password"
                                        minlength="6"
                                        placeholder="Min. 6 characters"
                                        autocomplete="new-password">
                                    <button
                                        type="button"
                                        class="vc-password-toggle"
                                        id="vcSignupPasswordToggle"
                                        aria-label="Show password"
                                        data-target="vcSignupPassword">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="vc-field">
                                <label for="vcSignupPasswordConfirm">
                                    Confirm Password
                                    <span>Optional — must match if you set a password</span>
                                </label>
                                <div class="vc-input-wrap vc-password-field">
                                    <i class="fa-solid fa-lock"></i>
                                    <input
                                        type="password"
                                        id="vcSignupPasswordConfirm"
                                        name="password_confirmation"
                                        minlength="6"
                                        placeholder="Re-enter password"
                                        autocomplete="new-password">
                                    <button
                                        type="button"
                                        class="vc-password-toggle"
                                        id="vcSignupPasswordConfirmToggle"
                                        aria-label="Show password"
                                        data-target="vcSignupPasswordConfirm">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="vc-field">
                                <label for="vcSignupGST">GSTIN <span>Optional</span></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-solid fa-receipt"></i>
                                    <input type="text" id="vcSignupGST" name="gst_number" placeholder="GST number">
                                </div>
                            </div>
                            <div class="vc-field">
                                <label for="vcSignupFSSAI">FSSAI Licence <span>Optional</span></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-solid fa-certificate"></i>
                                    <input type="text" id="vcSignupFSSAI" name="fssai_number" placeholder="FSSAI number">
                                </div>
                            </div>
                            <div class="vc-field vc-field-full">
                                <label for="vcSignupPAN">PAN Number <span>Optional</span></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-solid fa-id-card"></i>
                                    <input type="text" id="vcSignupPAN" name="pan_number" maxlength="10" placeholder="ABCDE1234F">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: ADDRESS -->
                    <div class="vc-form-step" data-signup-step="3">
                        <div class="vc-step-heading">
                            <span>Step 03</span>
                            <h2>Shop &amp; delivery address</h2>
                            <p>Pincode must be in our Hyderabad service area.</p>
                        </div>

                        <div class="vc-field">
                            <label for="vcSignupShopAddress">Shop Address <em>*</em></label>
                            <textarea id="vcSignupShopAddress" name="shop_address" rows="3" required placeholder="Building, street, area…"></textarea>
                        </div>

                        <label class="vc-same-address" style="margin:12px 0">
                            <input type="checkbox" id="vcSignupSameAddress" checked>
                            <span>Same as shop address for delivery</span>
                        </label>

                        <div class="vc-field" id="vcSignupDeliveryWrap" hidden>
                            <label for="vcSignupDeliveryAddress">Delivery Address <em>*</em></label>
                            <textarea id="vcSignupDeliveryAddress" name="delivery_address" rows="3" placeholder="Delivery building, street, area…"></textarea>
                        </div>

                        <div class="vc-form-grid">
                            <div class="vc-field">
                                <label for="vcSignupCity">City <em>*</em></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-solid fa-city"></i>
                                    <input type="text" id="vcSignupCity" name="city" value="Hyderabad" required>
                                </div>
                            </div>
                            <div class="vc-field">
                                <label for="vcSignupState">State <em>*</em></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-solid fa-map"></i>
                                    <input type="text" id="vcSignupState" name="state" value="Telangana" required>
                                </div>
                            </div>
                            <div class="vc-field">
                                <label for="vcSignupPincode">Pincode <em>*</em></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <input type="text" id="vcSignupPincode" name="pincode" maxlength="6" inputmode="numeric" required placeholder="6-digit pincode">
                                </div>
                                <small id="vcSignupPinHint" class="vc-login-hint"></small>
                            </div>
                            <div class="vc-field">
                                <label for="vcSignupLandmark">Landmark <span>Optional</span></label>
                                <div class="vc-input-wrap">
                                    <i class="fa-solid fa-signs-post"></i>
                                    <input type="text" id="vcSignupLandmark" name="landmark" placeholder="Nearby landmark">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: DOCUMENTS -->
                    <div class="vc-form-step" data-signup-step="4">
                        <div class="vc-step-heading">
                            <span>Step 04</span>
                            <h2>Upload documents</h2>
                            <p>All documents are optional right now. JPG, PNG or PDF up to 5 MB.</p>
                        </div>
                        <div class="vc-upload-grid">
                            <?php foreach ($vcDocTypes as [$name, $label, $icon]): ?>
                                <label class="vc-upload-card">
                                    <input type="file" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" accept="image/*,.pdf">
                                    <span class="vc-upload-icon"><i class="fa-solid <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>"></i></span>
                                    <div>
                                        <strong><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></strong>
                                        <small>Optional</small>
                                    </div>
                                    <span class="vc-upload-action"><i class="fa-solid fa-cloud-arrow-up"></i> Upload</span>
                                    <span class="vc-file-name">No file selected</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- STEP 5: REVIEW -->
                    <div class="vc-form-step" data-signup-step="5">
                        <div class="vc-step-heading">
                            <span>Step 05</span>
                            <h2>Review &amp; submit</h2>
                            <p>Confirm your details before submitting the application.</p>
                        </div>

                        <div class="vc-review-card">
                            <h3>Mobile</h3>
                            <p><strong id="vcReviewMobile">—</strong></p>
                        </div>
                        <div class="vc-review-card">
                            <h3>Business</h3>
                            <p><span>Type:</span> <strong id="vcReviewType">—</strong></p>
                            <p><span>Shop:</span> <strong id="vcReviewBusiness">—</strong></p>
                            <p><span>Owner:</span> <strong id="vcReviewOwner">—</strong></p>
                            <p><span>Email:</span> <strong id="vcReviewEmail">—</strong></p>
                            <p><span>Password:</span> <strong id="vcReviewPassword">—</strong></p>
                        </div>
                        <div class="vc-review-card">
                            <h3>Address</h3>
                            <p id="vcReviewAddress">—</p>
                        </div>
                        <div class="vc-review-card">
                            <h3>Documents</h3>
                            <p id="vcReviewDocs">None selected (optional)</p>
                        </div>

                        <label class="vc-signup-terms" style="margin-top:16px">
                            <input type="checkbox" id="vcSignupTerms" required>
                            <span>
                                I agree to the
                                <a href="terms-and-conditions.php" target="_blank" rel="noopener">Terms &amp; Conditions</a>
                                and
                                <a href="privacy-policy.php" target="_blank" rel="noopener">Privacy Policy</a>.
                            </span>
                        </label>
                    </div>

                    <div class="vc-form-actions vc-signup-actions">
                        <button type="button" class="vc-login-register" id="vcSignupPrev" hidden>
                            <i class="fa-solid fa-arrow-left"></i> Back
                        </button>
                        <button type="button" class="vc-login-submit" id="vcSignupNext">
                            <span id="vcSignupNextText">Send OTP</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="vc-login-divider vc-signup-login-divider">
                        <span>Already registered?</span>
                    </div>

                    <a href="login.php" class="vc-login-register vc-signup-login-link">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login here
                    </a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>
