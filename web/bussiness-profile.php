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
                            <span class="vc-bp-business-type">
                                Retail Shop
                            </span>

                            <h2>Fresh Mart Retail</h2>

                            <p>
                                Business ID:
                                <strong>VC-BIZ-2026-1024</strong>
                            </p>
                        </div>


                        <span class="vc-bp-status approved">
                            <i class="fa-solid fa-circle-check"></i>
                            Verified Business
                        </span>

                    </div>


                    <div class="vc-bp-mini-info">

                        <div>
                            <i class="fa-solid fa-user"></i>

                            <span>
                                <small>Owner</small>
                                <strong>Rahul Sharma</strong>
                            </span>
                        </div>


                        <div>
                            <i class="fa-solid fa-phone"></i>

                            <span>
                                <small>Mobile</small>
                                <strong>+91 98765 43210</strong>
                            </span>
                        </div>


                        <div>
                            <i class="fa-solid fa-envelope"></i>

                            <span>
                                <small>Email</small>
                                <strong>business@example.com</strong>
                            </span>
                        </div>


                        <div>
                            <i class="fa-solid fa-location-dot"></i>

                            <span>
                                <small>Location</small>
                                <strong>Rishikesh, Uttarakhand</strong>
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
                                <strong>Fresh Mart Retail</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>Business Type</span>
                                <strong>Retail Shop</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>Owner Name</span>
                                <strong>Rahul Sharma</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>Mobile Number</span>
                                <strong>+91 98765 43210</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>Email Address</span>
                                <strong>business@example.com</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>GST Number</span>
                                <strong>05ABCDE1234F1Z5</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>FSSAI Licence</span>
                                <strong>12345678901234</strong>
                            </div>

                            <div class="vc-bp-detail-item">
                                <span>PAN Number</span>
                                <strong>ABCDE1234F</strong>
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
                                <strong>
                                    Fresh Mart Retail
                                </strong>

                                <p>
                                    Shop No. 12, Green Market Complex,<br>
                                    Near Railway Road,<br>
                                    Rishikesh, Uttarakhand – 249201
                                </p>

                                <a href="#">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                    View on Google Maps
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


                        <div class="vc-bp-documents">

                            <!-- GST -->
                            <div class="vc-bp-document">

                                <div class="vc-bp-doc-info">

                                    <span class="vc-bp-doc-icon">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </span>

                                    <div>
                                        <strong>GST Certificate</strong>
                                        <small>gst-certificate.pdf</small>

                                        <span>
                                            Uploaded: 14 Aug 2026
                                        </span>
                                    </div>

                                </div>


                                <span class="vc-bp-doc-status verified">
                                    <i class="fa-solid fa-check"></i>
                                    Verified
                                </span>


                                <button
                                    type="button"
                                    class="vc-bp-doc-action">

                                    <i class="fa-solid fa-eye"></i>
                                    View

                                </button>

                            </div>


                            <!-- FSSAI -->
                            <div class="vc-bp-document">

                                <div class="vc-bp-doc-info">

                                    <span class="vc-bp-doc-icon">
                                        <i class="fa-solid fa-certificate"></i>
                                    </span>

                                    <div>
                                        <strong>FSSAI Licence</strong>
                                        <small>fssai-license.pdf</small>

                                        <span>
                                            Uploaded: 14 Aug 2026
                                        </span>
                                    </div>

                                </div>


                                <span class="vc-bp-doc-status verified">
                                    <i class="fa-solid fa-check"></i>
                                    Verified
                                </span>


                                <button
                                    type="button"
                                    class="vc-bp-doc-action">

                                    <i class="fa-solid fa-eye"></i>
                                    View

                                </button>

                            </div>


                            <!-- PAN -->
                            <div class="vc-bp-document">

                                <div class="vc-bp-doc-info">

                                    <span class="vc-bp-doc-icon">
                                        <i class="fa-solid fa-id-card"></i>
                                    </span>

                                    <div>
                                        <strong>PAN Card</strong>
                                        <small>pan-card.jpg</small>

                                        <span>
                                            Uploaded: 14 Aug 2026
                                        </span>
                                    </div>

                                </div>


                                <span class="vc-bp-doc-status verified">
                                    <i class="fa-solid fa-check"></i>
                                    Verified
                                </span>


                                <button
                                    type="button"
                                    class="vc-bp-doc-action">

                                    <i class="fa-solid fa-eye"></i>
                                    View

                                </button>

                            </div>


                            <!-- SHOP REGISTRATION -->
                            <div class="vc-bp-document">

                                <div class="vc-bp-doc-info">

                                    <span class="vc-bp-doc-icon">
                                        <i class="fa-solid fa-store"></i>
                                    </span>

                                    <div>
                                        <strong>Shop Registration</strong>
                                        <small>shop-registration.pdf</small>

                                        <span>
                                            Uploaded: 14 Aug 2026
                                        </span>
                                    </div>

                                </div>


                                <span class="vc-bp-doc-status reviewing">
                                    <i class="fa-solid fa-clock"></i>
                                    Under Review
                                </span>


                                <button
                                    type="button"
                                    class="vc-bp-doc-action">

                                    <i class="fa-solid fa-eye"></i>
                                    View

                                </button>

                            </div>


                            <!-- EXPIRED DOCUMENT -->
                            <div class="vc-bp-document vc-bp-doc-warning">

                                <div class="vc-bp-doc-info">

                                    <span class="vc-bp-doc-icon warning">
                                        <i class="fa-solid fa-file-circle-exclamation"></i>
                                    </span>

                                    <div>
                                        <strong>Trade Licence</strong>
                                        <small>trade-license.pdf</small>

                                        <span>
                                            Expired: 01 Aug 2026
                                        </span>
                                    </div>

                                </div>


                                <span class="vc-bp-doc-status expired">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Expired
                                </span>


                                <label class="vc-bp-replace-btn">

                                    <input
                                        type="file"
                                        accept="image/*,.pdf">

                                    <i class="fa-solid fa-arrows-rotate"></i>
                                    <span>Replace</span>

                                </label>

                            </div>


                            <!-- REJECTED DOCUMENT -->
                            <div class="vc-bp-document vc-bp-doc-rejected">

                                <div class="vc-bp-doc-info">

                                    <span class="vc-bp-doc-icon rejected">
                                        <i class="fa-solid fa-camera"></i>
                                    </span>

                                    <div>
                                        <strong>Shop-front Photo</strong>
                                        <small>shop-front.jpg</small>

                                        <span>
                                            Reason: Business signage unclear
                                        </span>
                                    </div>

                                </div>


                                <span class="vc-bp-doc-status rejected">
                                    <i class="fa-solid fa-xmark"></i>
                                    Rejected
                                </span>


                                <label class="vc-bp-replace-btn">

                                    <input
                                        type="file"
                                        accept="image/*"
                                        capture="environment">

                                    <i class="fa-solid fa-camera"></i>
                                    <span>Replace</span>

                                </label>

                            </div>

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


                        <div class="vc-bp-verification-badge">

                            <i class="fa-solid fa-circle-check"></i>

                            <div>
                                <strong>Approved</strong>
                                <small>
                                    Business successfully verified
                                </small>
                            </div>

                        </div>


                        <div class="vc-bp-verification-meta">

                            <div>
                                <span>Application ID</span>
                                <strong>VC-BIZ-2026-1024</strong>
                            </div>

                            <div>
                                <span>Submitted</span>
                                <strong>14 August 2026</strong>
                            </div>

                            <div>
                                <span>Approved</span>
                                <strong>16 August 2026</strong>
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
                                    <strong>3</strong>
                                    Verified
                                </p>
                            </div>


                            <div>
                                <span class="yellow">
                                    <i class="fa-solid fa-clock"></i>
                                </span>

                                <p>
                                    <strong>1</strong>
                                    Under Review
                                </p>
                            </div>


                            <div>
                                <span class="orange">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </span>

                                <p>
                                    <strong>1</strong>
                                    Expired
                                </p>
                            </div>


                            <div>
                                <span class="red">
                                    <i class="fa-solid fa-xmark"></i>
                                </span>

                                <p>
                                    <strong>1</strong>
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

                        <h3>Business Account Active</h3>

                        <p>
                            Your verified business account gives you access
                            to bulk ordering and eligible business pricing.
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