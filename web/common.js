const mobileToggle =
    document.getElementById("vcMobileToggle");

const mobileMenu =
    document.getElementById("vcMobileMenu");

const mobileOverlay =
    document.getElementById("vcMobileOverlay");

const mobileClose =
    document.getElementById("vcMobileClose");


function openMobileMenu() {

    mobileMenu.classList.add("active");
    mobileOverlay.classList.add("active");

    document.body.classList.add("vc-menu-open");

}


function closeMobileMenu() {

    mobileMenu.classList.remove("active");
    mobileOverlay.classList.remove("active");

    document.body.classList.remove("vc-menu-open");

}


mobileToggle.addEventListener("click", openMobileMenu);

mobileClose.addEventListener("click", closeMobileMenu);

mobileOverlay.addEventListener("click", closeMobileMenu);


/* Category Dropdown */

const categoryButton =
    document.getElementById("vcCategoryBtn");

const categoryDropdown =
    document.getElementById("vcCategoryDropdown");


categoryButton.addEventListener("click", function(e) {

    e.stopPropagation();

    categoryDropdown.classList.toggle("active");

});


// document.addEventListener("click", function() {

//     categoryDropdown.classList.remove("active");

// });
 
document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    const slider = document.getElementById("vghSlider");

    if (!slider) {
        return;
    }


    const slides =
        slider.querySelectorAll(".vgh-slide");

    const dots =
        slider.querySelectorAll(".vgh-dot");

    const nextButton =
        slider.querySelector(".vgh-next");

    const prevButton =
        slider.querySelector(".vgh-prev");


    let currentSlide = 0;

    let autoPlay;


    /* =====================================================
       SHOW SLIDE
    ====================================================== */

    function showSlide(index) {

        if (index >= slides.length) {
            index = 0;
        }

        if (index < 0) {
            index = slides.length - 1;
        }


        slides.forEach(function (slide) {

            slide.classList.remove("active");

        });


        dots.forEach(function (dot) {

            dot.classList.remove("active");

        });


        slides[index].classList.add("active");

        if (dots[index]) {
            dots[index].classList.add("active");
        }


        currentSlide = index;

    }



    /* =====================================================
       NEXT
    ====================================================== */

    function nextSlide() {

        showSlide(currentSlide + 1);

    }



    /* =====================================================
       PREVIOUS
    ====================================================== */

    function previousSlide() {

        showSlide(currentSlide - 1);

    }



    /* =====================================================
       AUTOPLAY
       6000 = 6 seconds per slide
    ====================================================== */

    function startAutoPlay() {

        stopAutoPlay();

        autoPlay = setInterval(function () {

            nextSlide();

        }, 6000);

    }



    function stopAutoPlay() {

        if (autoPlay) {

            clearInterval(autoPlay);

        }

    }



    /* =====================================================
       ARROWS
    ====================================================== */

    if (nextButton) {

        nextButton.addEventListener(
            "click",
            function () {

                nextSlide();

                startAutoPlay();

            }
        );

    }


    if (prevButton) {

        prevButton.addEventListener(
            "click",
            function () {

                previousSlide();

                startAutoPlay();

            }
        );

    }



    /* =====================================================
       DOT CLICK
    ====================================================== */

    dots.forEach(function (dot) {

        dot.addEventListener(
            "click",
            function () {

                const slideNumber =
                    parseInt(
                        this.getAttribute("data-slide")
                    );


                showSlide(slideNumber);

                startAutoPlay();

            }
        );

    });



    /* =====================================================
       START
    ====================================================== */

    showSlide(0);

    startAutoPlay();

});

document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    if (document.querySelector(".vbestSlider")) {

        new Swiper(".vbestSlider", {

            loop: true,

            speed: 500,

            spaceBetween: 18,

            grabCursor: true,

            autoplay: {

                delay: 1700,

                disableOnInteraction: false,

                pauseOnMouseEnter: true

            },

            navigation: {

                nextEl: ".vbest-next",

                prevEl: ".vbest-prev"

            },

            pagination: {

                el: ".vbest-pagination",

                clickable: true

            },

            breakpoints: {

                0: {
                    slidesPerView: 1.2,
                    spaceBetween: 12
                },

                480: {
                    slidesPerView: 2,
                    spaceBetween: 12
                },

                768: {
                    slidesPerView: 3,
                    spaceBetween: 15
                },

                1100: {
                    slidesPerView: 4,
                    spaceBetween: 18
                }

            }

        });

    }

});

document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    if (document.querySelector(".vfreshSlider")) {

        new Swiper(".vfreshSlider", {

            loop: true,

            speed: 500,

            slidesPerGroup: 1,

            spaceBetween: 18,

            grabCursor: true,

            autoplay: {
                delay: 1600,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },

            navigation: {
                nextEl: ".vfresh-next",
                prevEl: ".vfresh-prev"
            },

            pagination: {
                el: ".vfresh-pagination",
                clickable: true
            },

            breakpoints: {

                0: {
                    slidesPerView: 1.2,
                    spaceBetween: 12
                },

                480: {
                    slidesPerView: 2,
                    spaceBetween: 12
                },

                768: {
                    slidesPerView: 3,
                    spaceBetween: 15
                },

                1100: {
                    slidesPerView: 4,
                    spaceBetween: 18
                }

            }

        });

    }

});

document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    if (!document.querySelector(".vsrSlider")) {
        return;
    }

    new Swiper(".vsrSlider", {

        loop: true,

        speed: 500,

        slidesPerGroup: 1,

        spaceBetween: 18,

        grabCursor: true,

        observer: true,

        observeParents: true,

        autoplay: {
            delay: 1700,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
        },

        navigation: {
            nextEl: ".vsr-next",
            prevEl: ".vsr-prev"
        },

        pagination: {
            el: ".vsr-pagination",
            clickable: true
        },

        breakpoints: {

            0: {
                slidesPerView: 1.2,
                spaceBetween: 12
            },

            480: {
                slidesPerView: 2,
                spaceBetween: 12
            },

            768: {
                slidesPerView: 3,
                spaceBetween: 15
            },

            1100: {
                slidesPerView: 4,
                spaceBetween: 18
            }

        }

    });

});

