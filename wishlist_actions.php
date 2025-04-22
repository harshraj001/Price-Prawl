<?php
session_start();
require_once('includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Return JSON response for AJAX requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please log in to manage your wishlist']);
        exit;
    }
    
    // Redirect to login page for normal requests
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

// Handle different actions
switch ($action) {
    case 'add':
        // Add item to wishlist
        if (isset($_POST['product_name'], $_POST['product_url'])) {
            $product_name = $_POST['product_name'];
            $product_url = $_POST['product_url'];
            $product_image = $_POST['product_image'] ?? '';
            $current_price = isset($_POST['current_price']) ? floatval($_POST['current_price']) : null;
            $original_price = isset($_POST['original_price']) ? floatval($_POST['original_price']) : null;
            $retailer = $_POST['retailer'] ?? '';
            
            // Check if the item already exists in the wishlist
            $check_stmt = $pdo->prepare("SELECT id FROM wishlist_items WHERE user_id = ? AND product_url = ?");
            $check_stmt->execute([$user_id, $product_url]);
            
            if ($check_stmt->rowCount() > 0) {
                $response = ['success' => false, 'message' => 'This item is already in your wishlist'];
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO wishlist_items 
                    (user_id, product_name, product_url, product_image, current_price, original_price, retailer, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $result = $stmt->execute([
                    $user_id, 
                    $product_name, 
                    $product_url, 
                    $product_image, 
                    $current_price, 
                    $original_price, 
                    $retailer
                ]);
                  if ($result) {
                    $item_id = $pdo->lastInsertId();
                    
                    // Log wishlist activity
                    require_once 'includes/activity_logger.php';
                    logWishlistActivity($user_id, 'add', [
                        'product_name' => $product_name,
                        'product_url' => $product_url,
                        'current_price' => $current_price,
                        'original_price' => $original_price,
                        'retailer' => $retailer,
                        'item_id' => $item_id
                    ]);
                    
                    $response = [
                        'success' => true, 
                        'message' => 'Product added to your wishlist',
                        'item_id' => $item_id
                    ];
                } else {
                    $response = ['success' => false, 'message' => 'Failed to add product to wishlist'];
                }
            }
        } else {
            $response = ['success' => false, 'message' => 'Missing required product information'];
        }
        break;
          case 'remove':
        // Remove item from wishlist
        if (isset($_POST['wishlist_item_id'])) {
            $item_id = intval($_POST['wishlist_item_id']);
            
            // First delete any associated price alerts
            $delete_alerts = $pdo->prepare("DELETE FROM price_alerts WHERE wishlist_item_id = ? AND user_id = ?");
            $delete_alerts->execute([$item_id, $user_id]);
            
            // Then delete the wishlist item
            $stmt = $pdo->prepare("DELETE FROM wishlist_items WHERE id = ? AND user_id = ?");
            $stmt->execute([$item_id, $user_id]);              if ($stmt->rowCount() > 0) {
                // Log wishlist removal activity
                require_once 'includes/activity_logger.php';
                
                // Get product details before we log the removal
                $product_info = [];
                
                if (isset($_POST['product_name'])) {
                    $product_info['product_name'] = $_POST['product_name'];
                }
                
                logWishlistActivity($user_id, 'remove', array_merge([
                    'item_id' => $item_id,
                    'removal_time' => date('Y-m-d H:i:s')
                ], $product_info));
                
                $response = ['success' => true, 'message' => 'Product removed from your wishlist'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to remove product from wishlist'];
            }
        } else if (isset($_POST['product_url'])) {
            // Remove by product URL (when removing from product page)
            $product_url = $_POST['product_url'];
            
            // Get the wishlist item ID first
            $get_id_stmt = $pdo->prepare("SELECT id, product_name FROM wishlist_items WHERE user_id = ? AND product_url = ?");
            $get_id_stmt->execute([$user_id, $product_url]);
            $wishlist_item = $get_id_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($wishlist_item) {
                $item_id = $wishlist_item['id'];
                
                // First delete any associated price alerts
                $delete_alerts = $pdo->prepare("DELETE FROM price_alerts WHERE wishlist_item_id = ? AND user_id = ?");
                $delete_alerts->execute([$item_id, $user_id]);
                
                // Then delete the wishlist item
                $stmt = $pdo->prepare("DELETE FROM wishlist_items WHERE id = ? AND user_id = ?");
                $stmt->execute([$item_id, $user_id]);
                
                if ($stmt->rowCount() > 0) {
                    // Log wishlist removal activity
                    require_once 'includes/activity_logger.php';
                      // Product info for logging
                    $product_info = [
                        'product_name' => $wishlist_item['product_name'] ?? 'Unknown Product'
                    ];
                    if (isset($_POST['product_name'])) {
                        $product_info['product_name'] = $_POST['product_name'];
                    }
                    
                    logWishlistActivity($user_id, 'remove', array_merge([
                        'item_id' => $item_id,
                        'removal_time' => date('Y-m-d H:i:s')
                    ], $product_info));
                    
                    $response = ['success' => true, 'message' => 'Product removed from your wishlist'];
                } else {
                    $response = ['success' => false, 'message' => 'Failed to remove product from wishlist'];
                }
            }
        } else {            $response = ['success' => false, 'message' => 'Missing wishlist item ID'];
        }
        break;
        
    case 'check':
        // Check if the item is in the user's wishlist
        if (isset($_POST['product_url'])) {
            $product_url = $_POST['product_url'];
            
            $stmt = $pdo->prepare("SELECT id FROM wishlist_items WHERE user_id = ? AND product_url = ?");
            $stmt->execute([$user_id, $product_url]);
            
            if ($stmt->rowCount() > 0) {
                $response = ['success' => true, 'in_wishlist' => true, 'message' => 'Item is in wishlist'];
            } else {
                $response = ['success' => true, 'in_wishlist' => false, 'message' => 'Item is not in wishlist'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Missing product URL'];
        }
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
        break;
}

// Handle the response based on request type
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    // AJAX request - return JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Normal form submission - set message in session and redirect
    $_SESSION['wishlist_message'] = $response['message'];
    $_SESSION['wishlist_success'] = $response['success'];
    
    // Redirect back to referring page or wishlist page
    $redirect_url = $_SERVER['HTTP_REFERER'] ?? 'wishlist.php';
    header("Location: $redirect_url");
}
exit;
