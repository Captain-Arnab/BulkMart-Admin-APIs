<?php include('header.php'); ?>

<main class="vc-legal-page vc-help-page" data-vc-help="support">

    <section class="vc-legal-hero">
        <div class="vc-legal-container">
            <span class="vc-legal-eyebrow">
                <i class="fa-solid fa-headset"></i>
                Customer Support
            </span>
            <h1>We’re here to <span>help</span></h1>
            <p>
                Reach Veggiicart support for order issues, delivery questions, payments, or account help.
            </p>
        </div>
    </section>

    <section class="vc-legal-main">
        <div class="vc-legal-container">

            <div class="vc-support-grid">

                <a href="tel:+918099999086" class="vc-support-card">
                    <span class="vc-support-card-icon"><i class="fa-solid fa-phone-volume"></i></span>
                    <strong>Call us</strong>
                    <span>+91 8099999086</span>
                </a>

                <a href="mailto:Veggiicart@gmail.com" class="vc-support-card">
                    <span class="vc-support-card-icon"><i class="fa-regular fa-envelope"></i></span>
                    <strong>Email support</strong>
                    <span>Veggiicart@gmail.com</span>
                </a>

                <a href="https://wa.me/918099999086"
                   class="vc-support-card"
                   target="_blank"
                   rel="noopener noreferrer">
                    <span class="vc-support-card-icon"><i class="fa-brands fa-whatsapp"></i></span>
                    <strong>WhatsApp</strong>
                    <span>Chat with our team</span>
                </a>

                <a href="faq.php" class="vc-support-card">
                    <span class="vc-support-card-icon"><i class="fa-regular fa-circle-question"></i></span>
                    <strong>FAQs</strong>
                    <span>Browse common answers</span>
                </a>

            </div>

            <div class="vc-support-layout">

                <section class="vc-support-panel">
                    <div class="vc-support-panel-head">
                        <h2>Submit a support ticket</h2>
                        <p>Logged-in customers can send a request and track replies from our team.</p>
                    </div>

                    <div id="vcSupportGuestNote" class="vc-support-guest" hidden>
                        <p>Please log in to submit a support ticket.</p>
                        <a href="login.php?next=support.php" class="vc-help-cta-btn">Login to continue</a>
                    </div>

                    <form id="vcSupportTicketForm" class="vc-support-form" hidden>
                        <label>
                            Subject
                            <select name="subject_type" required>
                                <option value="">Select a topic</option>
                                <option value="Order issue">Order issue</option>
                                <option value="Delivery">Delivery</option>
                                <option value="Payment">Payment</option>
                                <option value="Account / KYC">Account / KYC</option>
                                <option value="Product quality">Product quality</option>
                                <option value="Other">Other</option>
                            </select>
                        </label>

                        <label>
                            Related order ID (optional)
                            <input type="number"
                                   name="related_order_id"
                                   min="1"
                                   placeholder="e.g. 1024">
                        </label>

                        <label>
                            Describe your issue
                            <textarea name="description"
                                      rows="5"
                                      required
                                      placeholder="Tell us what happened and how we can help…"></textarea>
                        </label>

                        <button type="submit" class="vc-help-cta-btn" id="vcSupportSubmitBtn">
                            Submit ticket
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </section>

                <section class="vc-support-panel">
                    <div class="vc-support-panel-head">
                        <h2>Your recent tickets</h2>
                        <p>Status updates appear here after you submit a request.</p>
                    </div>
                    <div id="vcSupportTickets" class="vc-support-tickets">
                        <p class="vc-help-loading">Loading…</p>
                    </div>
                </section>

            </div>

        </div>
    </section>

</main>

<?php include('footer.php'); ?>