document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    if (document.querySelector(".vroSlider")) {

        new Swiper(".vroSlider", {

            loop: true,

            speed: 500,

            slidesPerGroup: 1,

            spaceBetween: 18,

            grabCursor: true,

            autoplay: {
                delay: 1700,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },

            navigation: {
                nextEl: ".vro-next",
                prevEl: ".vro-prev"
            },

            pagination: {
                el: ".vro-pagination",
                clickable: true
            },

            breakpoints: {

                0: {
                    slidesPerView: 1.2,
                    spaceBetween: 12
                },

                480: {
                    slidesPerView: 2,
                    spaceBetween: 12
                },

                768: {
                    slidesPerView: 3,
                    spaceBetween: 15
                },

                1100: {
                    slidesPerView: 4,
                    spaceBetween: 18
                }

            }

        });

    }

});

document.addEventListener("DOMContentLoaded", function () {

    if (!document.querySelector(".vtestSlider")) {
        return;
    }

    new Swiper(".vtestSlider", {

        loop: true,

        speed: 500,

        slidesPerGroup: 1,

        spaceBetween: 18,

        grabCursor: true,

        observer: true,

        observeParents: true,

        autoplay: {
            delay: 1900,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
        },

        navigation: {
            nextEl: ".vtest-next",
            prevEl: ".vtest-prev"
        },

        pagination: {
            el: ".vtest-pagination",
            clickable: true
        },

        breakpoints: {

            0: {
                slidesPerView: 1,
                spaceBetween: 12
            },

            650: {
                slidesPerView: 2,
                spaceBetween: 15
            },

            1050: {
                slidesPerView: 3,
                spaceBetween: 18
            }

        }

    });

});

document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    /* ========================================
       PRODUCT IMAGE GALLERY
    ======================================== */

    const mainImage =
        document.getElementById("vcMainProductImage");

    const thumbnails =
        document.querySelectorAll(".vc-product-thumb");

    thumbnails.forEach(function (thumb) {

        thumb.addEventListener("click", function () {

            thumbnails.forEach(function (item) {
                item.classList.remove("active");
            });

            thumb.classList.add("active");

            const newImage =
                thumb.getAttribute("data-image");

            if (mainImage && newImage) {
                mainImage.src = newImage;
            }

        });

    });


    /* ========================================
       WEIGHT OPTIONS
    ======================================== */

    const weightButtons =
        document.querySelectorAll(".vc-weight-btn");

    const priceElement =
        document.querySelector(".vc-product-price strong");

    weightButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            weightButtons.forEach(function (item) {
                item.classList.remove("active");
            });

            button.classList.add("active");

            const price =
                button.getAttribute("data-price");

            if (priceElement && price) {
                priceElement.textContent = "₹" + price;
            }

        });

    });


    /* ========================================
       QUANTITY
    ======================================== */

    const qtyInput =
        document.getElementById("vcProductQty");

    const plusBtn =
        document.getElementById("vcQtyPlus");

    const minusBtn =
        document.getElementById("vcQtyMinus");


    if (plusBtn && qtyInput) {

        plusBtn.addEventListener("click", function () {

            let value =
                parseInt(qtyInput.value) || 1;

            qtyInput.value = value + 1;

        });

    }


    if (minusBtn && qtyInput) {

        minusBtn.addEventListener("click", function () {

            let value =
                parseInt(qtyInput.value) || 1;

            if (value > 1) {
                qtyInput.value = value - 1;
            }

        });

    }


    /* ========================================
       PRODUCT TABS
    ======================================== */

    const tabs =
        document.querySelectorAll(".vc-product-tab");

    const tabContents =
        document.querySelectorAll(".vc-product-tab-content");

    tabs.forEach(function (tab) {

        tab.addEventListener("click", function () {

            const target =
                tab.getAttribute("data-tab");


            tabs.forEach(function (item) {
                item.classList.remove("active");
            });


            tabContents.forEach(function (content) {
                content.classList.remove("active");
            });


            tab.classList.add("active");


            const targetContent =
                document.getElementById(target);

            if (targetContent) {
                targetContent.classList.add("active");
            }

        });

    });


    /* ========================================
       WISHLIST
    ======================================== */

    const wishlist =
        document.querySelector(".vc-product-wishlist");

    if (wishlist) {

        wishlist.addEventListener("click", function () {

            const icon =
                wishlist.querySelector("i");

            wishlist.classList.toggle("active");

            if (
                icon.classList.contains("fa-regular")
            ) {

                icon.classList.remove("fa-regular");
                icon.classList.add("fa-solid");

            } else {

                icon.classList.remove("fa-solid");
                icon.classList.add("fa-regular");

            }

        });

    }

});

