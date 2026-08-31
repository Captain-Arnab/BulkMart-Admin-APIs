<?php
// New customers start on the OTP + 5-step flow at register.php.
// This page remains for authenticated users completing / updating business KYC.
include('header.php');
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.VC && typeof VC.isLoggedIn === 'function' && !VC.isLoggedIn()) {
        window.location.replace('register.php');
    }
});
</script>

<!-- =========================================
     VEGIICART BUSINESS REGISTRATION
========================================== -->

<main class="vc-business-page">

    <section class="vc-business-hero">
        <div class="vc-business-container">

            <div class="vc-business-hero-content">
                <span class="vc-business-badge">
                    <i class="fa-solid fa-store"></i>
                    Vegiicart Business
                </span>

                <h1>
                    Register Your <span>Business</span>
                </h1>

                <p>
                    Join Vegiicart as a business customer and access reliable
                    fresh produce, grocery supplies, bulk pricing and convenient
                    doorstep delivery for your organisation.
                </p>

                <div class="vc-business-hero-points">
                    <span>
                        <i class="fa-solid fa-circle-check"></i>
                        Bulk pricing
                    </span>

                    <span>
                        <i class="fa-solid fa-circle-check"></i>
                        Business delivery
                    </span>

                    <span>
                        <i class="fa-solid fa-circle-check"></i>
                        Easy repeat ordering
                    </span>
                </div>
            </div>

        </div>
    </section>


    <section class="vc-business-form-section">
        <div class="vc-business-container">

            <div class="vc-registration-layout">

                <!-- =========================
                     SIDEBAR
                ========================== -->
                <aside class="vc-registration-sidebar">

                    <div class="vc-sidebar-title">
                        <span>Registration</span>
                        <h3>Complete Your Profile</h3>
                        <p>
                            Complete all five steps to submit your business
                            application.
                        </p>
                    </div>


                    <div class="vc-step-navigation">

                        <div class="vc-side-step active" data-side-step="1">
                            <span class="vc-side-step-number">1</span>

                            <div>
                                <strong>Business Type</strong>
                                <small>Select your business</small>
                            </div>
                        </div>


                        <div class="vc-side-line"></div>


                        <div class="vc-side-step" data-side-step="2">
                            <span class="vc-side-step-number">2</span>

                            <div>
                                <strong>Business Information</strong>
                                <small>Basic company details</small>
                            </div>
                        </div>


                        <div class="vc-side-line"></div>


                        <div class="vc-side-step" data-side-step="3">
                            <span class="vc-side-step-number">3</span>

                            <div>
                                <strong>Address</strong>
                                <small>Shop and delivery address</small>
                            </div>
                        </div>


                        <div class="vc-side-line"></div>


                        <div class="vc-side-step" data-side-step="4">
                            <span class="vc-side-step-number">4</span>

                            <div>
                                <strong>Documents</strong>
                                <small>Upload verification files</small>
                            </div>
                        </div>


                        <div class="vc-side-line"></div>


                        <div class="vc-side-step" data-side-step="5">
                            <span class="vc-side-step-number">5</span>

                            <div>
                                <strong>Review & Submit</strong>
                                <small>Confirm your application</small>
                            </div>
                        </div>

                    </div>


                    <div class="vc-registration-help">
                        <span>
                            <i class="fa-solid fa-headset"></i>
                        </span>

                        <div>
                            <strong>Need Help?</strong>
                            <p>
                                Contact our team if you need assistance with
                                business registration.
                            </p>

                            <a href="contact.php">
                                Contact Support
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                </aside>


                <!-- =========================
                     MAIN FORM
                ========================== -->

                <div class="vc-registration-main">

                    <!-- TOP PROGRESS -->
                    <div class="vc-mobile-progress">

                        <div class="vc-progress-meta">
                            <span>
                                Step <strong id="vcCurrentStepText">1</strong>
                                of 5
                            </span>

                            <strong id="vcProgressPercentage">20%</strong>
                        </div>

                        <div class="vc-progress-track">
                            <span id="vcProgressBar"></span>
                        </div>

                    </div>


                    <form id="vcBusinessRegistrationForm"
                          enctype="multipart/form-data"
                          novalidate>


                        <!-- =========================================
                             STEP 1
                        ========================================== -->

                        <div class="vc-form-step active" data-step="1">

                            <div class="vc-step-heading">
                                <span>Step 01</span>

                                <h2>
                                    What type of business do you run?
                                </h2>

                                <p>
                                    Select the option that best describes your
                                    business.
                                </p>
                            </div>


                            <div class="vc-business-types">

                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Retail Shop">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-shop"></i>
                                    </span>

                                    <strong>Retail Shop</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Kirana Store">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-basket-shopping"></i>
                                    </span>

                                    <strong>Kirana Store</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Supermarket">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </span>

                                    <strong>Supermarket</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Hotel">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-hotel"></i>
                                    </span>

                                    <strong>Hotel</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Restaurant">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-utensils"></i>
                                    </span>

                                    <strong>Restaurant</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Catering Service">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-bowl-food"></i>
                                    </span>

                                    <strong>Catering Service</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Hostel">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-bed"></i>
                                    </span>

                                    <strong>Hostel</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Hospital">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-hospital"></i>
                                    </span>

                                    <strong>Hospital</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Corporate Pantry">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-building"></i>
                                    </span>

                                    <strong>Corporate Pantry</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Juice Shop">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-glass-water"></i>
                                    </span>

                                    <strong>Juice Shop</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Vendor/Reseller">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-boxes-stacked"></i>
                                    </span>

                                    <strong>Vendor / Reseller</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>


                                <label class="vc-business-type-card">
                                    <input type="radio"
                                           name="business_type"
                                           value="Other">

                                    <span class="vc-type-icon">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </span>

                                    <strong>Other</strong>

                                    <span class="vc-type-check">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </label>

                            </div>


                            <div class="vc-form-error"
                                 id="vcBusinessTypeError">
                                Please select your business type.
                            </div>

                        </div>


                        <!-- =========================================
                             STEP 2
                        ========================================== -->

                        <div class="vc-form-step" data-step="2">

                            <div class="vc-step-heading">
                                <span>Step 02</span>

                                <h2>Business Information</h2>

                                <p>
                                    Tell us a little more about your business
                                    and owner details.
                                </p>
                            </div>


                            <div class="vc-form-grid">

                                <div class="vc-field vc-field-full">
                                    <label for="vcBusinessName">
                                        Business Name
                                        <em>*</em>
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-store"></i>

                                        <input type="text"
                                               id="vcBusinessName"
                                               name="business_name"
                                               placeholder="Enter registered business name"
                                               required>
                                    </div>
                                </div>


                                <div class="vc-field">
                                    <label for="vcOwnerName">
                                        Owner Name
                                        <em>*</em>
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-user"></i>

                                        <input type="text"
                                               id="vcOwnerName"
                                               name="owner_name"
                                               placeholder="Enter owner name"
                                               required>
                                    </div>
                                </div>


                                <div class="vc-field">
                                    <label for="vcMobile">
                                        Mobile Number
                                        <em>*</em>
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-phone"></i>

                                        <input type="tel"
                                               id="vcMobile"
                                               name="mobile"
                                               maxlength="10"
                                               placeholder="10-digit mobile number"
                                               required>
                                    </div>
                                </div>


                                <div class="vc-field vc-field-full">
                                    <label for="vcEmail">
                                        Email Address
                                        <em>*</em>
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-envelope"></i>

                                        <input type="email"
                                               id="vcEmail"
                                               name="email"
                                               placeholder="Enter business email"
                                               required>
                                    </div>
                                </div>


                                <div class="vc-field">
                                    <label for="vcGST">
                                        GST Number
                                        <span>Optional</span>
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-file-invoice"></i>

                                        <input type="text"
                                               id="vcGST"
                                               name="gst_number"
                                               placeholder="Enter GST number">
                                    </div>
                                </div>


                                <div class="vc-field">
                                    <label for="vcFSSAI">
                                        FSSAI Licence
                                        <span>Optional</span>
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-certificate"></i>

                                        <input type="text"
                                               id="vcFSSAI"
                                               name="fssai_number"
                                               placeholder="Enter FSSAI licence number">
                                    </div>
                                </div>


                                <div class="vc-field vc-field-full">
                                    <label for="vcPAN">
                                        PAN Number
                                        <span>Optional</span>
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-id-card"></i>

                                        <input type="text"
                                               id="vcPAN"
                                               name="pan_number"
                                               maxlength="10"
                                               placeholder="ABCDE1234F">
                                    </div>
                                </div>

                            </div>

                        </div>


                        <!-- =========================================
                             STEP 3
                        ========================================== -->

                        <div class="vc-form-step" data-step="3">

                            <div class="vc-step-heading">
                                <span>Step 03</span>

                                <h2>Business Address</h2>

                                <p>
                                    Enter your shop location and preferred
                                    delivery address.
                                </p>
                            </div>


                            <div class="vc-address-block">

                                <div class="vc-address-title">
                                    <span>
                                        <i class="fa-solid fa-store"></i>
                                    </span>

                                    <div>
                                        <strong>Shop Address</strong>
                                        <small>
                                            Your registered or operating
                                            business address.
                                        </small>
                                    </div>
                                </div>


                                <div class="vc-field">
                                    <label for="vcShopAddress">
                                        Shop Address
                                        <em>*</em>
                                    </label>

                                    <textarea id="vcShopAddress"
                                              name="shop_address"
                                              placeholder="Building, street, area..."
                                              required></textarea>
                                </div>

                            </div>


                            <div class="vc-address-block">

                                <div class="vc-address-title-row">

                                    <div class="vc-address-title">
                                        <span>
                                            <i class="fa-solid fa-truck"></i>
                                        </span>

                                        <div>
                                            <strong>
                                                Delivery Address
                                            </strong>

                                            <small>
                                                Where should your orders be
                                                delivered?
                                            </small>
                                        </div>
                                    </div>


                                    <label class="vc-same-address">
                                        <input type="checkbox"
                                               id="vcSameAddress">

                                        <span class="vc-custom-checkbox">
                                            <i class="fa-solid fa-check"></i>
                                        </span>

                                        Same as shop address
                                    </label>

                                </div>


                                <div class="vc-field">
                                    <label for="vcDeliveryAddress">
                                        Delivery Address
                                        <em>*</em>
                                    </label>

                                    <textarea id="vcDeliveryAddress"
                                              name="delivery_address"
                                              placeholder="Delivery building, street, area..."
                                              required></textarea>
                                </div>

                            </div>


                            <div class="vc-form-grid">

                                <div class="vc-field">
                                    <label for="vcCity">
                                        City
                                        <em>*</em>
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-city"></i>

                                        <input type="text"
                                               id="vcCity"
                                               name="city"
                                               placeholder="Enter city"
                                               required>
                                    </div>
                                </div>


                                <div class="vc-field">
                                    <label for="vcState">
                                        State
                                        <em>*</em>
                                    </label>

                                    <div class="vc-input-wrap">

                                        <i class="fa-solid fa-map"></i>

                                        <select id="vcState"
                                                name="state"
                                                required>

                                            <option value="">
                                                Select State
                                            </option>

                                            <option>Andhra Pradesh</option>
                                            <option>Assam</option>
                                            <option>Bihar</option>
                                            <option>Delhi</option>
                                            <option>Gujarat</option>
                                            <option>Haryana</option>
                                            <option>Himachal Pradesh</option>
                                            <option>Jharkhand</option>
                                            <option>Karnataka</option>
                                            <option>Kerala</option>
                                            <option>Madhya Pradesh</option>
                                            <option>Maharashtra</option>
                                            <option>Odisha</option>
                                            <option>Punjab</option>
                                            <option>Rajasthan</option>
                                            <option>Tamil Nadu</option>
                                            <option>Telangana</option>
                                            <option>Uttar Pradesh</option>
                                            <option>Uttarakhand</option>
                                            <option>West Bengal</option>

                                        </select>

                                    </div>
                                </div>


                                <div class="vc-field">
                                    <label for="vcPincode">
                                        Pincode
                                        <em>*</em>
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-location-dot"></i>

                                        <input type="text"
                                               id="vcPincode"
                                               name="pincode"
                                               maxlength="6"
                                               placeholder="6-digit pincode"
                                               required>
                                    </div>
                                </div>


                                <div class="vc-field">
                                    <label for="vcLandmark">
                                        Landmark
                                    </label>

                                    <div class="vc-input-wrap">
                                        <i class="fa-solid fa-signs-post"></i>

                                        <input type="text"
                                               id="vcLandmark"
                                               name="landmark"
                                               placeholder="Nearby landmark">
                                    </div>
                                </div>


                                <div class="vc-field vc-field-full">
                                    <label for="vcGoogleLocation">
                                        Google Maps Location
                                    </label>

                                    <div class="vc-location-field">

                                        <div class="vc-input-wrap">
                                            <i class="fa-solid fa-location-crosshairs"></i>

                                            <input type="url"
                                                   id="vcGoogleLocation"
                                                   name="google_maps_location"
                                                   placeholder="Paste Google Maps location link">
                                        </div>

                                        <button type="button"
                                                class="vc-location-btn"
                                                id="vcLocationButton">

                                            <i class="fa-solid fa-location-crosshairs"></i>
                                            Use Current Location
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- =========================================
                             STEP 4
                        ========================================== -->

                        <div class="vc-form-step" data-step="4">

                            <div class="vc-step-heading">
                                <span>Step 04</span>

                                <h2>Upload Business Documents</h2>

                                <p>
                                    Upload available verification documents.
                                    JPG, PNG and PDF files are supported.
                                </p>
                            </div>


                            <div class="vc-upload-note">

                                <span>
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>

                                <div>
                                    <strong>Your documents are secure</strong>

                                    <p>
                                        Documents are used only for business
                                        verification and account approval.
                                    </p>
                                </div>

                            </div>


                            <div class="vc-upload-grid">


                                <!-- GST -->
                                <label class="vc-upload-card">

                                    <input type="file"
                                           name="gst_certificate"
                                           accept="image/*,.pdf"
                                           capture="environment">

                                    <span class="vc-upload-icon">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </span>

                                    <div>
                                        <strong>GST Certificate</strong>
                                        <small>JPG, PNG or PDF</small>
                                    </div>

                                    <span class="vc-upload-action">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        Upload
                                    </span>

                                    <span class="vc-file-name">
                                        No file selected
                                    </span>

                                </label>


                                <!-- FSSAI -->
                                <label class="vc-upload-card">

                                    <input type="file"
                                           name="fssai_document"
                                           accept="image/*,.pdf"
                                           capture="environment">

                                    <span class="vc-upload-icon">
                                        <i class="fa-solid fa-certificate"></i>
                                    </span>

                                    <div>
                                        <strong>FSSAI Licence</strong>
                                        <small>JPG, PNG or PDF</small>
                                    </div>

                                    <span class="vc-upload-action">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        Upload
                                    </span>

                                    <span class="vc-file-name">
                                        No file selected
                                    </span>

                                </label>


                                <!-- SHOP REG -->
                                <label class="vc-upload-card">

                                    <input type="file"
                                           name="shop_registration"
                                           accept="image/*,.pdf">

                                    <span class="vc-upload-icon">
                                        <i class="fa-solid fa-shop"></i>
                                    </span>

                                    <div>
                                        <strong>Shop Registration</strong>
                                        <small>JPG, PNG or PDF</small>
                                    </div>

                                    <span class="vc-upload-action">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        Upload
                                    </span>

                                    <span class="vc-file-name">
                                        No file selected
                                    </span>

                                </label>


                                <!-- MSME -->
                                <label class="vc-upload-card">

                                    <input type="file"
                                           name="msme_certificate"
                                           accept="image/*,.pdf">

                                    <span class="vc-upload-icon">
                                        <i class="fa-solid fa-building-circle-check"></i>
                                    </span>

                                    <div>
                                        <strong>MSME Certificate</strong>
                                        <small>JPG, PNG or PDF</small>
                                    </div>

                                    <span class="vc-upload-action">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        Upload
                                    </span>

                                    <span class="vc-file-name">
                                        No file selected
                                    </span>

                                </label>


                                <!-- TRADE LICENCE -->
                                <label class="vc-upload-card">

                                    <input type="file"
                                           name="trade_licence"
                                           accept="image/*,.pdf">

                                    <span class="vc-upload-icon">
                                        <i class="fa-solid fa-file-signature"></i>
                                    </span>

                                    <div>
                                        <strong>Trade Licence</strong>
                                        <small>JPG, PNG or PDF</small>
                                    </div>

                                    <span class="vc-upload-action">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        Upload
                                    </span>

                                    <span class="vc-file-name">
                                        No file selected
                                    </span>

                                </label>


                                <!-- PAN -->
                                <label class="vc-upload-card">

                                    <input type="file"
                                           name="pan_card"
                                           accept="image/*,.pdf">

                                    <span class="vc-upload-icon">
                                        <i class="fa-solid fa-id-card"></i>
                                    </span>

                                    <div>
                                        <strong>PAN Card</strong>
                                        <small>JPG, PNG or PDF</small>
                                    </div>

                                    <span class="vc-upload-action">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        Upload
                                    </span>

                                    <span class="vc-file-name">
                                        No file selected
                                    </span>

                                </label>


                                <!-- AADHAAR -->
                                <label class="vc-upload-card">

                                    <input type="file"
                                           name="aadhaar_card"
                                           accept="image/*,.pdf">

                                    <span class="vc-upload-icon">
                                        <i class="fa-solid fa-address-card"></i>
                                    </span>

                                    <div>
                                        <strong>Aadhaar Card</strong>
                                        <small>JPG, PNG or PDF</small>
                                    </div>

                                    <span class="vc-upload-action">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        Upload
                                    </span>

                                    <span class="vc-file-name">
                                        No file selected
                                    </span>

                                </label>


                                <!-- SHOP PHOTO -->
                                <label class="vc-upload-card">

                                    <input type="file"
                                           name="shop_photo"
                                           accept="image/*"
                                           capture="environment">

                                    <span class="vc-upload-icon">
                                        <i class="fa-solid fa-camera"></i>
                                    </span>

                                    <div>
                                        <strong>Shop-front Photo</strong>
                                        <small>Camera or gallery</small>
                                    </div>

                                    <span class="vc-upload-action">
                                        <i class="fa-solid fa-camera"></i>
                                        Choose
                                    </span>

                                    <span class="vc-file-name">
                                        No file selected
                                    </span>

                                </label>


                                <!-- VISITING CARD -->
                                <label class="vc-upload-card">

                                    <input type="file"
                                           name="business_card"
                                           accept="image/*,.pdf">

                                    <span class="vc-upload-icon">
                                        <i class="fa-solid fa-address-book"></i>
                                    </span>

                                    <div>
                                        <strong>
                                            Business Visiting Card
                                        </strong>

                                        <small>Optional</small>
                                    </div>

                                    <span class="vc-upload-action">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        Upload
                                    </span>

                                    <span class="vc-file-name">
                                        No file selected
                                    </span>

                                </label>

                            </div>


                            <div class="vc-upload-support">

                                <span>
                                    <i class="fa-solid fa-camera"></i>
                                    Camera
                                </span>

                                <span>
                                    <i class="fa-solid fa-images"></i>
                                    Gallery
                                </span>

                                <span>
                                    <i class="fa-solid fa-file-pdf"></i>
                                    PDF
                                </span>

                                <small>
                                    Maximum recommended file size: 5 MB each
                                </small>

                            </div>

                        </div>


                        <!-- =========================================
                             STEP 5
                        ========================================== -->

                        <div class="vc-form-step" data-step="5">

                            <div class="vc-step-heading">
                                <span>Step 05</span>

                                <h2>Review & Submit</h2>

                                <p>
                                    Check your information before submitting
                                    your business registration.
                                </p>
                            </div>


                            <!-- REVIEW BUSINESS TYPE -->
                            <div class="vc-review-card">

                                <div class="vc-review-head">

                                    <div>
                                        <span>
                                            <i class="fa-solid fa-store"></i>
                                        </span>

                                        <h3>Business Type</h3>
                                    </div>

                                    <button type="button"
                                            class="vc-edit-step"
                                            data-edit="1">
                                        <i class="fa-solid fa-pen"></i>
                                        Edit
                                    </button>

                                </div>

                                <div class="vc-review-single">
                                    <span>Selected Business</span>
                                    <strong id="reviewBusinessType">—</strong>
                                </div>

                            </div>


                            <!-- BUSINESS DETAILS -->
                            <div class="vc-review-card">

                                <div class="vc-review-head">

                                    <div>
                                        <span>
                                            <i class="fa-solid fa-building"></i>
                                        </span>

                                        <h3>Business Information</h3>
                                    </div>

                                    <button type="button"
                                            class="vc-edit-step"
                                            data-edit="2">
                                        <i class="fa-solid fa-pen"></i>
                                        Edit
                                    </button>

                                </div>


                                <div class="vc-review-grid">

                                    <div>
                                        <span>Business Name</span>
                                        <strong id="reviewBusinessName">—</strong>
                                    </div>

                                    <div>
                                        <span>Owner Name</span>
                                        <strong id="reviewOwnerName">—</strong>
                                    </div>

                                    <div>
                                        <span>Mobile Number</span>
                                        <strong id="reviewMobile">—</strong>
                                    </div>

                                    <div>
                                        <span>Email</span>
                                        <strong id="reviewEmail">—</strong>
                                    </div>

                                    <div>
                                        <span>GST Number</span>
                                        <strong id="reviewGST">—</strong>
                                    </div>

                                    <div>
                                        <span>FSSAI Licence</span>
                                        <strong id="reviewFSSAI">—</strong>
                                    </div>

                                    <div>
                                        <span>PAN Number</span>
                                        <strong id="reviewPAN">—</strong>
                                    </div>

                                </div>

                            </div>


                            <!-- ADDRESS REVIEW -->
                            <div class="vc-review-card">

                                <div class="vc-review-head">

                                    <div>
                                        <span>
                                            <i class="fa-solid fa-location-dot"></i>
                                        </span>

                                        <h3>Address</h3>
                                    </div>

                                    <button type="button"
                                            class="vc-edit-step"
                                            data-edit="3">
                                        <i class="fa-solid fa-pen"></i>
                                        Edit
                                    </button>

                                </div>


                                <div class="vc-review-grid">

                                    <div class="vc-review-wide">
                                        <span>Shop Address</span>
                                        <strong id="reviewShopAddress">—</strong>
                                    </div>

                                    <div class="vc-review-wide">
                                        <span>Delivery Address</span>
                                        <strong id="reviewDeliveryAddress">—</strong>
                                    </div>

                                    <div>
                                        <span>City</span>
                                        <strong id="reviewCity">—</strong>
                                    </div>

                                    <div>
                                        <span>State</span>
                                        <strong id="reviewState">—</strong>
                                    </div>

                                    <div>
                                        <span>Pincode</span>
                                        <strong id="reviewPincode">—</strong>
                                    </div>

                                    <div>
                                        <span>Landmark</span>
                                        <strong id="reviewLandmark">—</strong>
                                    </div>

                                </div>

                            </div>


                            <!-- DOCUMENT REVIEW -->
                            <div class="vc-review-card">

                                <div class="vc-review-head">

                                    <div>
                                        <span>
                                            <i class="fa-solid fa-file-shield"></i>
                                        </span>

                                        <h3>Uploaded Documents</h3>
                                    </div>

                                    <button type="button"
                                            class="vc-edit-step"
                                            data-edit="4">
                                        <i class="fa-solid fa-pen"></i>
                                        Edit
                                    </button>

                                </div>

                                <div class="vc-review-documents"
                                     id="vcReviewDocuments">

                                    <p>
                                        No documents uploaded.
                                    </p>

                                </div>

                            </div>


                            <!-- TERMS -->
                            <div class="vc-terms-box">

                                <label>

                                    <input type="checkbox"
                                           id="vcTerms"
                                           required>

                                    <span class="vc-custom-checkbox">
                                        <i class="fa-solid fa-check"></i>
                                    </span>

                                    <span>
                                        I confirm that the information provided
                                        is correct and I agree to Vegiicart's
                                        <a href="terms-and-conditions.php">
                                            Terms & Conditions
                                        </a>
                                        and
                                        <a href="privacy-policy.php">
                                            Privacy Policy
                                        </a>.
                                    </span>

                                </label>

                            </div>

                        </div>


                        <!-- =========================================
                             NAVIGATION
                        ========================================== -->

                        <div class="vc-form-navigation">

                            <button type="button"
                                    class="vc-back-btn"
                                    id="vcPreviousButton">

                                <i class="fa-solid fa-arrow-left"></i>
                                Previous

                            </button>


                            <span class="vc-form-step-text">
                                Step
                                <strong id="vcBottomCurrentStep">1</strong>
                                of 5
                            </span>


                            <button type="button"
                                    class="vc-next-btn"
                                    id="vcNextButton">

                                Continue
                                <i class="fa-solid fa-arrow-right"></i>

                            </button>


                            <button type="submit"
                                    class="vc-submit-btn"
                                    id="vcSubmitButton">

                                <i class="fa-solid fa-paper-plane"></i>
                                Submit Application

                            </button>

                        </div>


                    </form>

                </div>

            </div>

        </div>
    </section>

</main>


<!-- =========================================
     SUCCESS MODAL
========================================== -->

<div class="vc-registration-success"
     id="vcRegistrationSuccess">

    <div class="vc-success-modal">

        <button type="button"
                class="vc-success-close"
                id="vcSuccessClose">

            <i class="fa-solid fa-xmark"></i>

        </button>

        <div class="vc-success-check">
            <i class="fa-solid fa-check"></i>
        </div>

        <span>Application Submitted</span>

        <h2>
            Thank You for Registering!
        </h2>

        <p>
            Your Vegiicart business registration application has been
            submitted successfully. Our team will review your details
            and contact you shortly.
        </p>

        <div class="vc-application-number">
            <small>Application ID</small>
            <strong id="vcRegAppId">—</strong>
        </div>

        <a href="index.php">
            Return to Home
            <i class="fa-solid fa-arrow-right"></i>
        </a>

    </div>

</div>

<?php include('footer.php'); ?>