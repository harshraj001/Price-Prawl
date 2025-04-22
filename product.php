<?php
/**
 * PricePrawl Product Details Page
 * Displays product information from a supported retailer
 */
session_start();
// --- PHP Logic ---
$url_param = isset($_GET['url']) ? filter_var($_GET['url'], FILTER_SANITIZE_URL) : '';
$pid = isset($_GET['pid']) ? trim(htmlspecialchars($_GET['pid'])) : '';
$pos = isset($_GET['pos']) ? filter_var($_GET['pos'], FILTER_SANITIZE_NUMBER_INT) : '';

// Strict validation for essential parameters
if (empty($pid) || empty($pos) || !ctype_digit((string)$pos)) {
    error_log("Missing or invalid parameters: PID='{$pid}', POS='{$pos}'");
    header('Location: index.php?error=missing_parameters');
    exit;
}

// Log product view activity if user is logged in
if (isset($_SESSION['user_id'])) {
    require_once 'includes/activity_logger.php';
    logProductViewActivity($_SESSION['user_id'], [
        'pid' => $pid,
        'pos' => $pos,
        'retailer' => getRetailerName($pos),
        'url' => $url_param,
        'view_time' => date('Y-m-d H:i:s')
    ]);
}

// --- Helper Function ---
function getRetailerName($pos) {
     $retailers = [
        63 => 'Amazon', 6326 => 'Amazon US', 8965 => 'Amazon UK', 8963 => 'Amazon Canada',
        2 => 'Flipkart', 111 => 'Myntra', 2191 => 'Ajio', 2190 => 'Tata Cliq', 6660 => 'JioMart',
        71 => 'Croma', 6607 => 'Reliance Digital', 129 => 'Snapdeal', 421 => 'ShopClues',
        1830 => 'Nykaa', 1429 => 'Paytm Mall', 333 => 'Pepperfry', 57 => 'Lenskart'
    ];
    return isset($retailers[$pos]) ? $retailers[$pos] : 'Unknown Retailer';
}
$retailerName = getRetailerName($pos);

// Set page title initially - JS will update it later
$pageTitle = 'Loading Product...';

// --- Include Header ---
// Brings in <!DOCTYPE>, <head>, Tailwind config, styles, opening <body>, header nav, ticker
include_once 'includes/header.php'; // Assumes header.php is correctly themed
?>

<!-- Page-specific JS files (Load AFTER the main content structure exists) -->
<!-- Ensure these paths are correct relative to product.php -->
<script src="productData.js" defer></script> <?php // Helper functions ?>
<script src="product-data-parser.js" defer></script> <?php // Dynamic section creation ?>