document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    const productGrid =
        document.getElementById("vcProductGrid");

    const products =
        Array.from(
            document.querySelectorAll(".vc-list-product-card")
        );

    const searchInput =
        document.getElementById("vcProductSearch");

    const sortSelect =
        document.getElementById("vcSortProducts");

    const categoryLabels =
        document.querySelectorAll(".vc-category-filter label");

    const categoryInputs =
        document.querySelectorAll(
            '.vc-category-filter input[name="category"]'
        );

    const noProducts =
        document.getElementById("vcNoProducts");

    const resultCount =
        document.querySelector(
            ".vc-shop-result-count strong"
        );


    let currentCategory = "all";


    /* =====================================
       SEARCH + FILTER
    ====================================== */

    function filterProducts() {

        const searchTerm =
            searchInput
                ? searchInput.value.toLowerCase().trim()
                : "";


        let visibleCount = 0;


        products.forEach(function (product) {

            const name =
                product.dataset.name.toLowerCase();

            const category =
                product.dataset.category;


            const matchesSearch =
                name.includes(searchTerm);


            const matchesCategory =
                currentCategory === "all" ||
                category === currentCategory;


            if (matchesSearch && matchesCategory) {

                product.style.display = "";

                visibleCount++;

            } else {

                product.style.display = "none";

            }

        });


        if (resultCount) {
            resultCount.textContent = visibleCount;
        }


        if (noProducts) {

            noProducts.style.display =
                visibleCount === 0
                    ? "block"
                    : "none";

        }

    }


    /* SEARCH */

    if (searchInput) {

        searchInput.addEventListener(
            "input",
            filterProducts
        );

    }


    /* CATEGORY */

    categoryInputs.forEach(function (input) {

        input.addEventListener("change", function () {

            currentCategory = input.value;


            categoryLabels.forEach(function (label) {

                label.classList.remove("active");

            });


            const parentLabel =
                input.closest("label");

            if (parentLabel) {
                parentLabel.classList.add("active");
            }


            filterProducts();

        });

    });



    /* =====================================
       SORT PRODUCTS
    ====================================== */

    if (sortSelect && productGrid) {

        sortSelect.addEventListener(
            "change",
            function () {

                const sortValue =
                    sortSelect.value;


                let sortedProducts =
                    [...products];


                if (sortValue === "low-high") {

                    sortedProducts.sort(
                        function (a, b) {

                            return (
                                parseFloat(a.dataset.price) -
                                parseFloat(b.dataset.price)
                            );

                        }
                    );

                }


                if (sortValue === "high-low") {

                    sortedProducts.sort(
                        function (a, b) {

                            return (
                                parseFloat(b.dataset.price) -
                                parseFloat(a.dataset.price)
                            );

                        }
                    );

                }


                if (sortValue === "name") {

                    sortedProducts.sort(
                        function (a, b) {

                            return a.dataset.name.localeCompare(
                                b.dataset.name
                            );

                        }
                    );

                }


                sortedProducts.forEach(
                    function (product) {

                        productGrid.appendChild(product);

                    }
                );

            }
        );

    }



    /* =====================================
       WISHLIST
    ====================================== */

    const wishlistButtons =
        document.querySelectorAll(
            ".vc-list-wishlist"
        );


    wishlistButtons.forEach(
        function (button) {

            button.addEventListener(
                "click",
                function () {

                    const icon =
                        button.querySelector("i");


                    button.classList.toggle(
                        "active"
                    );


                    if (!icon) {
                        return;
                    }


                    if (
                        icon.classList.contains(
                            "fa-regular"
                        )
                    ) {

                        icon.classList.remove(
                            "fa-regular"
                        );

                        icon.classList.add(
                            "fa-solid"
                        );

                    } else {

                        icon.classList.remove(
                            "fa-solid"
                        );

                        icon.classList.add(
                            "fa-regular"
                        );

                    }

                }
            );

        }
    );



    /* =====================================
       MOBILE FILTER
    ====================================== */

    const filterSidebar =
        document.getElementById(
            "vcShopFilter"
        );

    const filterOpen =
        document.getElementById(
            "vcFilterOpen"
        );

    const filterClose =
        document.getElementById(
            "vcFilterClose"
        );

    const filterOverlay =
        document.getElementById(
            "vcFilterOverlay"
        );


    function openFilter() {

        if (filterSidebar) {
            filterSidebar.classList.add("open");
        }

        if (filterOverlay) {
            filterOverlay.classList.add("show");
        }

        document.body.style.overflow =
            "hidden";

    }


    function closeFilter() {

        if (filterSidebar) {
            filterSidebar.classList.remove("open");
        }

        if (filterOverlay) {
            filterOverlay.classList.remove("show");
        }

        document.body.style.overflow =
            "";

    }


    if (filterOpen) {

        filterOpen.addEventListener(
            "click",
            openFilter
        );

    }


    if (filterClose) {

        filterClose.addEventListener(
            "click",
            closeFilter
        );

    }


    if (filterOverlay) {

        filterOverlay.addEventListener(
            "click",
            closeFilter
        );

    }

});

document.addEventListener("DOMContentLoaded", function () {

    // Live site: vc-app.js owns the login password eye toggle.
    // Binding here too would double-toggle (show then immediately hide).
    if (window.VC_LIVE) {
        return;
    }

    const passwordInput =
        document.getElementById("vcLoginPassword");

    const passwordToggle =
        document.getElementById("vcPasswordToggle");


    if (!passwordInput || !passwordToggle) {
        return;
    }

    if (passwordToggle.getAttribute("data-vc-bound") === "1") {
        return;
    }


    passwordToggle.addEventListener("click", function () {

        const icon =
            passwordToggle.querySelector("i");


        if (passwordInput.type === "password") {

            passwordInput.type = "text";

            if (icon) {
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }

            passwordToggle.setAttribute(
                "aria-label",
                "Hide password"
            );

        } else {

            passwordInput.type = "password";

            if (icon) {
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }

            passwordToggle.setAttribute(
                "aria-label",
                "Show password"
            );

        }

    });

});

document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".vc-signup-password-toggle").forEach(function (button) {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            var targetId = button.getAttribute("data-target");
            var input = targetId
                ? document.getElementById(targetId)
                : (button.parentElement && button.parentElement.querySelector("input"));
            if (!input) {
                return;
            }

            var icon = button.querySelector("i");
            var show = input.type === "password";
            input.type = show ? "text" : "password";

            if (icon) {
                icon.classList.toggle("fa-eye", !show);
                icon.classList.toggle("fa-eye-slash", show);
            }

            button.setAttribute("aria-label", show ? "Hide password" : "Show password");
        });
    });

});

