<?php
session_start();
require_once('includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if item ID is provided
if (!isset($_POST['wishlist_item_id'])) {
    $_SESSION['wishlist_message'] = 'Missing wishlist item ID';
    $_SESSION['wishlist_success'] = false;
    header("Location: wishlist.php");
    exit();
}

$item_id = intval($_POST['wishlist_item_id']);

// First delete any associated price alerts
$delete_alerts = $pdo->prepare("DELETE FROM price_alerts WHERE wishlist_item_id = ? AND user_id = ?");
$delete_alerts->execute([$item_id, $user_id]);

// Then delete the wishlist item
$stmt = $pdo->prepare("DELETE FROM wishlist_items WHERE id = ? AND user_id = ?");
$stmt->execute([$item_id, $user_id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['wishlist_message'] = 'Product removed from your wishlist';
    $_SESSION['wishlist_success'] = true;
} else {
    $_SESSION['wishlist_message'] = 'Failed to remove product from wishlist';
    $_SESSION['wishlist_success'] = false;
}

// Redirect back to wishlist page
header("Location: wishlist.php");
exit();
