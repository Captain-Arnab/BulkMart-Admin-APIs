<?php include('header.php'); ?>

<section class="vc-product-page">

    <div class="vc-product-container">

        <!-- BREADCRUMB -->
        <div class="vc-product-breadcrumb">
            <a href="index.php">Home</a>
            <i class="fa-solid fa-chevron-right"></i>

            <a href="shop.php">Shop</a>
            <i class="fa-solid fa-chevron-right"></i>

            <a href="vegetables.php">Vegetables</a>
            <i class="fa-solid fa-chevron-right"></i>

            <span>Fresh Tomatoes</span>
        </div>


        <!-- =========================================
             MAIN PRODUCT AREA
        ========================================== -->
        <div class="vc-product-main">


            <!-- =====================================
                 PRODUCT GALLERY
            ====================================== -->
            <div class="vc-product-gallery">


                <div class="vc-product-main-image">

                    <span class="vc-product-discount" hidden>
                        20% OFF
                    </span>


                    <button
                        type="button"
                        class="vc-product-wishlist"
                        aria-label="Add to wishlist">

                        <i class="fa-regular fa-heart"></i>

                    </button>


                    <img
                        id="vcMainProductImage"
                        src="images/vegiicart-logo.jpeg"
                        alt="Product image">
                    <iframe
                        id="vcMainProductPdf"
                        class="vc-main-pdf"
                        title="PDF preview"
                        hidden></iframe>

                </div>


                <div class="vc-product-thumbnails" id="vcProductThumbs"></div>

            </div>



            <!-- =====================================
                 PRODUCT INFORMATION
            ====================================== -->
            <div class="vc-product-info">


                <span class="vc-product-category">
                    Fresh Vegetables
                </span>


                <h1>
                    Farm Fresh Red Tomatoes
                </h1>


                <!-- RATING -->
                <div class="vc-product-rating">

                    <div class="vc-stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>

                    <span>
                        4.8
                    </span>

                    <a href="#reviews">
                        124 Reviews
                    </a>

                </div>


                <!-- PRICE -->
                <div class="vc-product-price">

                    <strong id="vcProductPrice">
                        ₹60
                    </strong>

                    <small class="vc-product-unit"></small>

                    <del id="vcProductMrp" hidden></del>

                    <span id="vcProductSave" hidden></span>

                </div>


                <!-- SHORT DESCRIPTION -->
                <p class="vc-product-short-description" id="vcProductShortDesc">
                    Loading product details…
                </p>


                <!-- STOCK -->
                <div class="vc-product-stock">

                    <span>
                        <i class="fa-solid fa-circle-check"></i>
                        In Stock
                    </span>

                    <small>
                        Fresh stock available for delivery
                    </small>

                </div>


                <!-- =====================================
                     QUANTITY TIERS (25 / 50 / 75 / 100 KG)
                ====================================== -->
                <div class="vc-product-option" id="vcQtyTierBlock">

                    <div class="vc-product-option-title">

                        <strong>
                            Select Quantity
                        </strong>

                        <span id="vcQtyTierHint">
                            Fixed bulk packs in KG
                        </span>

                    </div>


                    <div class="vc-weight-options" id="vcQtyTiers">

                        <button type="button" class="vc-weight-btn active" data-qty="25">25 KG</button>
                        <button type="button" class="vc-weight-btn" data-qty="50">50 KG</button>
                        <button type="button" class="vc-weight-btn" data-qty="75">75 KG</button>
                        <button type="button" class="vc-weight-btn" data-qty="100">100 KG</button>

                    </div>

                    <div class="vc-bulk-quote-row">
                        <span>Need more than 100 KG?</span>
                        <button type="button" class="vc-bulk-quote-btn" id="vcBulkQuoteBtn">
                            Get Bulk Quote
                        </button>
                    </div>

                    <p class="vc-qty-line-total" id="vcQtyLineTotal" hidden></p>

                </div>



                <!-- =====================================
                     QUANTITY + CART (non-kg products keep stepper)
                ====================================== -->
                <div class="vc-product-purchase">


                    <div class="vc-product-qty" id="vcLegacyQtyBox" hidden>

                        <button
                            type="button"
                            id="vcQtyMinus">

                            <i class="fa-solid fa-minus"></i>

                        </button>


                        <input
                            type="number"
                            id="vcProductQty"
                            value="25"
                            min="1">


                        <button
                            type="button"
                            id="vcQtyPlus">

                            <i class="fa-solid fa-plus"></i>

                        </button>

                    </div>



                    <button
                        type="button"
                        class="vc-add-cart-btn"
                        id="vcAddCartBtn">

                        <i class="fa-solid fa-basket-shopping"></i>

                        <span>
                            Add to Cart
                        </span>

                    </button>

                </div>



                <!-- BUY NOW -->
                <a
                    href="checkout.php"
                    class="vc-buy-now-btn">

                    Buy Now

                    <i class="fa-solid fa-arrow-right"></i>

                </a>



                <!-- =====================================
                     PRODUCT FEATURES
                ====================================== -->
                <div class="vc-product-benefits">


                    <div class="vc-product-benefit">

                        <span>
                            <i class="fa-solid fa-leaf"></i>
                        </span>

                        <div>
                            <strong>
                                Farm Fresh
                            </strong>

                            <small>
                                Carefully selected produce
                            </small>
                        </div>

                    </div>



                    <div class="vc-product-benefit">

                        <span>
                            <i class="fa-solid fa-truck-fast"></i>
                        </span>

                        <div>
                            <strong>
                                Quick Delivery
                            </strong>

                            <small>
                                Delivered fresh to your door
                            </small>
                        </div>

                    </div>



                    <div class="vc-product-benefit">

                        <span>
                            <i class="fa-solid fa-shield-heart"></i>
                        </span>

                        <div>
                            <strong>
                                Quality Checked
                            </strong>

                            <small>
                                Freshness you can trust
                            </small>
                        </div>

                    </div>

                </div>



                <!-- =====================================
                     DELIVERY PINCODE
                ====================================== -->
                <div class="vc-delivery-check">


                    <div class="vc-delivery-check-title">

                        <i class="fa-solid fa-location-dot"></i>

                        <div>

                            <strong>
                                Check Delivery Availability
                            </strong>

                            <span>
                                Enter your PIN code
                            </span>

                        </div>

                    </div>


                    <div class="vc-pincode-form">

                        <input
                            type="text"
                            maxlength="6"
                            placeholder="Enter PIN code">


                        <button type="button">
                            Check
                        </button>

                    </div>

                </div>


            </div>

        </div>



        <!-- =========================================
             PRODUCT DETAILS
        ========================================== -->
        <div class="vc-product-details">


            <!-- TAB BUTTONS -->
            <div class="vc-product-tabs">

                <button
                    type="button"
                    class="vc-product-tab active"
                    data-tab="description">

                    Description

                </button>


                <button
                    type="button"
                    class="vc-product-tab"
                    data-tab="benefits">

                    Benefits

                </button>


                <button
                    type="button"
                    class="vc-product-tab"
                    data-tab="storage">

                    Storage Tips

                </button>


                <button
                    type="button"
                    class="vc-product-tab"
                    data-tab="reviews">

                    Reviews

                </button>

            </div>



            <!-- =====================================
                 DESCRIPTION
            ====================================== -->
            <div
                class="vc-product-tab-content active"
                id="description">


                <div class="vc-product-description-layout">


                    <div>

                        <span class="vc-section-tag">
                            About This Product
                        </span>


                        <div id="vcProductDescriptionText" class="vc-product-richtext">
                            <p class="vc-tab-empty">Details coming soon</p>
                        </div>

                    </div>



                    <div class="vc-product-specifications" id="vcProductSpecs">

                        <div>
                            <span>Category</span>
                            <strong data-spec="category">—</strong>
                        </div>


                        <div>
                            <span>Product</span>
                            <strong data-spec="name">—</strong>
                        </div>


                        <div>
                            <span>Quality</span>
                            <strong data-spec="grade">—</strong>
                        </div>


                        <div>
                            <span>Unit</span>
                            <strong data-spec="unit">—</strong>
                        </div>


                        <div>
                            <span>Origin</span>
                            <strong data-spec="origin">—</strong>
                        </div>

                    </div>


                </div>

            </div>



            <!-- =====================================
                 BENEFITS TAB
            ====================================== -->
            <div
                class="vc-product-tab-content"
                id="benefits">


                <div id="vcProductBenefits" class="vc-product-richtext vc-product-tab-panel">
                    <p class="vc-tab-empty">Details coming soon</p>
                </div>

            </div>



            <!-- =====================================
                 STORAGE
            ====================================== -->
            <div
                class="vc-product-tab-content"
                id="storage">


                <div class="vc-storage-box">

                    <i class="fa-solid fa-box-open"></i>

                    <div>

                        <h3>
                            Storage Recommendation
                        </h3>


                        <div id="vcProductStorage" class="vc-product-richtext">
                            <p class="vc-tab-empty">Details coming soon</p>
                        </div>

                    </div>

                </div>

            </div>



            <!-- =====================================
                 REVIEWS
            ====================================== -->
            <div
                class="vc-product-tab-content"
                id="reviews">


                <div class="vc-review-summary">

                    <div>

                        <strong>
                            4.8
                        </strong>


                        <div class="vc-stars">

                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>

                        </div>


                        <span>
                            Based on 124 customer reviews
                        </span>

                    </div>


                    <button type="button">
                        Write a Review
                    </button>


                </div>

            </div>


        </div>



        <!-- =========================================
             RELATED PRODUCTS
        ========================================== -->
        <div class="vc-related-products">


            <div class="vc-related-heading">

                <div>

                    <span>
                        You May Also Like
                    </span>


                    <h2>
                        More Fresh Picks
                    </h2>

                </div>


                <a href="shop.php">

                    View All Products

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>



            <div class="vc-related-grid">


                <!-- =================================
                     CARROT
                ================================== -->
                <article class="vc-related-card">

                    <div class="vc-related-image">

                        <span>
                            Fresh
                        </span>

                        <img
                            src="https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Carrots">


                        <button
                            type="button"
                            aria-label="Add carrot to wishlist">

                            <i class="fa-regular fa-heart"></i>

                        </button>

                    </div>


                    <div class="vc-related-content">

                        <small>
                            Vegetables
                        </small>


                        <h3>
                            Fresh Carrots
                        </h3>


                        <div class="vc-related-bottom">

                            <strong>
                                ₹80 / Kg
                            </strong>


                            <a href="product.php">
                                <i class="fa-solid fa-plus"></i>
                            </a>

                        </div>

                    </div>

                </article>



                <!-- =================================
                     POTATO
                ================================== -->
                <article class="vc-related-card">

                    <div class="vc-related-image">

                        <span>
                            Popular
                        </span>


                        <img
                            src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Potatoes">


                        <button
                            type="button"
                            aria-label="Add potato to wishlist">

                            <i class="fa-regular fa-heart"></i>

                        </button>

                    </div>


                    <div class="vc-related-content">

                        <small>
                            Vegetables
                        </small>


                        <h3>
                            Fresh Potatoes
                        </h3>


                        <div class="vc-related-bottom">

                            <strong>
                                ₹55 / Kg
                            </strong>


                            <a href="product.php">
                                <i class="fa-solid fa-plus"></i>
                            </a>

                        </div>

                    </div>

                </article>



                <!-- =================================
                     ONION
                ================================== -->
                <article class="vc-related-card">

                    <div class="vc-related-image">

                        <span>
                            Daily Essential
                        </span>


                        <img
                            src="https://images.unsplash.com/photo-1618512496248-a07fe83aa8cb?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Onions">


                        <button
                            type="button"
                            aria-label="Add onion to wishlist">

                            <i class="fa-regular fa-heart"></i>

                        </button>

                    </div>


                    <div class="vc-related-content">

                        <small>
                            Vegetables
                        </small>


                        <h3>
                            Fresh Onions
                        </h3>


                        <div class="vc-related-bottom">

                            <strong>
                                ₹65 / Kg
                            </strong>


                            <a href="product.php">
                                <i class="fa-solid fa-plus"></i>
                            </a>

                        </div>

                    </div>

                </article>



                <!-- =================================
                     SPINACH
                ================================== -->
                <article class="vc-related-card">

                    <div class="vc-related-image">

                        <span>
                            Healthy
                        </span>


                        <img
                            src="https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=700&q=85"
                            alt="Fresh Spinach">


                        <button
                            type="button"
                            aria-label="Add spinach to wishlist">

                            <i class="fa-regular fa-heart"></i>

                        </button>

                    </div>


                    <div class="vc-related-content">

                        <small>
                            Leafy Greens
                        </small>


                        <h3>
                            Fresh Spinach
                        </h3>


                        <div class="vc-related-bottom">

                            <strong>
                                ₹40 / Bunch
                            </strong>


                            <a href="product.php">
                                <i class="fa-solid fa-plus"></i>
                            </a>

                        </div>

                    </div>

                </article>


            </div>

        </div>


    </div>