document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    const grid =
        document.getElementById("vcCategoryGrid");

    const products =
        Array.from(
            document.querySelectorAll(".vc-category-card")
        );

    const search =
        document.getElementById("vcCategorySearch");

    const sort =
        document.getElementById("vcCategorySort");

    const resultCount =
        document.getElementById("vcCategoryResultCount");

    const noResults =
        document.getElementById("vcCategoryNoResults");


    /* =====================================
       SEARCH
    ====================================== */

    function filterProducts() {

        const value =
            search
                ? search.value.toLowerCase().trim()
                : "";

        let visible = 0;


        products.forEach(function (product) {

            const name =
                product.dataset.name.toLowerCase();


            if (name.includes(value)) {

                product.style.display = "";

                visible++;

            } else {

                product.style.display = "none";

            }

        });


        if (resultCount) {
            resultCount.textContent = visible;
        }


        if (noResults) {

            noResults.style.display =
                visible === 0
                    ? "block"
                    : "none";

        }

    }


    if (search) {

        search.addEventListener(
            "input",
            filterProducts
        );

    }


    /* =====================================
       SORT
    ====================================== */

    if (sort && grid) {

        sort.addEventListener("change", function () {

            const value =
                sort.value;

            let sorted =
                [...products];


            if (value === "low-high") {

                sorted.sort(function (a, b) {

                    return (
                        parseFloat(a.dataset.price) -
                        parseFloat(b.dataset.price)
                    );

                });

            }


            if (value === "high-low") {

                sorted.sort(function (a, b) {

                    return (
                        parseFloat(b.dataset.price) -
                        parseFloat(a.dataset.price)
                    );

                });

            }


            if (value === "name") {

                sorted.sort(function (a, b) {

                    return a.dataset.name.localeCompare(
                        b.dataset.name
                    );

                });

            }


            sorted.forEach(function (product) {

                grid.appendChild(product);

            });

        });

    }


    /* =====================================
       WISHLIST
    ====================================== */

    document
        .querySelectorAll(".vc-category-wishlist")
        .forEach(function (button) {

            button.addEventListener("click", function () {

                const icon =
                    button.querySelector("i");

                button.classList.toggle("active");


                if (!icon) {
                    return;
                }


                if (
                    icon.classList.contains("fa-regular")
                ) {

                    icon.classList.remove("fa-regular");

                    icon.classList.add("fa-solid");

                } else {

                    icon.classList.remove("fa-solid");

                    icon.classList.add("fa-regular");

                }

            });

        });


    /* =====================================
       MOBILE FILTER
    ====================================== */

    const sidebar =
        document.getElementById(
            "vcCategorySidebar"
        );

    const openButton =
        document.getElementById(
            "vcCategoryFilterOpen"
        );

    const closeButton =
        document.getElementById(
            "vcCategoryFilterClose"
        );

    const overlay =
        document.getElementById(
            "vcCategoryFilterOverlay"
        );


    function openFilter() {

        if (sidebar) {
            sidebar.classList.add("open");
        }

        if (overlay) {
            overlay.classList.add("show");
        }

        document.body.style.overflow =
            "hidden";

    }


    function closeFilter() {

        if (sidebar) {
            sidebar.classList.remove("open");
        }

        if (overlay) {
            overlay.classList.remove("show");
        }

        document.body.style.overflow =
            "";

    }


    if (openButton) {

        openButton.addEventListener(
            "click",
            openFilter
        );

    }


    if (closeButton) {

        closeButton.addEventListener(
            "click",
            closeFilter
        );

    }


    if (overlay) {

        overlay.addEventListener(
            "click",
            closeFilter
        );

    }

});

