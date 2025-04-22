// Global wishlist functions

// Check wishlist status for all product cards when page loads
window.checkWishlistStatus = async function() {
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
};

// Run checkWishlistStatus when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', checkWishlistStatus);

window.toggleWishlist = async function(btn) {
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
};

// Update wishlist button visual state
function updateWishlistButton(btn, isInWishlist) {
    if (!btn) return;
    
    if (isInWishlist) {
        // Item is in wishlist - show filled red heart
        btn.classList.add('bg-brand-surface-subtle', 'dark:bg-dark-brand-surface-subtle');
        btn.classList.add('text-red-500', 'dark:text-red-400');
        btn.classList.remove('text-brand-text-secondary', 'dark:text-dark-brand-text-secondary');
        btn.querySelector('svg').setAttribute('fill', 'currentColor');
        btn.title = "Remove from Wishlist";
    } else {
        // Item is not in wishlist - show empty heart
        btn.classList.remove('bg-brand-surface-subtle', 'dark:bg-dark-brand-surface-subtle');
        btn.classList.remove('text-red-500', 'dark:text-red-400');
        btn.classList.add('text-brand-text-secondary', 'dark:text-dark-brand-text-secondary');
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