<!-- Main Content Area (Container opened in header.php) -->

    <div id="product-content-area" class="space-y-6 md:space-y-8">

        <!-- Product Details Card -->
        <div id="product-details-card" class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50">
            <div class="md:flex">
                <!-- Product Image Section -->
                <div class="md:w-2/5 p-4 md:p-6 flex justify-center items-center">
                    <!-- Placeholder: Dark bg color added -->
                    <div class="relative flex items-center justify-center w-full aspect-square bg-gray-200 dark:bg-gray-700/50 rounded animate-pulse overflow-hidden" id="product-image-container">
                        <?php /* Image element added by JS */ ?>
                         <!-- Stock Status Badge - Classes updated by JS, added dark:bg-* -->
                         <div id="stock-status-badge" class="absolute top-2 right-2 text-white text-xs font-bold px-2 py-1 rounded shadow-sm opacity-0 transition-opacity duration-300"></div>
                    </div>
                </div>

                <!-- Product Details Section -->
                <div class="md:w-3/5 p-4 md:p-6 border-t md:border-t-0 md:border-l border-brand-border/30 dark:border-dark-brand-border/50 flex flex-col">
                    <!-- Product Name Placeholder: Dark bg added -->
                    <div class="mb-3 h-7 w-4/5 rounded bg-gray-300 dark:bg-gray-600/80 animate-pulse" id="product-name-placeholder"></div>
                    <h1 id="product-name" class="text-xl md:text-2xl font-bold mb-3 text-brand-text-primary dark:text-dark-brand-text-primary hidden"></h1>

                    <!-- Brand & Category Placeholders: Dark bg added -->
                    <div class="mb-4 text-sm space-y-1.5">
                        <div class="h-4 w-1/2 rounded bg-gray-300 dark:bg-gray-600/80 animate-pulse" id="product-brand-placeholder"></div>
                        <div id="product-brand-container" class="hidden">
                            <span class="text-brand-text-secondary dark:text-dark-brand-text-secondary">Brand: </span>
                            <span id="product-brand" class="font-semibold text-brand-text-primary dark:text-dark-brand-text-primary"></span>
                        </div>
                        <div class="h-4 w-3/5 rounded bg-gray-300 dark:bg-gray-600/80 animate-pulse" id="product-category-placeholder"></div>
                         <div id="product-category-container" class="hidden">
                            <span class="text-brand-text-secondary dark:text-dark-brand-text-secondary">Category: </span>
                            <span id="product-category" class="font-semibold text-brand-text-primary dark:text-dark-brand-text-primary"></span>
                        </div>
                    </div>

                    <!-- Rating Placeholder: Dark bg added -->
                    <div id="product-rating-section" class="flex items-center mb-4 text-sm opacity-0 transition-opacity duration-300">
                         <div class="h-4 w-2/5 rounded bg-gray-300 dark:bg-gray-600/80 animate-pulse" id="product-rating-placeholder"></div>
                         <div id="product-rating-content" class="hidden flex items-center">
                             <!-- Dark mode star color handled by yellow-400/500 potentially, check appearance -->
                            <div id="rating-stars" class="text-yellow-400 dark:text-yellow-500 mr-1.5 flex items-center"></div>
                            <span id="rating-value" class="text-brand-text-primary dark:text-dark-brand-text-primary font-medium"></span>
                            <span class="text-brand-text-secondary dark:text-dark-brand-text-secondary/80">/5</span>
                            <span id="rating-count" class="ml-2 text-brand-text-secondary dark:text-dark-brand-text-secondary/80 text-xs">(?)</span>
                         </div>
                    </div>

                    <!-- Price Placeholder: Dark bg added -->
                    <div class="mb-6">
                         <div class="h-9 w-2/5 rounded bg-gray-300 dark:bg-gray-600/80 animate-pulse" id="product-price-placeholder"></div>
                        <div id="product-price" class="text-3xl font-bold text-brand-accent dark:text-dark-brand-accent hidden"></div>
                    </div>

                    <!-- Buy Now Button Placeholder: Dark bg added -->
                    <div class="mb-6 mt-auto pt-4">
                         <div class="h-12 w-3/5 rounded-lg bg-gray-300 dark:bg-gray-600/80 animate-pulse" id="buy-now-placeholder"></div>
                         <!-- JS applies dark mode classes to the button itself -->
                        <a id="buy-now-link" href="#" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent font-semibold py-3 px-6 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header opacity-0 pointer-events-none hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"> <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /> </svg>
                            <span id="buy-now-text">Buy on Retailer</span>
                        </a>
                        
                        <!-- Action Buttons -->
                        <div class="flex space-x-3 mt-3">
                            <!-- Set Alert Button -->
                            <button id="set-alert-btn" class="flex-1 flex items-center justify-center bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent font-medium py-2 px-4 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header opacity-0 pointer-events-none hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                Set Price Alert
                            </button>
                            
                            <!-- Add to Wishlist Button -->
                            <button id="add-wishlist-btn" class="flex-1 flex items-center justify-center border border-brand-border dark:border-dark-brand-border text-brand-text-primary dark:text-dark-brand-text-primary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle font-medium py-2 px-4 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 dark:focus:ring-offset-dark-brand-header opacity-0 pointer-events-none hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Add to Wishlist
                            </button>
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="border-t border-brand-border/40 dark:border-dark-brand-border/50 pt-4 mt-4">
                         <!-- Dark mode text colors applied -->
                        <div class="text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary/80">
                            <p>Product ID: <span id="product-pid" class="font-medium text-brand-text-primary dark:text-dark-brand-text-secondary"><?php echo htmlspecialchars($pid); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thumbnail Gallery Section (Container added by JS if images exist) -->
            <div id="thumbnail-section-container"></div>

        </div> <!-- End product-details-card -->

        <!-- Additional Data Section -->
         <div id="additional-data-container" class="space-y-6 md:space-y-8">
             <!-- Loading Indicator: Dark mode styles added -->
             <div id="additional-data-loader" class="p-6 text-center hidden bg-brand-header dark:bg-dark-brand-header rounded-xl shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50">
                 <div class="flex flex-col items-center">
                     <div class="loader mb-3"></div> <?php // Loader style defined in header ?>
                     <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary text-sm">Loading price history & comparisons...</p>
                 </div>
             </div>
             <!-- Error Message: Dark mode styles added -->
             <div id="additional-data-error" class="p-6 text-center hidden bg-red-100 dark:bg-red-900/30 rounded-xl border border-red-300 dark:border-red-700/50">
                 <p class="text-red-700 dark:text-red-300 text-sm mb-3">Could not load additional product information.</p>
                 <button id="retry-additional-data" class="text-sm text-brand-accent dark:text-dark-brand-accent hover:underline focus:outline-none focus:ring-2 focus:ring-brand-accent dark:focus:ring-dark-brand-accent rounded px-2 py-1">Retry</button>
             </div>
             <?php /* Sections added by product-data-parser.js need dark styles applied there */ ?>
         </div> <!-- End additional-data-container -->

         <!-- Error display for initial fetch failure: Dark mode styles added -->
         <div id="initial-fetch-error" class="hidden bg-red-100 dark:bg-red-900/30 rounded-lg shadow-md p-8 text-center border border-red-300 dark:border-red-700/50">
             <div class="text-red-500 dark:text-red-400 text-5xl mb-4"><i class="fas fa-exclamation-circle"></i></div>
             <h2 class="text-2xl font-bold mb-2 text-brand-text-primary dark:text-dark-brand-text-primary">Error Loading Product</h2>
             <p id="initial-fetch-error-message" class="text-brand-text-secondary dark:text-dark-brand-text-secondary mb-6">Could not fetch product details.</p>
             <button onclick="fetchInitialProductData(); this.parentElement.classList.add('hidden');" class="bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent font-bold py-2 px-4 rounded transition duration-300">
                 <i class="fas fa-sync-alt mr-2"></i>Try Again
             </button>
         </div>

    </div> <?php // End product-content-area ?>

    <?php // Container div is closed in footer.php ?>


