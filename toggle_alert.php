<?php
session_start();
require_once('includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if alert ID and new status are provided
if (!isset($_POST['alert_id']) || !isset($_POST['new_status'])) {
    $_SESSION['alert_message'] = 'Missing alert ID or status';
    $_SESSION['alert_success'] = false;
    header("Location: wishlist.php");
    exit();
}

$alert_id = intval($_POST['alert_id']);
$new_status = intval($_POST['new_status']) ? 1 : 0;

// Update the alert status
$stmt = $pdo->prepare("
    UPDATE price_alerts 
    SET is_active = ?, updated_at = NOW() 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$new_status, $alert_id, $user_id]);

if ($stmt->rowCount() > 0) {
    $status_text = $new_status ? 'activated' : 'paused';
    $_SESSION['alert_message'] = "Price alert {$status_text} successfully";
    $_SESSION['alert_success'] = true;
} else {
    $_SESSION['alert_message'] = 'Failed to update alert status';
    $_SESSION['alert_success'] = false;
}

// Redirect back to wishlist page
header("Location: wishlist.php");
exit();
