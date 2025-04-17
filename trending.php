<?php
session_start(); // Ensure $_SESSION is available
$pageTitle = 'Trending Deals';
include_once 'includes/header.php';
?>

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
                <button id="category-toggle-button" aria-haspopup="true" aria-expanded="false" class="md:hidden w-full flex justify-between items-center px-4 py-3 bg-brand-header dark:bg-dark-brand-header border border-brand-border/50 dark:border-dark-brand-border/50 rounded-lg shadow-sm text-brand-text-primary dark:text-dark-brand-text-primary mb-3 focus:outline-none focus:ring-2 focus:ring-brand-accent dark:focus:ring-dark-brand-accent">
                    <span id="selected-category-name" class="font-semibold">Select Category...</span>
                    <svg id="category-toggle-icon" class="w-5 h-5 text-brand-text-secondary dark:text-dark-brand-text-secondary transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                 <!-- Sidebar Content -->
                <div id="categorySidebarContent" class="hidden md:block bg-brand-header dark:bg-dark-brand-header md:bg-transparent md:dark:bg-transparent p-4 md:p-0 rounded-lg md:rounded-none shadow-md md:shadow-none border border-brand-border/30 dark:border-dark-brand-border/50 md:border-none md:mt-0 mt-2">
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
                    
                    // Check if user is logged in (PHP variable passed to JS)
                    const isUserLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
                    
                    card.innerHTML = `<div class="relative flex flex-col h-full">
                        <a href="${product.link || '#'}" target="_blank" rel="noopener noreferrer" class="flex-1 flex flex-col">
                            <div class="product-image-container relative aspect-square bg-white dark:bg-gray-100 p-2 overflow-hidden">
                                <img src="${product.image || 'placeholder.png'}" alt="${product.name || 'Product Image'}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.onerror=null; this.src='https://placehold.co/200/FAF6F2/93785B?text=Error'; this.style.objectFit='contain';">
                                ${discountPercentage ? `<span class="absolute top-1.5 left-1.5 bg-red-600 dark:bg-red-700 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm">${discountPercentage}% OFF</span>` : ''}
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
                        <!-- Wishlist Button -->
                        <button 
                            onclick="event.preventDefault(); event.stopPropagation(); ${isUserLoggedIn ? 'addToWishlist(this)' : 'showLoginModal()'}" 
                            class="absolute top-2 right-2 p-2 rounded-full bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-800 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent transition-colors z-10"
                            data-product-name="${product.name || ''}"
                            data-product-url="${product.link || ''}"
                            data-product-image="${product.image || ''}"
                            data-current-price="${product.cur_price || ''}"
                            data-original-price="${product.last_price || ''}"
                            data-retailer="${product.site_name || ''}"
                            aria-label="Add to wishlist">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </button>
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

<!-- Wishlist Notification Toast -->
<div id="wishlist-toast" class="fixed bottom-4 right-4 bg-white dark:bg-gray-800 text-brand-text-primary dark:text-dark-brand-text-primary shadow-lg rounded-lg p-4 flex items-start max-w-xs z-50 transform translate-y-10 opacity-0 transition-all duration-300 invisible">
    <div id="toast-icon" class="mr-3 flex-shrink-0 text-green-500 dark:text-green-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    <div class="flex-1">
        <p id="toast-message" class="font-medium">Item added to wishlist</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" id="toast-detail">You can view your items in your wishlist</p>
    </div>
    <button onclick="hideToast()" class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<!-- Wishlist JavaScript -->
<script>
    // Function to add a product to the wishlist
    function addToWishlist(button) {
        // Get product data from button attributes
        const productData = {
            product_name: button.getAttribute('data-product-name'),
            product_url: button.getAttribute('data-product-url'),
            product_image: button.getAttribute('data-product-image'),
            current_price: button.getAttribute('data-current-price'),
            original_price: button.getAttribute('data-original-price'),
            retailer: button.getAttribute('data-retailer')
        };
        
        // Create form data
        const formData = new FormData();
        formData.append('action', 'add');
        for (const key in productData) {
            formData.append(key, productData[key]);
        }
        
        // Send AJAX request to add to wishlist
        fetch('wishlist_actions.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Set appropriate toast icon and message
            const toastIcon = document.getElementById('toast-icon');
            const toastMessage = document.getElementById('toast-message');
            const toastDetail = document.getElementById('toast-detail');
            
            if (data.success) {
                // Success - show green checkmark
                toastIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                `;
                toastMessage.textContent = 'Added to Wishlist';
                toastDetail.textContent = productData.product_name;
                
                // Change the button appearance to indicate item is in wishlist
                button.classList.add('text-red-500', 'dark:text-red-400');
                button.classList.remove('text-brand-text-secondary', 'dark:text-dark-brand-text-secondary');
                button.querySelector('svg').setAttribute('fill', 'currentColor');
            } else {
                // Error - show warning icon
                toastIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                `;
                toastMessage.textContent = data.message || 'Could not add to wishlist';
                toastDetail.textContent = 'Please try again';
            }
            
            // Show the toast notification
            showToast();
        })
        .catch(error => {
            console.error('Error:', error);
            // Show error toast
            document.getElementById('toast-icon').innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `;
            document.getElementById('toast-message').textContent = 'Error adding to wishlist';
            document.getElementById('toast-detail').textContent = 'Please try again later';
            showToast();
        });
    }
    
    // Function to show toast notification
    function showToast() {
        const toast = document.getElementById('wishlist-toast');
        toast.classList.remove('invisible', 'opacity-0', 'translate-y-10');
        toast.classList.add('opacity-100', 'translate-y-0');
        
        // Auto hide after 5 seconds
        setTimeout(hideToast, 5000);
    }
    
    // Function to hide toast notification
    function hideToast() {
        const toast = document.getElementById('wishlist-toast');
        toast.classList.add('opacity-0', 'translate-y-10');
        setTimeout(() => {
            toast.classList.add('invisible');
        }, 300);
    }
    
    // Function to show login modal
    function showLoginModal() {
        document.getElementById('login-modal').classList.remove('hidden');
    }
    
    // Function to close login modal
    function closeLoginModal() {
        document.getElementById('login-modal').classList.add('hidden');
    }
    
    // Close login modal when clicking outside
    document.getElementById('login-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLoginModal();
        }
    });
</script>