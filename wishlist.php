<?php
session_start();
require_once('includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];

// Fetch wishlist items
$wishlist_stmt = $pdo->prepare("SELECT * FROM wishlist_items WHERE user_id = ? ORDER BY updated_at DESC");
$wishlist_stmt->execute([$user_id]);
$wishlist_items = $wishlist_stmt->fetchAll();

// Fetch price alerts with product details
$alerts_stmt = $pdo->prepare("
    SELECT pa.*, wi.product_name, wi.product_image, wi.current_price, wi.product_url, wi.retailer
    FROM price_alerts pa
    JOIN wishlist_items wi ON pa.wishlist_item_id = wi.id
    WHERE pa.user_id = ?
    ORDER BY pa.updated_at DESC
");
$alerts_stmt->execute([$user_id]);
$price_alerts = $alerts_stmt->fetchAll();

/**
 * PricePrawl - Wishlist Page
 */
$pageTitle = "My Wishlist";
$currentPage = basename($_SERVER['SCRIPT_NAME']);
include 'includes/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 py-8 md:py-12 max-w-6xl">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary">My Wishlist</h1>
        <a href="account.php" class="flex items-center text-sm text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Back to Account
        </a>
    </div>
    
    <!-- Tab Navigation -->
    <div class="mb-8 border-b border-brand-border/30 dark:border-dark-brand-border/30">
        <div class="flex space-x-8">
            <button id="tab-wishlist" class="text-base font-medium text-brand-accent dark:text-dark-brand-accent border-b-2 border-brand-accent dark:border-dark-brand-accent px-1 py-4 focus:outline-none">
                Wishlist Items (<?php echo count($wishlist_items); ?>)
            </button>
            <button id="tab-alerts" class="text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-text-primary dark:hover:text-dark-brand-text-primary px-1 py-4 focus:outline-none">
                Price Alerts (<?php echo count($price_alerts); ?>)
            </button>
        </div>
    </div>
    
    <!-- Wishlist Items Tab Content -->
    <div id="content-wishlist" class="tab-content">
        <?php if (count($wishlist_items) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($wishlist_items as $item): 
                    // Calculate discount percentage if original price exists and is higher than current price
                    $discount = 0;
                    if (!empty($item['original_price']) && $item['original_price'] > $item['current_price']) {
                        $discount = round(($item['original_price'] - $item['current_price']) / $item['original_price'] * 100);
                    }
                ?>
                <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 flex flex-col">
                    <div class="relative">
                        <img src="<?= !empty($item['product_image']) ? htmlspecialchars($item['product_image']) : 'https://via.placeholder.com/300x300?text=No+Image' ?>" 
                             alt="<?= htmlspecialchars($item['product_name']) ?>" 
                             class="w-full h-48 object-cover">
                        <?php if ($discount > 0): ?>
                        <div class="absolute top-3 right-3 bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent text-xs font-bold px-2 py-1 rounded-md">
                            -<?= $discount ?>%
                        </div>
                        <?php endif; ?>
                        <form method="post" action="remove_from_wishlist.php" class="inline">
                            <input type="hidden" name="wishlist_item_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="absolute top-3 left-3 bg-white/90 dark:bg-gray-800/90 p-2 rounded-full text-gray-500 hover:text-red-500 dark:hover:text-red-400 transition-colors" aria-label="Remove from wishlist">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary mb-1"><?= htmlspecialchars($item['product_name']) ?></h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary mb-4">
                                From <?= htmlspecialchars($item['retailer'] ?? 'Unknown Retailer') ?>
                            </p>
                        </div>                        <div class="mt-auto">
                            <div class="flex items-end mb-3">
                                <span class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary">₹<?= number_format($item['current_price'], 0) ?></span>
                                <?php if ($discount > 0): ?>                                <span class="ml-2 text-sm line-through text-brand-text-secondary dark:text-dark-brand-text-secondary">
                                    ₹<?= number_format($item['original_price'], 0) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="flex space-x-2">
                                <a href="search.php?query=<?= htmlspecialchars($item['product_url']) ?>" class="flex-1 px-4 py-2 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent text-sm font-medium rounded-lg text-center transition-colors">
                                    View Product
                                </a>
                                <button type="button" class="px-4 py-2 border border-brand-border dark:border-dark-brand-border text-brand-text-primary dark:text-dark-brand-text-primary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle text-sm font-medium rounded-lg transition-colors" data-toggle="modal" data-target="#setAlertModal" data-item-id="<?= $item['id'] ?>">
                                    Set Alert
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-brand-text-secondary dark:text-dark-brand-text-secondary mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
                <h2 class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-2">Your wishlist is empty</h2>
                <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mb-6">Start adding products to track prices and get alerts</p>
                <a href="index.php" class="px-5 py-2.5 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg font-medium transition-colors duration-200 inline-block">
                    Browse Products
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Price Alerts Tab Content -->
    <div id="content-alerts" class="tab-content hidden">
        <?php if (count($price_alerts) > 0): ?>
            <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-brand-border/30 dark:border-dark-brand-border/30">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Product</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Current Price</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Target Price</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Status</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($price_alerts as $alert): ?>
                            <tr class="border-b border-brand-border/30 dark:border-dark-brand-border/30">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 rounded overflow-hidden bg-gray-100 dark:bg-gray-700">
                                            <img src="<?= !empty($alert['product_image']) ? htmlspecialchars($alert['product_image']) : 'https://via.placeholder.com/100x100?text=No+Image' ?>" 
                                                 alt="<?= htmlspecialchars($alert['product_name']) ?>" 
                                                 class="h-10 w-10 object-cover">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary">
                                                <?= htmlspecialchars($alert['product_name']) ?>
                                            </div>
                                            <div class="text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">
                                                <?= htmlspecialchars($alert['retailer'] ?? 'Unknown Retailer') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>                                <td class="px-6 py-4 text-sm text-brand-text-primary dark:text-dark-brand-text-primary">
                                    ₹<?= number_format($alert['current_price'], 0) ?>
                                </td>                                <td class="px-6 py-4 text-sm text-brand-text-primary dark:text-dark-brand-text-primary">
                                    ₹<?= number_format($alert['target_price'], 0) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $status_class = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-400';
                                    $status_text = 'Waiting';
                                    
                                    if ($alert['current_price'] <= $alert['target_price']) {
                                        $status_class = 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-400';
                                        $status_text = 'Target reached';
                                    }
                                    
                                    if (!$alert['is_active']) {
                                        $status_class = 'bg-gray-100 text-gray-800 dark:bg-gray-800/20 dark:text-gray-400';
                                        $status_text = 'Inactive';
                                    }
                                    ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= $status_class ?>">
                                        <?= $status_text ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="search.php?query=<?= htmlspecialchars($alert['product_url']) ?>" class="text-sm text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover transition-colors">
                                        Visit
                                    </a>
                                    <form method="post" action="toggle_alert.php" class="inline-block">
                                        <input type="hidden" name="alert_id" value="<?= $alert['id'] ?>">
                                        <input type="hidden" name="new_status" value="<?= $alert['is_active'] ? '0' : '1' ?>">
                                        <button type="submit" class="text-sm text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover transition-colors">
                                            <?= $alert['is_active'] ? 'Pause' : 'Resume' ?>
                                        </button>
                                    </form>
                                    <form method="post" action="delete_alert.php" class="inline-block">
                                        <input type="hidden" name="alert_id" value="<?= $alert['id'] ?>">
                                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-brand-text-secondary dark:text-dark-brand-text-secondary mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <h2 class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-2">No price alerts set</h2>
                <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mb-6">Set price alerts on your wishlist items to get notified of price drops</p>
                <button id="go-to-wishlist" class="px-5 py-2.5 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg font-medium transition-colors duration-200">
                    Go to Wishlist                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- JavaScript for Tabs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabWishlist = document.getElementById('tab-wishlist');
    const tabAlerts = document.getElementById('tab-alerts');
    const contentWishlist = document.getElementById('content-wishlist');
    const contentAlerts = document.getElementById('content-alerts');
    const goToWishlistBtn = document.getElementById('go-to-wishlist');
    
    // Function to switch tabs
    function switchTab(activeTab, activeContent, inactiveTab, inactiveContent) {
        // Update tab styles
        activeTab.classList.remove('text-brand-text-secondary', 'dark:text-dark-brand-text-secondary');
        activeTab.classList.add('text-brand-accent', 'dark:text-dark-brand-accent', 'border-b-2', 'border-brand-accent', 'dark:border-dark-brand-accent');
        
        inactiveTab.classList.remove('text-brand-accent', 'dark:text-dark-brand-accent', 'border-b-2', 'border-brand-accent', 'dark:border-dark-brand-accent');
        inactiveTab.classList.add('text-brand-text-secondary', 'dark:text-dark-brand-text-secondary');
        
        // Show/hide content
        activeContent.classList.remove('hidden');
        inactiveContent.classList.add('hidden');
    }
    
    // Tab click events
    tabWishlist.addEventListener('click', function() {
        switchTab(tabWishlist, contentWishlist, tabAlerts, contentAlerts);
    });
    
    tabAlerts.addEventListener('click', function() {
        switchTab(tabAlerts, contentAlerts, tabWishlist, contentWishlist);
    });
    
    // Go to wishlist button in alerts empty state
    if (goToWishlistBtn) {
        goToWishlistBtn.addEventListener('click', function() {
            switchTab(tabWishlist, contentWishlist, tabAlerts, contentAlerts);
        });    }
});
</script>

<!-- Set Price Alert Modal -->
<div id="setAlertModal" class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50 hidden">
    <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 p-6 max-w-md w-full mx-4">
        <div class="text-center mb-5">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-brand-accent dark:text-dark-brand-accent">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-2">Set Price Alert</h3>
            <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mb-4">We'll notify you when the price drops to your target price</p>
        </div>
        
        <form id="alertForm" action="alert_actions.php" method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" id="wishlist_item_id" name="wishlist_item_id" value="">
            
            <div class="mb-5">
                <label for="product_name" class="block text-sm font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary mb-1">Product</label>
                <input type="text" id="product_name" class="w-full bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 rounded-lg py-2 px-3 text-brand-text-primary dark:text-dark-brand-text-primary" readonly>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="current_price" class="block text-sm font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary mb-1">Current Price</label>
                    <input type="text" id="current_price" class="w-full bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 rounded-lg py-2 px-3 text-brand-text-primary dark:text-dark-brand-text-primary" readonly>
                </div>
                <div>
                    <label for="target_price" class="block text-sm font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary mb-1">Target Price (₹)</label>
                    <input type="number" id="target_price" name="target_price" class="w-full bg-white dark:bg-dark-brand-header border border-brand-border/50 dark:border-dark-brand-border/50 rounded-lg py-2 px-3 text-brand-text-primary dark:text-dark-brand-text-primary focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none" required>
                </div>
            </div>
            
            <div class="flex justify-center space-x-3 mt-6">
                <button type="button" onclick="closeAlertModal()" class="px-4 py-2 text-brand-text-primary dark:text-dark-brand-text-primary border border-brand-border dark:border-dark-brand-border rounded-lg hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover">
                    Set Alert
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for Modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set Alert Modal Functionality
    const setAlertButtons = document.querySelectorAll('[data-toggle="modal"][data-target="#setAlertModal"]');
    const setAlertModal = document.getElementById('setAlertModal');
    
    // Open modal when Set Alert button is clicked
    setAlertButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const itemRow = this.closest('.bg-brand-header');
            const productName = itemRow.querySelector('h3').textContent.trim();
            const currentPrice = itemRow.querySelector('.text-xl').textContent.trim().replace('₹', '').replace(',', '');
            
            // Populate the form
            document.getElementById('wishlist_item_id').value = itemId;
            document.getElementById('product_name').value = productName;
            document.getElementById('current_price').value = '₹' + currentPrice;
            document.getElementById('target_price').value = Math.floor(Number(currentPrice) * 0.9); // Default to 10% below current price
            
            // Show the modal
            setAlertModal.classList.remove('hidden');
        });
    });
    
    // Close modal when clicking outside
    setAlertModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeAlertModal();
        }
    });
});

// Function to close the Set Alert modal
function closeAlertModal() {
    document.getElementById('setAlertModal').classList.add('hidden');
}
</script>
</body>
</html>
