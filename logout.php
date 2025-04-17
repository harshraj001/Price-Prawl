<?php
/**
 * PricePrawl - Logout
 * This script handles user logout
 */

// Start the session
session_start();

// Include database connection for remember token cleanup
require_once 'includes/db_connect.php';

// Check if remember token cookie exists
if (isset($_COOKIE['remember_token'])) {
    // Delete token from database
    try {
        $token = $_COOKIE['remember_token'];
        $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE token = ?");
        $stmt->execute([$token]);
    } catch (PDOException $e) {
        // Log the error but continue with logout
        error_log("Error deleting remember token: " . $e->getMessage());
    }
    
    // Delete the cookie by setting expiration in the past
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// Clear all session variables
$_SESSION = array();

// If session cookie exists, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php?message=logged_out");
exit(); 