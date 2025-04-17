<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = false;
$error = '';

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Basic validation
    if (empty($first_name) || empty($last_name)) {
        $error = "First name and last name are required.";
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "A valid email is required.";
    } else {
        // Check if email already exists for different user
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email address is already in use by another account.";
        } else {
            // Format full name
            $full_name = $first_name . ' ' . $last_name;
            
            // Update user profile
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $full_name, $email, $user_id);
            
            if ($stmt->execute()) {
                // Log the activity
                $description = "Updated profile information";
                $activity_type = "profile_update";
                
                $stmt = $conn->prepare("INSERT INTO user_activity (user_id, activity_type, description) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $activity_type, $description);
                $stmt->execute();
                
                $success = true;
                
                // Update session variables if needed
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_email'] = $email;
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
    }
}

// Redirect back to account page with status
$redirect_url = 'account.php';
if ($success) {
    $redirect_url .= '?updated=1';
} elseif (!empty($error)) {
    $redirect_url .= '?error=' . urlencode($error);
}

header("Location: " . $redirect_url);
exit; 