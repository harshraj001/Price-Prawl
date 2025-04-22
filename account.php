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
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get user's first and last name
$first_name = $user['first_name'] ?? '';
$last_name = $user['last_name'] ?? '';

// Count price alerts
$alert_stmt = $pdo->prepare("SELECT COUNT(*) as alert_count FROM price_alerts WHERE user_id = ?");
$alert_stmt->execute([$user_id]);
$alert_data = $alert_stmt->fetch();
$alert_count = $alert_data['alert_count'];

// Count wishlist items
$wishlist_stmt = $pdo->prepare("SELECT COUNT(*) as wishlist_count FROM wishlist_items WHERE user_id = ?");
$wishlist_stmt->execute([$user_id]);
$wishlist_data = $wishlist_stmt->fetch();
$wishlist_count = $wishlist_data['wishlist_count'];

// Get recent activity from user_activity table
$activity_stmt = $pdo->prepare("
    SELECT * FROM user_activity 
    WHERE user_id = ? 
    ORDER BY timestamp DESC 
    LIMIT 5
");
$activity_stmt->execute([$user_id]);
$activities = $activity_stmt->fetchAll();

// Handle success/error messages from update_profile.php
$profile_updated = isset($_GET['updated']) && $_GET['updated'] == '1';
$profile_error = isset($_GET['error']) ? $_GET['error'] : '';

/**
 * PricePrawl - Account Page
 */
$pageTitle = "My Account";
$currentPage = basename($_SERVER['SCRIPT_NAME']);
include 'includes/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 py-8 md:py-12 max-w-4xl">
    <h1 class="text-2xl sm:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-8">My Account</h1>
    
    <?php if (isset($_GET['message']) && $_GET['message'] == 'registration_complete'): ?>
    <div class="mb-6 p-4 bg-green-100 dark:bg-green-500/10 border border-green-300 dark:border-green-500/30 text-green-800 dark:text-green-300 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 flex-shrink-0 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-medium">Registration Successful!</p>
                <p class="text-sm mt-1">Your account has been created and you're now logged in. Welcome to PricePrawl!</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($profile_updated): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Your profile has been updated successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($profile_error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($profile_error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 sticky top-24">                <div class="p-6">
                    <div class="flex items-center mb-6">                        <div class="w-14 h-14 rounded-full bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent flex items-center justify-center text-2xl font-semibold" style="aspect-ratio: 1/1; min-width: 56px;">
                            <?php 
                            // Get user initials from first_name and last_name
                            $initials = '';
                            if (!empty($user['first_name'])) {
                                $initials .= strtoupper(substr($user['first_name'], 0, 1));
                            }
                            if (!empty($user['last_name'])) {
                                $initials .= strtoupper(substr($user['last_name'], 0, 1));
                            }
                            echo $initials; 
                            ?>
                        </div>
                        <div class="ml-4"><h2 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                            </h2>
                        </div>
                    </div>
                    
                    <nav class="space-y-1">
                        <a href="#profile" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-accent dark:text-dark-brand-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Profile
                        </a>
                        <a href="#preferences" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Alert Preferences
                        </a>
                        <a href="wishlist.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            Wishlist
                        </a>
                        <a href="#security" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            Security
                        </a>
                        <div class="border-t border-brand-border/30 dark:border-dark-brand-border/30 my-2 pt-2">
                            <a href="logout.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                Logout
                            </a>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="lg:col-span-2">            <!-- Profile Section -->
            <section id="profile" class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-6">Profile Information</h2>
                    
                    <form class="space-y-6" method="post" action="update_profile.php">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" class="w-full px-4 py-2.5 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200">
                            </div>
                            
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" class="w-full px-4 py-2.5 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200">
                            </div>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-4 py-2.5 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200">
                        </div>
                        
                        <div>
                            <button type="submit" class="px-5 py-2.5 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg font-medium transition-colors duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </section>
            
            <!-- Preferences Section -->
            <section id="preferences" class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-6">Alert Preferences</h2>
                    
                    <form class="space-y-6" method="post" action="update_preferences.php">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="email_alerts" class="rounded text-brand-accent focus:ring-brand-accent dark:focus:ring-dark-brand-accent border-brand-border/50 dark:border-dark-brand-border/50 bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30" checked>
                                <span class="ml-2 text-sm text-brand-text-primary dark:text-dark-brand-text-primary">Receive email notifications for price drops</span>
                            </label>
                        </div>
                        
                        <div>
                            <label for="price_drop_threshold" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Minimum price drop percentage to notify</label>
                            <div class="flex items-center">
                                <input type="number" id="price_drop_threshold" name="price_drop_threshold" min="1" max="90" value="10" class="w-24 px-4 py-2.5 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary text-sm transition duration-200">
                                <span class="ml-2 text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">%</span>
                            </div>
                            <p class="mt-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">You'll be notified when the price drops by this percentage or more.</p>
                        </div>
                        
                        <div>
                            <button type="submit" class="px-5 py-2.5 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg font-medium transition-colors duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header">
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </section>
            
            <!-- Security Section -->
            <section id="security" class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-6">Security Settings</h2>
                    
                    <form class="space-y-6" method="post" action="update_password.php">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2.5 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200">
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="w-full px-4 py-2.5 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200">
                            <p class="mt-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">Password must be at least 8 characters long and include uppercase, lowercase, and numbers.</p>
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2.5 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200">
                        </div>
                        
                        <div>
                            <button type="submit" class="px-5 py-2.5 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg font-medium transition-colors duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </section>
            
            <!-- Account Activity Summary -->
            <section class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-6">Account Summary</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-brand-surface-subtle/70 dark:bg-dark-brand-surface-subtle/70 rounded-lg p-4 border border-brand-border/20 dark:border-dark-brand-border/20">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-brand-accent/10 dark:bg-dark-brand-accent/20 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Items in Wishlist</p>
                                    <p class="text-2xl font-semibold text-brand-text-primary dark:text-dark-brand-text-primary"><?php echo $wishlist_count; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-brand-surface-subtle/70 dark:bg-dark-brand-surface-subtle/70 rounded-lg p-4 border border-brand-border/20 dark:border-dark-brand-border/20">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-brand-accent/10 dark:bg-dark-brand-accent/20 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Price Alerts</p>
                                    <p class="text-2xl font-semibold text-brand-text-primary dark:text-dark-brand-text-primary"><?php echo $alert_count; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-brand-border/20 dark:border-dark-brand-border/20 pt-6">
                        <h3 class="text-sm font-semibold text-brand-text-primary dark:text-dark-brand-text-primary mb-3">Recent Activity</h3>
                        
                        <div class="space-y-4">
                            <?php if (empty($activities)): ?>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">No recent activity found.</p>
                            <?php else: ?>
                                <?php foreach ($activities as $activity): ?>
                                <div class="flex items-start">
                                    <?php 
                                    $icon_class = 'text-brand-accent dark:text-dark-brand-accent';
                                    $bg_class = 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle';
                                    $icon_path = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />';
                                    
                                    // Different icon based on activity type
                                    if ($activity['activity_type'] == 'price_drop') {
                                        $icon_class = 'text-green-600 dark:text-green-400';
                                        $bg_class = 'bg-green-100 dark:bg-green-900/20';
                                        $icon_path = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />';
                                    } elseif ($activity['activity_type'] == 'alert_created') {
                                        $icon_path = '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />';
                                    }
                                    ?>
                                    <div class="p-1.5 rounded-full <?php echo $bg_class; ?> mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?php echo $icon_class; ?>">
                                            <?php echo $icon_path; ?>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-brand-text-primary dark:text-dark-brand-text-primary">
                                            <?php echo htmlspecialchars($activity['description']); ?>
                                        </p>
                                        <p class="text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary mt-1">
                                            <?php 
                                            $timestamp = strtotime($activity['timestamp']);
                                            $now = time();
                                            $diff = $now - $timestamp;
                                            
                                            if ($diff < 60) {
                                                echo "Just now";
                                            } elseif ($diff < 3600) {
                                                echo floor($diff / 60) . " minutes ago";
                                            } elseif ($diff < 86400) {
                                                echo floor($diff / 3600) . " hours ago";
                                            } elseif ($diff < 604800) {
                                                echo floor($diff / 86400) . " days ago";
                                            } elseif ($diff < 2592000) {
                                                echo floor($diff / 604800) . " weeks ago";
                                            } else {
                                                echo date("M j, Y", $timestamp);
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-6">
                            <a href="activity.php" class="text-sm text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover font-medium transition-colors">
                                View all activity
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 