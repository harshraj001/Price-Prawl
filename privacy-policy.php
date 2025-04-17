<?php
session_start(); // Start session for user data and authentication
require_once('includes/db_connect.php'); // Include database connection

// Set page title
$pageTitle = "Privacy Policy";
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Include header with all styling and navigation
include 'includes/header.php';
?>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-brand-text-primary mb-2">Privacy Policy</h1>
                <p class="text-brand-text-secondary">Last updated: <?php echo date('F d, Y'); ?></p>
            </div>

            <!-- Privacy Policy Content -->
            <div class="bg-brand-header rounded-xl p-6 md:p-8 shadow-md border border-brand-border/30">
                <section class="mb-8">
                    <h2 class="text-xl font-bold text-brand-text-primary mb-4">1. Introduction</h2>
                    <p class="text-brand-text-secondary mb-4">
                        Welcome to PricePrawl. We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you about how we look after your personal data when you visit our website and tell you about your privacy rights and how the law protects you.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-bold text-brand-text-primary mb-4">2. Data We Collect</h2>
                    <p class="text-brand-text-secondary mb-4">
                        We collect and process the following types of personal data:
                    </p>
                    <ul class="list-disc list-inside text-brand-text-secondary space-y-2 ml-4">
                        <li>Account information (email, name, password)</li>
                        <li>Product tracking preferences</li>
                        <li>Price alert settings</li>
                        <li>Browser and device information</li>
                        <li>Usage data and analytics</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-bold text-brand-text-primary mb-4">3. How We Use Your Data</h2>
                    <p class="text-brand-text-secondary mb-4">
                        We use your personal data for the following purposes:
                    </p>
                    <ul class="list-disc list-inside text-brand-text-secondary space-y-2 ml-4">
                        <li>To provide and maintain our service</li>
                        <li>To notify you about price changes and deals</li>
                        <li>To provide customer support</li>
                        <li>To gather analysis or valuable information to improve our service</li>
                        <li>To monitor the usage of our service</li>
                        <li>To detect, prevent and address technical issues</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-bold text-brand-text-primary mb-4">4. Data Security</h2>
                    <p class="text-brand-text-secondary mb-4">
                        We have implemented appropriate security measures to prevent your personal data from being accidentally lost, used, or accessed in an unauthorized way, altered, or disclosed. We limit access to your personal data to those employees, agents, contractors, and other third parties who have a business need to know.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-bold text-brand-text-primary mb-4">5. Your Rights</h2>
                    <p class="text-brand-text-secondary mb-4">
                        Under certain circumstances, you have rights under data protection laws in relation to your personal data, including the right to:
                    </p>
                    <ul class="list-disc list-inside text-brand-text-secondary space-y-2 ml-4">
                        <li>Request access to your personal data</li>
                        <li>Request correction of your personal data</li>
                        <li>Request erasure of your personal data</li>
                        <li>Object to processing of your personal data</li>
                        <li>Request restriction of processing your personal data</li>
                        <li>Request transfer of your personal data</li>
                        <li>Right to withdraw consent</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-bold text-brand-text-primary mb-4">6. Contact Us</h2>
                    <p class="text-brand-text-secondary mb-4">
                        If you have any questions about this privacy policy or our privacy practices, please contact us at:
                    </p>
                    <p class="text-brand-text-secondary">
                        Email: privacy@priceprawl.com<br>
                        Address: [Your Business Address]
                    </p>
                </section>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 