document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    const searchInput =
        document.getElementById("vcSearchInput");

    const searchButton =
        document.getElementById("vcSearchButton");

    const keywordText =
        document.getElementById("vcSearchKeyword");

    const resultCount =
        document.getElementById("vcSearchCount");

    const productGrid =
        document.getElementById("vcSearchGrid");

    const products =
        Array.from(
            document.querySelectorAll(".vc-search-card")
        );

    const emptyState =
        document.getElementById("vcSearchEmpty");

    const sort =
        document.getElementById("vcSearchSort");

    const categoryInputs =
        document.querySelectorAll(
            'input[name="search_category"]'
        );

    const categoryLabels =
        document.querySelectorAll(
            ".vc-search-filter-list label"
        );


    let currentCategory = "all";


    /* SEARCH */

    function filterProducts() {

        const keyword =
            searchInput.value
                .toLowerCase()
                .trim();

        let visible = 0;


        products.forEach(function (product) {

            const productName =
                product.dataset.name.toLowerCase();

            const category =
                product.dataset.category;


            const matchesSearch =
                productName.includes(keyword);


            const matchesCategory =
                currentCategory === "all" ||
                category === currentCategory;


            if (matchesSearch && matchesCategory) {

                product.style.display = "";

                visible++;

            } else {

                product.style.display = "none";

            }

        });


        if (keywordText) {

            keywordText.textContent =
                keyword
                    ? "“" + searchInput.value.trim() + "”"
                    : "“All Products”";

        }


        if (resultCount) {
            resultCount.textContent = visible;
        }


        if (emptyState) {

            emptyState.style.display =
                visible === 0
                    ? "block"
                    : "none";

        }

    }


    searchInput.addEventListener(
        "input",
        filterProducts
    );


    if (searchButton) {

        searchButton.addEventListener(
            "click",
            filterProducts
        );

    }


    /* CATEGORY */

    categoryInputs.forEach(function (input) {

        input.addEventListener(
            "change",
            function () {

                currentCategory =
                    input.value;


                categoryLabels.forEach(
                    function (label) {

                        label.classList.remove(
                            "active"
                        );

                    }
                );


                const label =
                    input.closest("label");


                if (label) {
                    label.classList.add("active");
                }


                filterProducts();

            }
        );

    });


    /* SORT */

    if (sort && productGrid) {

        sort.addEventListener(
            "change",
            function () {

                let sorted =
                    [...products];


                if (sort.value === "low-high") {

                    sorted.sort(
                        function (a, b) {

                            return (
                                parseFloat(a.dataset.price) -
                                parseFloat(b.dataset.price)
                            );

                        }
                    );

                }


                if (sort.value === "high-low") {

                    sorted.sort(
                        function (a, b) {

                            return (
                                parseFloat(b.dataset.price) -
                                parseFloat(a.dataset.price)
                            );

                        }
                    );

                }


                if (sort.value === "name") {

                    sorted.sort(
                        function (a, b) {

                            return a.dataset.name.localeCompare(
                                b.dataset.name
                            );

                        }
                    );

                }


                sorted.forEach(
                    function (product) {

                        productGrid.appendChild(product);

                    }
                );

            }
        );

    }


    /* WISHLIST */

    document
        .querySelectorAll(".vc-search-wishlist")
        .forEach(function (button) {

            button.addEventListener(
                "click",
                function () {

                    const icon =
                        button.querySelector("i");


                    button.classList.toggle(
                        "active"
                    );


                    if (
                        icon.classList.contains(
                            "fa-regular"
                        )
                    ) {

                        icon.classList.remove(
                            "fa-regular"
                        );

                        icon.classList.add(
                            "fa-solid"
                        );

                    } else {

                        icon.classList.remove(
                            "fa-solid"
                        );

                        icon.classList.add(
                            "fa-regular"
                        );

                    }

                }
            );

        });


    /* MOBILE FILTER */

    const sidebar =
        document.getElementById(
            "vcSearchSidebar"
        );

    const open =
        document.getElementById(
            "vcSearchFilterOpen"
        );

    const close =
        document.getElementById(
            "vcSearchFilterClose"
        );

    const overlay =
        document.getElementById(
            "vcSearchOverlay"
        );


    function openFilter() {

        sidebar.classList.add("open");

        overlay.classList.add("show");

        document.body.style.overflow =
            "hidden";

    }


    function closeFilter() {

        sidebar.classList.remove("open");

        overlay.classList.remove("show");

        document.body.style.overflow =
            "";

    }


    if (open) {
        open.addEventListener("click", openFilter);
    }

    if (close) {
        close.addEventListener("click", closeFilter);
    }

    if (overlay) {
        overlay.addEventListener("click", closeFilter);
    }


    /* INITIAL RESULT */

    filterProducts();

});

document.addEventListener("DOMContentLoaded", function () {

    if (window.VC_LIVE) {
        return;
    }

    const tabs = document.querySelectorAll(".vc-order-tab");
    const orders = document.querySelectorAll(".vc-order-card");
    const search = document.getElementById("vcOrderSearch");
    const noOrders = document.getElementById("vcNoOrders");

    let currentFilter = "all";

    function filterOrders() {

        const searchValue = search.value
            .toLowerCase()
            .trim();

        let visibleOrders = 0;

        orders.forEach(order => {

            const orderStatus =
                order.dataset.status.toLowerCase();

            const orderId =
                order.dataset.order.toLowerCase();

            const statusMatch =
                currentFilter === "all" ||
                orderStatus === currentFilter;

            const searchMatch =
                orderId.includes(searchValue);

            if (statusMatch && searchMatch) {

                order.style.display = "block";
                visibleOrders++;

            } else {

                order.style.display = "none";

            }

        });

        noOrders.style.display =
            visibleOrders === 0 ? "block" : "none";

    }


    tabs.forEach(tab => {

        tab.addEventListener("click", function () {

            tabs.forEach(item =>
                item.classList.remove("active")
            );

            this.classList.add("active");

            currentFilter =
                this.dataset.filter;

            filterOrders();

        });

    });


    search.addEventListener(
        "input",
        filterOrders
    );

});

