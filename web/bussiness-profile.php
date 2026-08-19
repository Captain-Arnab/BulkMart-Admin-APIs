<?php include('header.php'); ?>

<!-- =========================================
     VEGIICART BUSINESS PROFILE PAGE
========================================== -->

<main class="vc-business-profile-page">

    <!-- HERO -->
    <section class="vc-bp-hero">
        <div class="vc-bp-container">

            <div class="vc-bp-hero-inner">

                <div>
                    <span class="vc-bp-eyebrow">
                        <i class="fa-solid fa-building"></i>
                        Business Account
                    </span>

                    <h1>
                        Business <span>Profile</span>
                    </h1>

                    <p>
                        Manage your registered business information,
                        verification details and uploaded documents.
                    </p>
                </div>

                <a href="edit-business-profile.php"
                   class="vc-bp-edit-main">

                    <i class="fa-solid fa-pen-to-square"></i>
                    Edit Business Details

                </a>

            </div>

        </div>
    </section>


    <section class="vc-bp-main">
        <div class="vc-bp-container">

            <!-- PROFILE SUMMARY -->
            <div class="vc-bp-profile-summary">

                <div class="vc-bp-avatar">
                    <i class="fa-solid fa-store"></i>
                </div>

                <div class="vc-bp-summary-content">

                    <div class="vc-bp-summary-top">

                        <div>
                            <span class="vc-bp-business-type" id="vcBpType">—</span>

                            <h2 id="vcBpName">—</h2>

                            <p>
                                Business ID:
                                <strong id="vcBpId">—</strong>
                            </p>
                        </div>


                        <span class="vc-bp-status pending" id="vcBpStatusBadge">
                            <i class="fa-solid fa-clock"></i>
                            <span id="vcBpStatusText">Pending verification</span>
                        </span>

                    </div>


                    <div class="vc-bp-mini-info">

                        <div>
                            <i class="fa-solid fa-user"></i>

                            <span>
                                <small>Owner</small>
                                <strong id="vcBpOwner">—</strong>
                            </span>
                        </div>


                        <div>
                            <i class="fa-solid fa-phone"></i>

                            <span>
                                <small>Mobile</small>
                                <strong id="vcBpMobile">—</strong>
                            </span>
                        </div>


                        <div>
                            <i class="fa-solid fa-envelope"></i>

                            <span>
                                <small>Email</small>
                                <strong id="vcBpEmail">—</strong>
                            </span>
                        </div>


                        <div>
                            <i class="fa-solid fa-location-dot"></i>

                            <span>
                                <small>Location</small>
                                <strong id="vcBpLocation">—</strong>
                            </span>
                        </div>

                    </div>

                </div>

            </div>


            <!-- MAIN GRID -->
            <div class="vc-bp-layout">

                <!-- LEFT -->
                <div class="vc-bp-left">

                    <!-- BUSINESS DETAILS -->
                    <div class="vc-bp-card">

                        <div class="vc-bp-card-head">

                            <div>
                                <span>Registered Information</span>
                                <h3>Business Details</h3>
                            </div>

                            <a href="edit-business-profile.php">
                                <i class="fa-solid fa-pen"></i>
                                Edit
                            </a>

                        </div>


                        <div class="vc-bp-details-grid">

                            <div class="vc-bp-detail-item">
                                <span>Business Name</span>
                                <strong id="vcBpDetailName">—</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>Business Type</span>
                                <strong id="vcBpDetailType">—</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>Owner Name</span>
                                <strong id="vcBpDetailOwner">—</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>Mobile Number</span>
                                <strong id="vcBpDetailMobile">—</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>Email Address</span>
                                <strong id="vcBpDetailEmail">—</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>GST Number</span>
                                <strong id="vcBpGst">—</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>FSSAI Licence</span>
                                <strong id="vcBpFssai">—</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>PAN Number</span>
                                <strong id="vcBpPan">—</strong>
                            </div>

                        </div>

                    </div>


                    <!-- ADDRESS -->
                    <div class="vc-bp-card">

                        <div class="vc-bp-card-head">

                            <div>
                                <span>Registered Location</span>
                                <h3>Business Address</h3>
                            </div>

                            <a href="edit-business-profile.php">
                                <i class="fa-solid fa-pen"></i>
                                Edit
                            </a>

                        </div>


                        <div class="vc-bp-address">

                            <span class="vc-bp-address-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>

                            <div>
                                <strong id="vcBpAddrName">—</strong>

                                <p id="vcBpAddrText">No address saved yet.</p>

                                <a href="manage-address.php">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                    Manage addresses
                                </a>
                            </div>

                        </div>

                    </div>


                    <!-- DOCUMENT SECTION -->
                    <div class="vc-bp-card">

                        <div class="vc-bp-card-head vc-bp-doc-head">

                            <div>
                                <span>Verification Documents</span>
                                <h3>Uploaded Documents</h3>
                            </div>

                            <button
                                type="button"
                                class="vc-bp-upload-new"
                                id="vcUploadNewDocument">

                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                Upload New Document

                            </button>

                        </div>


                        <div class="vc-bp-documents" id="vcBpDocuments">
                            <p class="vc-live-empty">Loading documents…</p>
                        </div>

                    </div>

                </div>


                                <!-- RIGHT -->
                <aside class="vc-bp-right">

                    <!-- VERIFICATION -->
                    <div class="vc-bp-side-card">

                        <div class="vc-bp-side-title">

                            <span>
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>

                            <h3>Verification Status</h3>

                        </div>


                        <div class="vc-bp-verification-badge" id="vcBpVerifyBox">

                            <i class="fa-solid fa-clock"></i>

                            <div>
                                <strong id="vcBpVerifyTitle">Pending</strong>
                                <small id="vcBpVerifySub">Verification in progress</small>
                            </div>

                        </div>


                        <div class="vc-bp-verification-meta">

                            <div>
                                <span>Application ID</span>
                                <strong id="vcBpAppId">—</strong>
                            </div>

                            <div>
                                <span>Submitted</span>
                                <strong id="vcBpSubmitted">—</strong>
                            </div>

                            <div>
                                <span>Status</span>
                                <strong id="vcBpKycRaw">—</strong>
                            </div>

                        </div>

                    </div>


                    <!-- DOCUMENT SUMMARY -->
                    <div class="vc-bp-side-card">

                        <div class="vc-bp-side-title">

                            <span>
                                <i class="fa-solid fa-folder-open"></i>
                            </span>

                            <h3>Document Summary</h3>

                        </div>


                        <div class="vc-bp-document-summary">

                            <div>
                                <span class="green">
                                    <i class="fa-solid fa-check"></i>
                                </span>

                                <p>
                                    <strong id="vcBpDocVerified">0</strong>
                                    Verified
                                </p>
                            </div>


                            <div>
                                <span class="yellow">
                                    <i class="fa-solid fa-clock"></i>
                                </span>

                                <p>
                                    <strong id="vcBpDocReview">0</strong>
                                    Under Review
                                </p>
                            </div>


                            <div>
                                <span class="orange">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </span>

                                <p>
                                    <strong id="vcBpDocExpired">0</strong>
                                    Missing
                                </p>
                            </div>


                            <div>
                                <span class="red">
                                    <i class="fa-solid fa-xmark"></i>
                                </span>

                                <p>
                                    <strong id="vcBpDocRejected">0</strong>
                                    Rejected
                                </p>
                            </div>

                        </div>

                    </div>


                    <!-- ACCOUNT BENEFITS -->
                    <div class="vc-bp-benefits">

                        <span class="vc-bp-benefit-icon">
                            <i class="fa-solid fa-crown"></i>
                        </span>

                        <h3 id="vcBpBenefitTitle">Business account</h3>

                        <p id="vcBpBenefitCopy">
                            Complete verification to unlock bulk ordering and eligible business pricing.
                        </p>

                        <a href="products.php">
                            Start Shopping
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>


                    <!-- SUPPORT -->
                    <div class="vc-bp-support">

                        <i class="fa-solid fa-headset"></i>

                        <div>
                            <strong>Need Assistance?</strong>

                            <p>
                                Contact our business support team for
                                verification or document-related help.
                            </p>

                            <a href="contact.php">
                                Contact Support
                            </a>
                        </div>

                    </div>

                </aside>

            </div>

        </div>
    </section>

