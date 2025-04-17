<?php
session_start(); // Start session for user data and authentication
require_once('includes/db_connect.php'); // Include database connection

// Set page title
$pageTitle = "Terms of Service";
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Include header with all styling and navigation
include 'includes/header.php';
?>

    <main class="container mx-auto px-4 sm:px-6 max-w-5xl lg:max-w-6xl py-12">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Terms of Service</h1>
            
            <div class="prose prose-lg max-w-none">
                <p class="text-brand-text-secondary mb-6">Last updated: <?php echo date('F d, Y'); ?></p>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">1. Acceptance of Terms</h2>
                    <p class="text-brand-text-secondary mb-4">
                        By accessing and using PricePrawl, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our service.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">2. Description of Service</h2>
                    <p class="text-brand-text-secondary mb-4">
                        PricePrawl is a price tracking service that monitors product prices across various e-commerce platforms. We provide price history, alerts, and deal recommendations to help users make informed purchasing decisions.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">3. User Responsibilities</h2>
                    <ul class="list-disc pl-6 text-brand-text-secondary space-y-2 mb-4">
                        <li>You must be at least 13 years old to use this service</li>
                        <li>You are responsible for maintaining the confidentiality of your account</li>
                        <li>You agree to provide accurate and complete information</li>
                        <li>You will not use the service for any illegal or unauthorized purpose</li>
                        <li>You will not interfere with or disrupt the service</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">4. Price Data and Accuracy</h2>
                    <p class="text-brand-text-secondary mb-4">
                        While we strive to provide accurate price information, we cannot guarantee the accuracy of all data. Prices are collected from various sources and may be subject to change without notice. Users should verify prices directly on retailer websites before making purchases.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">5. Intellectual Property</h2>
                    <p class="text-brand-text-secondary mb-4">
                        All content, features, and functionality of PricePrawl are owned by us and are protected by international copyright, trademark, and other intellectual property laws.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">6. Limitation of Liability</h2>
                    <p class="text-brand-text-secondary mb-4">
                        PricePrawl shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use or inability to use the service.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">7. Changes to Terms</h2>
                    <p class="text-brand-text-secondary mb-4">
                        We reserve the right to modify these terms at any time. We will notify users of any material changes by posting the new terms on this page.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">8. Contact Information</h2>
                    <p class="text-brand-text-secondary mb-4">
                        If you have any questions about these Terms of Service, please contact us at:
                        <br>
                        Email: support@priceprawl.com
                    </p>
                </section>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 