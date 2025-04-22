<?php
session_start(); // Ensure $_SESSION is available
$pageTitle = 'Trending Deals';
include_once 'includes/header.php';
?>
<!-- Wishlist functionality script -->
<script src="js/wishlist.js"></script>

<!-- Main Content Area (Container opened in header.php) -->

    <h1 id="mainHeading" class="text-3xl md:text-4xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-4">
        Trending Deals
    </h1>
    <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mb-6">
        Explore popular products. Select a category to filter results.
    </p>

    <!-- Loader -->
    <div class="loader mx-auto" id="loader"></div>

    <!-- Main Layout: Sidebar/Dropdown + Grid -->
    <div class="flex flex-col md:flex-row gap-6 md:gap-8">

        <!-- Sidebar/Dropdown Area -->
        <aside class="w-full md:w-56 lg:w-64 flex-shrink-0 mb-6 md:mb-0 relative">
            <div class="md:sticky md:top-[80px]">
                <!-- Dropdown Toggle Button (Mobile/Tablet) -->
                <div class="md:hidden sticky top-0 z-30 bg-brand-bg-light dark:bg-dark-brand-bg-light pb-3 pt-3">
                    <button id="category-toggle-button" aria-haspopup="true" aria-expanded="false" class="w-full flex justify-between items-center px-4 py-3 bg-brand-header dark:bg-dark-brand-header border border-brand-border/50 dark:border-brand-border/50 rounded-lg shadow-sm text-brand-text-primary dark:text-dark-brand-text-primary focus:outline-none focus:ring-2 focus:ring-brand-accent dark:focus:ring-dark-brand-accent">
                        <span id="selected-category-name" class="font-semibold">Select Category...</span>
                        <svg id="category-toggle-icon" class="w-5 h-5 text-brand-text-secondary dark:text-dark-brand-text-secondary transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                </div>
                 <!-- Sidebar Content -->
                <div id="categorySidebarContent" class="hidden md:block bg-brand-header dark:bg-dark-brand-header md:bg-transparent md:dark:bg-transparent p-4 md:p-0 rounded-lg md:rounded-none shadow-md md:shadow-none border border-brand-border/30 dark:border-dark-brand-border/50 md:border-none md:mt-0 mt-2 md:static absolute left-0 right-0 z-20">
                    <h2 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary border-b border-brand-border/50 dark:border-dark-brand-border/50 pb-2 mb-3 hidden md:block">Categories</h2>
                    <div id="categorySidebarLinks" class="space-y-1 max-h-[50vh] md:max-h-none overflow-y-auto md:overflow-y-visible">
                         <div id="category-loader" class="flex items-center justify-center p-4"><div class="loader !m-0 !w-5 !h-5"></div></div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Product Grid -->
        <section id="productGrid" class="flex-grow min-w-0 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <!-- Products populated here by JS -->
        </section>
    </div>

<?php // Main container div is closed in footer.php ?>

<!-- Custom Styles specific to Trending Page -->
<style>
    /* Sidebar link styling */
    #categorySidebarLinks a { display: block; padding: 6px 10px; font-size: 0.875rem; border-radius: 6px; transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out; color: theme('colors.brand-text-secondary'); word-break: break-word; cursor: pointer; }
    .dark #categorySidebarLinks a { color: theme('colors.dark-brand-text-secondary'); }
    #categorySidebarLinks a:hover { background-color: theme('colors.brand-surface-subtle'); color: theme('colors.brand-accent'); }
    .dark #categorySidebarLinks a:hover { background-color: theme('colors.dark-brand-surface-subtle'); color: theme('colors.dark-brand-accent'); }
    #categorySidebarLinks a.active { background-color: theme('colors.brand-accent'); color: theme('colors.brand-text-on-accent'); font-weight: 600; }
    .dark #categorySidebarLinks a.active { background-color: theme('colors.dark-brand-accent'); color: theme('colors.dark-brand-text-on-accent'); }

    /* Dropdown animation (optional but recommended) */
    #categorySidebarContent.dropdown-entering { animation: slideDown 0.2s ease-out forwards; display: block !important; }
    #categorySidebarContent.dropdown-leaving { animation: slideUp 0.2s ease-in forwards; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px) scaleY(0.95); } to { opacity: 1; transform: translateY(0) scaleY(1); } }
    @keyframes slideUp { from { opacity: 1; transform: translateY(0) scaleY(1); } to { opacity: 0; transform: translateY(-10px) scaleY(0.95); } }
