<!DOCTYPE html>
<?php
session_start();
require_once('includes/db_connect.php');
?>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | PricePrawl' : 'PricePrawl - Smart Price Tracking'; ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font (Poppins) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Highcharts library (if needed on pages using this header) -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <!-- Theme Switcher -->
    <script src="./js/theme-switcher.js" defer></script>    <!-- Navigation Script for Hamburger -->
    <script src="./js/navigation.js" defer></script>
    
    <!-- Wishlist functionality -->
    <script src="./js/wishlist.js" defer></script>

    <!-- Dark Mode CSS (Link kept for structure, content should be minimal/empty) -->
    <link rel="stylesheet" href="css/dark-mode.css">    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
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
                    boxShadow: {
                        'dark-hover': '0 6px 15px rgba(193, 154, 107, 0.1)', 'card': '0 2px 5px rgba(0, 0, 0, 0.05)', 'dark-card': '0 2px 4px rgba(0, 0, 0, 0.2)',
                    },
                    animation: {
                        'marquee': 'marquee 60s linear infinite',
                    },
                    keyframes: {
                        'marquee': { '0%': { transform: 'translateX(0%)' }, '100%': { transform: 'translateX(-50%)' }, }
                    }
                }
            }
        }
    </script>

    <!-- Custom Styles -->
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        ::placeholder { color: #93785B; opacity: 0.7; } .dark ::placeholder { color: #A89A8B; opacity: 0.7; }
        ::-webkit-scrollbar { width: 8px; } ::-webkit-scrollbar-track { background: #DBCFBF; } ::-webkit-scrollbar-thumb { background-color: #AC8968; border-radius: 10px; border: 2px solid #DBCFBF; } ::-webkit-scrollbar-thumb:hover { background-color: #93785B; }
        .dark ::-webkit-scrollbar-track { background: #181818; } .dark ::-webkit-scrollbar-thumb { background-color: #4F4F4F; border: 2px solid #181818; } .dark ::-webkit-scrollbar-thumb:hover { background-color: #686868; }
        *:focus-visible { outline: 2px solid theme('colors.brand-accent'); outline-offset: 2px; border-radius: 3px; } .dark *:focus-visible { outline: 2px solid theme('colors.dark-brand-accent'); }
        .ticker-wrap { width: 100%; overflow: hidden; height: 40px; padding: 0; box-sizing: border-box; } .ticker-item { display: inline-flex; align-items: center; padding: 0 35px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; white-space: nowrap; height: 40px; flex-shrink: 0; }        /* Loader Style with Animation Fix */
        @keyframes spinner-rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loader { 
            border: 4px solid rgba(172, 137, 104, 0.2); 
            border-top: 4px solid #865D36; 
            border-radius: 50%; 
            width: 30px; 
            height: 30px; 
            animation-name: spinner-rotate;
            animation-duration: 1s;
            animation-timing-function: linear;
            animation-iteration-count: infinite;
            margin: 20px auto; 
        }
        .dark .loader { 
            border: 4px solid rgba(193, 154, 107, 0.3); 
            border-top: 4px solid #C19A6B; 
        }
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
            <div class="container mx-auto px-4 sm:px-6 py-3 flex justify-between items-center gap-4 max-w-5xl lg:max-w-6xl relative">
                <a href="index.php" class="flex-shrink-0 flex items-center text-2xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary hover:opacity-80 transition-opacity duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mr-2 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="hidden sm:inline">PricePrawl</span>
                </a>
                <div class="flex-grow max-w-xs md:max-w-sm"></div>
                <div class="flex items-center space-x-2 sm:space-x-3 md:space-x-4 flex-shrink-0">
                    <button id="theme-toggle" title="Toggle Theme" class="p-2 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:bg-brand-surface-subtle/80 dark:hover:bg-dark-brand-surface-subtle/80 transition-colors duration-200">
                        <span id="theme-icon"><!-- Icon --></span>
                    </button>                    <?php
                    // Get count of user's alerts if logged in
                    $alert_count = 0;
                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];
                        // Query to count both wishlist items and price alerts
                        $alert_stmt = $pdo->prepare("
                            SELECT 
                                (SELECT COUNT(*) FROM price_alerts WHERE user_id = ?) + 
                                (SELECT COUNT(*) FROM wishlist_items WHERE user_id = ?) as total_count
                        ");
                        $alert_stmt->execute([$user_id, $user_id]);
                        $alert_data = $alert_stmt->fetch();
                        $alert_count = $alert_data['total_count'] ?? 0;
                    }
                    ?>
                    <a href="wishlist.php" title="Alerts / Wishlist" class="p-2 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:bg-brand-surface-subtle/80 dark:hover:bg-dark-brand-surface-subtle/80 transition-colors duration-200 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="absolute -top-1 -right-1 bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"><?php echo $alert_count; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])): ?>
                    <!-- User is logged in - show initials with dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" title="Account" class="p-2 rounded-full bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover transition-colors duration-200 font-semibold w-8 h-8 flex items-center justify-center">
                            <?php echo substr($_SESSION['user_name'], 0, 1); ?>
                        </button>
                        
                        <!-- Dropdown menu -->
                        <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 overflow-hidden z-50">
                            <div class="py-1">
                                <a href="account.php" class="block px-4 py-2 text-sm text-brand-text-primary dark:text-dark-brand-text-primary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle">Account Settings</a>
                                <hr class="my-1 border-brand-border/40 dark:border-dark-brand-border/40">
                                <a href="logout.php" class="block px-4 py-2 text-sm text-brand-text-primary dark:text-dark-brand-text-primary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle">Log Out</a>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Default account button when not logged in -->
                    <a href="account.php" title="Account" class="p-2 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:bg-brand-surface-subtle/80 dark:hover:bg-dark-brand-surface-subtle/80 transition-colors duration-200">
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


             <!-- Mobile Navigation Menu -->
             <div id="mobile-menu" class="md:hidden hidden absolute top-full left-0 right-0 bg-brand-header dark:bg-dark-brand-header shadow-lg border-t border-brand-border/40 dark:border-dark-brand-border/50 z-40 transition-transform duration-300 ease-in-out transform origin-top scale-y-0">
                 <nav class="px-4 pt-2 pb-4 space-y-1">
                    <a href="trending.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent <?php echo ($currentPage == 'trending.php' || $currentPage == 'index.php') ? 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-accent dark:text-dark-accent font-semibold' : ''; ?>">Trending Deals</a>
                    <a href="price-drops.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent <?php echo ($currentPage == 'price-drops.php') ? 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-accent dark:text-dark-accent font-semibold' : ''; ?>">Price Drops</a>
                    <a href="supported-sites.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent <?php echo ($currentPage == 'supported-sites.php') ? 'bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle text-brand-accent dark:text-dark-accent font-semibold' : ''; ?>">Supported Sites</a>
                    <a href="wishlist.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent">Wishlist/Alerts</a>
                    <a href="account.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-text-secondary dark:text-dark-brand-text-secondary hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle hover:text-brand-accent dark:hover:text-dark-brand-accent">Account</a>
                 </nav>
            </div>
        </header>

        <!-- Ticker Banner (Keep as is) -->
        <div class="bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-dark dark:text-dark-brand-text-on-dark overflow-hidden shadow-inner">
            <div class="ticker-wrap"><div class="ticker animate-marquee flex"><span class="ticker-item">PRICE HISTORY CHARTS</span><span class="ticker-item">INSTANT PRICE DROP ALERTS</span><span class="ticker-item">TRACK MILLIONS OF PRODUCTS</span><span class="ticker-item">NEVER OVERPAY AGAIN</span><span class="ticker-item">PRICE HISTORY CHARTS</span><span class="ticker-item">INSTANT PRICE DROP ALERTS</span><span class="ticker-item">TRACK MILLIONS OF PRODUCTS</span><span class="ticker-item">NEVER OVERPAY AGAIN</span></div></div>
        </div>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col items-center px-4">
            <!-- Hero Section (Keep as is) -->            <section class="text-center max-w-4xl w-full min-h-screen flex flex-col justify-center items-center py-12 md:py-20">
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])): ?>
                <div class="mb-4">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-text-primary dark:text-dark-brand-text-primary tracking-tight">
                        Hello, <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-brand-accent-hover dark:from-dark-brand-accent dark:to-dark-brand-accent-hover"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </h2>
                </div>
                <?php endif; ?>
                <h1 class="hero-heading text-5xl sm:text-6xl lg:text-7xl font-extrabold mb-5 text-brand-text-primary dark:text-dark-brand-text-primary leading-tight tracking-tight">Find the <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-border via-brand-accent to-brand-accent-hover dark:from-dark-brand-border dark:via-dark-brand-accent dark:to-dark-brand-accent-hover">Lowest Price,</span><br /> Every Time.</h1>
                <p class="text-lg md:text-xl text-brand-text-secondary dark:text-dark-brand-text-secondary mb-8 max-w-2xl mx-auto leading-relaxed">Your ultimate tool for tracking price history and discovering the best deals across the web.</p>                <div class="max-w-2xl mx-auto w-full mb-10">
                     <form action="search.php" method="GET" class="relative flex shadow-lg dark:shadow-dark-card transform transition-all duration-300 hover:scale-[1.01] hover:shadow-xl dark:hover:shadow-lg"><span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-brand-text-secondary dark:text-dark-brand-text-secondary pointer-events-none z-10"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg></span><input type="text" name="query" placeholder="Enter product URL..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" class="w-full pl-12 pr-4 py-4 rounded-l-lg bg-brand-header dark:bg-dark-brand-header border-2 border-r-0 border-brand-border/70 dark:border-dark-brand-border/60 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-2 focus:ring-brand-accent/30 dark:focus:ring-dark-brand-accent/30 focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary transition duration-200 text-base" /><button type="submit" class="px-7 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-r-lg font-semibold transition-colors duration-200 flex-shrink-0 border-2 border-brand-accent dark:border-dark-brand-accent hover:border-brand-accent-hover dark:hover:border-dark-brand-accent-hover text-base hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header">Search</button></form>
                    
                    <?php if (isset($_GET['error'])): ?>
                        <?php if ($_GET['error'] == 'invalid_url'): ?>
                            <div class="mt-4 p-3 bg-red-100 dark:bg-red-500/10 border border-red-300 dark:border-red-500/30 text-red-800 dark:text-red-300 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm">Unable to process this URL. Please enter a valid product URL from a supported retailer.</span>
                                </div>
                            </div>
                        <?php elseif ($_GET['error'] == 'processing_failed'): ?>
                            <div class="mt-4 p-3 bg-red-100 dark:bg-red-500/10 border border-red-300 dark:border-red-500/30 text-red-800 dark:text-red-300 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm"><?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Something went wrong while processing the URL. Please try again later.'; ?></span>
                                </div>
                            </div>
                        <?php elseif ($_GET['error'] == 'empty_search'): ?>
                            <div class="mt-4 p-3 bg-red-100 dark:bg-red-500/10 border border-red-300 dark:border-red-500/30 text-red-800 dark:text-red-300 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm">Please enter a product URL to search.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary mb-6">Explore popular features:</p>
                    <div class="flex flex-wrap justify-center items-center gap-3 sm:gap-4"><a href="#trending" class="bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle hover:bg-brand-border/50 dark:hover:bg-dark-brand-border/40 border border-brand-border/50 dark:border-dark-brand-border/50 hover:border-brand-accent/70 dark:hover:border-dark-brand-accent/60 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-text-primary dark:hover:text-dark-brand-text-primary text-sm font-medium py-2.5 px-5 rounded-full transition duration-200 shadow-sm hover:shadow-md flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" /></svg>Price Charts</a><a href="#retailers" class="bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle hover:bg-brand-border/50 dark:hover:bg-dark-brand-border/40 border border-brand-border/50 dark:border-dark-brand-border/50 hover:border-brand-accent/70 dark:hover:border-dark-brand-accent/60 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-text-primary dark:hover:text-dark-brand-text-primary text-sm font-medium py-2.5 px-5 rounded-full transition duration-200 shadow-sm hover:shadow-md flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h2.25m-2.25 0V8.25a.75.75 0 0 0-.75-.75h-5.25a.75.75 0 0 0-.75.75v12.75m0-12.75h-5.25a.75.75 0 0 0-.75.75v12.75m0 0H6m12 0a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v12A2.25 2.25 0 0 0 6.75 21H18Z" /></svg> Top Retailers</a><a href="#price-drops" class="bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle hover:bg-brand-border/50 dark:hover:bg-dark-brand-border/40 border border-brand-border/50 dark:border-dark-brand-border/50 hover:border-brand-accent/70 dark:hover:border-dark-brand-accent/60 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-text-primary dark:hover:text-dark-brand-text-primary text-sm font-medium py-2.5 px-5 rounded-full transition duration-200 shadow-sm hover:shadow-md flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" /></svg> Deal Alerts</a></div>                </div>
            </section>

            <!-- Average Statistics Banner (Keep as is) -->
             <section class="w-full max-w-5xl mx-auto mb-16 md:mb-20">
                 <div class="bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle p-5 rounded-lg shadow-card dark:shadow-dark-card flex flex-wrap justify-around items-center gap-y-4 border border-brand-border/20 dark:border-dark-brand-border/30"><div class="text-center px-4 py-2"><div class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider font-medium">Today's Drops</div><div class="text-lg sm:text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mt-1">3,254</div></div><div class="text-center px-4 py-2 border-x border-brand-border/30 dark:border-dark-brand-border/40"><div class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider font-medium">Highest Drop</div><div class="text-lg sm:text-xl font-bold text-red-600 dark:text-red-400 mt-1">62%</div></div><div class="text-center px-4 py-2 border-r border-brand-border/30 dark:border-dark-brand-border/40"><div class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider font-medium">Average Drop</div><div class="text-lg sm:text-xl font-bold text-brand-accent dark:text-dark-brand-accent mt-1">18%</div></div><div class="text-center px-4 py-2"><div class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider font-medium">Last Updated</div><div class="text-lg sm:text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mt-1">5 mins ago</div></div></div>
            </section>

            <!-- Trending Products Section -->
            <section id="trending" class="w-full max-w-7xl mx-auto mb-16 md:mb-24 py-10">
                 <div class="flex justify-between items-center mb-8 px-2">
                    <div><h2 class="text-2xl md:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary">Trending Now</h2><p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mt-1 text-sm md:text-base">Products everyone is watching right now</p></div>
                    <a href="trending.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent text-sm font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-bg-light">View all<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg></a>
                </div>

                <!-- Trending Products Horizontal Scroll -->
                <!-- REMOVED group from this div -->
                <div class="relative">
                    <div id="trendingContainer" class="overflow-x-auto pb-4 hide-scrollbar">
                        <div id="trendingProducts" class="flex gap-5 min-w-max px-2">
                             <!-- Loader is shown initially, replaced by cards or error -->
                            <div id="trendingLoader" class="flex items-center justify-center w-full py-10">
                                <div class="loader"></div>
                            </div>
                        </div>
                    </div>
                     <!-- Scroll buttons: Added scroll-button class for JS targeting -->
                    <button id="scrollLeft" class="scroll-button absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 p-2.5 rounded-full bg-brand-header dark:bg-dark-brand-header border border-brand-border/60 dark:border-dark-brand-border/60 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:border-brand-accent dark:hover:border-dark-brand-accent shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-brand-accent dark:focus:ring-dark-brand-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>
                    <button id="scrollRight" class="scroll-button absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 p-2.5 rounded-full bg-brand-header dark:bg-dark-brand-header border border-brand-border/60 dark:border-dark-brand-border/60 text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:border-brand-accent dark:hover:border-dark-brand-accent shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-brand-accent dark:focus:ring-dark-brand-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </button>
                </div>
                <!-- Error State -->
                <div id="trendingError" class="hidden text-center text-brand-text-secondary dark:text-dark-brand-text-secondary py-10 px-2">
                    <p>Could not load trending products. Please refresh or try again later.</p>
                </div>
            </section>

            <!-- Price Drop Alerts Section -->
             <section id="price-drops" class="w-full max-w-7xl mx-auto mb-16 md:mb-24 py-10">
                 <div class="bg-gradient-to-br from-brand-header to-brand-bg-light/70 dark:from-dark-brand-header dark:to-dark-brand-bg-light/80 rounded-2xl overflow-hidden border border-brand-border/30 dark:border-dark-brand-border/40 shadow-lg dark:shadow-dark-card">
                    <div class="p-6 sm:p-8 md:p-10">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
                            <div><h2 class="text-2xl md:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary">Biggest Price Drops</h2><p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mt-2 text-sm md:text-base">Products with significant recent price reductions</p></div>
                            <a href="#" class="inline-flex items-center justify-center px-5 py-3 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent font-semibold rounded-lg transition-all duration-200 whitespace-nowrap shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header flex-shrink-0"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>Set Alert Preferences</a>
                        </div>

                        <!-- Price Drop Table Container -->
                        <div class="overflow-x-auto rounded-lg border border-brand-border/40 dark:border-dark-brand-border/50 shadow-inner bg-brand-header/70 dark:bg-dark-brand-header/70">
                            <table class="min-w-full divide-y divide-brand-border/40 dark:divide-dark-brand-border/50">
                                <thead class="bg-brand-bg-light/30 dark:bg-dark-brand-bg-light/40">
                                    <tr>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider">Product</th>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider">Store</th>
                                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider">Current</th>
                                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider">Previous</th>
                                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider">Drop</th>
                                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <!-- ADDED ID HERE, REMOVED STATIC ROWS -->
                                <tbody id="priceDropTableBody" class="divide-y divide-brand-border/40 dark:divide-dark-brand-border/50">
                                    <!-- Rows will be populated here by JavaScript -->
                                    <!-- Loader Row -->
                                    <tr id="priceDropLoader">
                                         <td colspan="6" class="text-center py-10">
                                            <div class="loader mx-auto"></div>
                                         </td>
                                    </tr>
                                    <!-- Error Row -->
                                    <tr id="priceDropError" class="hidden">
                                         <td colspan="6" class="text-center py-10 px-4 text-brand-text-secondary dark:text-dark-brand-text-secondary">
                                             Could not load price drop data. Please try again later.
                                         </td>
                                    </tr>
                                    <!-- No Data Row -->
                                    <tr id="priceDropNoData" class="hidden">
                                         <td colspan="6" class="text-center py-10 px-4 text-brand-text-secondary dark:text-dark-brand-text-secondary">
                                             No significant price drops found currently.
                                         </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8 text-center">
                             <a href="price-drops.php" class="text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover text-sm font-semibold inline-flex items-center group">See all price drops<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 ml-1.5 transform transition-transform duration-200 group-hover:translate-x-1"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg></a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Top Retailers Section (Keep as is) -->
             <section id="retailers" class="w-full max-w-7xl mx-auto mb-16 md:mb-24 px-2">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-8"><div><h2 class="text-2xl md:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary">Popular Retailers</h2><p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mt-1 text-sm md:text-base">Track prices across these major online stores</p></div><a href="supported-sites.php" class="mt-3 sm:mt-0 text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover text-sm font-semibold inline-flex items-center group self-start sm:self-center">View all supported sites<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 ml-1.5 transform transition-transform duration-200 group-hover:translate-x-1"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg></a></div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5"><a href="https://www.amazon.in" target="_blank" rel="noopener" class="group bg-brand-header dark:bg-dark-brand-header p-5 rounded-xl border border-brand-border/40 dark:border-dark-brand-border/50 flex flex-col items-center justify-center text-center transition duration-300 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 hover:-translate-y-1"><img src="https://compare.buyhatke.com/images/site_icons_m/amazon.png" alt="Amazon Logo" class="w-12 h-12 sm:w-16 sm:h-16 mb-4 object-contain transition-transform duration-300 group-hover:scale-105"><h3 class="text-sm sm:text-base font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Amazon</h3></a><a href="https://www.flipkart.com" target="_blank" rel="noopener" class="group bg-brand-header dark:bg-dark-brand-header p-5 rounded-xl border border-brand-border/40 dark:border-dark-brand-border/50 flex flex-col items-center justify-center text-center transition duration-300 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 hover:-translate-y-1"><img src="https://compare.buyhatke.com/images/site_icons_m/flipkart1.png" alt="Flipkart Logo" class="w-12 h-12 sm:w-16 sm:h-16 mb-4 object-contain transition-transform duration-300 group-hover:scale-105"><h3 class="text-sm sm:text-base font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Flipkart</h3></a><a href="https://www.myntra.com" target="_blank" rel="noopener" class="group bg-brand-header dark:bg-dark-brand-header p-5 rounded-xl border border-brand-border/40 dark:border-dark-brand-border/50 flex flex-col items-center justify-center text-center transition duration-300 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 hover:-translate-y-1"><img src="https://compare.buyhatke.com/images/site_icons_m/myntra.png" alt="Myntra Logo" class="w-12 h-12 sm:w-16 sm:h-16 mb-4 object-contain transition-transform duration-300 group-hover:scale-105"><h3 class="text-sm sm:text-base font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Myntra</h3></a><a href="https://www.ajio.com" target="_blank" rel="noopener" class="group bg-brand-header dark:bg-dark-brand-header p-5 rounded-xl border border-brand-border/40 dark:border-dark-brand-border/50 flex flex-col items-center justify-center text-center transition duration-300 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 hover:-translate-y-1"><img src="https://compare.buyhatke.com/images/site_icons_m/ajio.png" alt="Ajio Logo" class="w-12 h-12 sm:w-16 sm:h-16 mb-4 object-contain transition-transform duration-300 group-hover:scale-105"><h3 class="text-sm sm:text-base font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Ajio</h3></a><a href="https://www.croma.com" target="_blank" rel="noopener" class="group bg-brand-header dark:bg-dark-brand-header p-5 rounded-xl border border-brand-border/40 dark:border-dark-brand-border/50 flex flex-col items-center justify-center text-center transition duration-300 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 hover:-translate-y-1"><img src="https://compare.buyhatke.com/images/site_icons_m/croma.png" alt="Croma Logo" class="w-12 h-12 sm:w-16 sm:h-16 mb-4 object-contain transition-transform duration-300 group-hover:scale-105"><h3 class="text-sm sm:text-base font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Croma</h3></a><a href="https://www.tatacliq.com" target="_blank" rel="noopener" class="group bg-brand-header dark:bg-dark-brand-header p-5 rounded-xl border border-brand-border/40 dark:border-dark-brand-border/50 flex flex-col items-center justify-center text-center transition duration-300 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 hover:-translate-y-1"><img src="https://compare.buyhatke.com/images/site_icons_m/tatacliq.png" alt="Tatacliq Logo" class="w-12 h-12 sm:w-16 sm:h-16 mb-4 object-contain transition-transform duration-300 group-hover:scale-105"><h3 class="text-sm sm:text-base font-semibold text-brand-text-primary dark:text-dark-brand-text-primary">Tatacliq</h3></a></div>
            </section>

            <!-- How It Works Section (Keep as is) -->
             <section class="w-full max-w-7xl mx-auto mb-16 md:mb-20 px-2">
                <h2 class="text-2xl md:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary text-center mb-12">How PricePrawl Works</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8"><div class="text-center bg-brand-header dark:bg-dark-brand-header p-8 rounded-xl border border-brand-border/30 dark:border-dark-brand-border/50 shadow-card dark:shadow-dark-card transform transition duration-300 hover:scale-[1.02] hover:shadow-lg dark:hover:shadow-dark-hover"><div class="bg-brand-accent/10 dark:bg-dark-brand-accent/20 w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-6 border-2 border-brand-accent/20 dark:border-dark-brand-accent/30"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg></div><h3 class="text-xl font-semibold text-brand-text-primary dark:text-dark-brand-text-primary mb-3">1. Search Products</h3><p class="text-brand-text-secondary dark:text-dark-brand-text-secondary text-sm leading-relaxed">Enter a product URL or name from supported retailers to view its price history.</p></div><div class="text-center bg-brand-header dark:bg-dark-brand-header p-8 rounded-xl border border-brand-border/30 dark:border-dark-brand-border/50 shadow-card dark:shadow-dark-card transform transition duration-300 hover:scale-[1.02] hover:shadow-lg dark:hover:shadow-dark-hover"><div class="bg-brand-accent/10 dark:bg-dark-brand-accent/20 w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-6 border-2 border-brand-accent/20 dark:border-dark-brand-accent/30"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg></div><h3 class="text-xl font-semibold text-brand-text-primary dark:text-dark-brand-text-primary mb-3">2. Set Price Alerts</h3><p class="text-brand-text-secondary dark:text-dark-brand-text-secondary text-sm leading-relaxed">Set your desired price and get instantly notified via email when it drops.</p></div><div class="text-center bg-brand-header dark:bg-dark-brand-header p-8 rounded-xl border border-brand-border/30 dark:border-dark-brand-border/50 shadow-card dark:shadow-dark-card transform transition duration-300 hover:scale-[1.02] hover:shadow-lg dark:hover:shadow-dark-hover"><div class="bg-brand-accent/10 dark:bg-dark-brand-accent/20 w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-6 border-2 border-brand-accent/20 dark:border-dark-brand-accent/30"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V6.375m18 15v-1.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg></div><h3 class="text-xl font-semibold text-brand-text-primary dark:text-dark-brand-text-primary mb-3">3. Save Money</h3><p class="text-brand-text-secondary dark:text-dark-brand-text-secondary text-sm leading-relaxed">Use price history charts and alerts to buy products at their lowest price point.</p></div></div>
            </section>
        </main>

        <!-- Footer (Keep as is) -->
         <footer class="bg-brand-header dark:bg-dark-brand-header border-t border-brand-border/40 dark:border-dark-brand-border/50 mt-16 md:mt-20">
             <div class="container mx-auto px-4 sm:px-6 py-8 text-center text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">© <?php echo date("Y"); ?> PricePrawl. All rights reserved. | <a href="#" class="hover:text-brand-accent dark:hover:text-dark-brand-accent transition-colors hover:underline">Privacy Policy</a> | <a href="#" class="hover:text-brand-accent dark:hover:text-dark-brand-accent transition-colors hover:underline">Terms of Service</a></div>
        </footer>

    </div>

    <!-- Consolidated JavaScript -->
    <script>
        // Constants and Helpers
        const CORS_PROXY = 'https://cors-ninja.harshraj864869.workers.dev/proxy?url=';
        const BASE_API_PATH = 'https://buyhatke.com/price-history-deals';
        const API_SUFFIX = '/__data.json?x-sveltekit-invalidated=001';
        const API_URL = CORS_PROXY + BASE_API_PATH + API_SUFFIX;

        const formatPrice = (price) => {
            if (typeof price !== 'number') return 'N/A';
            return price.toLocaleString('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: 0, maximumFractionDigits: 0 });
        };

        const getRatingStars = (rating) => { // Keep this helper if needed elsewhere, otherwise remove
            if (typeof rating !== 'number' || rating < 0 || rating > 5) return '';
            rating = Math.max(0, Math.min(5, rating));
            const fullStars = Math.floor(rating);
            const halfStar = rating % 1 >= 0.4 ? 1 : 0;
            const emptyStars = 5 - fullStars - halfStar;
            return '★'.repeat(fullStars) + (halfStar ? '½' : '') + '☆'.repeat(emptyStars);
        };

        // --- Trending Products Logic ---
        (() => {
            const trendingContainer = document.getElementById('trendingContainer');
            const trendingProductsDiv = document.getElementById('trendingProducts');
            const trendingLoader = document.getElementById('trendingLoader');
            const trendingError = document.getElementById('trendingError');
            const scrollLeftBtn = document.getElementById('scrollLeft');
            const scrollRightBtn = document.getElementById('scrollRight');

            // ADDED group class to the outermost div here
            const createProductCard = (product) => {
                let discountPercentage = product.price_drop_per;
                 if (typeof discountPercentage !== 'number' || discountPercentage <= 0) {
                    if (typeof product.last_price === 'number' && typeof product.cur_price === 'number' && product.last_price > product.cur_price && product.last_price > 0) {
                        discountPercentage = Math.round(((product.last_price - product.cur_price) / product.last_price) * 100);
                    } else { discountPercentage = null; }
                }
                const ratingValue = typeof product.rating === 'number' ? product.rating : null;
                const ratingStars = ratingValue !== null ? getRatingStars(ratingValue) : '';
                const ratingCountText = (typeof product.ratingCount === 'number' && product.ratingCount > 0) ? `${product.ratingCount.toLocaleString('en-IN')} Ratings` : '';
                const hasRatingInfo = ratingStars && ratingCountText;

                // Added 'group' class to the outer div
                return `
                    <div class="w-64 md:w-72 flex-shrink-0 group">
                        <a href="${product.link || '#'}" target="_blank" rel="noopener noreferrer" class="block">
                            <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 transition duration-300 group-hover:shadow-lg dark:group-hover:shadow-dark-hover group-hover:border-brand-border/70 dark:group-hover:border-dark-brand-border/70 transform group-hover:-translate-y-1">
                                <div class="relative">                                    <img src="${product.image || 'https://placehold.co/300x200/FAF6F2/3E362E?text=No+Image'}" alt="${product.name || 'Product Image'}" class="w-full h-40 object-contain bg-white dark:bg-gray-100 p-1" onerror="this.onerror=null; this.src='https://placehold.co/300x200/FAF6F2/93785B?text=Error';" loading="lazy">
                                    ${discountPercentage !== null && discountPercentage > 0 ? `<span class="absolute top-2 left-2 bg-red-600 dark:bg-red-700 text-white text-[11px] font-bold px-2 py-0.5 rounded shadow-sm">-${discountPercentage}%</span>` : ''}                                    <button title="Add to Wishlist" 
                                           class="wishlist-btn absolute top-2 right-2 bg-brand-header/70 dark:bg-dark-brand-header/70 backdrop-blur-sm p-1.5 rounded-full text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-red-500 dark:hover:text-red-400 transition-colors duration-200 opacity-0 group-focus-within:opacity-100 group-hover:opacity-100 focus:opacity-100" 
                                           data-product-name="${product.name || 'Product'}" 
                                           data-product-url="${product.link || '#'}" 
                                           data-product-image="${product.image || ''}" 
                                           data-product-price="${product.cur_price || 0}" 
                                           data-product-original-price="${product.last_price || product.cur_price || 0}" 
                                           data-product-retailer="${product.site_name || 'Unknown'}"
                                           onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(this);">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center gap-1.5 mb-2"><img src="${product.site_logo || ''}" alt="${product.site_name || 'Store'}" class="w-4 h-4 rounded-sm object-contain flex-shrink-0 ${!product.site_logo ? 'hidden' : ''}"><span class="text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary truncate flex-grow">${product.site_name || 'Store'}</span></div>
                                    <h3 class="font-semibold text-sm text-brand-text-primary dark:text-dark-brand-text-primary mb-1 h-10 line-clamp-2 leading-tight" title="${product.name || 'N/A'}">${product.name || 'N/A'}</h3>
                                    ${hasRatingInfo ? `<div class="flex items-center gap-1.5 mb-2 h-4"><span class="text-xs text-yellow-500 flex-shrink-0">${ratingStars}</span><span class="text-[11px] text-brand-text-secondary dark:text-dark-brand-text-secondary truncate">${ratingCountText}</span></div>` : '<div class="h-4 mb-2"></div>'}
                                    <div class="flex items-baseline gap-2 mt-2"><span class="text-lg font-bold text-brand-accent dark:text-dark-brand-accent">${formatPrice(product.cur_price)}</span>${typeof product.last_price === 'number' && product.last_price > product.cur_price ? `<span class="text-xs line-through text-brand-text-secondary dark:text-dark-brand-text-secondary/80">${formatPrice(product.last_price)}</span>` : ''}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
            };

            const fetchTrendingProducts = async () => {
                trendingLoader.classList.remove('hidden');
                trendingError.classList.add('hidden');
                trendingProductsDiv.innerHTML = ''; // Clear previous or loader

                try {
                    const response = await fetch(API_URL); // Use constant
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const data = await response.json();
                    const dataNode = data?.nodes?.find(node => node?.type === 'data' && Array.isArray(node?.data) && node.data.length > 3);
                    if (!dataNode || !Array.isArray(dataNode.data)) throw new Error('Invalid data structure');

                    const products = [];
                    const rawProducts = dataNode.data.filter(item => typeof item === 'object' && item !== null && !Array.isArray(item) && item.link !== undefined);
                    rawProducts.forEach(item => {
                         const product = {};
                         for(const key in item) { const valueIndex = item[key]; product[key] = (typeof valueIndex === 'number' && valueIndex >= 0 && valueIndex < dataNode.data.length) ? dataNode.data[valueIndex] : item[key]; }
                         if (product.name && product.link && product.cur_price !== undefined) products.push(product);
                     });

                    if (products.length === 0) throw new Error('No valid products found');

                    products.slice(0, 12).forEach(product => { trendingProductsDiv.innerHTML += createProductCard(product); });
                    trendingLoader.classList.add('hidden'); // Hide loader only on success
                    updateScrollButtons(); // Update buttons after adding content

                } catch (error) {
                    console.error('Error fetching trending products:', error);
                    trendingError.classList.remove('hidden');
                    trendingLoader.classList.add('hidden'); // Hide loader on error too
                    trendingProductsDiv.innerHTML = ''; // Ensure clean state
                }
            };

            // Scroll button visibility logic
            const updateScrollButtons = () => {
                if (!trendingContainer || !scrollLeftBtn || !scrollRightBtn) return; // Ensure elements exist

                const tolerance = 10; // Pixels tolerance
                const canScrollLeft = trendingContainer.scrollLeft > tolerance;
                const canScrollRight = trendingContainer.scrollWidth > trendingContainer.clientWidth + trendingContainer.scrollLeft + tolerance;

                scrollLeftBtn.classList.toggle('is-visible', canScrollLeft);
                scrollRightBtn.classList.toggle('is-visible', canScrollRight);
            };

            // Event Listeners for Trending Section
            scrollLeftBtn?.addEventListener('click', () => trendingContainer?.scrollBy({ left: -350, behavior: 'smooth' }));
            scrollRightBtn?.addEventListener('click', () => trendingContainer?.scrollBy({ left: 350, behavior: 'smooth' }));
            trendingContainer?.addEventListener('scroll', updateScrollButtons, { passive: true });
            window.addEventListener('resize', updateScrollButtons);

            // Initial Load for Trending
            fetchTrendingProducts();
        })();


        // --- Price Drop Logic ---
        (() => {
            const priceDropTableBody = document.getElementById('priceDropTableBody');
            const priceDropLoader = document.getElementById('priceDropLoader');
            const priceDropError = document.getElementById('priceDropError');
            const priceDropNoData = document.getElementById('priceDropNoData');

            const createPriceDropRow = (product) => {
                let discountPercentage = null;
                if (typeof product.price_drop_per === 'number' && product.price_drop_per > 0) {
                    discountPercentage = product.price_drop_per;
                } else if (typeof product.last_price === 'number' && typeof product.cur_price === 'number' && product.last_price > product.cur_price && product.last_price > 0) {
                    discountPercentage = Math.round(((product.last_price - product.cur_price) / product.last_price) * 100);
                }

                // Only create a row if there's a valid positive discount
                if (discountPercentage === null || discountPercentage <= 0) {
                    return ''; // Don't render rows without a price drop
                }                return `
                    <tr class="hover:bg-brand-bg-light/40 dark:hover:bg-dark-brand-header/90 transition-colors duration-150">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 rounded bg-brand-bg-light dark:bg-dark-brand-bg-light/80 p-1 shadow-inner">
                                     <img class="h-full w-full rounded-sm object-contain"
                                          src="${product.image || 'https://placehold.co/100x100/FAF6F2/3E362E?text=N/A'}"
                                          alt="${product.name || 'Product'}"
                                          onerror="this.onerror=null; this.src='https://placehold.co/100x100/FAF6F2/93785B?text=Error';"
                                          loading="lazy">
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary line-clamp-2 leading-tight" title="${product.name || 'N/A'}">
                                        <a href="${product.link || '#'}" target="_blank" rel="noopener noreferrer" class="hover:underline">${product.name || 'N/A'}</a>
                                    </div>
                                     <!-- Optionally add category if available in data -->
                                    <!-- <div class="text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">Category</div> -->
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-brand-text-primary dark:text-dark-brand-text-primary">
                            ${product.site_name || 'N/A'}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold text-brand-accent dark:text-dark-brand-accent">
                            ${formatPrice(product.cur_price)}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary line-through">
                            ${(typeof product.last_price === 'number' && product.last_price > product.cur_price) ? formatPrice(product.last_price) : ''}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-500/20 text-red-800 dark:text-red-300">
                                -${discountPercentage}%
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                            <a href="${product.link || '#'}" target="_blank" rel="noopener noreferrer" class="font-semibold text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover hover:underline">
                                View Deal
                            </a>
                        </td>
                    </tr>
                `;
            };

            const fetchPriceDropProducts = async () => {
                if (!priceDropTableBody || !priceDropLoader || !priceDropError || !priceDropNoData) return; // Exit if elements are missing

                priceDropLoader.classList.remove('hidden');
                priceDropError.classList.add('hidden');
                priceDropNoData.classList.add('hidden');
                priceDropTableBody.innerHTML = ''; // Clear previous content (important!)
                 priceDropTableBody.appendChild(priceDropLoader); // Re-add loader while fetching


                try {
                    const response = await fetch(API_URL); // Use constant
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const data = await response.json();
                    const dataNode = data?.nodes?.find(node => node?.type === 'data' && Array.isArray(node?.data) && node.data.length > 3);
                     if (!dataNode || !Array.isArray(dataNode.data)) throw new Error('Invalid data structure');

                    const products = [];
                    const rawProducts = dataNode.data.filter(item => typeof item === 'object' && item !== null && !Array.isArray(item) && item.link !== undefined);
                     rawProducts.forEach(item => {
                         const product = {};
                         for(const key in item) { const valueIndex = item[key]; product[key] = (typeof valueIndex === 'number' && valueIndex >= 0 && valueIndex < dataNode.data.length) ? dataNode.data[valueIndex] : item[key]; }
                         if (product.name && product.link && product.cur_price !== undefined) products.push(product);
                     });

                    // Calculate discount percentage where needed and filter/sort
                    const productsWithDrop = products.map(p => {
                        let discount = null;
                        if (typeof p.price_drop_per === 'number' && p.price_drop_per > 0) {
                            discount = p.price_drop_per;
                        } else if (typeof p.last_price === 'number' && typeof p.cur_price === 'number' && p.last_price > p.cur_price && p.last_price > 0) {
                            discount = Math.round(((p.last_price - p.cur_price) / p.last_price) * 100);
                        }
                        return { ...p, calculated_drop: discount };
                    }).filter(p => p.calculated_drop !== null && p.calculated_drop > 0) // Only keep those with a drop > 0
                      .sort((a, b) => b.calculated_drop - a.calculated_drop); // Sort descending by drop %

                    priceDropTableBody.innerHTML = ''; // Clear loader before adding rows

                    if (productsWithDrop.length === 0) {
                        priceDropTableBody.appendChild(priceDropNoData);
                        priceDropNoData.classList.remove('hidden');
                    } else {
                        // Display top 5-10 price drops
                        productsWithDrop.slice(0, 8).forEach(product => {
                            priceDropTableBody.innerHTML += createPriceDropRow(product);
                        });
                    }

                } catch (error) {
                    console.error('Error fetching price drop products:', error);
                     priceDropTableBody.innerHTML = ''; // Clear loader on error
                    priceDropTableBody.appendChild(priceDropError); // Add error row
                    priceDropError.classList.remove('hidden');
                } finally {
                     // Ensure loader is always hidden at the end unless replaced by error/no data
                    priceDropLoader.remove(); // Remove loader element completely
                }
            };

             // Initial Load for Price Drops
            fetchPriceDropProducts();

        })();

    </script>

<!-- Wishlist Notification Toast -->
<div id="wishlist-toast" class="fixed bottom-4 right-4 bg-white dark:bg-gray-800 text-brand-text-primary dark:text-dark-brand-text-primary shadow-lg rounded-lg p-4 flex items-start max-w-xs z-50 transform translate-y-10 opacity-0 transition-all duration-300 invisible">
    <div id="toast-icon" class="mr-3 flex-shrink-0 text-green-500 dark:text-green-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    <div class="flex-1">
        <p id="toast-message" class="font-medium">Item added to wishlist</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" id="toast-detail">You can view your items in your wishlist</p>
    </div>
    <button onclick="hideToast()" class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<!-- Login Required Modal -->
<div id="login-modal" class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50 hidden">
    <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50 p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-brand-accent dark:text-dark-brand-accent">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-2">Login Required</h3>
            <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mb-6">Please log in to add items to your wishlist</p>
            
            <div class="flex justify-center space-x-3">
                <button onclick="closeLoginModal()" class="px-4 py-2 text-brand-text-primary dark:text-dark-brand-text-primary border border-brand-border dark:border-dark-brand-border rounded-lg hover:bg-brand-surface-subtle dark:hover:bg-dark-brand-surface-subtle">
                    Cancel
                </button>
                <a href="login.php" class="px-4 py-2 bg-brand-accent dark:bg-dark-brand-accent text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover">
                    Log In
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to add a product to the wishlist
    function addToWishlist(button) {
        // Check if user is logged in (we'll handle this on the server side too)
        <?php if (!isset($_SESSION['user_id'])): ?>
            // Show login modal if not logged in
            document.getElementById('login-modal').classList.remove('hidden');
            return;
        <?php endif; ?>
        
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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

</body>
</html>