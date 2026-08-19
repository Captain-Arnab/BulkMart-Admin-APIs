<?php include('header.php'); ?>


<!-- ============================================
     VEGIICART BUSINESS VERIFICATION STATUS PAGE
============================================= -->

<main class="vc-verification-page">

    <!-- HERO -->
    <section class="vc-verification-hero">
        <div class="vc-verification-container">

            <span class="vc-verification-eyebrow">
                <i class="fa-solid fa-shield-check"></i>
                Vegiicart Business
            </span>

            <h1>
                Business Verification
                <span>Status</span>
            </h1>

            <p>
                Track your business registration application, review submitted
                documents and complete any action required for approval.
            </p>

        </div>
    </section>


    <section class="vc-verification-main">

        <div class="vc-verification-container">

            <!-- DEMO STATUS SWITCHER
                 Remove this block after PHP integration -->
            <div class="vc-status-demo">

                <span>Preview Status:</span>

                <button
                    type="button"
                    class="active"
                    data-status-target="pending">
                    Pending
                </button>

                <button
                    type="button"
                    data-status-target="approved">
                    Approved
                </button>

                <button
                    type="button"
                    data-status-target="rejected">
                    Rejected
                </button>

            </div>


            <!-- ==========================================
                 COMMON APPLICATION INFORMATION
            =========================================== -->
            <div class="vc-application-overview">

                <div class="vc-application-number-box">

                    <span class="vc-application-icon">
                        <i class="fa-solid fa-file-signature"></i>
                    </span>

                    <div>
                        <small>Application Number</small>
                        <strong>VC-BIZ-2026-1024</strong>
                    </div>

                </div>


                <div class="vc-application-meta">

                    <div>
                        <span>
                            <i class="fa-regular fa-calendar"></i>
                        </span>

                        <div>
                            <small>Submitted Date</small>
                            <strong>14 August 2026</strong>
                        </div>
                    </div>


                    <div>
                        <span>
                            <i class="fa-solid fa-store"></i>
                        </span>

                        <div>
                            <small>Business</small>
                            <strong>Fresh Mart Retail</strong>
                        </div>
                    </div>


                    <div>
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>

                        <div>
                            <small>Owner</small>
                            <strong>Rahul Sharma</strong>
                        </div>
                    </div>

                </div>

            </div>


            <!-- ==========================================
                 PENDING STATUS
            =========================================== -->

            <section
                class="vc-verification-status-panel active"
                id="vcStatusPending">

                <div class="vc-status-card vc-status-pending">

                    <div class="vc-status-visual">

                        <div class="vc-status-icon">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>

                        <span class="vc-status-pill">
                            Pending Verification
                        </span>

                    </div>


                    <div class="vc-status-content">

                        <span class="vc-status-small-title">
                            Application Under Review
                        </span>

                        <h2>
                            We're Reviewing Your
                            <span>Business Application</span>
                        </h2>

                        <p>
                            Your application has been received successfully.
                            Our verification team is reviewing your business
                            information and uploaded documents.
                        </p>


                        <div class="vc-review-notice">

                            <i class="fa-solid fa-circle-info"></i>

                            <div>
                                <strong>No action is required right now.</strong>

                                <p>
                                    You'll receive an update once verification
                                    is completed or if additional information is
                                    required.
                                </p>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- PROGRESS -->
                <div class="vc-status-section-card">

                    <div class="vc-section-heading">

                        <div>
                            <span>Verification Progress</span>
                            <h3>Application Journey</h3>
                        </div>

                    </div>


                    <div class="vc-verification-progress">

                        <div class="vc-progress-step completed">

                            <span>
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <div>
                                <strong>Application Submitted</strong>
                                <small>14 Aug 2026</small>
                            </div>

                        </div>


                        <div class="vc-progress-line completed"></div>


                        <div class="vc-progress-step current">

                            <span>
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>

                            <div>
                                <strong>Under Review</strong>
                                <small>Verification in progress</small>
                            </div>

                        </div>


                        <div class="vc-progress-line"></div>


                        <div class="vc-progress-step">

                            <span>
                                <i class="fa-solid fa-user-check"></i>
                            </span>

                            <div>
                                <strong>Final Decision</strong>
                                <small>Pending</small>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- DOCUMENTS -->
                <div class="vc-status-section-card">

                    <div class="vc-section-heading">

                        <div>
                            <span>Submitted Files</span>
                            <h3>Uploaded Documents</h3>
                        </div>

                        <strong class="vc-document-total">
                            6 Documents
                        </strong>

                    </div>


                    <div class="vc-document-grid">

                        <div class="vc-document-item">

                            <span class="vc-document-icon">
                                <i class="fa-solid fa-file-pdf"></i>
                            </span>

                            <div>
                                <strong>GST Certificate</strong>
                                <small>gst-certificate.pdf</small>
                            </div>

                            <span class="vc-doc-status reviewing">
                                Reviewing
                            </span>

                        </div>


                        <div class="vc-document-item">

                            <span class="vc-document-icon">
                                <i class="fa-solid fa-file-image"></i>
                            </span>

                            <div>
                                <strong>PAN Card</strong>
                                <small>pan-card.jpg</small>
                            </div>

                            <span class="vc-doc-status verified">
                                Verified
                            </span>

                        </div>


                        <div class="vc-document-item">

                            <span class="vc-document-icon">
                                <i class="fa-solid fa-id-card"></i>
                            </span>

                            <div>
                                <strong>Aadhaar Card</strong>
                                <small>aadhaar.pdf</small>
                            </div>

                            <span class="vc-doc-status verified">
                                Verified
                            </span>

                        </div>


                        <div class="vc-document-item">

                            <span class="vc-document-icon">
                                <i class="fa-solid fa-shop"></i>
                            </span>

                            <div>
                                <strong>Shop Registration</strong>
                                <small>registration.pdf</small>
                            </div>

                            <span class="vc-doc-status reviewing">
                                Reviewing
                            </span>

                        </div>


                        <div class="vc-document-item">

                            <span class="vc-document-icon">
                                <i class="fa-solid fa-camera"></i>
                            </span>

                            <div>
                                <strong>Shop-front Photo</strong>
                                <small>shop-front.jpg</small>
                            </div>

                            <span class="vc-doc-status verified">
                                Verified
                            </span>

                        </div>


                        <div class="vc-document-item">

                            <span class="vc-document-icon">
                                <i class="fa-solid fa-certificate"></i>
                            </span>

                            <div>
                                <strong>FSSAI Licence</strong>
                                <small>fssai.pdf</small>
                            </div>

                            <span class="vc-doc-status reviewing">
                                Reviewing
                            </span>

                        </div>

                    </div>

                </div>


                <div class="vc-status-actions">

                    <a href="contact.php" class="vc-outline-action">
                        <i class="fa-solid fa-headset"></i>
                        Contact Support
                    </a>

                    <a href="business-profile.php" class="vc-primary-action">
                        View Application
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </section>


            <!-- ==========================================
                 APPROVED STATUS
            =========================================== -->

            <section
                class="vc-verification-status-panel"
                id="vcStatusApproved">

                <div class="vc-status-card vc-status-approved">

                    <div class="vc-status-visual">

                        <div class="vc-status-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                        <span class="vc-status-pill">
                            Business Approved
                        </span>

                    </div>


                    <div class="vc-status-content">

                        <span class="vc-status-small-title">
                            Verification Successful
                        </span>

                        <h2>
                            Congratulations!
                            <span>Your Business is Approved.</span>
                        </h2>

                        <p>
                            Your business verification has been completed
                            successfully. You can now access Vegiicart's
                            business shopping features, bulk pricing and
                            eligible business benefits.
                        </p>


                        <div class="vc-approved-benefits">

                            <div>
                                <i class="fa-solid fa-tags"></i>

                                <span>
                                    <strong>Business Pricing</strong>
                                    Special rates on eligible products
                                </span>
                            </div>


                            <div>
                                <i class="fa-solid fa-boxes-stacked"></i>

                                <span>
                                    <strong>Bulk Ordering</strong>
                                    Order larger quantities easily
                                </span>
                            </div>


                            <div>
                                <i class="fa-solid fa-truck-fast"></i>

                                <span>
                                    <strong>Business Delivery</strong>
                                    Convenient delivery support
                                </span>
                            </div>

                        </div>

                    </div>

                </div>


                <div class="vc-approved-summary">

                    <div class="vc-approved-profile">

                        <span>
                            <i class="fa-solid fa-store"></i>
                        </span>

                        <div>
                            <small>Verified Business</small>
                            <h3>Fresh Mart Retail</h3>

                            <p>
                                Retail Shop · Rishikesh, Uttarakhand
                            </p>
                        </div>

                        <strong>
                            <i class="fa-solid fa-circle-check"></i>
                            Verified
                        </strong>

                    </div>

                </div>


                <div class="vc-status-actions vc-approved-actions">

                    <a
                        href="business-profile.php"
                        class="vc-outline-action">

                        <i class="fa-solid fa-building-user"></i>
                        View Business Profile
                    </a>


                    <a
                        href="products.php"
                        class="vc-primary-action">

                        <i class="fa-solid fa-cart-shopping"></i>
                        Start Shopping
                    </a>

                </div>

            </section>


            <!-- ==========================================
                 REJECTED STATUS
            =========================================== -->

            <section
                class="vc-verification-status-panel"
                id="vcStatusRejected">

                <div class="vc-status-card vc-status-rejected">

                    <div class="vc-status-visual">

                        <div class="vc-status-icon">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <span class="vc-status-pill">
                            Action Required
                        </span>

                    </div>


                    <div class="vc-status-content">

                        <span class="vc-status-small-title">
                            Verification Not Completed
                        </span>

                        <h2>
                            Your Application Needs
                            <span>Some Corrections</span>
                        </h2>

                        <p>
                            We were unable to approve your application because
                            some submitted documents could not be verified.
                            Please review the reason below and upload the
                            corrected documents.
                        </p>

                    </div>

                </div>


                <!-- REJECTION REASON -->
                <div class="vc-rejection-reason">

                    <span class="vc-rejection-icon">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </span>

                    <div>
                        <small>Rejection Reason</small>

                        <h3>
                            GST certificate is unclear and the shop-front
                            photograph does not clearly show the business name.
                        </h3>

                        <p>
                            Please upload a clear GST certificate and a recent
                            shop-front photograph where the shop signage is
                            clearly visible.
                        </p>
                    </div>

                </div>


                <!-- REJECTED DOCUMENTS -->
                <div class="vc-status-section-card">

                    <div class="vc-section-heading">

                        <div>
                            <span>Action Required</span>
                            <h3>Documents Requiring Re-upload</h3>
                        </div>

                    </div>


                    <div class="vc-rejected-documents">

                        <!-- DOC 1 -->
                        <div class="vc-rejected-document">

                            <div class="vc-rejected-doc-info">

                                <span>
                                    <i class="fa-solid fa-file-pdf"></i>
                                </span>

                                <div>
                                    <strong>GST Certificate</strong>
                                    <small>gst-certificate.pdf</small>
                                </div>

                            </div>


                            <div class="vc-rejected-message">

                                <span>
                                    <i class="fa-solid fa-xmark"></i>
                                    Rejected
                                </span>

                                <p>
                                    Document image is blurred and GST number
                                    cannot be verified.
                                </p>

                            </div>


                            <label class="vc-reupload-btn">

                                <input
                                    type="file"
                                    accept="image/*,.pdf">

                                <i class="fa-solid fa-cloud-arrow-up"></i>

                                <span>Re-upload</span>

                            </label>

                        </div>


                        <!-- DOC 2 -->
                        <div class="vc-rejected-document">

                            <div class="vc-rejected-doc-info">

                                <span>
                                    <i class="fa-solid fa-camera"></i>
                                </span>

                                <div>
                                    <strong>Shop-front Photo</strong>
                                    <small>shop-front.jpg</small>
                                </div>

                            </div>


                            <div class="vc-rejected-message">

                                <span>
                                    <i class="fa-solid fa-xmark"></i>
                                    Rejected
                                </span>

                                <p>
                                    Store signage and business name are not
                                    clearly visible.
                                </p>

                            </div>


                            <label class="vc-reupload-btn">

                                <input
                                    type="file"
                                    accept="image/*"
                                    capture="environment">

                                <i class="fa-solid fa-camera"></i>

                                <span>Re-upload</span>

                            </label>

                        </div>

                    </div>

                </div>


                <div class="vc-rejection-help">

                    <i class="fa-solid fa-lightbulb"></i>

                    <div>
                        <strong>Before re-uploading</strong>

                        <p>
                            Make sure documents are clear, complete and
                            readable. Images should not be blurred, cropped or
                            covered by glare.
                        </p>
                    </div>

                </div>


                <div class="vc-status-actions">

                    <a href="contact.php" class="vc-outline-action">
                        <i class="fa-solid fa-headset"></i>
                        Contact Support
                    </a>


                    <button
                        type="button"
                        class="vc-primary-action vc-resubmit-button"
                        id="vcResubmitApplication">

                        <i class="fa-solid fa-paper-plane"></i>
                        Resubmit Application

                    </button>

                </div>

            </section>

        </div>

    </section>

</main>


<!-- RESUBMIT SUCCESS POPUP -->

<div class="vc-resubmit-modal" id="vcResubmitModal">

    <div class="vc-resubmit-modal-card">

        <button
            type="button"
            class="vc-modal-close"
            id="vcResubmitClose">

            <i class="fa-solid fa-xmark"></i>

        </button>

        <span class="vc-modal-check">
            <i class="fa-solid fa-check"></i>
        </span>

        <h3>Application Resubmitted</h3>

        <p>
            Your corrected documents have been submitted for another review.
            You'll receive an update once verification is completed.
        </p>

        <button
            type="button"
            class="vc-modal-done"
            id="vcResubmitDone">
            Done
        </button>

    </div>

</div>


<?php include('footer.php'); ?>