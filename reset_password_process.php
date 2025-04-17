<?php
/**
 * PricePrawl - Reset Password Process
 * This script handles password reset form submission
 */

// Start the session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if the necessary session variables exist
if (!isset($_SESSION['reset_token']) || !isset($_SESSION['reset_user_id'])) {
    header("Location: reset_password.php?error=invalid_token");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $token = $_SESSION['reset_token'];
    $userId = $_SESSION['reset_user_id'];
    
    // Validate inputs
    if (empty($password) || empty($confirmPassword)) {
        header("Location: reset_password.php?token=$token&error=empty_fields");
        exit();
    }
    
    // Validate password match
    if ($password !== $confirmPassword) {
        header("Location: reset_password.php?token=$token&error=passwords_mismatch");
        exit();
    }
    
    // Validate password strength (at least 8 characters with at least 1 number and 1 special character)
    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        header("Location: reset_password.php?token=$token&error=weak_password");
        exit();
    }
    
    try {
        // Verify token is valid and not expired
        $stmt = $pdo->prepare("
            SELECT id FROM password_reset_tokens 
            WHERE token = ? AND user_id = ? AND expires_at > NOW() 
            LIMIT 1
        ");
        $stmt->execute([$token, $userId]);
        
        if (!$stmt->fetch()) {
            // Token is invalid or expired
            header("Location: reset_password.php?error=expired_token");
            exit();
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Update the user's password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
        
        // Delete the used token
        $stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
        $stmt->execute([$token]);
        
        // Commit transaction
        $pdo->commit();
        
        // Clear session variables
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_user_id']);
        
        // Redirect to login page with success message
        header("Location: login.php?message=password_reset");
        exit();
        
    } catch (PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        
        error_log("Database error: " . $e->getMessage());
        header("Location: reset_password.php?token=$token&error=database_error");
        exit();
    }
} else {
    // If not POST request, redirect to reset password page
    header("Location: reset_password.php");
    exit();
} 