<?php
session_start();
require_once('includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if alert ID is provided
if (!isset($_POST['alert_id'])) {
    $_SESSION['alert_message'] = 'Missing alert ID';
    $_SESSION['alert_success'] = false;
    header("Location: wishlist.php");
    exit();
}

$alert_id = intval($_POST['alert_id']);

// Delete the price alert
$stmt = $pdo->prepare("DELETE FROM price_alerts WHERE id = ? AND user_id = ?");
$stmt->execute([$alert_id, $user_id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['alert_message'] = 'Price alert deleted successfully';
    $_SESSION['alert_success'] = true;
} else {
    $_SESSION['alert_message'] = 'Failed to delete price alert';
    $_SESSION['alert_success'] = false;
}

// Redirect back to wishlist page
header("Location: wishlist.php");
exit();
