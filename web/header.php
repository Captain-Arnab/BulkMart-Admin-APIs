<?php
require_once __DIR__ . '/vc-bootstrap.php';
$vcPage = basename($_SERVER['SCRIPT_FILENAME'] ?? $_SERVER['SCRIPT_NAME'] ?? 'index.php', '.php');
$vcSiteLogo = function_exists('site_logo_src') ? site_logo_src() : 'images/vegiicart-logo.jpeg';
$vcSiteFavicon = function_exists('site_favicon_src') ? site_favicon_src() : 'images/vegiicart-logo.jpeg';
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Vegiicart - Fresh Fruits & Vegetables</title>
    <link rel="icon" href="<?= htmlspecialchars($vcSiteFavicon, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($vcSiteFavicon, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="style.css?v=pdp-clean-1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

</head>

<body data-vc-page="<?= htmlspecialchars($vcPage, ENT_QUOTES, 'UTF-8') ?>">


<header class="vc-header">

    <!-- =======================================================
         TOP BAR
    ======================================================== -->
    <div class="vc-topbar">

        <div class="vc-container vc-topbar-inner">

            <div class="vc-topbar-left">

                <span>
                    <i class="fa-solid fa-circle-check"></i>
                    Farm Fresh Produce
                </span>

                <span>
                    <i class="fa-solid fa-shield-heart"></i>
                    Quality Assured
                </span>

                <span>
                    <i class="fa-solid fa-truck-fast"></i>
                    On-Time Delivery
                </span>

                <span>
                    <i class="fa-solid fa-tags"></i>
                    Best Prices
                </span>

            </div>


            <div class="vc-topbar-right">

                <span>Need Help?</span>

                <a href="tel:+918099999086">
                    <i class="fa-solid fa-phone"></i>
                    +91 8099999086
                </a>

                <span class="vc-top-divider"></span>

                <a href="mailto:Veggiicart@gmail.com">
                    <i class="fa-regular fa-envelope"></i>
                    Veggiicart@gmail.com
                </a>

            </div>

        </div>

    </div>



    <!-- =======================================================
         MAIN HEADER
    ======================================================== -->
    <div class="vc-main-header">

        <div class="vc-container vc-main-header-inner">


            <!-- Mobile menu button -->
            <button class="vc-mobile-toggle"
                    id="vcMobileToggle"
                    type="button"
                    aria-label="Open Menu">

                <i class="fa-solid fa-bars"></i>

            </button>



            <!-- LOGO -->
            <div class="vc-logo-wrap">

                <a href="index.php" class="vc-logo">

                    <img src="<?= htmlspecialchars($vcSiteLogo, ENT_QUOTES, 'UTF-8') ?>"
                         alt="Vegiicart">

                </a>

            </div>



            <!-- DELIVERY LOCATION -->
            <div class="vc-location-wrap" id="vcLocationWrap">

                <button class="vc-location"
                        type="button"
                        id="vcHeaderLocation"
                        aria-haspopup="listbox"
                        aria-expanded="false"
                        aria-controls="vcLocationDropdown"
                        aria-label="Choose delivery location">

                    <span class="vc-location-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>

                    <span class="vc-location-text">

                        <small>Deliver to</small>

                        <strong id="vcHeaderLocationLabel">Select Location</strong>

                    </span>

                    <i class="fa-solid fa-chevron-down vc-location-arrow"></i>

                </button>

                <div class="vc-location-dropdown"
                     id="vcLocationDropdown"
                     role="listbox"
                     aria-label="Saved delivery addresses"
                     hidden>

                    <div class="vc-location-dropdown-head">
                        <strong>Select delivery location</strong>
                        <a href="manage-address.php">Manage</a>
                    </div>

                    <div class="vc-location-dropdown-list" id="vcLocationDropdownList">
                        <p class="vc-location-dropdown-loading">Loading addresses…</p>
                    </div>

                    <a class="vc-location-dropdown-add" href="manage-address.php">
                        <i class="fa-solid fa-plus"></i>
                        Add new address
                    </a>

                </div>

            </div>



            <!-- SEARCH -->
            <form class="vc-search" action="product-search.php" method="get">

                <input type="search"
                       name="q"
                       placeholder="Search fresh vegetables, fruits, leafy greens..."
                       autocomplete="off">

                <button type="submit"
                        aria-label="Search">

                    <i class="fa-solid fa-magnifying-glass"></i>

                </button>

            </form>



            <!-- HEADER ACTIONS -->
            <div class="vc-actions">


                <!-- OFFERS -->
                <a href="offer.php"
                   class="vc-action">

                    <span class="vc-action-icon">
                        <i class="fa-solid fa-tags"></i>
                    </span>

                    <span class="vc-action-name">
                        Offers
                    </span>

                </a>



                <!-- WISHLIST -->
                <a href="wishlist.php"
                   class="vc-action">

                    <span class="vc-action-icon">

                        <i class="fa-regular fa-heart"></i>

                        <span class="vc-count" id="vcHeaderWishlistCount">
                            0
                        </span>

                    </span>

                    <span class="vc-action-name">
                        Wishlist
                    </span>

                </a>



                <!-- CART -->
                <a href="cart.php"
                   class="vc-action">

                    <span class="vc-action-icon">

                        <i class="fa-solid fa-cart-shopping"></i>

                        <span class="vc-count" id="vcHeaderCartCount">
                            0
                        </span>

                    </span>

                    <span class="vc-action-name">
                        Cart
                    </span>

                </a>



                <!-- ACCOUNT -->
                <a href="login.php"
                   class="vc-account">

                    <span class="vc-account-icon">
                        <i class="fa-regular fa-user"></i>
                    </span>

                    <span class="vc-account-content">

                        <small>
                            My Account
                        </small>

                        <strong>
                            Login / Register
                        </strong>

                    </span>

                </a>


            </div>

        </div>


        <!-- MOBILE SEARCH -->
        <div class="vc-container vc-mobile-search-wrap">

            <form class="vc-mobile-search" action="product-search.php" method="get">

                <input type="search"
                       name="q"
                       placeholder="Search vegetables, fruits and more...">

                <button type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

            </form>

        </div>

    </div>



    <!-- =======================================================
         NAVIGATION
    ======================================================== -->
    <div class="vc-navigation">

        <div class="vc-container vc-nav-inner">


            <!-- CATEGORY BUTTON -->
            <div class="vc-category-wrap">

                <button class="vc-category-btn"
                        id="vcCategoryBtn"
                        type="button">

                    <i class="fa-solid fa-bars-staggered"></i>

                    <span>
                        All Categories
                    </span>

                    <i class="fa-solid fa-chevron-down"></i>

                </button>


                <div class="vc-category-dropdown"
                     id="vcCategoryDropdown">

                    <a href="vegetables.php">
                        <i class="fa-solid fa-carrot"></i>
                        Fresh Vegetables
                    </a>

                    <a href="fruits.php">
                        <i class="fa-solid fa-apple-whole"></i>
                        Fresh Fruits
                    </a>

                    <a href="leafy-greens.php">
                        <i class="fa-solid fa-leaf"></i>
                        Leafy Greens
                    </a>

                    <a href="herbs.php">
                        <i class="fa-solid fa-seedling"></i>
                        Herbs & Seasoning
                    </a>

                    <a href="exotic.php">
                        <i class="fa-solid fa-bowl-food"></i>
                        Exotic Produce
                    </a>

                    <a href="bulk-orders.php">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        Bulk Orders
                    </a>

                </div>

            </div>



            <!-- DESKTOP MENU -->
            <nav class="vc-desktop-menu">

                <a href="index.php"
                   class="active">
                    Home
                </a>

                <a href="vegetables.php">
                    Vegetables
                </a>

                <a href="fruits.php">
                    Fruits
                </a>

                <a href="leafy-greens.php">
                    Leafy Greens
                </a>

                <a href="herbs.php">
                    Herbs & Seasoning
                </a>

                <a href="exotic.php">
                    Exotic Produce
                </a>

                <a href="bulk-orders.php">
                    Bulk Orders
                </a>

                <a href="contact.php">
                    Contact Us
                </a>

            </nav>


        </div>

    </div>



    <!-- =======================================================
         FEATURE STRIP
    ======================================================== -->
    <div class="vc-features">

        <div class="vc-container vc-features-grid">


            <div class="vc-feature">

                <span class="vc-feature-icon">
                    <i class="fa-solid fa-truck-fast"></i>
                </span>

                <div>

                    <strong>
                        Fast Delivery
                    </strong>

                    <small>
                        On-time, every time
                    </small>

                </div>

            </div>



            <div class="vc-feature">

                <span class="vc-feature-icon">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>

                <div>

                    <strong>
                        Bulk Order Benefits
                    </strong>

                    <small>
                        Best rates for businesses
                    </small>

                </div>

            </div>



            <div class="vc-feature">

                <span class="vc-feature-icon">
                    <i class="fa-solid fa-certificate"></i>
                </span>

                <div>

                    <strong>
                        Quality Guarantee
                    </strong>

                    <small>
                        Farm fresh, always
                    </small>

                </div>

            </div>



            <div class="vc-feature">

                <span class="vc-feature-icon">
                    <i class="fa-solid fa-headset"></i>
                </span>

                <div>

                    <strong>
                        Customer Support
                    </strong>

                    <small>
                        We're here to help
                    </small>

                </div>

            </div>


        </div>

    </div>

