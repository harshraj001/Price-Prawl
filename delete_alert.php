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

// Get alert info before deleting for logging
$get_alert_stmt = $pdo->prepare("
    SELECT pa.*, wi.product_name, wi.current_price, wi.retailer 
    FROM price_alerts pa 
    JOIN wishlist_items wi ON pa.wishlist_item_id = wi.id 
    WHERE pa.id = ? AND pa.user_id = ?
");
$get_alert_stmt->execute([$alert_id, $user_id]);
$alert_info = $get_alert_stmt->fetch();

// Delete the price alert
$stmt = $pdo->prepare("DELETE FROM price_alerts WHERE id = ? AND user_id = ?");
$stmt->execute([$alert_id, $user_id]);

if ($stmt->rowCount() > 0) {
    // Log price alert deletion activity
    require_once 'includes/activity_logger.php';
    logAlertActivity($user_id, 'remove', [
        'alert_id' => $alert_id,
        'product_name' => $alert_info['product_name'] ?? 'Unknown product',
        'target_price' => $alert_info['target_price'] ?? 0,
        'current_price' => $alert_info['current_price'] ?? null,
        'retailer' => $alert_info['retailer'] ?? null
    ]);
    
    $_SESSION['alert_message'] = 'Price alert deleted successfully';
    $_SESSION['alert_success'] = true;
} else {
    $_SESSION['alert_message'] = 'Failed to delete price alert';
    $_SESSION['alert_success'] = false;
}

// Redirect back to wishlist page
header("Location: wishlist.php");
exit();