</main>


<!-- =========================================
     UPLOAD DOCUMENT MODAL
========================================== -->

<div class="vc-bp-modal" id="vcBpUploadModal">

    <div class="vc-bp-modal-card">

        <button
            type="button"
            class="vc-bp-modal-close"
            id="vcBpModalClose">

            <i class="fa-solid fa-xmark"></i>

        </button>


        <div class="vc-bp-modal-icon">
            <i class="fa-solid fa-cloud-arrow-up"></i>
        </div>

        <h3>Upload New Document</h3>

        <p>
            Select the document type and upload a clear JPG, PNG or PDF file.
        </p>


        <div class="vc-bp-modal-field">

            <label>Document Type</label>

            <select id="vcNewDocumentType">

                <option value="">
                    Select document
                </option>

                <option>GST Certificate</option>
                <option>FSSAI Licence</option>
                <option>Shop Registration</option>
                <option>MSME Certificate</option>
                <option>Trade Licence</option>
                <option>PAN Card</option>
                <option>Aadhaar Card</option>
                <option>Shop-front Photo</option>
                <option>Business Visiting Card</option>

            </select>

        </div>


        <label class="vc-bp-modal-upload">

            <input
                type="file"
                id="vcNewDocumentFile"
                accept="image/*,.pdf">

            <i class="fa-solid fa-file-arrow-up"></i>

            <strong>Choose File</strong>

            <small id="vcNewDocumentName">
                JPG, PNG or PDF · Maximum 5 MB
            </small>

        </label>


        <button
            type="button"
            class="vc-bp-modal-submit"
            id="vcBpUploadSubmit">

            <i class="fa-solid fa-cloud-arrow-up"></i>
            Upload Document

        </button>

    </div>

</div>

<?php include('footer.php'); ?>