<?php
/**
 * PricePrawl - Database Connection
 */

// Include configuration
require_once __DIR__ . '/config.php';

try {
    // Construct Data Source Name (DSN)
    $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";
    
    // Database connection options
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    // Create a PDO instance (connects to the database)
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
    
} catch (PDOException $e) {
    // Log error and display a user-friendly message
    error_log("Database Connection Error: " . $e->getMessage());
    
    if ($config['debug_mode']) {
        die("Database Connection Failed: " . $e->getMessage());
    } else {
        die("We're experiencing technical difficulties. Please try again later.");
    }
} 