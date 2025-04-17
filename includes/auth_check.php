<?php
/**
 * PricePrawl - Authentication Check
 * Include this file at the top of any page that requires a logged-in user
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection for remember me token check
require_once __DIR__ . '/db_connect.php';

// Function to check if user is logged in
function isLoggedIn() {
    // Check if user id exists in session
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    
    // Check for remember me cookie
    if (isset($_COOKIE['remember_token']) && !empty($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        try {
            global $pdo;
            
            // Find remember token in database that's not expired
            $stmt = $pdo->prepare("
                SELECT user_id 
                FROM remember_tokens 
                WHERE token = ? AND expires_at > NOW() 
                LIMIT 1
            ");
            $stmt->execute([$token]);
            $result = $stmt->fetch();
            
            if ($result) {
                // Get user data
                $stmt = $pdo->prepare("
                    SELECT id, first_name, email
                    FROM users 
                    WHERE id = ?
                ");
                $stmt->execute([$result['user_id']]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'];
                    
                    // Update last login time
                    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    
                    // Refresh token and extend expiry
                    $newToken = bin2hex(random_bytes(32));
                    $expires = time() + (86400 * 30); // 30 days
                    
                    // Update token in database
                    $stmt = $pdo->prepare("
                        UPDATE remember_tokens 
                        SET token = ?, expires_at = ? 
                        WHERE token = ?
                    ");
                    $stmt->execute([$newToken, date('Y-m-d H:i:s', $expires), $token]);
                    
                    // Set new cookie
                    setcookie('remember_token', $newToken, $expires, '/', '', true, true);
                    
                    return true;
                }
            }
        } catch (PDOException $e) {
            error_log("Database error in auth check: " . $e->getMessage());
        }
    }
    
    return false;
}

// If not logged in, redirect to login page
if (!isLoggedIn()) {
    // Store the current URL to redirect back after login
    if (!empty($_SERVER['REQUEST_URI'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    }
    
    // Redirect to login page
    header("Location: " . dirname($_SERVER['PHP_SELF']) . "/login.php?error=auth_required");
    exit();
} 