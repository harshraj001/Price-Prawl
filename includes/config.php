<?php
/**
 * PricePrawl - Configuration Settings
 */

// Database Configuration
$config = [
    // Database Settings
    'db_host' => 'localhost',
    'db_name' => 'priceprawl',
    'db_user' => 'root',
    'db_pass' => '',
    'db_charset' => 'utf8mb4',
    
    // SMTP Email Settings
    'smtp_host' => 'smtp.example.com',
    'smtp_port' => 587,
    'smtp_username' => 'your_email@example.com', // Replace with your actual email
    'smtp_password' => 'your_app_password', // Replace with your actual app password
    'from_email' => 'your_email@example.com', // Replace with your actual email
    
    // Application Settings
    'site_url' => 'http://localhost/dashboard',
    'site_name' => 'PricePrawl',
    
    // Security Settings
    'session_timeout' => 1800, // 30 minutes
    'cookie_lifetime' => 604800, // 1 week
    
    // Other settings
    'debug_mode' => true
];

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Error reporting based on debug mode
if ($config['debug_mode']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}