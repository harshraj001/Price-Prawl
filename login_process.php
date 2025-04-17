<?php
/**
 * PricePrawl - Login Process
 * This script handles user login
 */

// Start the session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=empty_fields");
        exit();
    }
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("
            SELECT id, first_name, email, password, email_verified 
            FROM users 
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // User not found
            header("Location: login.php?error=invalid_credentials");
            exit();
        }
        
        // Check if email is verified
        if (!$user['email_verified']) {
            // Email not verified
            header("Location: login.php?error=email_not_verified");
            exit();
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            // Invalid password
            header("Location: login.php?error=invalid_credentials");
            exit();
        }
        
        // Update last login time
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'];
        
        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + (86400 * 30); // 30 days
            
            // Store token in database
            $stmt = $pdo->prepare("
                INSERT INTO remember_tokens (user_id, token, expires_at) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$user['id'], $token, date('Y-m-d H:i:s', $expires)]);
            
            // Set cookie
            setcookie('remember_token', $token, $expires, '/', '', true, true);
        }
        
        // Check if there's a redirect URL in session
        if (isset($_SESSION['redirect_after_login'])) {
            $redirectUrl = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirectUrl");
            exit();
        }
        
        // Redirect to account page
        header("Location: account.php");
        exit();
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header("Location: login.php?error=database_error");
        exit();
    }
} else {
    // If not POST request, redirect to login page
    header("Location: login.php");
    exit();
} 