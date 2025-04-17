<?php
/**
 * PricePrawl Footer Template
 * Common footer for all pages
 */
?>
            </div> <!-- Closing container from header.php -->
        </main> <!-- Closing main from header.php -->

        <!-- Footer Section - Using consistent classes -->
        <footer class="bg-brand-header dark:bg-dark-brand-header border-t border-brand-border/40 dark:border-dark-brand-border/50 mt-16 md:mt-20 shadow-inner">
            <div class="container mx-auto px-4 sm:px-6 py-10 md:py-12 max-w-5xl lg:max-w-6xl">
                <!-- Main Footer Content -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <!-- About Section -->
                    <div>
                        <h3 class="text-brand-text-primary dark:text-dark-brand-text-primary font-semibold text-lg mb-4">About PricePrawl</h3>
                        <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary text-sm leading-relaxed">
                            PricePrawl helps you track prices across multiple e-commerce platforms, ensuring you never miss a deal.
                            Our advanced system monitors millions of products to bring you the best prices.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-brand-text-primary dark:text-dark-brand-text-primary font-semibold text-lg mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="trending.php" class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">Trending Deals</a></li>
                            <li><a href="price-drops.php" class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">Price Drops</a></li>
                            <li><a href="supported-sites.php" class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">Supported Sites</a></li>
                            <li><a href="wishlist.php" class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">My Wishlist</a></li>
                        </ul>
                    </div>

                    <!-- Supported Retailers -->
                    <div>
                         <h3 class="text-brand-text-primary dark:text-dark-brand-text-primary font-semibold text-lg mb-4">Top Retailers</h3>
                        <ul class="space-y-2">
                             <li><a href="supported-sites.php#amazon" class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">Amazon</a></li>
                            <li><a href="supported-sites.php#flipkart" class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">Flipkart</a></li>
                            <li><a href="supported-sites.php#myntra" class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">Myntra</a></li>
                             <li><a href="supported-sites.php" class="text-sm text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover font-medium transition-colors">View All Sites...</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="border-t border-brand-border/40 dark:border-dark-brand-border/50 pt-8 mt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-y-4 md:gap-y-0">
                        <!-- Copyright -->
                        <div class="text-brand-text-secondary dark:text-dark-brand-text-secondary text-xs sm:text-sm order-3 md:order-1">
                            © <?php echo date('Y'); ?> PricePrawl. All rights reserved.
                        </div>

                        <!-- Social Links -->
                        <div class="flex space-x-5 order-1 md:order-2">
                            <a href="#" class="text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent transition-colors" aria-label="Facebook">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                            </a>
                            <a href="#" class="text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent transition-colors" aria-label="Twitter">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                            </a>
                            <a href="#" class="text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent transition-colors" aria-label="Instagram">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/></svg>
                            </a>
                        </div>

                        <!-- Legal Links -->
                         <div class="flex flex-wrap justify-center md:justify-end gap-x-6 gap-y-2 order-2 md:order-3">
                            <a href="privacy-policy.php" class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">Privacy Policy</a>
                            <a href="terms-of-service.php" class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">Terms of Service</a>
                            <a href="cookie-policy.php" class="text-xs sm:text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary hover:text-brand-accent dark:hover:text-dark-brand-accent hover:underline transition-colors">Cookie Policy</a>
                        </div>

                    </div>
                </div>
            </div>
        </footer>

    </div> <!-- Closing min-h-screen -->

    <?php // Any closing body scripts would go here ?>

</body>
</html>