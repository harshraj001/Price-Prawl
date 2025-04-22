<?php
/**
 * PricePrawl - User Activity Page
 * Displays a comprehensive log of user activities
 */
session_start();
require_once('includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20; // Records per page
$offset = ($page - 1) * $per_page;

// Handle filtering
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$valid_filters = ['all', 'search', 'wishlist', 'alert', 'auth', 'profile'];
if (!in_array($filter, $valid_filters)) {
    $filter = 'all';
}

// Build the SQL query based on filter
$where_clause = "WHERE user_id = ?";
$params = [$user_id];

if ($filter !== 'all') {
    $where_clause .= " AND activity_type LIKE ?";
    $params[] = $filter . '%';
}

// Count total records for pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM user_activity $where_clause");
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

// Make sure page is within valid range
if ($page < 1) {
    $page = 1;
} elseif ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
}

// Get user activities with pagination
// A more reliable approach for named parameters
$named_params = [];
$named_where_clause = $where_clause;

// Replace each ? with a named parameter
for ($i = 0; $i < count($params); $i++) {
    $param_name = ":whereParam$i";
    // Only replace the first occurrence of ? each time
    $pos = strpos($named_where_clause, '?');
    if ($pos !== false) {
        $named_where_clause = substr_replace($named_where_clause, $param_name, $pos, 1);
    }
    $named_params[$param_name] = $params[$i];
}

$stmt = $pdo->prepare("
    SELECT * FROM user_activity 
    $named_where_clause
    ORDER BY timestamp DESC 
    LIMIT :offset, :per_page
");

// Bind pagination parameters
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);

// Bind WHERE parameters
foreach ($named_params as $param_name => $param_value) {
    $stmt->bindValue($param_name, $param_value);
}
$stmt->execute();
$activities = $stmt->fetchAll();

// Page setup
$pageTitle = "Activity History";
$currentPage = basename($_SERVER['SCRIPT_NAME']);
include 'includes/header.php';

/**
 * Helper function to format the activity description for display
 */
function formatActivityDescription($activity) {
    $description = htmlspecialchars($activity['description']);
    $type = $activity['activity_type'];
    $details = json_decode($activity['details'], true) ?: [];
    
    // Add icons based on activity type
    $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>';
    
    if (strpos($type, 'search') === 0) {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3 text-blue-500 dark:text-blue-400"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>';
    } elseif (strpos($type, 'wishlist') === 0) {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3 text-red-500 dark:text-red-400"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>';
    } elseif (strpos($type, 'alert') === 0) {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3 text-yellow-500 dark:text-yellow-400"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.311 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>';
    } elseif (strpos($type, 'auth') === 0) {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3 text-green-500 dark:text-green-400"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>';
    } elseif (strpos($type, 'profile') === 0) {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3 text-purple-500 dark:text-purple-400"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>';
    }
    
    // Add detailed info for specific activity types
    $additional_html = '';
    if (!empty($details)) {
        if (isset($details['product_name']) || isset($details['query'])) {
            $item_name = isset($details['product_name']) ? $details['product_name'] : $details['query'];
            $additional_html .= '<div class="mt-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">' . htmlspecialchars($item_name) . '</div>';
        }
        
        if (isset($details['url']) && filter_var($details['url'], FILTER_VALIDATE_URL)) {
            $domain = parse_url($details['url'], PHP_URL_HOST);
            $additional_html .= '<div class="mt-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">' . htmlspecialchars($domain) . '</div>';
        }
        
        // Include price information when available
        if (isset($details['price']) || (isset($details['current_price']) && isset($details['original_price']))) {
            $price_text = isset($details['price']) 
                ? 'Price: ' . number_format($details['price'], 2)
                : 'Price: ' . number_format($details['current_price'], 2) . ' (was ' . number_format($details['original_price'], 2) . ')';
            $additional_html .= '<div class="mt-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">' . $price_text . '</div>';
        }
    }
    
    // Return formatted activity with icon
    return '<div class="flex items-start">' . $icon . '<div><div class="text-brand-text-primary dark:text-dark-brand-text-primary">' . $description . '</div>' . $additional_html . '</div></div>';
}

/**
 * Helper function to format relative time
 */
function getRelativeTime($timestamp) {
    $time = strtotime($timestamp);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        return floor($diff / 60) . " minutes ago";
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . " hours ago";
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . " days ago";
    } elseif ($diff < 2592000) {
        return floor($diff / 604800) . " weeks ago";
    } else {
        return date("M j, Y", $time);
    }
}
?>