document.addEventListener("DOMContentLoaded", function () {

    /* =========================================
       SHOW / HIDE PASSWORD
    ========================================= */

    const toggleButtons =
        document.querySelectorAll(".vg-password-toggle");

    toggleButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            const targetId =
                this.getAttribute("data-target");

            const input =
                document.getElementById(targetId);

            const icon =
                this.querySelector("i");

            if (input.type === "password") {

                input.type = "text";

                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");

            } else {

                input.type = "password";

                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");

            }

        });

    });


    /* =========================================
       PASSWORD STRENGTH
    ========================================= */

    const password =
        document.getElementById("newPassword");

    const confirmPassword =
        document.getElementById("confirmPassword");

    const strengthBars =
        document.querySelector(".vg-strength-bars");

    const strengthText =
        document.getElementById("vgStrengthText");


    const ruleLength =
        document.getElementById("ruleLength");

    const ruleUpper =
        document.getElementById("ruleUpper");

    const ruleNumber =
        document.getElementById("ruleNumber");

    const ruleSpecial =
        document.getElementById("ruleSpecial");


    function updateRule(element, valid) {

        if (valid) {
            element.classList.add("valid");
        } else {
            element.classList.remove("valid");
        }

    }


    password.addEventListener("input", function () {

        const value = password.value;

        const hasLength =
            value.length >= 8;

        const hasUpper =
            /[A-Z]/.test(value);

        const hasNumber =
            /[0-9]/.test(value);

        const hasSpecial =
            /[^A-Za-z0-9]/.test(value);


        updateRule(
            ruleLength,
            hasLength
        );

        updateRule(
            ruleUpper,
            hasUpper
        );

        updateRule(
            ruleNumber,
            hasNumber
        );

        updateRule(
            ruleSpecial,
            hasSpecial
        );


        let score = 0;

        if (hasLength) score++;
        if (hasUpper) score++;
        if (hasNumber) score++;
        if (hasSpecial) score++;


        strengthBars.className =
            "vg-strength-bars";


        if (value.length === 0) {

            strengthText.textContent =
                "Enter Password";

        }

        else if (score === 1) {

            strengthBars.classList.add("weak");

            strengthText.textContent =
                "Weak";

        }

        else if (score === 2) {

            strengthBars.classList.add("medium");

            strengthText.textContent =
                "Medium";

        }

        else if (score === 3) {

            strengthBars.classList.add("good");

            strengthText.textContent =
                "Good";

        }

        else if (score === 4) {

            strengthBars.classList.add("strong");

            strengthText.textContent =
                "Strong";

        }


        checkPasswordMatch();

    });


    /* =========================================
       PASSWORD MATCH
    ========================================= */

    const matchMessage =
        document.getElementById("vgPasswordMatch");


    function checkPasswordMatch() {

        if (confirmPassword.value === "") {

            matchMessage.textContent = "";

            matchMessage.className =
                "vg-password-match";

            return;
        }


        if (
            password.value ===
            confirmPassword.value
        ) {

            matchMessage.textContent =
                "✓ Passwords match";

            matchMessage.className =
                "vg-password-match success";

        }

        else {

            matchMessage.textContent =
                "Passwords do not match";

            matchMessage.className =
                "vg-password-match error";

        }

    }


    confirmPassword.addEventListener(
        "input",
        checkPasswordMatch
    );

});

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("vcBusinessRegistrationForm");

    if (!form) {
        return;
    }

    const steps = document.querySelectorAll(".vc-form-step");
    const sideSteps = document.querySelectorAll(".vc-side-step");

    const nextButton = document.getElementById("vcNextButton");
    const previousButton = document.getElementById("vcPreviousButton");
    const submitButton = document.getElementById("vcSubmitButton");

    const progressBar = document.getElementById("vcProgressBar");
    const currentStepText =
        document.getElementById("vcCurrentStepText");

    const bottomCurrentStep =
        document.getElementById("vcBottomCurrentStep");

    const progressPercentage =
        document.getElementById("vcProgressPercentage");

    let currentStep = 1;
    const totalSteps = 5;


    /* =========================
       SHOW STEP
    ========================== */

    function showStep(step) {

        currentStep = step;

        steps.forEach(function (item) {

            item.classList.remove("active");

            if (Number(item.dataset.step) === step) {
                item.classList.add("active");
            }

        });


        sideSteps.forEach(function (item) {

            const itemStep = Number(item.dataset.sideStep);

            item.classList.remove("active", "completed");

            if (itemStep === step) {
                item.classList.add("active");
            }

            if (itemStep < step) {
                item.classList.add("completed");
            }

        });


        const percentage =
            Math.round((step / totalSteps) * 100);

        progressBar.style.width = percentage + "%";

        currentStepText.textContent = step;
        bottomCurrentStep.textContent = step;
        progressPercentage.textContent = percentage + "%";


        previousButton.style.visibility =
            step === 1 ? "hidden" : "visible";


        if (step === totalSteps) {

            nextButton.style.display = "none";
            submitButton.style.display = "inline-flex";

            buildReview();

        } else {

            nextButton.style.display = "inline-flex";
            submitButton.style.display = "none";

        }


        if (window.innerWidth < 850) {

            document
                .querySelector(".vc-registration-main")
                .scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });

        }

    }


    /* =========================
       STEP VALIDATION
    ========================== */

    function validateCurrentStep() {

        if (currentStep === 1) {

            const selectedBusiness =
                document.querySelector(
                    'input[name="business_type"]:checked'
                );

            const error =
                document.getElementById("vcBusinessTypeError");

            if (!selectedBusiness) {

                error.style.display = "block";

                return false;

            }

            error.style.display = "none";

            return true;

        }


        const activeStep =
            document.querySelector(
                '.vc-form-step[data-step="' +
                currentStep +
                '"]'
            );

        const requiredFields =
            activeStep.querySelectorAll("[required]");

        let valid = true;


        requiredFields.forEach(function (field) {

            if (!field.checkValidity()) {

                field.reportValidity();

                valid = false;

                return;

            }

        });


        return valid;

    }


    /* =========================
       NEXT / PREVIOUS
    ========================== */

    nextButton.addEventListener("click", function () {

        if (!validateCurrentStep()) {
            return;
        }

        if (currentStep < totalSteps) {
            showStep(currentStep + 1);
        }

    });


    previousButton.addEventListener("click", function () {

        if (currentStep > 1) {
            showStep(currentStep - 1);
        }

    });


    /* =========================
       BUSINESS TYPE ERROR
    ========================== */

    document
        .querySelectorAll(
            'input[name="business_type"]'
        )
        .forEach(function (radio) {

            radio.addEventListener(
                "change",
                function () {

                    document
                        .getElementById(
                            "vcBusinessTypeError"
                        )
                        .style.display = "none";

                }
            );

        });


    /* =========================
       SAME ADDRESS
    ========================== */

    const sameAddress =
        document.getElementById("vcSameAddress");

    const shopAddress =
        document.getElementById("vcShopAddress");

    const deliveryAddress =
        document.getElementById("vcDeliveryAddress");


    sameAddress.addEventListener("change", function () {

        if (this.checked) {

            deliveryAddress.value =
                shopAddress.value;

            deliveryAddress.readOnly = true;

        } else {

            deliveryAddress.readOnly = false;

        }

    });


    shopAddress.addEventListener("input", function () {

        if (sameAddress.checked) {

            deliveryAddress.value =
                shopAddress.value;

        }

    });


    /* =========================
       FILE UPLOAD DISPLAY
    ========================== */

    const uploadInputs =
        document.querySelectorAll(
            ".vc-upload-card input[type='file']"
        );


    uploadInputs.forEach(function (input) {

        input.addEventListener("change", function () {

            const card =
                this.closest(".vc-upload-card");

            const fileName =
                card.querySelector(".vc-file-name");

            const action =
                card.querySelector(".vc-upload-action");


            if (this.files.length > 0) {

                const file = this.files[0];


                /* 5MB Limit */

                if (file.size > 5 * 1024 * 1024) {

                    alert(
                        "Please select a file smaller than 5 MB."
                    );

                    this.value = "";

                    return;

                }


                fileName.textContent = file.name;

                card.classList.add("has-file");

                action.innerHTML =
                    '<i class="fa-solid fa-circle-check"></i> Uploaded';

            } else {

                fileName.textContent =
                    "No file selected";

                card.classList.remove("has-file");

            }

        });

    });


    /* =========================
       GOOGLE CURRENT LOCATION
    ========================== */

    const locationButton =
        document.getElementById(
            "vcLocationButton"
        );

    const googleLocation =
        document.getElementById(
            "vcGoogleLocation"
        );


    locationButton.addEventListener(
        "click",
        function () {

            if (!navigator.geolocation) {

                alert(
                    "Location services are not supported by your browser."
                );

                return;

            }


            locationButton.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin"></i> Locating...';


            navigator.geolocation.getCurrentPosition(

                function (position) {

                    const latitude =
                        position.coords.latitude;

                    const longitude =
                        position.coords.longitude;


                    googleLocation.value =
                        "https://www.google.com/maps?q=" +
                        latitude +
                        "," +
                        longitude;


                    locationButton.innerHTML =
                        '<i class="fa-solid fa-circle-check"></i> Location Added';

                },

                function () {

                    alert(
                        "Unable to access your location. Please paste your Google Maps link manually."
                    );

                    locationButton.innerHTML =
                        '<i class="fa-solid fa-location-crosshairs"></i> Use Current Location';

                }

            );

        }
    );


    /* =========================
       BUILD REVIEW
    ========================== */

    function setReview(id, value) {

        document.getElementById(id).textContent =
            value && value.trim()
                ? value
                : "Not provided";

    }


    function buildReview() {

        const businessType =
            document.querySelector(
                'input[name="business_type"]:checked'
            );


        setReview(
            "reviewBusinessType",
            businessType
                ? businessType.value
                : ""
        );


        setReview(
            "reviewBusinessName",
            document.getElementById(
                "vcBusinessName"
            ).value
        );


        setReview(
            "reviewOwnerName",
            document.getElementById(
                "vcOwnerName"
            ).value
        );


        setReview(
            "reviewMobile",
            document.getElementById(
                "vcMobile"
            ).value
        );


        setReview(
            "reviewEmail",
            document.getElementById(
                "vcEmail"
            ).value
        );


        setReview(
            "reviewGST",
            document.getElementById(
                "vcGST"
            ).value
        );


        setReview(
            "reviewFSSAI",
            document.getElementById(
                "vcFSSAI"
            ).value
        );


        setReview(
            "reviewPAN",
            document.getElementById(
                "vcPAN"
            ).value
        );


        setReview(
            "reviewShopAddress",
            document.getElementById(
                "vcShopAddress"
            ).value
        );


        setReview(
            "reviewDeliveryAddress",
            document.getElementById(
                "vcDeliveryAddress"
            ).value
        );


        setReview(
            "reviewCity",
            document.getElementById(
                "vcCity"
            ).value
        );


        setReview(
            "reviewState",
            document.getElementById(
                "vcState"
            ).value
        );


        setReview(
            "reviewPincode",
            document.getElementById(
                "vcPincode"
            ).value
        );


        setReview(
            "reviewLandmark",
            document.getElementById(
                "vcLandmark"
            ).value
        );


        buildDocumentReview();

    }


    /* =========================
       DOCUMENT REVIEW
    ========================== */

    function buildDocumentReview() {

        const reviewBox =
            document.getElementById(
                "vcReviewDocuments"
            );

        reviewBox.innerHTML = "";

        let documentCount = 0;


        uploadInputs.forEach(function (input) {

            if (input.files.length > 0) {

                documentCount++;

                const label =
                    input.closest(
                        ".vc-upload-card"
                    )
                    .querySelector(
                        "strong"
                    )
                    .textContent;


                const item =
                    document.createElement(
                        "span"
                    );


                item.className =
                    "vc-review-doc-item";


                item.innerHTML =
                    '<i class="fa-solid fa-circle-check"></i>' +
                    label;


                reviewBox.appendChild(item);

            }

        });


        if (documentCount === 0) {

            reviewBox.innerHTML =
                "<p>No documents uploaded.</p>";

        }

    }


    /* =========================
       EDIT BUTTONS
    ========================== */

    document
        .querySelectorAll(".vc-edit-step")
        .forEach(function (button) {

            button.addEventListener(
                "click",
                function () {

                    const editStep =
                        Number(
                            this.dataset.edit
                        );

                    showStep(editStep);

                }
            );

        });


    /* =========================
       FORM SUBMIT
    ========================== */

    form.addEventListener("submit", function (event) {

        if (window.VC_LIVE) {
            return;
        }

        event.preventDefault();


        const terms =
            document.getElementById("vcTerms");


        if (!terms.checked) {

            alert(
                "Please accept the Terms & Conditions before submitting."
            );

            return;

        }


        /*
        ==========================================
        FRONTEND DEMO SUBMISSION

        Replace this section later with:
        - AJAX
        - PHP
        - Database
        - FormData upload
        ==========================================
        */


        document
            .getElementById(
                "vcRegistrationSuccess"
            )
            .classList.add("show");

    });


    /* =========================
       SUCCESS CLOSE
    ========================== */

    document
        .getElementById(
            "vcSuccessClose"
        )
        .addEventListener(
            "click",
            function () {

                document
                    .getElementById(
                        "vcRegistrationSuccess"
                    )
                    .classList.remove("show");

            }
        );


    /* Initial State */

    showStep(1);

});