</section>

<!-- Bulk enquiry modal -->
<div class="vc-bulk-modal" id="vcBulkEnquiryModal" aria-hidden="true">
    <div class="vc-bulk-modal-card" role="dialog" aria-modal="true" aria-labelledby="vcBulkModalTitle">
        <button type="button" class="vc-bulk-modal-close" id="vcBulkModalClose" aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="vc-bulk-modal-head">
            <span class="vc-bulk-modal-icon"><i class="fa-solid fa-truck-ramp-box"></i></span>
            <div>
                <small>Bulk orders</small>
                <h2 id="vcBulkModalTitle">Get a Bulk Quote</h2>
            </div>
        </div>

        <p class="vc-bulk-modal-lead">
            Tell us what you need above 100 KG. Our team will call you within 24 hours
            (<strong>(Veggiicart@gmail.com · +91 8099999086)</strong>.
        </p>

        <form id="vcBulkEnquiryForm" class="vc-bulk-form" novalidate>
            <div class="vc-bulk-form-grid">
                <label>
                    <span>Name *</span>
                    <input type="text" name="name" required placeholder="Your full name" autocomplete="name">
                </label>
                <label>
                    <span>Business Name</span>
                    <input type="text" name="business_name" placeholder="Restaurant / store name" autocomplete="organization">
                </label>
                <label>
                    <span>Mobile Number *</span>
                    <input type="tel" name="mobile" required placeholder="10-digit mobile" autocomplete="tel">
                </label>
                <label>
                    <span>Product</span>
                    <input type="text" name="product_label" id="vcBulkProductLabel" readonly>
                    <input type="hidden" name="product_id" id="vcBulkProductId" value="">
                </label>
                <label>
                    <span>Required Quantity *</span>
                    <input type="text" name="required_quantity" required placeholder="e.g. 150 kg or 200–300 kg">
                </label>
                <label>
                    <span>Pincode *</span>
                    <input type="text" name="pincode" required placeholder="Delivery pincode" inputmode="numeric">
                </label>
                <label class="vc-bulk-span-2">
                    <span>Delivery Location *</span>
                    <input type="text" name="delivery_location" required placeholder="Area / city / landmark">
                </label>
                <label>
                    <span>Preferred Delivery Date</span>
                    <input type="date" name="preferred_delivery_date">
                </label>
                <label class="vc-bulk-span-2">
                    <span>Additional Requirement</span>
                    <textarea name="additional_requirement" rows="3" placeholder="Grade, packaging, timing notes…"></textarea>
                </label>
            </div>

            <p class="vc-bulk-form-error" id="vcBulkFormError" hidden></p>
            <p class="vc-bulk-form-success" id="vcBulkFormSuccess" hidden>
                Thanks — our team will contact you within 24 hours.
            </p>

            <button type="submit" class="vc-bulk-submit-btn" id="vcBulkSubmitBtn">
                Submit Enquiry
            </button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>