<!-- Gallery Modal (Already themed previously) -->
<div id="gallery-modal" class="fixed inset-0 bg-black/90 dark:bg-black/95 z-[999] flex items-center justify-center hidden p-4 transition-opacity duration-300 opacity-0">
    <div class="relative w-full max-w-4xl mx-auto">
        <button id="close-gallery" class="absolute -top-8 right-0 md:top-2 md:right-2 text-white text-3xl hover:text-gray-300 transition-colors z-[1001]" aria-label="Close Gallery"><i class="fas fa-times"></i></button>
        <button id="prev-image" class="absolute left-0 md:-left-10 top-1/2 transform -translate-y-1/2 text-white text-4xl hover:text-gray-300 transition-colors z-[1000] p-2 rounded-full bg-black/30 hover:bg-black/50" aria-label="Previous Image"><i class="fas fa-chevron-left"></i></button>
        <button id="next-image" class="absolute right-0 md:-right-10 top-1/2 transform -translate-y-1/2 text-white text-4xl hover:text-gray-300 transition-colors z-[1000] p-2 rounded-full bg-black/30 hover:bg-black/50" aria-label="Next Image"><i class="fas fa-chevron-right"></i></button>
        <div id="gallery-image-container" class="flex items-center justify-center h-[80vh] md:h-auto"><img id="gallery-image" src="" alt="Product Image Full Size" class="max-h-[80vh] max-w-full object-contain rounded-lg shadow-xl"></div>
        <div class="text-center text-white/80 text-sm mt-3"><span id="current-image-index">1</span> / <span id="total-images">0</span></div>
    </div>
</div>