document.addEventListener("DOMContentLoaded", function () {

    /* =====================================
       STATUS PREVIEW SWITCH
       Remove after PHP integration
    ====================================== */

    const statusButtons =
        document.querySelectorAll(
            "[data-status-target]"
        );

    const statusPanels =
        document.querySelectorAll(
            ".vc-verification-status-panel"
        );


    statusButtons.forEach(function (button) {

        button.addEventListener(
            "click",
            function () {

                const target =
                    this.dataset.statusTarget;


                statusButtons.forEach(function (btn) {
                    btn.classList.remove("active");
                });


                this.classList.add("active");


                statusPanels.forEach(function (panel) {
                    panel.classList.remove("active");
                });


                if (target === "pending") {

                    document
                        .getElementById(
                            "vcStatusPending"
                        )
                        .classList.add("active");

                }


                if (target === "approved") {

                    document
                        .getElementById(
                            "vcStatusApproved"
                        )
                        .classList.add("active");

                }


                if (target === "rejected") {

                    document
                        .getElementById(
                            "vcStatusRejected"
                        )
                        .classList.add("active");

                }

            }
        );

    });


    /* =====================================
       RE-UPLOAD DOCUMENT DISPLAY
    ====================================== */

    const reuploadInputs =
        document.querySelectorAll(
            ".vc-reupload-btn input[type='file']"
        );


    reuploadInputs.forEach(function (input) {

        input.addEventListener("change", function () {

            if (!this.files.length) {
                return;
            }


            const label =
                this.closest(".vc-reupload-btn");


            const file =
                this.files[0];


            /* 5 MB FILE LIMIT */

            if (file.size > 5 * 1024 * 1024) {

                alert(
                    "Please upload a file smaller than 5 MB."
                );

                this.value = "";

                return;

            }


            label.classList.add("uploaded");


            label.querySelector("span").textContent =
                "Uploaded";


            label.querySelector("i").className =
                "fa-solid fa-circle-check";

        });

    });


    /* =====================================
       RESUBMIT APPLICATION
    ====================================== */

    const resubmitButton =
        document.getElementById(
            "vcResubmitApplication"
        );

    const modal =
        document.getElementById(
            "vcResubmitModal"
        );

    const closeButton =
        document.getElementById(
            "vcResubmitClose"
        );

    const doneButton =
        document.getElementById(
            "vcResubmitDone"
        );


    if (resubmitButton) {

        resubmitButton.addEventListener(
            "click",
            function () {

                const uploadedFiles =
                    document.querySelectorAll(
                        ".vc-reupload-btn.uploaded"
                    );


                if (
                    uploadedFiles.length <
                    reuploadInputs.length
                ) {

                    alert(
                        "Please re-upload all rejected documents before resubmitting your application."
                    );

                    return;

                }


                modal.classList.add("show");

            }
        );

    }


    function closeModal() {
        modal.classList.remove("show");
    }


    closeButton.addEventListener(
        "click",
        closeModal
    );


    doneButton.addEventListener(
        "click",
        closeModal
    );


    modal.addEventListener(
        "click",
        function (event) {

            if (event.target === modal) {
                closeModal();
            }

        }
    );

});

