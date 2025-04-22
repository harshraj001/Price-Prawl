<?php
session_start(); // Start session
$pageTitle = 'Biggest Price Drops'; // Set page title
include_once 'includes/header.php'; // Include header with theme setup
?>

<!-- Wishlist functionality script -->
<script src="js/wishlist.js"></script>
<!-- Main Content Area (Container opened in header.php) -->

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-2">Biggest Price Drops</h1>
        <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary">Products with significant recent price reductions, sorted by discount.</p>
    </div>

    <!-- Statistics Banner - Added Dark Mode Classes -->
    <div class="bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle p-5 rounded-lg shadow-card dark:shadow-dark-card mb-8 flex flex-wrap justify-around items-center gap-y-4 border border-brand-border/20 dark:border-dark-brand-border/30">
        <div class="text-center px-4 py-2">
            <div class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider font-medium">Today's Drops</div>
            <div class="text-lg sm:text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mt-1">3,254</div> <?php // Placeholder data ?>
        </div>
        <div class="text-center px-4 py-2 border-x border-brand-border/30 dark:border-dark-brand-border/40">
            <div class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider font-medium">Highest Drop</div>
            <div class="text-lg sm:text-xl font-bold text-red-600 dark:text-red-400 mt-1">62%</div> <?php // Placeholder data ?>
        </div>
        <div class="text-center px-4 py-2 border-r border-brand-border/30 dark:border-dark-brand-border/40">
            <div class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider font-medium">Average Drop</div>
            <div class="text-lg sm:text-xl font-bold text-brand-accent dark:text-dark-brand-accent mt-1">18%</div> <?php // Placeholder data ?>
        </div>
        <div class="text-center px-4 py-2">
            <div class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider font-medium">Last Updated</div>
            <div class="text-lg sm:text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mt-1">5 mins ago</div> <?php // Placeholder data ?>
        </div>
    </div>

    <!-- Price Drops Grid -->
    <div id="priceDropsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <!-- Products will be loaded here by JS -->
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="hidden flex justify-center items-center py-16">
        <div class="loader"></div> <?php // Uses loader style from header ?>
    </div>

    <!-- Error State -->
    <div id="errorState" class="hidden text-center py-16 px-4">
        <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary">Unable to load price drops. Please try refreshing the page.</p>
        <button onclick="fetchPriceDrops()" class="mt-4 text-sm text-brand-accent dark:text-dark-brand-accent hover:underline">Retry</button>
    </div>

<?php // Main container div is closed in footer.php ?>