<div class="container mx-auto px-4 sm:px-6 py-8 max-w-5xl">
    <div class="flex flex-wrap items-center justify-between mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary">Activity History</h1>
        
        <a href="account.php" class="inline-flex items-center text-sm text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Account
        </a>
    </div>
    
    <!-- Filter Options -->
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="?filter=all" class="px-3 py-1.5 rounded-full text-sm <?= $filter === 'all' ? 'bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent' : 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-border/50 dark:hover:bg-dark-brand-border/40' ?>">
            All Activities
        </a>
        <a href="?filter=search" class="px-3 py-1.5 rounded-full text-sm <?= $filter === 'search' ? 'bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent' : 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-border/50 dark:hover:bg-dark-brand-border/40' ?>">
            Searches
        </a>
        <a href="?filter=wishlist" class="px-3 py-1.5 rounded-full text-sm <?= $filter === 'wishlist' ? 'bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent' : 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-border/50 dark:hover:bg-dark-brand-border/40' ?>">
            Wishlist
        </a>
        <a href="?filter=alert" class="px-3 py-1.5 rounded-full text-sm <?= $filter === 'alert' ? 'bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent' : 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-border/50 dark:hover:bg-dark-brand-border/40' ?>">
            Price Alerts
        </a>
        <a href="?filter=auth" class="px-3 py-1.5 rounded-full text-sm <?= $filter === 'auth' ? 'bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent' : 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-border/50 dark:hover:bg-dark-brand-border/40' ?>">
            Account Access
        </a>
        <a href="?filter=profile" class="px-3 py-1.5 rounded-full text-sm <?= $filter === 'profile' ? 'bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent' : 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-border/50 dark:hover:bg-dark-brand-border/40' ?>">
            Profile Updates
        </a>
    </div>
    
    <!-- Activity List Card -->
    <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 mb-6">
        <div class="p-6">
            <?php if (empty($activities)): ?>
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-brand-text-secondary dark:text-dark-brand-text-secondary/50 mb-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary">
                        No activities found for the selected filter.
                    </p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-brand-border/20 dark:divide-dark-brand-border/20">
                    <?php foreach ($activities as $activity): ?>
                    <li class="py-4 first:pt-0 last:pb-0">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                            <div class="flex-grow">
                                <?= formatActivityDescription($activity) ?>
                            </div>
                            <div class="text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary sm:text-right whitespace-nowrap">
                                <?= getRelativeTime($activity['timestamp']) ?>
                                <div class="text-xs opacity-70">
                                    <?= date('M j, Y - g:i a', strtotime($activity['timestamp'])) ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="flex justify-center mt-8">
        <div class="flex space-x-1">
            <?php if ($page > 1): ?>
            <a href="?page=1&filter=<?= $filter ?>" class="px-3 py-1 rounded border border-brand-border/50 dark:border-dark-brand-border/50 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle transition-colors">
                &laquo;
            </a>
            <a href="?page=<?= $page-1 ?>&filter=<?= $filter ?>" class="px-3 py-1 rounded border border-brand-border/50 dark:border-dark-brand-border/50 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle transition-colors">
                &lsaquo;
            </a>
            <?php endif; ?>
            
            <?php
            $range = 2;
            $show_dots = false;
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == 1 || $i == $total_pages || ($i >= $page - $range && $i <= $page + $range)) {
                    $active_class = $i == $page
                        ? 'bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent border-brand-accent dark:border-dark-brand-accent'
                        : 'text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle';
            ?>
                <a href="?page=<?= $i ?>&filter=<?= $filter ?>" class="px-3 py-1 rounded border border-brand-border/50 dark:border-dark-brand-border/50 <?= $active_class ?> transition-colors">
                    <?= $i ?>
                </a>
            <?php
                    $show_dots = true;
                } elseif ($show_dots) {
                    echo '<span class="px-3 py-1 text-brand-text-secondary dark:text-dark-brand-text-secondary">&hellip;</span>';
                    $show_dots = false;
                }
            }
            ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page+1 ?>&filter=<?= $filter ?>" class="px-3 py-1 rounded border border-brand-border/50 dark:border-dark-brand-border/50 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle transition-colors">
                &rsaquo;
            </a>
            <a href="?page=<?= $total_pages ?>&filter=<?= $filter ?>" class="px-3 py-1 rounded border border-brand-border/50 dark:border-dark-brand-border/50 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle transition-colors">
                &raquo;
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
