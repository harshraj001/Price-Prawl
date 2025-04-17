<?php
/**
 * PricePrawl Supported Sites Page
 * Lists all supported retailers organized by category
 */

session_start();
// Set page title
$pageTitle = "Supported Sites";

// Include header (contains theme config, base styles, header HTML)
include 'includes/header.php';
?>

<?php // The <main> tag and main container div are opened in header.php ?>

    <!-- Hero Section -->
    <section class="bg-brand-header dark:bg-dark-brand-header border-b border-brand-border/40 dark:border-dark-brand-border/50 mb-8 md:mb-12 py-10 md:py-12"> <?php // Adjusted padding and border ?>
        <div class="container mx-auto px-4 sm:px-6 text-center max-w-3xl">
            <h1 class="text-3xl sm:text-4xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-4">Supported Sites</h1>
            <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                Track prices across all major e-commerce platforms in India and beyond. We support a wide range of retailers to help you find the best deals everywhere you shop.
            </p>
        </div>
    </section>

    <!-- Retailer Sections -->
    <section id="retailers" class="container mx-auto px-4 sm:px-6 pb-12 md:pb-16 space-y-12">

        <!-- Major E-commerce Platforms -->
        <div>
            <h2 class="text-2xl font-semibold text-brand-text-primary dark:text-dark-brand-text-primary mb-6 pb-2 border-b border-brand-border/30 dark:border-dark-brand-border/50">Major E-commerce Platforms</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php // Card structure with updated styling and dark mode classes ?>
                <a href="https://www.amazon.in" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                             <?php // Using generic store icon for consistency, replace with specific logos if available ?>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h2.25m-2.25 0V8.25a.75.75 0 0 0-.75-.75h-5.25a.75.75 0 0 0-.75.75v12.75m0-12.75h-5.25a.75.75 0 0 0-.75.75v12.75m0 0H6m12 0a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v12A2.25 2.25 0 0 0 6.75 21H18Z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Amazon</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">India's largest online marketplace</p> <?php // Updated description ?>
                        </div>
                    </div>
                </a>
                 <a href="https://www.flipkart.com" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h2.25m-2.25 0V8.25a.75.75 0 0 0-.75-.75h-5.25a.75.75 0 0 0-.75.75v12.75m0-12.75h-5.25a.75.75 0 0 0-.75.75v12.75m0 0H6m12 0a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v12A2.25 2.25 0 0 0 6.75 21H18Z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Flipkart</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Leading Indian e-commerce platform</p>
                        </div>
                    </div>
                </a>
                <a href="https://www.myntra.com" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                     <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 11.25v2.25m6.75-2.25v2.25m2.25-2.25v2.25M6 21h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" /></svg> <?php // Fashion icon ?>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Myntra</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Top fashion & lifestyle destination</p>
                        </div>
                    </div>
                </a>
                 <a href="https://www.ajio.com" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                     <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 11.25v2.25m6.75-2.25v2.25m2.25-2.25v2.25M6 21h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">AJIO</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Reliance's curated fashion</p>
                        </div>
                    </div>
                </a>
                 <a href="https://www.tatacliq.com" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                     <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h2.25m-2.25 0V8.25a.75.75 0 0 0-.75-.75h-5.25a.75.75 0 0 0-.75.75v12.75m0-12.75h-5.25a.75.75 0 0 0-.75.75v12.75m0 0H6m12 0a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v12A2.25 2.25 0 0 0 6.75 21H18Z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Tata CLiQ</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Tata's curated marketplace</p>
                        </div>
                    </div>
                </a>
                 <a href="https://www.jiomart.com" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                     <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg> <?php // Grocery icon ?>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">JioMart</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Groceries and daily essentials</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Electronics Retailers -->
        <div>
            <h2 class="text-2xl font-semibold text-brand-text-primary dark:text-dark-brand-text-primary mb-6 pb-2 border-b border-brand-border/30 dark:border-dark-brand-border/50">Electronics Retailers</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <a href="https://www.croma.com" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" /></svg> <?php // Electronics icon ?>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Croma</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Tata's electronics chain</p>
                        </div>
                    </div>
                </a>
                <a href="https://www.reliancedigital.in" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center gap-4">
                         <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Reliance Digital</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Wide range of electronics</p>
                        </div>
                    </div>
                </a>
                 <?php // Add more electronics stores if needed ?>
            </div>
        </div>

        <!-- Fashion & Lifestyle -->
         <div>
            <h2 class="text-2xl font-semibold text-brand-text-primary dark:text-dark-brand-text-primary mb-6 pb-2 border-b border-brand-border/30 dark:border-dark-brand-border/50">Fashion & Lifestyle</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <a href="https://www.nykaa.com" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                     <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.9-2.25Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M14 4.5V3a2.25 2.25 0 0 0-2.25-2.25A4.5 4.5 0 0 0 7.5 5.25V6.75m4.5 9.75h.75a2.25 2.25 0 0 0 2.25-2.25v-1.5a2.25 2.25 0 0 0-2.25-2.25h-.75m-4.5 6.75h1.5a2.25 2.25 0 0 0 2.25-2.25v-1.5a2.25 2.25 0 0 0-2.25-2.25h-1.5m3 0a3 3 0 0 0-3-3V6.75A3 3 0 0 0 9 3.75m-.75 12.75h.008v.008H8.25v-.008Zm0 0c0 .023.002.046.002.07v.008h.007v-.008c0-.023.002-.046.002-.07Zm.003-4.5v.008h.007v-.008Zm-.75-1.5h.008v.008H7.5v-.008Zm.75 0h.008v.008H8.25v-.008Zm0 0v-.008h.007V9.75h-.007Zm1.5 1.5h.008v.008H9.75v-.008Zm-.75 0v.008h.007V11.25h-.007Zm0 0v.008h.007v-.008Zm0 0v-.008h.007V11.25h-.007Zm.75 1.5h.008v.008H10.5v-.008Zm-.75 0v-.008h.007V12.75h-.007Zm1.5 0h.008v.008H11.25v-.008Zm.75 0h.008v.008H12v-.008Zm.75 0h.008v.008H12.75v-.008Zm0 0h-.002-1.5Zm-1.5 1.5v.008h.007V14.25h-.007Zm0 0h.002l.001.002.001.002v.008h.007V14.25h-.007Zm.75-1.5h.008v.008H12.75v-.008Zm-.75 0h.008v.008H12v-.008Zm0 0h-.002-3.0Z" /></svg> <?php // Beauty/Cosmetics icon ?>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Nykaa</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Beauty and fashion destination</p>
                        </div>
                    </div>
                </a>
                <?php // Add other fashion stores like Clovia, ShoppersStop if desired, using appropriate icons ?>
            </div>
        </div>

        <!-- Specialty Stores -->
        <div>
            <h2 class="text-2xl font-semibold text-brand-text-primary dark:text-dark-brand-text-primary mb-6 pb-2 border-b border-brand-border/30 dark:border-dark-brand-border/50">Specialty Stores</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <a href="https://www.lenskart.com" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center gap-4">
                         <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg> <?php // Eyewear icon placeholder ?>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Lenskart</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Eyewear specialist</p>
                        </div>
                    </div>
                </a>
                <a href="https://www.pepperfry.com" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center gap-4">
                         <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg> <?php // Home/Furniture icon ?>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Pepperfry</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Furniture and home goods</p>
                        </div>
                    </div>
                </a>
                 <a href="https://www.decathlon.in" target="_blank" rel="noopener" class="group block bg-brand-header dark:bg-dark-brand-header border border-brand-border/40 dark:border-dark-brand-border/50 rounded-lg p-5 hover:shadow-lg dark:hover:shadow-dark-hover hover:border-brand-border/80 dark:hover:border-dark-brand-border/80 transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center gap-4">
                         <div class="w-12 h-12 bg-brand-surface-subtle dark:bg-dark-brand-surface-subtle rounded-full flex items-center justify-center flex-shrink-0">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-accent dark:text-dark-brand-accent"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" /></svg> <?php // Sports icon ?>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-brand-text-primary dark:text-dark-brand-text-primary group-hover:text-brand-accent dark:group-hover:text-dark-brand-accent mb-0.5 transition-colors">Decathlon</h3>
                            <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">Sports equipment and apparel</p> <?php // Slightly updated description ?>
                        </div>
                    </div>
                </a>
                 <?php // Add Snapdeal, Shopclues, Paytm Mall if still relevant ?>
            </div>
        </div>

        <!-- Add more categories/sections as needed -->

    </section>

<?php
// Include footer
include 'includes/footer.php';
?>