document.addEventListener("DOMContentLoaded", function () {

    /* ==========================
       UPLOAD NEW DOCUMENT MODAL
    =========================== */

    const uploadButton =
        document.getElementById("vcUploadNewDocument");

    const modal =
        document.getElementById("vcBpUploadModal");

    const closeButton =
        document.getElementById("vcBpModalClose");

    const uploadSubmit =
        document.getElementById("vcBpUploadSubmit");

    const documentType =
        document.getElementById("vcNewDocumentType");

    const documentFile =
        document.getElementById("vcNewDocumentFile");

    const documentName =
        document.getElementById("vcNewDocumentName");


    uploadButton.addEventListener("click", function () {
        modal.classList.add("show");
    });


    function closeModal() {
        modal.classList.remove("show");
    }


    closeButton.addEventListener(
        "click",
        closeModal
    );


    modal.addEventListener(
        "click",
        function (event) {

            if (event.target === modal) {
                closeModal();
            }

        }
    );


    /* ==========================
       FILE NAME
    =========================== */

    documentFile.addEventListener(
        "change",
        function () {

            if (!this.files.length) {
                return;
            }

            const file = this.files[0];


            if (file.size > 5 * 1024 * 1024) {

                alert(
                    "Please select a file smaller than 5 MB."
                );

                this.value = "";

                documentName.textContent =
                    "JPG, PNG or PDF · Maximum 5 MB";

                return;
            }


            documentName.textContent =
                file.name;

        }
    );


    /* ==========================
       UPLOAD BUTTON
    =========================== */

    uploadSubmit.addEventListener(
        "click",
        function () {

            if (!documentType.value) {

                alert(
                    "Please select the document type."
                );

                return;
            }


            if (!documentFile.files.length) {

                alert(
                    "Please choose a document to upload."
                );

                return;
            }


            /*
             Connect FormData + PHP/AJAX here.
            */


            alert(
                "Document uploaded successfully."
            );


            documentType.value = "";
            documentFile.value = "";

            documentName.textContent =
                "JPG, PNG or PDF · Maximum 5 MB";


            closeModal();

        }
    );


    /* ==========================
       REPLACE DOCUMENTS
    =========================== */

    const replaceInputs =
        document.querySelectorAll(
            ".vc-bp-replace-btn input"
        );


    replaceInputs.forEach(function (input) {

        input.addEventListener(
            "change",
            function () {

                if (!this.files.length) {
                    return;
                }


                const file =
                    this.files[0];


                if (file.size > 5 * 1024 * 1024) {

                    alert(
                        "Please upload a file smaller than 5 MB."
                    );

                    this.value = "";

                    return;

                }


                const label =
                    this.closest(
                        ".vc-bp-replace-btn"
                    );


                label.classList.add(
                    "uploaded"
                );


                label.querySelector("i").className =
                    "fa-solid fa-circle-check";


                label.querySelector("span").textContent =
                    "New File Added";

            }
        );

    });

});
