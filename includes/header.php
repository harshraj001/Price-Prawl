<?php
/**
 * PricePrawl
 * Common header for all pages
 */

// Include database connection
require_once(__DIR__ . '/db_connect.php');

// Determine the current page for active link styling (optional, example)
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Get user data if logged in
$userInitial = '';
$alertCount = 0;
$isLoggedIn = isset($_SESSION['user_id']);

if ($isLoggedIn) {
    // Get user's first name initial if available
    if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
        $userInitial = strtoupper(substr($_SESSION['user_name'], 0, 1));
    }
    
    // Get count of user's alerts and wishlist items
    $user_id = $_SESSION['user_id'];
    // Query to count both wishlist items and price alerts
    $alert_stmt = $pdo->prepare("
        SELECT 
            (SELECT COUNT(*) FROM price_alerts WHERE user_id = ?) + 
            (SELECT COUNT(*) FROM wishlist_items WHERE user_id = ?) as total_count
    ");
    $alert_stmt->execute([$user_id, $user_id]);
    $alert_data = $alert_stmt->fetch();
    $alertCount = $alert_data['total_count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | PricePrawl' : 'PricePrawl - Smart Price Tracking'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <link rel="icon" href="favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="manifest" href="site.webmanifest">
    <meta name="theme-color" content="#865D36">
    
    <!-- Critical CSS Inline for initial render -->
    <style>
        body { background-color: #181818; } /* Dark background during load */
    </style>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font (Poppins) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Loader animations -->
    <link rel="stylesheet" href="css/loader.css">

    <!-- Highcharts library (if needed on pages using this header) -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <!-- Theme Switcher -->
    <script src="./js/theme-switcher.js" defer></script>
    <!-- Navigation Script for Hamburger -->
    <script src="./js/navigation.js" defer></script>

    <!-- Dark Mode CSS (Link kept for structure, content should be minimal/empty) -->
    <link rel="stylesheet" href="css/dark-mode.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Tailwind Configuration - **Identical to finalized index.php** -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: { // Copied from final index.php version
                        'brand-bg-light': '#DBCFBF', 'dark-brand-bg-light': '#181818',
                        'brand-header': '#FAF6F2', 'dark-brand-header': '#242424',
                        'brand-surface-subtle': 'rgba(172, 137, 104, 0.3)', 'dark-brand-surface-subtle': 'rgba(193, 154, 107, 0.2)',
                        'brand-border': '#AC8968', 'dark-brand-border': '#5A5A5A',
                        'brand-border-strong': '#93785B', 'dark-brand-border-strong': '#C19A6B',
                        'brand-accent': '#865D36', 'dark-brand-accent': '#C19A6B',
                        'brand-accent-hover': '#6D4A2B', 'dark-brand-accent-hover': '#D4B08C',
                        'brand-text-primary': '#3E362E', 'dark-brand-text-primary': '#EAEAEA',
                        'brand-text-secondary': '#93785B', 'dark-brand-text-secondary': '#A89A8B',
                        'brand-text-on-dark': '#FAF6F2', 'dark-brand-text-on-dark': '#EAEAEA',
                        'brand-text-on-accent': '#FAF6F2', 'dark-brand-text-on-accent': '#161616',
                    },
                    boxShadow: { // Copied from final index.php version
                        'dark-hover': '0 6px 15px rgba(193, 154, 107, 0.1)', 'card': '0 2px 5px rgba(0, 0, 0, 0.05)', 'dark-card': '0 2px 4px rgba(0, 0, 0, 0.2)',
                    },
                    animation: { // Copied from final index.php version
                        'marquee': 'marquee 60s linear infinite',
                    },
                    keyframes: { // Copied from final index.php version
                        'marquee': { '0%': { transform: 'translateX(0%)' }, '100%': { transform: 'translateX(-50%)' }, }
                    }
                }
            }
        }
    </script>

    <!-- Custom Styles - **Base styles including loader fix** -->
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        ::placeholder { color: #93785B; opacity: 0.7; } .dark ::placeholder { color: #A89A8B; opacity: 0.7; }
        ::-webkit-scrollbar { width: 8px; } ::-webkit-scrollbar-track { background: #DBCFBF; } ::-webkit-scrollbar-thumb { background-color: #AC8968; border-radius: 10px; border: 2px solid #DBCFBF; } ::-webkit-scrollbar-thumb:hover { background-color: #93785B; }
        .dark ::-webkit-scrollbar-track { background: #181818; } .dark ::-webkit-scrollbar-thumb { background-color: #4F4F4F; border: 2px solid #181818; } .dark ::-webkit-scrollbar-thumb:hover { background-color: #686868; }
        *:focus-visible { outline: 2px solid theme('colors.brand-accent'); outline-offset: 2px; border-radius: 3px; } .dark *:focus-visible { outline: 2px solid theme('colors.dark-brand-accent'); }
        .ticker-wrap { width: 100%; overflow: hidden; height: 40px; padding: 0; box-sizing: border-box; } .ticker-item { display: inline-flex; align-items: center; padding: 0 35px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; white-space: nowrap; height: 40px; flex-shrink: 0; }
        /* Removed duplicated loader styling - now in loader.css */
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; } .hide-scrollbar::-webkit-scrollbar { display: none; }
        .dark .hero-heading { text-shadow: 1px 1px 3px rgba(0,0,0,0.3); }
        .scroll-button { opacity: 0; visibility: hidden; transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out; } .scroll-button.is-visible { opacity: 0.6; visibility: visible; } .scroll-button.is-visible:hover { opacity: 1; }
        /* Highcharts styling is handled via JS options */
    </style>
</head>
<body class="bg-brand-bg-light dark:bg-dark-brand-bg-light text-brand-text-primary dark:text-dark-brand-text-primary transition-colors duration-300">

    <div class="min-h-screen flex flex-col">
        <!-- Header Section -->
        <header class="sticky top-0 z-50 border-b border-brand-border/40 dark:border-dark-brand-border/50 bg-brand-header dark:bg-dark-brand-header shadow-sm">
            <!-- Main Header Row -->
            <div class="container mx-auto px-4 sm:px-6 py-3 flex justify-between items-center gap-4 max-w-5xl lg:max-w-6xl relative"> <?php // Added relative for mobile menu positioning ?>
                <a href="index.php" class="flex-shrink-0 flex items-center text-2xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary hover:opacity-80 transition-opacity duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mr-2 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="hidden sm:inline">PricePrawl</span>
                </a>
                <div class="flex-grow max-w-xs md:max-w-sm"></div>
                 <div class="flex items-center space-x-2 sm:space-x-3 md:space-x-4 flex-shrink-0">
                    <button id="theme-toggle" title="Toggle Theme" class="p-2 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:bg-brand-surface-subtle/80 dark:hover:bg-dark-brand-surface-subtle/80 transition-colors duration-200">
                        <span id="theme-icon"><!-- Icon --></span>
                    </button>
                    
                    <!-- Wishlist/Alerts button with notification counter -->
                    <a href="wishlist.php" title="Alerts / Wishlist" class="p-2 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:bg-brand-surface-subtle/80 dark:hover:bg-dark-brand-surface-subtle/80 transition-colors duration-200 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                        <?php if ($isLoggedIn): ?>
                        <span class="absolute -top-1 -right-1 bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"><?php echo $alertCount; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- User account button/dropdown -->
                    <?php if ($isLoggedIn): ?>
                    <div class="relative">
                        <div x-data="{ open: false }">
                            <!-- User initial button when logged in -->
                            <button @click="open = !open" @click.outside="open = false" title="Account" class="p-2 rounded-full bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover transition-colors duration-200 font-semibold w-8 h-8 flex items-center justify-center">
                                <?php echo $userInitial ?: 'U'; ?>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-brand-header dark:bg-dark-brand-header border border-brand-border/50 dark:border-dark-brand-border/50 ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="account.php" class="block px-4 py-2 text-sm text-brand-text-primary dark:text-dark-brand-text-primary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle">Account Settings</a>
                                    <hr class="my-1 border-brand-border/40 dark:border-dark-brand-border/40">
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-brand-text-primary dark:text-dark-brand-text-primary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle">Log Out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Default account button when not logged in - completely separated from Alpine.js -->
                    <a href="account.php" title="Account" class="account-link-no-dropdown p-2 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:bg-brand-surface-subtle/80 dark:hover:bg-dark-brand-surface-subtle/80 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    </a>
                    <?php endif; ?>
                    
                    <button id="hamburger-button" aria-label="Toggle Menu" aria-expanded="false" class="md:hidden p-2 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:bg-brand-surface-subtle/80 dark:hover:bg-dark-brand-surface-subtle/80 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                </div>
            </div>

            <!-- Secondary Navigation Row (Desktop) -->
            <nav class="container mx-auto px-4 sm:px-6 py-2 hidden md:flex justify-center items-center space-x-8 border-t border-brand-border/40 dark:border-dark-brand-border/50">
                <a href="trending.php" class="text-sm font-medium py-1 transition-colors duration-200 <?php echo ($currentPage == 'trending.php' || $currentPage == 'index.php') ? 'text-brand-accent dark:text-dark-brand-text-primary font-semibold' : 'text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-text-primary'; ?>">Trending Deals</a>
                <a href="price-drops.php" class="text-sm font-medium py-1 transition-colors duration-200 <?php echo ($currentPage == 'price-drops.php') ? 'text-brand-accent dark:text-dark-brand-text-primary font-semibold' : 'text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-text-primary'; ?>">Price Drops</a>
                <a href="supported-sites.php" class="text-sm font-medium py-1 transition-colors duration-200 <?php echo ($currentPage == 'supported-sites.php') ? 'text-brand-accent dark:text-dark-brand-text-primary font-semibold' : 'text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-text-primary'; ?>">Supported Sites</a>
            </nav>

            <!-- Search Bar (Small Screens) -->
            <div class="container mx-auto px-4 sm:px-6 py-3 md:hidden border-t border-brand-border/40 dark:border-dark-brand-border/50">
                 <form action="search.php" method="GET" class="relative">
                    <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-brand-text-secondary dark:text-dark-brand-text-secondary pointer-events-none"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg></span>
                    <input type="text" name="query_mobile" placeholder="Enter product URL..." class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-brand-bg-light/80 dark:bg-dark-brand-bg-light/80 border border-brand-border/60 dark:border-dark-brand-border/70 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200 shadow-inner"/>
                 </form>
            </div>

             <!-- Mobile Navigation Menu -->
             <div id="mobile-menu" class="md:hidden hidden absolute top-full left-0 right-0 bg-brand-header dark:bg-dark-brand-header shadow-lg border-t border-brand-border/40 dark:border-dark-brand-border/50 z-40 transition-transform duration-300 ease-in-out transform origin-top scale-y-0"> <?php // Animation classes can be added ?>
                 <nav class="px-4 pt-2 pb-4 space-y-1">
                    <a href="trending.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent <?php echo ($currentPage == 'trending.php' || $currentPage == 'index.php') ? 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-accent dark:text-dark-brand-accent font-semibold' : ''; ?>">Trending Deals</a>
                    <a href="price-drops.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent <?php echo ($currentPage == 'price-drops.php') ? 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-accent dark:text-dark-brand-accent font-semibold' : ''; ?>">Price Drops</a>
                    <a href="supported-sites.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent <?php echo ($currentPage == 'supported-sites.php') ? 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-accent dark:text-dark-brand-accent font-semibold' : ''; ?>">Supported Sites</a>
                    <a href="wishlist.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent">Wishlist/Alerts</a>
                    <a href="account.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent">Account</a>
                 </nav>
            </div>

        </header>
         <div class="bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-dark dark:text-dark-brand-text-on-dark overflow-hidden shadow-inner">
            <div class="ticker-wrap"><div class="ticker animate-marquee flex"><span class="ticker-item">PRICE HISTORY CHARTS</span><span class="ticker-item">INSTANT PRICE DROP ALERTS</span><span class="ticker-item">TRACK MILLIONS OF PRODUCTS</span><span class="ticker-item">NEVER OVERPAY AGAIN</span><span class="ticker-item">PRICE HISTORY CHARTS</span><span class="ticker-item">INSTANT PRICE DROP ALERTS</span><span class="ticker-item">TRACK MILLIONS OF PRODUCTS</span><span class="ticker-item">NEVER OVERPAY AGAIN</span></div></div>
        </div>
        <main class="flex-grow">
            <div class="container mx-auto px-4 sm:px-6 py-8 md:py-12 max-w-5xl lg:max-w-6xl">
            <?php // Page content starts here ?>