<!-- Page specific Javascript (Keep functional JS from previous step) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- PARAMETER & ELEMENT REFERENCES (Keep from previous step) ---
    const urlParams = new URLSearchParams(window.location.search);
    const pos = urlParams.get('pos');
    const pid = urlParams.get('pid');

    if (!pid || !pos || !/^\d+$/.test(pos)) {
        console.error("Missing, empty, or invalid essential parameters (pid, pos). Cannot load product.");
        displayInitialFetchError("Missing or invalid product identifiers in the URL.");
        document.getElementById('product-details-card')?.classList.add('hidden');
        return;
    }
    console.log(`Initializing product page for POS: ${pos}, PID: ${pid}`);

    const productNameEl = document.getElementById('product-name');
    const productNamePlaceholder = document.getElementById('product-name-placeholder');
    // ... (all other element references from previous step) ...
    const productBrandEl = document.getElementById('product-brand');
    const productBrandContainer = document.getElementById('product-brand-container');
    const productBrandPlaceholder = document.getElementById('product-brand-placeholder');
    const productCategoryEl = document.getElementById('product-category');
    const productCategoryContainer = document.getElementById('product-category-container');
    const productCategoryPlaceholder = document.getElementById('product-category-placeholder');
    const productImageContainer = document.getElementById('product-image-container');
    const productPriceEl = document.getElementById('product-price');
    const productPricePlaceholder = document.getElementById('product-price-placeholder');
    const stockStatusBadgeEl = document.getElementById('stock-status-badge');
    const buyNowLinkEl = document.getElementById('buy-now-link');
    const buyNowTextEl = document.getElementById('buy-now-text');
    const buyNowPlaceholder = document.getElementById('buy-now-placeholder');
    const productPidEl = document.getElementById('product-pid');
    const productRatingSectionEl = document.getElementById('product-rating-section');
    const productRatingPlaceholder = document.getElementById('product-rating-placeholder');
    const productRatingContentEl = document.getElementById('product-rating-content');
    const ratingStarsEl = document.getElementById('rating-stars');
    const ratingValueEl = document.getElementById('rating-value');
    const ratingCountEl = document.getElementById('rating-count');
    const thumbnailSectionContainer = document.getElementById('thumbnail-section-container');
    const additionalDataContainer = document.getElementById('additional-data-container');
    const additionalDataLoader = document.getElementById('additional-data-loader');
    const additionalDataError = document.getElementById('additional-data-error');
    const retryAdditionalDataBtn = document.getElementById('retry-additional-data');
    const initialFetchErrorContainer = document.getElementById('initial-fetch-error');
    const initialFetchErrorMessage = document.getElementById('initial-fetch-error-message');
    const galleryModal = document.getElementById('gallery-modal');
    const galleryImageEl = document.getElementById('gallery-image');
    const currentImageIndexSpan = document.getElementById('current-image-index');
    const totalImagesSpan = document.getElementById('total-images');
    let galleryImages = [];
    let currentImageIndex = 0;
    let initialProductData = null;


    // --- GALLERY FUNCTIONS (Keep from previous step) ---
    function openGallery(index) { /* ... */
         if (!galleryModal || galleryImages.length === 0) return;
            currentImageIndex = index;
            updateGalleryImage();
            galleryModal.classList.remove('hidden');
            requestAnimationFrame(() => { galleryModal.classList.add('opacity-100'); });
            document.body.style.overflow = 'hidden';
    }
    function closeGallery() { /* ... */
        if (!galleryModal) return;
        galleryModal.classList.remove('opacity-100');
        setTimeout(() => { galleryModal.classList.add('hidden'); }, 300);
        document.body.style.overflow = '';
    }
    function navigateGallery(direction) { /* ... */
         if (galleryImages.length === 0) return;
            currentImageIndex = (currentImageIndex + direction + galleryImages.length) % galleryImages.length;
            updateGalleryImage();
    }
    function updateGalleryImage() { /* ... */
         if (!galleryImageEl || !currentImageIndexSpan || !totalImagesSpan) return;
            galleryImageEl.src = galleryImages[currentImageIndex] || '';
            galleryImageEl.alt = `Product Image ${currentImageIndex + 1}`;
            currentImageIndexSpan.textContent = currentImageIndex + 1;
            totalImagesSpan.textContent = galleryImages.length;
    }


    // --- PLACEHOLDER FUNCTIONS (Keep from previous step) ---
    function hidePlaceholders() { /* ... */
        productNamePlaceholder?.classList.add('hidden');
        productBrandPlaceholder?.classList.add('hidden');
        productCategoryPlaceholder?.classList.add('hidden');
        productPricePlaceholder?.classList.add('hidden');
        buyNowPlaceholder?.classList.add('hidden');
        productRatingPlaceholder?.classList.add('hidden');
        productImageContainer?.classList.remove('animate-pulse', 'bg-gray-200', 'dark:bg-gray-700', 'dark:bg-gray-700/50'); // Ensure all placeholder styles removed
    }
    function showUIElements() { /* ... */
        productNameEl?.classList.remove('hidden');
        productBrandContainer?.classList.remove('hidden');
        productCategoryContainer?.classList.remove('hidden');
        productPriceEl?.classList.remove('hidden');
        buyNowLinkEl?.classList.remove('hidden');
        // Show action buttons
        document.getElementById('set-alert-btn')?.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
        document.getElementById('add-wishlist-btn')?.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
    }

    // --- UI UPDATE FUNCTION (Dark Mode fixes integrated) ---
     function updateProductUI(data) {
        if (!data) {
            console.error("No data provided to updateProductUI");
            displayInitialFetchError("Received invalid data while loading product details.");
            return;
        }
        console.log("Updating UI with data:", data);

        hidePlaceholders(); // Remove placeholders first
        showUIElements(); // Make actual content containers visible

        document.title = `${data.name || 'Product Details'} | PricePrawl`;

        const safeUpdateText = (el, content, fallback = 'N/A') => {
            if (el) el.textContent = content || fallback;
        };

        safeUpdateText(productNameEl, data.name);
        safeUpdateText(productBrandEl, data.brand);
        safeUpdateText(productCategoryEl, data.category);
        safeUpdateText(productPidEl, data.pid);

        // Update Image
        if (productImageContainer) {
            let img = productImageContainer.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                img.id = 'main-product-image';
                 // Added dark:bg-gray-100 for image background consistency
                img.className = 'max-w-full max-h-full h-auto object-contain p-2 opacity-0 transition-opacity duration-500 bg-white dark:bg-gray-100';
                productImageContainer.appendChild(img);
            }
            img.src = data.image || 'https://placehold.co/300/FAF6F2/93785B?text=No+Image';
            img.alt = data.name || 'Product Image';
            requestAnimationFrame(() => { img.classList.add('opacity-100'); });
        }

        // Update Price
        if (productPriceEl) {
            safeUpdateText(productPriceEl,
                data.cur_price !== undefined && data.cur_price !== null
                ? `₹${Number(data.cur_price).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`
                : 'Price Unavailable'
            );
        }

        // Update Stock Status Badge (Added dark mode classes)
        if (stockStatusBadgeEl) {
            stockStatusBadgeEl.classList.remove('opacity-0');
            if (data.inStock === 1) {
                stockStatusBadgeEl.textContent = 'In Stock';
                 // Added dark:bg-green-700, dark:text-white for consistency
                stockStatusBadgeEl.className = 'absolute top-2 right-2 bg-green-600 dark:bg-green-700 text-white dark:text-white text-xs font-bold px-2 py-1 rounded shadow-sm opacity-100 transition-opacity duration-300';
            } else if (data.inStock === 0) {
                stockStatusBadgeEl.textContent = 'Out of Stock';
                 // Added dark:bg-red-700, dark:text-white for consistency
                stockStatusBadgeEl.className = 'absolute top-2 right-2 bg-red-600 dark:bg-red-700 text-white dark:text-white text-xs font-bold px-2 py-1 rounded shadow-sm opacity-100 transition-opacity duration-300';
            } else {
                stockStatusBadgeEl.classList.add('opacity-0');
                stockStatusBadgeEl.textContent = '';
            }
        }

         // Update Rating Section (Added dark mode star color)
        if (productRatingSectionEl && productRatingContentEl && data.rating !== undefined && data.rating !== null && data.rating > 0) {
            const ratingValue = parseFloat(data.rating);
            const ratingCount = data.ratingCount || 0;
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= ratingValue) starsHtml += '<i class="fas fa-star"></i>';
                else if (i - 0.5 <= ratingValue) starsHtml += '<i class="fas fa-star-half-alt"></i>';
                else starsHtml += '<i class="far fa-star"></i>';
            }
            if(ratingStarsEl) {
                ratingStarsEl.innerHTML = starsHtml;
                // Ensure star color works in dark mode
                ratingStarsEl.classList.add('text-yellow-400', 'dark:text-yellow-500');
            }
            if(ratingValueEl) ratingValueEl.textContent = ratingValue.toFixed(1);
            if(ratingCountEl) ratingCountEl.textContent = `(${Number(ratingCount).toLocaleString('en-IN')} ratings)`;

            productRatingContentEl.classList.remove('hidden');
            productRatingSectionEl.classList.add('opacity-100');
        } else if (productRatingSectionEl) {
            productRatingSectionEl.classList.add('opacity-0');
            productRatingContentEl?.classList.add('hidden');
        }

        // Update Buy Now Link
        if (buyNowLinkEl && buyNowTextEl) {
            if (data.link && data.link !== '#') {
                buyNowLinkEl.href = data.link;
                 // Ensure dark mode classes are present when enabled
                buyNowLinkEl.className = 'inline-flex items-center justify-center bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent font-semibold py-3 px-6 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header opacity-100 pointer-events-auto'; // Set opacity to 1, remove hidden
                safeUpdateText(buyNowTextEl, `Buy on ${data.site_name || 'Retailer'}`);
            } else {
                buyNowLinkEl.classList.add('opacity-0', 'pointer-events-none', 'hidden'); // Keep hidden if no link
            }
        }

        // Update Thumbnail Gallery (Added dark mode bg for image container)
        if (thumbnailSectionContainer && data.thumbnailImages && Array.isArray(data.thumbnailImages) && data.thumbnailImages.length > 0) {
            galleryImages = [...data.thumbnailImages];
             // Add dark:bg classes to container and items
            let galleryHtml = `<div class="p-4 border-t border-brand-border/40 dark:border-dark-brand-border/50 mt-4">
                <h3 class="text-lg font-semibold mb-3 text-brand-text-primary dark:text-dark-brand-text-primary">More Images</h3>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2" id="thumbnail-gallery">`;

            data.thumbnailImages.forEach((image, index) => {
                galleryHtml += `
                    <div class="border border-brand-border/40 dark:border-dark-brand-border/60 rounded-md overflow-hidden cursor-pointer hover:opacity-80 dark:hover:opacity-70 transition-opacity aspect-square flex items-center justify-center bg-white dark:bg-gray-700/30 group"
                         onclick="openGallery(${index})">
                        <img src="${image}" alt="Product Thumbnail ${index+1}" class="max-w-full max-h-full object-contain p-1 transition-transform duration-200 group-hover:scale-105" loading="lazy">
                    </div>`;
            });
            galleryHtml += `</div></div>`;
            thumbnailSectionContainer.innerHTML = galleryHtml;
            updateGalleryImage();
        } else if (thumbnailSectionContainer) {
             thumbnailSectionContainer.innerHTML = '';
             galleryImages = [];
        }
    }

    // --- FETCHING FUNCTIONS (Keep from previous step) ---
    function displayInitialFetchError(message) { /* ... */
        hidePlaceholders();
        document.getElementById('product-details-card')?.classList.add('hidden');
        if (initialFetchErrorMessage) initialFetchErrorMessage.textContent = message || "Could not fetch product details. Please check the URL or try again later.";
        if (initialFetchErrorContainer) initialFetchErrorContainer.classList.remove('hidden');
    }
    async function fetchInitialProductData() { /* ... (no theme changes needed here) ... */
        console.log("Starting initial data fetch...");
        initialFetchErrorContainer?.classList.add('hidden');
        document.getElementById('product-details-card')?.classList.remove('hidden');
        // Placeholders are shown via HTML structure

        const originalApiUrl = `https://buyhatke.com/api/productData?pos=${pos}&pid=${pid}`;
        const corsProxyUrl = 'https://cors-ninja.harshraj864869.workers.dev/proxy?url=';
        const apiUrl = `${corsProxyUrl}${encodeURIComponent(originalApiUrl)}`;

        try {
            const response = await fetch(apiUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error(`Network response was not ok: ${response.status}`);
            const data = await response.json();

            if (data && data.status === 1 && data.data) {
                initialProductData = data.data;
                updateProductUI(initialProductData);
                showAdditionalDataLoading();
                fetchAdditionalProductData(initialProductData);
            } else {
                throw new Error(data.msg || 'Invalid API response format');
            }
        } catch (error) {
            console.error('Error fetching initial product data:', error);
            displayInitialFetchError(error.message);
        }
     }
    function showAdditionalDataLoading() { /* ... */
         additionalDataError?.classList.add('hidden');
        additionalDataLoader?.classList.remove('hidden');
    }
    function hideAdditionalDataLoading() { /* ... */
         additionalDataLoader?.classList.add('hidden');
    }
     function showAdditionalDataError() { /* ... */
        hideAdditionalDataLoading();
        additionalDataError?.classList.remove('hidden');
     }
     async function fetchAdditionalProductData(initialData) { /* ... (no theme changes needed here) ... */
         if (!initialData?.site_name || !initialData?.name || !initialData?.site_pos || (!initialData?.internalPid && !initialData?.pid) ) {
             console.warn("Missing required data for additional fetch.", initialData);
             hideAdditionalDataLoading();
             return;
         }
         console.log("Fetching additional data...");

         const siteName = initialData.site_name.toLowerCase().replace(/[^a-z0-9-]+/g, '-').replace(/--+/g, '-').replace(/^-|-$/g, '');
         const formattedName = initialData.name.toLowerCase().replace(/[^\p{L}\p{N}\s-]/gu, '').replace(/\s+/g, '-').substring(0, 80);
         const productId = initialData.internalPid || initialData.pid;

         const additionalDataUrl = `https://buyhatke.com/${siteName}-${formattedName}-price-in-india-${initialData.site_pos}-${productId}/__data.json?x-sveltekit-invalidated=001`;
         const corsProxyUrl = 'https://cors-ninja.harshraj864869.workers.dev/proxy?url=';
         const proxiedUrl = `${corsProxyUrl}${encodeURIComponent(additionalDataUrl)}`;

         try {
             const response = await fetch(proxiedUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
             if (!response.ok) throw new Error(`Network response was not ok: ${response.status}`);
             const additionalData = await response.json();
             console.log("Additional data received:", additionalData);

             hideAdditionalDataLoading();

             if (typeof parseAndDisplayAdditionalData === 'function') {
                 if (additionalData && additionalData.nodes) {
                    parseAndDisplayAdditionalData(additionalData.nodes, 'additional-data-container', initialProductData?.currencySymbol || '₹');
                 } else {
                    console.warn("Additional data received but no 'nodes' array found.");
                 }
             } else {
                 console.error("Parsing function 'parseAndDisplayAdditionalData' not found.");
                 showAdditionalDataError(); // Show error if parser missing
             }

         } catch (error) {
             console.error('Error fetching or processing additional data:', error);
             showAdditionalDataError();
         }
     }


    // --- EVENT LISTENERS (Keep from previous step) ---
    galleryModal?.querySelector('#close-gallery')?.addEventListener('click', closeGallery);
    galleryModal?.querySelector('#prev-image')?.addEventListener('click', () => navigateGallery(-1));
    galleryModal?.querySelector('#next-image')?.addEventListener('click', () => navigateGallery(1));
    retryAdditionalDataBtn?.addEventListener('click', () => { /* ... */
        if (initialProductData) {
             showAdditionalDataLoading();
             fetchAdditionalProductData(initialProductData);
        } else { console.error("Cannot retry: Initial product data not available."); }
    });
    document.addEventListener('keydown', function(e) { /* ... */
        if (!galleryModal || galleryModal.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeGallery();
        if (e.key === 'ArrowLeft') navigateGallery(-1);
        if (e.key === 'ArrowRight') navigateGallery(1);
    });
    
    // --- WISHLIST AND ALERT FUNCTIONALITY ---
    // Function to check if product is in wishlist
    async function checkIfInWishlist() {
        <?php if (!isset($_SESSION['user_id'])): ?>
            return false;
        <?php endif; ?>
        
        if (!initialProductData || !initialProductData.link) {
            console.error("Cannot check wishlist status: Product data not available");
            return false;
        }
        
        try {
            const checkData = new FormData();
            checkData.append('action', 'check');
            checkData.append('product_url', initialProductData.link);
            
            const response = await fetch('wishlist_actions.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: checkData
            });
            
            const data = await response.json();
            return data.in_wishlist === true;
        } catch (error) {
            console.error('Error checking wishlist status:', error);
            return false;
        }
    }
    
    // Function to update wishlist button appearance
    function updateWishlistButton(isInWishlist) {
        const button = document.getElementById('add-wishlist-btn');
        if (!button) return;
        
        if (isInWishlist) {
            // Item is in wishlist - show filled heart
            button.classList.add('bg-brand-surface-subtle', 'dark:bg-dark-brand-surface-subtle');
            button.classList.add('text-brand-accent', 'dark:text-dark-brand-accent');
            button.querySelector('svg').setAttribute('fill', 'currentColor');
            button.innerHTML = button.innerHTML.replace(/<svg[\s\S]*?<\/svg>\s*Add to Wishlist/, 
                `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg> Remove from Wishlist`);
        } else {
            // Item is not in wishlist - show empty heart
            button.classList.remove('bg-brand-surface-subtle', 'dark:bg-dark-brand-surface-subtle');
            button.classList.remove('text-brand-accent', 'dark:text-dark-brand-accent');
            button.querySelector('svg').setAttribute('fill', 'none');
            button.innerHTML = button.innerHTML.replace(/<svg[\s\S]*?<\/svg>\s*Remove from Wishlist/, 
                `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg> Add to Wishlist`);
        }
    }
    
    // Check wishlist status when product data is loaded
    fetchInitialProductData().then(() => {
        if (initialProductData) {
            checkIfInWishlist().then(isInWishlist => {
                updateWishlistButton(isInWishlist);
            });
        }
    });
    
    // Add to Wishlist Button Listener
    document.getElementById('add-wishlist-btn')?.addEventListener('click', function() {
        // Check if user is logged in (we'll handle this on the server side too)
        <?php if (!isset($_SESSION['user_id'])): ?>
            // Show login modal if it exists, otherwise redirect to login page
            if (document.getElementById('login-modal')) {
                document.getElementById('login-modal').classList.remove('hidden');
            } else {
                window.location.href = 'login.php';
            }
            return;
        <?php endif; ?>
        
        if (!initialProductData) {
            console.error("Cannot add to wishlist: Product data not available");
            return;
        }
        
        // Get product data
        const productData = {
            product_name: initialProductData.name || 'Unknown Product',
            product_url: initialProductData.link || window.location.href,
            product_image: initialProductData.image || '',
            current_price: initialProductData.cur_price || 0,
            original_price: initialProductData.last_price || initialProductData.cur_price || 0,
            retailer: initialProductData.site_name || document.getElementById('retailer-name').textContent || 'Unknown Retailer'
        };
        
        // Determine if we're adding or removing from wishlist based on button text
        const isRemoving = this.textContent.trim().includes('Remove from Wishlist');
        const action = isRemoving ? 'remove' : 'add';
        
        // Create form data
        const formData = new FormData();
        formData.append('action', action);
        
        if (isRemoving) {
            // For removal, we just need the product URL
            formData.append('product_url', productData.product_url);
        } else {
            // For adding, we need all product details
            for (const key in productData) {
                formData.append(key, productData[key]);
            }
        }
        
        // Send AJAX request to add/remove from wishlist
        fetch('wishlist_actions.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const actionText = isRemoving ? 'removed from' : 'added to';
            showWishlistToast(data.success, data.message, productData.product_name);
            
            // Update button appearance if successful
            if (data.success) {
                updateWishlistButton(!isRemoving);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showWishlistToast(false, 'Error adding to wishlist', 'Please try again later');
        });
    });
    
    // Set Price Alert Button Listener
    document.getElementById('set-alert-btn')?.addEventListener('click', function() {
        // Check if user is logged in
        <?php if (!isset($_SESSION['user_id'])): ?>
            // Show login modal if it exists, otherwise redirect to login page
            if (document.getElementById('login-modal')) {
                document.getElementById('login-modal').classList.remove('hidden');
            } else {
                window.location.href = 'login.php';
            }
            return;
        <?php endif; ?>
        
        if (!initialProductData) {
            console.error("Cannot set alert: Product data not available");
            return;
        }
        
        // First add to wishlist if not already in wishlist
        const addToWishlistAndShowAlertModal = () => {
            const productData = {
                product_name: initialProductData.name || 'Unknown Product',
                product_url: initialProductData.link || window.location.href,
                product_image: initialProductData.image || '',
                current_price: initialProductData.cur_price || 0,
                original_price: initialProductData.last_price || initialProductData.cur_price || 0,
                retailer: initialProductData.site_name || document.getElementById('retailer-name').textContent || 'Unknown Retailer'
            };
            
            const formData = new FormData();
            formData.append('action', 'add');
            for (const key in productData) {
                formData.append(key, productData[key]);
            }
            
            fetch('wishlist_actions.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // After adding to wishlist (or if already in wishlist), redirect to wishlist page with alert section active
                window.location.href = 'wishlist.php?action=set_alert';
            })
            .catch(error => {
                console.error('Error:', error);
                showWishlistToast(false, 'Error setting up alert', 'Please try again later');
            });
        };
        
        addToWishlistAndShowAlertModal();
    });
    
    // Create and show toast notification for wishlist actions
    function showWishlistToast(success, message, detail) {
        // Check if toast already exists, if not create it
        let toast = document.getElementById('wishlist-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'wishlist-toast';
            toast.className = 'fixed bottom-4 right-4 bg-white dark:bg-gray-800 text-brand-text-primary dark:text-dark-brand-text-primary shadow-lg rounded-lg p-4 flex items-start max-w-xs z-50 transform translate-y-10 opacity-0 transition-all duration-300 invisible';
            
            const toastHTML = `
                <div id="toast-icon" class="mr-3 flex-shrink-0 text-green-500 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p id="toast-message" class="font-medium">Item added to wishlist</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" id="toast-detail">You can view your items in your wishlist</p>
                </div>
                <button onclick="hideWishlistToast()" class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            
            toast.innerHTML = toastHTML;
            document.body.appendChild(toast);
        }
        
        // Set appropriate toast icon and message
        const toastIcon = document.getElementById('toast-icon');
        const toastMessage = document.getElementById('toast-message');
        const toastDetail = document.getElementById('toast-detail');
        
        if (success) {
            // Success - show green checkmark
            toastIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `;
            toastMessage.textContent = message || 'Added to Wishlist';
            toastDetail.textContent = detail || 'You can view your items in your wishlist';
        } else {
            // Error - show warning icon
            toastIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            `;
            toastMessage.textContent = message || 'Could not add to wishlist';
            toastDetail.textContent = detail || 'Please try again';
        }
        
        // Show the toast notification
        toast.classList.remove('invisible', 'opacity-0', 'translate-y-10');
        toast.classList.add('opacity-100', 'translate-y-0');
        
        // Auto hide after 5 seconds
        setTimeout(hideWishlistToast, 5000);
    }
    
    // Function to hide toast notification
    window.hideWishlistToast = function() {
        const toast = document.getElementById('wishlist-toast');
        if (toast) {
            toast.classList.add('opacity-0', 'translate-y-10');
            setTimeout(() => {
                toast.classList.add('invisible');
            }, 300);
        }
    };

    // --- INITIAL FETCH ---
    fetchInitialProductData(); // Start the process

}); // End DOMContentLoaded
</script>


<?php
// --- Include Footer ---
include_once 'includes/footer.php';
?>