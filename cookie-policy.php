<?php
session_start(); // Start session for user data and authentication
require_once('includes/db_connect.php'); // Include database connection

// Set page title
$pageTitle = "Cookie Policy";
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Include header with all styling and navigation
include 'includes/header.php';
?>

    <main class="container mx-auto px-4 sm:px-6 max-w-5xl lg:max-w-6xl py-12">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Cookie Policy</h1>
            
            <div class="prose prose-lg max-w-none">
                <p class="text-brand-text-secondary mb-6">Last updated: <?php echo date('F d, Y'); ?></p>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">1. What Are Cookies</h2>
                    <p class="text-brand-text-secondary mb-4">
                        Cookies are small text files that are placed on your computer or mobile device when you visit a website. They are widely used to make websites work more efficiently and provide a better user experience.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">2. How We Use Cookies</h2>
                    <p class="text-brand-text-secondary mb-4">
                        We use cookies for the following purposes:
                    </p>
                    <ul class="list-disc pl-6 text-brand-text-secondary space-y-2 mb-4">
                        <li>Essential cookies: Required for the website to function properly</li>
                        <li>Preference cookies: Remember your settings and preferences</li>
                        <li>Analytics cookies: Help us understand how visitors use our website</li>
                        <li>Marketing cookies: Track your visit across our website to help us deliver more relevant advertising</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">3. Types of Cookies We Use</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xl font-medium mb-2">Essential Cookies</h3>
                            <p class="text-brand-text-secondary">
                                These cookies are necessary for the website to function and cannot be switched off. They include cookies for login, security, and basic site functionality.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-xl font-medium mb-2">Analytics Cookies</h3>
                            <p class="text-brand-text-secondary">
                                We use analytics cookies to understand how visitors interact with our website. This helps us improve our services and user experience.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-xl font-medium mb-2">Preference Cookies</h3>
                            <p class="text-brand-text-secondary">
                                These cookies remember your preferences and settings to enhance your browsing experience.
                            </p>
                        </div>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">4. Managing Cookies</h2>
                    <p class="text-brand-text-secondary mb-4">
                        Most web browsers allow you to control cookies through their settings preferences. However, limiting cookies may impact your experience using our website. To learn more about cookies and how to manage them, visit <a href="https://www.aboutcookies.org/" class="text-brand-primary hover:text-brand-secondary" target="_blank" rel="noopener noreferrer">www.aboutcookies.org</a>.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">5. Third-Party Cookies</h2>
                    <p class="text-brand-text-secondary mb-4">
                        Some cookies are placed by third-party services that appear on our pages. We use trusted third-party services that track this information on our behalf. These third-party services have their own privacy policies and may collect information about you as described in their respective privacy policies.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">6. Updates to This Policy</h2>
                    <p class="text-brand-text-secondary mb-4">
                        We may update this Cookie Policy from time to time. Any changes will be posted on this page with an updated revision date.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">7. Contact Us</h2>
                    <p class="text-brand-text-secondary mb-4">
                        If you have any questions about our Cookie Policy, please contact us at:
                        <br>
                        Email: privacy@priceprawl.com
                    </p>
                </section>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 