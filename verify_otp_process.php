<?php
/**
 * PricePrawl - OTP Verification Process
 * This script handles OTP verification and completes user registration
 */

// Start the session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if registration data exists in session
if (!isset($_SESSION['registration']) || empty($_SESSION['registration'])) {
    header("Location: register.php?error=session_expired");
    exit();
}

// Process verification form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $submittedOTP = trim($_POST['otp'] ?? '');
    
    // Validate OTP
    if (empty($submittedOTP)) {
        header("Location: verify_otp.php?error=empty_otp");
        exit();
    }
    
    // Get registration data from session
    $registrationData = $_SESSION['registration'];
    $storedOTP = $registrationData['otp'] ?? '';
    $otpExpiry = $registrationData['otp_expiry'] ?? '';
    $currentTime = date('Y-m-d H:i:s');
    
    // Check if OTP is expired
    if ($currentTime > $otpExpiry) {
        header("Location: verify_otp.php?error=expired_otp");
        exit();
    }
    
    // Verify OTP
    if ($submittedOTP !== $storedOTP) {
        header("Location: verify_otp.php?error=invalid_otp");
        exit();
    }
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert the new user into database
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password, created_at, email_verified)
            VALUES (?, ?, ?, ?, NOW(), 1)
        ");
        
        $stmt->execute([
            $registrationData['first_name'],
            $registrationData['last_name'],
            $registrationData['email'],
            $registrationData['password'] // Already hashed in register_process.php
        ]);
        
        $userId = $pdo->lastInsertId();
        
        // Create default user preferences
        $stmt = $pdo->prepare("
            INSERT INTO user_preferences (user_id, email_alerts, price_drop_threshold, created_at)
            VALUES (?, 1, 10, NOW())
        ");
        
        $stmt->execute([$userId]);
        
        // Commit transaction
        $pdo->commit();
        
        // Clear registration data from session
        unset($_SESSION['registration']);        // Set user as logged in
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $registrationData['email'];
        $_SESSION['user_name'] = $registrationData['first_name'];
        
        // Log user registration activity
        require_once 'includes/activity_logger.php';
        logAuthActivity($userId, 'register', [
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'registration_time' => date('Y-m-d H:i:s')
        ]);
        
        // Set welcome message and redirect to index page
        $_SESSION['welcome_message'] = "registration_complete";
        header("Location: index.php");
        exit();
        
    } catch (PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        
        error_log("Database error: " . $e->getMessage());
        header("Location: verify_otp.php?error=database_error");
        exit();
    }
} else {
    // If not POST request, redirect to OTP verification page
    header("Location: verify_otp.php");
    exit();
} 