</style>

<!-- JavaScript for Trending Page -->
<script>
      (() => {
        // --- Configuration ---
        const CORS_PROXY = 'https://cors-ninja.harshraj864869.workers.dev/proxy?url=';
        const BASE_API_PATH = 'https://buyhatke.com/price-history-deals';
        const API_SUFFIX = '/__data.json?x-sveltekit-invalidated=001';
        const INITIAL_CATEGORY_NAME = "Hot Deals";
        const INITIAL_CATEGORY_SLUG = "hot-deals";

        // --- DOM References ---
        const productGrid = document.getElementById('productGrid');
        const categorySidebarLinksContainer = document.getElementById('categorySidebarLinks');
        const categorySidebarContent = document.getElementById('categorySidebarContent');
        const categoryToggleButton = document.getElementById('category-toggle-button');
        const categoryToggleIcon = document.getElementById('category-toggle-icon');
        const selectedCategoryNameSpan = document.getElementById('selected-category-name');
        const categoryLoader = document.getElementById('category-loader');
        const mainHeading = document.getElementById('mainHeading');
        const loader = document.getElementById('loader'); // Main grid loader

        if (!productGrid || !categorySidebarLinksContainer || !mainHeading || !loader || !categorySidebarContent || !categoryToggleButton || !categoryToggleIcon || !selectedCategoryNameSpan || !categoryLoader) {
            console.error("Error: Missing essential HTML elements for Trending Page JS.");
            return;
        }

        // --- Helper Functions ---
        function createSlug(text) { if (!text) return ''; return text.toString().toLowerCase().replace(/\s+/g, '-').replace(/&/g, '-and-').replace(/[^\w\-]+/g, '').replace(/\-\-+/g, '-').replace(/^-+/, '').replace(/-+$/, ''); }
        const formatPrice = (price) => { if (typeof price !== 'number') return ''; return price.toLocaleString('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: 0, maximumFractionDigits: 0 }); };
        const getRatingStars = (rating) => { if (typeof rating !== 'number' || rating < 0 || rating > 5) return ''; rating = Math.max(0, Math.min(5, rating)); const fullStars = Math.floor(rating); const halfStar = rating % 1 >= 0.4 ? 1 : 0; const emptyStars = 5 - fullStars - halfStar; return '★'.repeat(fullStars) + (halfStar ? '½' : '') + '☆'.repeat(emptyStars); };
        function showLoader(show) { loader.style.display = show ? 'block' : 'none'; }
        function showCategoryLoader(show) { categoryLoader.style.display = show ? 'flex': 'none';}
        function setActiveCategoryLink(slug) {
            if (!categorySidebarLinksContainer) return; const links = categorySidebarLinksContainer.querySelectorAll('a'); let activeName = INITIAL_CATEGORY_NAME; links.forEach(link => { const isActive = link.dataset.slug === slug; link.classList.toggle('active', isActive); if(isActive) { activeName = link.textContent; } }); if(selectedCategoryNameSpan) { selectedCategoryNameSpan.textContent = activeName; }
         }

        // --- Toggle Sidebar/Dropdown ---
        function toggleCategories(forceOpen = null) {
            if (!categorySidebarContent || !categoryToggleButton || !categoryToggleIcon) return; // Safety check

            const isHidden = categorySidebarContent.classList.contains('hidden');
            const shouldBeOpen = forceOpen !== null ? forceOpen : isHidden;

            categoryToggleButton.setAttribute('aria-expanded', shouldBeOpen ? 'true' : 'false');
            categoryToggleIcon.classList.toggle('rotate-180', shouldBeOpen);

            // Use animation classes for smooth transition
            if (shouldBeOpen) {
                categorySidebarContent.classList.remove('hidden', 'dropdown-leaving');
                categorySidebarContent.classList.add('dropdown-entering');
            } else {
                categorySidebarContent.classList.remove('dropdown-entering');
                categorySidebarContent.classList.add('dropdown-leaving');
                // Wait for animation before fully hiding
                setTimeout(() => {
                    // Only hide if it's still meant to be hidden (user might have clicked again quickly)
                    if (categorySidebarContent.classList.contains('dropdown-leaving')) {
                         categorySidebarContent.classList.add('hidden');
                         categorySidebarContent.classList.remove('dropdown-leaving');
                    }
                }, 200); // Match CSS animation duration (0.2s)
            }
        }
        // Attach listener ONLY if button exists
        categoryToggleButton?.addEventListener('click', (e) => {
             e.stopPropagation(); // Prevent body click listener from closing immediately
             toggleCategories();
        });

        // --- Data Processing and Display (Keep logic with dark mode classes) ---
        function processAndDisplayData(parsedData, categoryName, updateSidebar = true) {
            try {
                const dataNode = parsedData?.nodes?.find(node => node?.type === 'data' && Array.isArray(node?.data));
                if (!dataNode || !Array.isArray(dataNode.data)) { throw new Error('Invalid data structure.'); }
                const mainDataArray = dataNode.data; const products = []; const categories = []; let lastProcessedIndex = -1;
                // --- Product Extraction ---
                for (let i = 0; i < mainDataArray.length; i++) { const item = mainDataArray[i]; if (typeof item === 'object' && item !== null && !Array.isArray(item) && 'name' in item && 'link' in item /* ... */) { const product = {}; let maxIndexUsed = i; for (const key in item) { const valueIndex = item[key]; if (typeof valueIndex === 'number' && valueIndex >= 0 && valueIndex < mainDataArray.length) { product[key] = mainDataArray[valueIndex]; maxIndexUsed = Math.max(maxIndexUsed, valueIndex); } else product[key] = undefined; } if (product.name && product.link && product.cur_price !== undefined) { products.push(product); lastProcessedIndex = Math.max(lastProcessedIndex, maxIndexUsed); } } }
                // --- Category Extraction ---
                if (updateSidebar) { let categoryStartIndex = -1; /* ... find start index ... */ if (categoryStartIndex === -1) categoryStartIndex = lastProcessedIndex + 1; for (let i = categoryStartIndex; i < mainDataArray.length; i++) { const item = mainDataArray[i]; if (typeof item === 'object' && item !== null && !Array.isArray(item) && 'id' in item && 'name' in item && 'link' in item && !('cur_price' in item)) { const category = {}; for (const key in item) { if (key === 'name' || key === 'link') { const valueIndex = item[key]; if (typeof valueIndex === 'number' && valueIndex >= 0 && valueIndex < mainDataArray.length) category[key] = mainDataArray[valueIndex]; else category[key] = undefined; } } if (category.name && typeof category.link === 'string' && category.link.trim() !== '') categories.push(category); } } }

                // --- Update UI ---
                mainHeading.textContent = categoryName + " Deals"; document.title = categoryName + " Deals - PricePrawl";
                if (updateSidebar) {
                   showCategoryLoader(false); categorySidebarLinksContainer.innerHTML = ''; const hotDealsLink = document.createElement('a'); hotDealsLink.textContent = INITIAL_CATEGORY_NAME; hotDealsLink.dataset.slug = INITIAL_CATEGORY_SLUG; hotDealsLink.href = `#${INITIAL_CATEGORY_SLUG}`; categorySidebarLinksContainer.appendChild(hotDealsLink); categories.sort((a, b) => a.name.localeCompare(b.name)); categories.forEach(cat => { const link = document.createElement('a'); const slug = (typeof cat.link === 'string' && cat.link.trim() !== '') ? cat.link.trim() : createSlug(cat.name); link.textContent = cat.name; link.dataset.slug = slug; link.href = `#${slug}`; link.title = cat.name; categorySidebarLinksContainer.appendChild(link); });
                }
                productGrid.innerHTML = '';
                if (products.length === 0) { productGrid.innerHTML = '<p class="col-span-full text-center text-brand-text-secondary dark:text-dark-brand-text-secondary">No products found.</p>'; }
                else { products.forEach(product => { /* ... (Keep product card generation with dark classes) ... */
                    const card = document.createElement('div'); card.className = 'product-card bg-brand-header dark:bg-dark-brand-header border border-brand-border/30 dark:border-dark-brand-border/50 rounded-lg shadow-card dark:shadow-dark-card hover:shadow-lg dark:hover:shadow-dark-hover transition-all duration-200 transform hover:-translate-y-1 group overflow-hidden flex flex-col'; let discountPercentage = product.price_drop_per; if (typeof discountPercentage !== 'number' || discountPercentage <= 0) { if (typeof product.last_price === 'number' && typeof product.cur_price === 'number' && product.last_price > product.cur_price && product.last_price > 0) { discountPercentage = Math.round(((product.last_price - product.cur_price) / product.last_price) * 100); } else { discountPercentage = null; } } const ratingValue = typeof product.rating === 'number' ? product.rating : -1; const ratingStars = getRatingStars(ratingValue); const ratingCountText = (typeof product.ratingCount === 'number' && product.ratingCount > 0) ? `${product.ratingCount.toLocaleString('en-IN')} Ratings` : ''; const hasRatingInfo = ratingValue >= 0 && ratingStars && ratingCountText;
                    
                    card.innerHTML = `<div class="relative flex flex-col h-full">
                        <a href="search.php?query=${product.link || '#'}" target="" rel="noopener noreferrer" class="flex-1 flex flex-col">
                            <div class="product-image-container relative aspect-square bg-white dark:bg-gray-100 p-2 overflow-hidden flex items-center justify-center">
                                <img src="${product.image || 'placeholder.png'}" alt="${product.name || 'Product Image'}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.onerror=null; this.src='https://placehold.co/200/FAF6F2/93785B?text=Error'; this.style.objectFit='contain';">
                                ${discountPercentage ? `<span class="absolute top-1.5 left-1.5 bg-red-600 dark:bg-red-700 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm">${discountPercentage}% OFF</span>` : ''}
                                
                                <?php if (isset($_SESSION['user_id'])): ?>
                                <!-- Wishlist Button (Logged in users) -->
                                <button 
                                    class="wishlist-btn absolute top-1.5 right-1.5 bg-brand-header/70 dark:bg-dark-brand-header/70 backdrop-blur-sm p-1.5 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-red-500 dark:hover:text-red-400 transition-colors duration-200 opacity-0 group-focus-within:opacity-100 group-hover:opacity-100 focus:opacity-100"
                                    onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(this)"
                                    data-product-url="${product.link || '#'}" 
                                    data-product-name="${product.name || ''}" 
                                    data-product-image="${product.image || ''}"
                                    data-product-price="${product.cur_price || ''}"
                                    data-product-original-price="${product.last_price || ''}"
                                    data-product-retailer="${product.site_name || ''}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                    </svg>
                                </button>
                                <?php else: ?>
                                <!-- Wishlist Button (Logged out users - redirects to login) -->
                                <a href="login.php" class="absolute top-1.5 right-1.5 bg-brand-header/70 dark:bg-dark-brand-header/70 backdrop-blur-sm p-1.5 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-red-500 dark:hover:text-red-400 transition-colors duration-200 opacity-0 group-focus-within:opacity-100 group-hover:opacity-100 focus:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                            <div class="p-3 flex flex-col flex-grow">
                                <p class="product-title text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1 h-10 line-clamp-2 leading-tight" title="${product.name || ''}">${product.name || 'N/A'}</p>
                                ${hasRatingInfo ? `<div class="rating-container flex items-center gap-1 mb-1.5 text-xs"><span class="stars text-yellow-400 dark:text-yellow-500">${ratingStars}</span><span class="rating-count text-brand-text-secondary dark:text-dark-brand-text-secondary/80">${ratingCountText}</span></div>` : '<div class="h-5 mb-1.5"></div>'}
                                <div class="product-details mt-auto pt-1">
                                    <div class="product-footer flex justify-between items-end">
                                        <div class="product-price-container">
                                            <span class="product-price text-lg font-bold text-brand-accent dark:text-dark-brand-accent">${formatPrice(product.cur_price)}</span>
                                            ${typeof product.last_price === 'number' && product.last_price > product.cur_price ? `<span class="product-last-price text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary/80 line-through ml-1.5">${formatPrice(product.last_price)}</span>` : ''}
                                        </div>
                                        ${product.site_logo ? `<img src="${product.site_logo}" alt="${product.site_name || ''}" class="product-site-logo h-6 w-6 rounded-full object-contain border border-black/5 dark:border-white/10 bg-white" loading="lazy" title="${product.site_name || ''}">` : '<div class="h-6 w-6"></div>'}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>`; 
                    
                    productGrid.appendChild(card);
                 }); }
            } catch (processingError) { console.error("Data Processing Error:", processingError); productGrid.innerHTML = `<p class="col-span-full text-center text-red-500 dark:text-red-400">Error displaying products: ${processingError.message}</p>`; }
        }

        // --- Main Fetch Function ---
        async function fetchAndDisplay(categorySlug = null, categoryName = INITIAL_CATEGORY_NAME, updateSidebar = false) {
            showLoader(true); productGrid.innerHTML = ''; if (updateSidebar) { showCategoryLoader(true); categorySidebarLinksContainer.innerHTML = ''; }
            let apiUrl = BASE_API_PATH; if (categorySlug && categorySlug !== INITIAL_CATEGORY_SLUG) { apiUrl += `/${categorySlug}`; } apiUrl += API_SUFFIX;
            const proxyApiUrl = CORS_PROXY + apiUrl; console.log("Fetching from:", proxyApiUrl);
            try {
                const response = await fetch(proxyApiUrl); if (!response.ok) { throw new Error(`HTTP error! Status: ${response.status}`); }
                const parsedData = await response.json();
                processAndDisplayData(parsedData, categoryName, updateSidebar);
                setActiveCategoryLink(categorySlug || INITIAL_CATEGORY_SLUG);
            } catch (error) { console.error("Fetch Error:", error); productGrid.innerHTML = `<p class="col-span-full text-center text-red-500 dark:text-red-400">Failed to load products: ${error.message}</p>`; if(updateSidebar) showCategoryLoader(false);}
            finally { showLoader(false); if(updateSidebar) showCategoryLoader(false); }
        }

        // --- Event Listener for Category Clicks ---
        categorySidebarLinksContainer.addEventListener('click', (event) => {
            const linkElement = event.target.closest('a');
            if (linkElement && categorySidebarLinksContainer.contains(linkElement)) {
                event.preventDefault(); const slug = linkElement.dataset.slug; const name = linkElement.textContent;
                if (!linkElement.classList.contains('active') && slug) {
                     fetchAndDisplay(slug, name, false); if (window.innerWidth < 768) { toggleCategories(false); }
                 } else if (window.innerWidth < 768) { toggleCategories(false); }
            }
        });

        // --- Close Dropdown Listeners (Added Back) ---
        document.addEventListener('click', (event) => {
            if (window.innerWidth < 768 && categorySidebarContent && !categorySidebarContent.classList.contains('hidden') && categoryToggleButton && !categoryToggleButton.contains(event.target) && !categorySidebarContent.contains(event.target)) {
                 toggleCategories(false);
            }
         });
         document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && window.innerWidth < 768 && categorySidebarContent && !categorySidebarContent.classList.contains('hidden')) {
                 toggleCategories(false);
            }
         });

        // --- Page Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            // Existing code will remain here
            checkWishlistStatus();
        });
        
        // --- Wishlist Functionality ---
        // Check wishlist status for all product cards
        async function checkWishlistStatus() {
            <?php if (!isset($_SESSION['user_id'])): ?>
                return; // Do nothing if user is not logged in
            <?php endif; ?>
            
            // Wait for products to load
            setTimeout(async () => {
                const wishlistBtns = document.querySelectorAll('.wishlist-btn');
                if (wishlistBtns.length === 0) return;
                
                for (const btn of wishlistBtns) {
                    const productUrl = btn.dataset.productUrl;
                    if (!productUrl) continue;
                    
                    try {
                        const checkData = new FormData();
                        checkData.append('action', 'check');
                        checkData.append('product_url', productUrl);
                        
                        const response = await fetch('wishlist_actions.php', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: checkData
                        });
                        
                        const data = await response.json();
                        if (data.in_wishlist === true) {
                            updateWishlistButton(btn, true);
                        }
                    } catch (error) {
                        console.error('Error checking wishlist status:', error);
                    }
                }
            }, 1000); // Wait for products to be rendered
        }
        
        // Toggle wishlist status
        async function toggleWishlist(btn) {
            const productUrl = btn.dataset.productUrl;
            const productName = btn.dataset.productName;
            const productImage = btn.dataset.productImage;
            const currentPrice = btn.dataset.productPrice;
            const originalPrice = btn.dataset.productOriginalPrice;
            const retailer = btn.dataset.productRetailer;
            
            if (!productUrl || !productName) {
                console.error("Missing product data for wishlist action");
                return;
            }
            
            // Check if product is already in wishlist
            try {
                const checkData = new FormData();
                checkData.append('action', 'check');
                checkData.append('product_url', productUrl);
                
                const checkResponse = await fetch('wishlist_actions.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: checkData
                });
                
                const checkResult = await checkResponse.json();
                const isInWishlist = checkResult.in_wishlist === true;
                
                // Create form data for add or remove
                const actionData = new FormData();
                if (isInWishlist) {
                    // Remove from wishlist
                    actionData.append('action', 'remove');
                    actionData.append('product_url', productUrl);
                } else {
                    // Add to wishlist
                    actionData.append('action', 'add');
                    actionData.append('product_name', productName);
                    actionData.append('product_url', productUrl);
                    actionData.append('product_image', productImage || '');
                    actionData.append('current_price', currentPrice || '');
                    actionData.append('original_price', originalPrice || '');
                    actionData.append('retailer', retailer || '');
                }
                
                const actionResponse = await fetch('wishlist_actions.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: actionData
                });
                
                const actionResult = await actionResponse.json();
                if (actionResult.success) {
                    // Toggle visual state
                    updateWishlistButton(btn, !isInWishlist);
                    
                    // Show feedback toast
                    showToast(actionResult.message);
                } else {
                    showToast("Error: " + (actionResult.message || "Could not update wishlist"));
                }
            } catch (error) {
                console.error('Error managing wishlist:', error);
                showToast("Error updating wishlist. Please try again.");
            }
        }
        
        // Update wishlist button visual state
        function updateWishlistButton(btn, isInWishlist) {
            if (!btn) return;
            
            if (isInWishlist) {
                // Item is in wishlist - show filled heart
                btn.classList.add('bg-brand-surface-subtle', 'dark:bg-dark-brand-surface-subtle');
                btn.classList.add('text-brand-accent', 'dark:text-dark-brand-accent');
                btn.querySelector('svg').setAttribute('fill', 'currentColor');
                btn.title = "Remove from Wishlist";
            } else {
                // Item is not in wishlist - show empty heart
                btn.classList.remove('bg-brand-surface-subtle', 'dark:bg-dark-brand-surface-subtle');
                btn.classList.remove('text-brand-accent', 'dark:text-dark-brand-accent');
                btn.querySelector('svg').setAttribute('fill', 'none');
                btn.title = "Add to Wishlist";
            }
        }
        
        // Show toast notification
        function showToast(message) {
            // Check if toast container already exists
            let toastContainer = document.getElementById('toast-container');
            
            if (!toastContainer) {
                // Create new toast container
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'fixed bottom-4 right-4 z-50 flex flex-col gap-2';
                document.body.appendChild(toastContainer);
            }
            
            // Create new toast
            const toast = document.createElement('div');
            toast.className = 'bg-brand-accent/90 dark:bg-dark-brand-accent/90 text-white px-4 py-2 rounded-md shadow-lg transform transition-all duration-300 flex items-center';
            toast.innerHTML = `
                <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span>${message}</span>
            `;
            
            // Add to container
            toastContainer.appendChild(toast);
            
            // Remove after delay
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    toast.remove();
                    // Remove container if empty
                    if (toastContainer.children.length === 0) {
                        toastContainer.remove();
                    }
                }, 300);
            }, 3000);
        }

        // --- Initial Data Load ---
        fetchAndDisplay(INITIAL_CATEGORY_SLUG, INITIAL_CATEGORY_NAME, true);

      })();
    </script>

<?php
include_once 'includes/footer.php';
?>

<!-- Login Required Modal -->
<div id="login-modal" class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50 hidden">
    <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-brand-accent dark:text-dark-brand-accent">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-2">Login Required</h3>
            <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mb-6">Please log in to add items to your wishlist</p>
            
            <div class="flex justify-center space-x-3">
                <a href="login.php" class="px-4 py-2 bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover transition-colors">
                    Login
                </a>
                <button onclick="closeLoginModal()" class="px-4 py-2 border border-brand-border dark:border-dark-brand-border text-brand-text-primary dark:text-dark-brand-text-primary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>