</header>



<!-- ==========================================================
     MOBILE OFFCANVAS MENU
=========================================================== -->
<div class="vc-mobile-overlay"
     id="vcMobileOverlay"></div>


<aside class="vc-mobile-menu"
       id="vcMobileMenu">


    <div class="vc-mobile-menu-head">

        <div class="vc-mobile-logo">

            <img src="<?= htmlspecialchars($vcSiteLogo, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Vegiicart">

        </div>


        <button type="button"
                id="vcMobileClose">

            <i class="fa-solid fa-xmark"></i>

        </button>

    </div>



    <button type="button"
            class="vc-mobile-location"
            id="vcMobileLocation"
            aria-haspopup="listbox"
            aria-expanded="false"
            aria-label="Choose delivery location">

        <i class="fa-solid fa-location-dot"></i>

        <div>

            <small>
                Delivery Location
            </small>

            <strong id="vcMobileLocationLabel">
                Select Location
            </strong>

        </div>

        <i class="fa-solid fa-chevron-down vc-mobile-location-arrow" aria-hidden="true"></i>

    </button>

    <div class="vc-mobile-location-panel"
         id="vcMobileLocationPanel"
         hidden>

        <div class="vc-location-dropdown-list" id="vcMobileLocationList">
            <p class="vc-location-dropdown-loading">Loading addresses…</p>
        </div>

        <a class="vc-location-dropdown-add" href="manage-address.php">
            <i class="fa-solid fa-plus"></i>
            Add new address
        </a>

    </div>



    <nav class="vc-mobile-nav">

        <a href="index.php">
            <i class="fa-solid fa-house"></i>
            Home
        </a>

        <a href="vegetables.php">
            <i class="fa-solid fa-carrot"></i>
            Vegetables
        </a>

        <a href="fruits.php">
            <i class="fa-solid fa-apple-whole"></i>
            Fruits
        </a>

        <a href="leafy-greens.php">
            <i class="fa-solid fa-leaf"></i>
            Leafy Greens
        </a>

        <a href="herbs.php">
            <i class="fa-solid fa-seedling"></i>
            Herbs & Seasoning
        </a>

        <a href="exotic.php">
            <i class="fa-solid fa-bowl-food"></i>
            Exotic Produce
        </a>

        <a href="bulk-orders.php">
            <i class="fa-solid fa-boxes-stacked"></i>
            Bulk Orders
        </a>

        <a href="offers.php">
            <i class="fa-solid fa-tags"></i>
            Offers
        </a>

        <a href="wishlist.php">
            <i class="fa-regular fa-heart"></i>
            Wishlist
        </a>

        <a href="contact.php">
            <i class="fa-regular fa-envelope"></i>
            Contact Us
        </a>

    </nav>



    <div class="vc-mobile-account">

        <a href="login.php">

            <i class="fa-regular fa-circle-user"></i>

            <div>

                <small>
                    Welcome to Vegiicart
                </small>

                <strong>
                    Login / Register
                </strong>

            </div>

            <i class="fa-solid fa-chevron-right"></i>

        </a>

    </div>


</aside>


 