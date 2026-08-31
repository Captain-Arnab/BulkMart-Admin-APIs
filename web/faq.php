<?php include('header.php'); ?>

<main class="vc-legal-page vc-help-page" data-vc-help="faq">

    <section class="vc-legal-hero">
        <div class="vc-legal-container">
            <span class="vc-legal-eyebrow">
                <i class="fa-regular fa-circle-question"></i>
                Help Centre
            </span>
            <h1>Frequently Asked <span>Questions</span></h1>
            <p>
                Quick answers about ordering, delivery, payments, and your Veggiicart account.
            </p>
        </div>
    </section>

    <section class="vc-legal-main">
        <div class="vc-legal-container">
            <div class="vc-help-toolbar">
                <label class="vc-help-search" for="vcFaqSearch">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search"
                           id="vcFaqSearch"
                           placeholder="Search FAQs…"
                           autocomplete="off">
                </label>
                <div class="vc-help-filters" id="vcFaqFilters" aria-label="FAQ categories"></div>
            </div>

            <div class="vc-faq-list" id="vcFaqList">
                <p class="vc-help-loading">Loading FAQs…</p>
            </div>

            <div class="vc-help-cta">
                <div>
                    <h2>Still need help?</h2>
                    <p>Our support team can help with orders, delivery, and account issues.</p>
                </div>
                <a href="support.php" class="vc-help-cta-btn">
                    Contact Support
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

</main>

<?php include('footer.php'); ?>