<script>
    // Wrap in IIFE
    (() => {
        // --- Configuration & DOM References ---
        const CORS_PROXY = 'https://cors-ninja.harshraj864869.workers.dev/proxy?url=';
        const BASE_API_PATH = 'https://buyhatke.com/price-history-deals'; // Using same endpoint as trending
        const API_SUFFIX = '/__data.json?x-sveltekit-invalidated=001';
        const priceDropsGrid = document.getElementById('priceDropsGrid');
        const loadingState = document.getElementById('loadingState');
        const errorState = document.getElementById('errorState');

        if (!priceDropsGrid || !loadingState || !errorState) {
             console.error("Missing essential elements for Price Drops page.");
             return;
        }

        // --- Helper Functions ---
        const formatPrice = (price) => { /* ... (keep exact function from previous correct versions) ... */
            if (typeof price !== 'number') return ''; return price.toLocaleString('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: 0, maximumFractionDigits: 0 });
        };
        const getRatingStars = (rating) => { /* ... (keep exact function from previous correct versions) ... */
             if (typeof rating !== 'number' || rating < 0 || rating > 5) return ''; rating = Math.max(0, Math.min(5, rating)); const fullStars = Math.floor(rating); const halfStar = rating % 1 >= 0.4 ? 1 : 0; const emptyStars = 5 - fullStars - halfStar; return '★'.repeat(fullStars) + (halfStar ? '½' : '') + '☆'.repeat(emptyStars);
        };

        // --- Create Product Card HTML (with Dark Mode classes) ---
        const createProductCard = (product) => {
            // Calculate discount percentage robustly
            let discountPercentage = null;
            const lastPrice = parseFloat(product.last_price);
            const currentPrice = parseFloat(product.cur_price);

             if (typeof product.price_drop_per === 'number' && product.price_drop_per > 0) {
                 discountPercentage = Math.round(product.price_drop_per); // Use provided if valid
             } else if (!isNaN(lastPrice) && !isNaN(currentPrice) && lastPrice > currentPrice && lastPrice > 0) {
                 discountPercentage = Math.round(((lastPrice - currentPrice) / lastPrice) * 100);
             }

            // Skip rendering if no positive discount calculated
            if (discountPercentage === null || discountPercentage <= 0) {
                return ''; // Don't show products without a significant drop
            }

            const ratingValue = typeof product.rating === 'number' ? product.rating : -1;
            const ratingStars = getRatingStars(ratingValue);
            const ratingCountText = (typeof product.ratingCount === 'number' && product.ratingCount > 0) ? `${product.ratingCount.toLocaleString('en-IN')} Ratings` : '';
            const hasRatingInfo = ratingValue >= 0 && ratingStars && ratingCountText;

            return `
                <div class="product-card bg-brand-header dark:bg-dark-brand-header border border-brand-border/30 dark:border-dark-brand-border/50 rounded-lg shadow-card dark:shadow-dark-card hover:shadow-lg dark:hover:shadow-dark-hover transition-all duration-200 transform hover:-translate-y-1 group overflow-hidden flex flex-col">
                    <div class="relative flex flex-col h-full">
                        <a href="search.php?query=${product.link || '#'}" rel="noopener noreferrer" class="flex-1 flex flex-col">
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
                                <div class="flex items-center gap-1.5 mb-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">
                                    ${product.site_logo ? `<img src="${product.site_logo}" alt="${product.site_name || ''}" class="h-4 w-auto rounded-sm object-contain flex-shrink-0">` : ''}
                                    <span class="truncate flex-grow">${product.site_name || 'Store'}</span>
                                </div>
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
                    </div>
                </div>`;
        };

        // --- Fetch and display price drops ---
        const fetchPriceDrops = async () => {
            loadingState.classList.remove('hidden');
            errorState.classList.add('hidden');
            priceDropsGrid.innerHTML = '';

            try {
                const response = await fetch(CORS_PROXY + BASE_API_PATH + API_SUFFIX);
                if (!response.ok) throw new Error('Network response failed');
                const data = await response.json();
                const dataNode = data?.nodes?.find(node => node?.type === 'data' && Array.isArray(node?.data));
                if (!dataNode || !Array.isArray(dataNode.data)) throw new Error('Invalid API data structure');

                // --- Product Extraction (Same as trending) ---
                const products = [];
                const mainDataArray = dataNode.data;
                for (let i = 0; i < mainDataArray.length; i++) { const item = mainDataArray[i]; if (typeof item === 'object' && item !== null && !Array.isArray(item) && 'name' in item && 'link' in item /* ... */) { const product = {}; for (const key in item) { const valueIndex = item[key]; if (typeof valueIndex === 'number' && valueIndex >= 0 && valueIndex < mainDataArray.length) { product[key] = mainDataArray[valueIndex]; } else product[key] = undefined; } if (product.name && product.link && product.cur_price !== undefined) { products.push(product); } } }

                // --- Calculate Discount and Filter/Sort ---
                 const productsWithDrop = products.map(p => {
                     let discount = null;
                     const lastPrice = parseFloat(p.last_price);
                     const currentPrice = parseFloat(p.cur_price);
                     if (typeof p.price_drop_per === 'number' && p.price_drop_per > 0) {
                         discount = Math.round(p.price_drop_per);
                     } else if (!isNaN(lastPrice) && !isNaN(currentPrice) && lastPrice > currentPrice && lastPrice > 0) {
                         discount = Math.round(((lastPrice - currentPrice) / lastPrice) * 100);
                     }
                     return { ...p, calculated_drop: discount };
                 }).filter(p => p.calculated_drop !== null && p.calculated_drop > 0) // Only keep those with a positive drop
                   .sort((a, b) => b.calculated_drop - a.calculated_drop); // Sort descending by discount %


                // Display products
                if (productsWithDrop.length === 0) {
                     priceDropsGrid.innerHTML = '<p class="col-span-full text-center text-brand-text-secondary dark:text-dark-brand-text-secondary py-10">No significant price drops found right now.</p>';
                } else {
                    productsWithDrop.forEach(product => {
                        priceDropsGrid.innerHTML += createProductCard(product);
                    });
                }

            } catch (error) {
                console.error('Error fetching price drops:', error);
                errorState.classList.remove('hidden');
            } finally {
                loadingState.classList.add('hidden');
            }
        };

         // Make fetch function global ONLY if the retry button needs it
         window.fetchPriceDrops = fetchPriceDrops;

        // --- Page Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            fetchPriceDrops();
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

    })(); // End IIFE
</script>

<?php
include_once 'includes/footer.php'; // Include footer
?>