<?php include('header.php'); ?>

<!-- =========================
     VEGIICART - MANAGE ADDRESSES
========================= -->

<section class="vg-address-page">

    <div class="vg-address-container">

        <!-- Page Heading -->
        <div class="vg-address-heading">

            <div>
                <span class="vg-address-label">My Account</span>

                <h1>Manage Addresses</h1>

                <p>
                    Add, edit and manage your saved delivery addresses
                    for faster checkout.
                </p>
            </div>

            <a href="my-profile.php" class="vg-address-back">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Profile
            </a>

        </div>


        <!-- =========================
             ADDRESS SUMMARY
        ========================= -->

        <div class="vg-address-summary">

            <div class="vg-summary-item">
                <span class="vg-summary-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </span>

                <div>
                    <strong>3</strong>
                    <span>Saved Addresses</span>
                </div>
            </div>


            <div class="vg-summary-item">
                <span class="vg-summary-icon">
                    <i class="fa-solid fa-house-circle-check"></i>
                </span>

                <div>
                    <strong>Home</strong>
                    <span>Default Address</span>
                </div>
            </div>


            <button type="button"
                    class="vg-add-address-btn"
                    onclick="document.getElementById('vgNewAddressForm').scrollIntoView({behavior:'smooth'});">

                <i class="fa-solid fa-plus"></i>
                Add New Address

            </button>

        </div>


        <!-- =========================
             SAVED ADDRESSES
        ========================= -->

        <div class="vg-address-section-heading">

            <div>
                <span>Saved Addresses</span>
                <h2>Your Delivery Addresses</h2>
            </div>

        </div>


        <div class="vg-saved-address-grid">
            <p class="vc-live-empty">Loading addresses…</p>
        </div>


        <!-- =========================
             ADD NEW ADDRESS
        ========================= -->

        <div class="vg-new-address-box"
             id="vgNewAddressForm">

            <div class="vg-new-address-header">

                <span class="vg-new-address-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </span>

                <div>
                    <span>Add Address</span>
                    <h2>Add New Delivery Address</h2>

                    <p>
                        Save a new delivery location to make your
                        future Vegiicart orders faster.
                    </p>
                </div>

            </div>


            <form class="vg-address-form">

                <div class="vg-form-grid">


                    <div class="vg-form-group">

                        <label>Full Name *</label>

                        <div class="vg-input-box">
                            <i class="fa-regular fa-user"></i>

                            <input type="text"
                                   placeholder="Enter full name"
                                   required>
                        </div>

                    </div>


                    <div class="vg-form-group">

                        <label>Mobile Number *</label>

                        <div class="vg-input-box">
                            <i class="fa-solid fa-phone"></i>

                            <input type="tel"
                                   placeholder="Enter mobile number"
                                   required>
                        </div>

                    </div>


                    <div class="vg-form-group">

                        <label>Alternate Mobile Number</label>

                        <div class="vg-input-box">
                            <i class="fa-solid fa-mobile-screen-button"></i>

                            <input type="tel"
                                   placeholder="Alternate mobile number">
                        </div>

                    </div>


                    <div class="vg-form-group">

                        <label>PIN Code *</label>

                        <div class="vg-input-box">
                            <i class="fa-solid fa-location-crosshairs"></i>

                            <input type="text"
                                   name="pincode"
                                   placeholder="Enter PIN code"
                                   maxlength="6"
                                   required>
                        </div>

                    </div>


                    <div class="vg-form-group vg-full-field">

                        <label>House / Flat / Building *</label>

                        <div class="vg-input-box">
                            <i class="fa-solid fa-house"></i>

                            <input type="text"
                                   name="line1"
                                   placeholder="House number, flat or building"
                                   required>
                        </div>

                    </div>


                    <div class="vg-form-group vg-full-field">

                        <label>Street / Area / Locality *</label>

                        <div class="vg-input-box">
                            <i class="fa-solid fa-road"></i>

                            <input type="text"
                                   name="line2"
                                   placeholder="Street, area or locality"
                                   required>
                        </div>

                    </div>


                    <div class="vg-form-group">

                        <label>Landmark</label>

                        <div class="vg-input-box">
                            <i class="fa-solid fa-map-pin"></i>

                            <input type="text"
                                   name="landmark"
                                   placeholder="Nearby landmark">
                        </div>

                    </div>


                    <div class="vg-form-group">

                        <label>City *</label>

                        <div class="vg-input-box">
                            <i class="fa-solid fa-city"></i>

                            <input type="text"
                                   name="city"
                                   placeholder="Enter city"
                                   required>
                        </div>

                    </div>


                    <div class="vg-form-group">

                        <label>State *</label>

                        <div class="vg-input-box">

                            <i class="fa-solid fa-map"></i>

                            <select name="state" required>

                                <option value="">
                                    Select State
                                </option>

                                <option>Uttarakhand</option>
                                <option>Uttar Pradesh</option>
                                <option>Delhi</option>
                                <option>Haryana</option>
                                <option>Punjab</option>
                                <option>Rajasthan</option>

                            </select>

                        </div>

                    </div>


                    <div class="vg-form-group">

                        <label>Country *</label>

                        <div class="vg-input-box">
                            <i class="fa-solid fa-earth-asia"></i>

                            <input type="text"
                                   value="India"
                                   readonly>
                        </div>

                    </div>

                </div>



                <!-- ADDRESS TYPE -->

                <div class="vg-address-type-section">

                    <label class="vg-type-main-label">
                        Address Type
                    </label>


                    <div class="vg-address-type-options">

                        <label class="vg-type-option">

                            <input type="radio"
                                   name="address_type"
                                   value="Home"
                                   checked>

                            <span>
                                <i class="fa-solid fa-house"></i>

                                <strong>Home</strong>

                                <small>Residential address</small>
                            </span>

                        </label>


                        <label class="vg-type-option">

                            <input type="radio"
                                   name="address_type"
                                   value="Office">

                            <span>
                                <i class="fa-solid fa-building"></i>

                                <strong>Office</strong>

                                <small>Workplace address</small>
                            </span>

                        </label>


                        <label class="vg-type-option">

                            <input type="radio"
                                   name="address_type"
                                   value="Other">

                            <span>
                                <i class="fa-solid fa-location-dot"></i>

                                <strong>Other</strong>

                                <small>Other delivery location</small>
                            </span>

                        </label>

                    </div>

                </div>



                <!-- DEFAULT -->

                <label class="vg-default-check">

                    <input type="checkbox">

                    <span class="vg-checkmark"></span>

                    <span>
                        <strong>Make this my default address</strong>

                        <small>
                            This address will automatically be selected
                            during checkout.
                        </small>
                    </span>

                </label>



                <!-- BUTTONS -->

                <div class="vg-address-form-actions">

                    <button type="reset"
                            class="vg-cancel-btn">

                        Cancel

                    </button>

                    <button type="submit"
                            class="vg-save-address-btn">

                        <i class="fa-solid fa-check"></i>

                        Save Address

                    </button>

                </div>

            </form>

        </div>

    </div>

</section>

<?php include('footer.php'); ?>