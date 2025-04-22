<?php
session_start();
require_once('includes/db_connect.php');
require_once('includes/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

// Handle different actions
switch ($action) {
    case 'add':
        // Add price alert
        if (isset($_POST['wishlist_item_id'], $_POST['target_price'])) {
            $wishlist_item_id = intval($_POST['wishlist_item_id']);
            $target_price = floatval($_POST['target_price']);
            
            // Validate target price
            if ($target_price <= 0) {
                $_SESSION['alert_message'] = 'Target price must be greater than zero';
                $_SESSION['alert_success'] = false;
                header("Location: wishlist.php");
                exit();
            }
            
            // Check if the item belongs to the user
            $check_stmt = $pdo->prepare("SELECT wi.*, u.email, u.first_name FROM wishlist_items wi JOIN users u ON wi.user_id = u.id WHERE wi.id = ? AND wi.user_id = ?");
            $check_stmt->execute([$wishlist_item_id, $user_id]);
            $item = $check_stmt->fetch();
            
            if (!$item) {
                $_SESSION['alert_message'] = 'Item not found or does not belong to you';
                $_SESSION['alert_success'] = false;
                header("Location: wishlist.php");
                exit();
            }
            
            // Check if alert already exists
            $check_alert_stmt = $pdo->prepare("SELECT id FROM price_alerts WHERE wishlist_item_id = ? AND user_id = ?");
            $check_alert_stmt->execute([$wishlist_item_id, $user_id]);
            
            if ($check_alert_stmt->rowCount() > 0) {
                // Update existing alert
                $update_stmt = $pdo->prepare("
                    UPDATE price_alerts 
                    SET target_price = ?, is_active = 1, updated_at = NOW() 
                    WHERE wishlist_item_id = ? AND user_id = ?
                ");
                $result = $update_stmt->execute([$target_price, $wishlist_item_id, $user_id]);
                  if ($result) {
                    // Send confirmation email
                    sendAlertConfirmationEmail($item, $target_price, true);
                    
                    // Log price alert update activity
                    require_once 'includes/activity_logger.php';
                    logAlertActivity($user_id, 'updated', [
                        'wishlist_item_id' => $wishlist_item_id,
                        'product_name' => $item['product_name'],
                        'target_price' => $target_price,
                        'current_price' => $item['current_price'] ?? null,
                        'retailer' => $item['retailer'] ?? null
                    ]);
                    
                    $_SESSION['alert_message'] = 'Price alert updated successfully';
                    $_SESSION['alert_success'] = true;
                } else {
                    $_SESSION['alert_message'] = 'Failed to update price alert';
                    $_SESSION['alert_success'] = false;
                }
            } else {
                // Create new alert
                $stmt = $pdo->prepare("
                    INSERT INTO price_alerts 
                    (user_id, wishlist_item_id, target_price, is_active, created_at, updated_at) 
                    VALUES (?, ?, ?, 1, NOW(), NOW())
                ");
                $result = $stmt->execute([
                    $user_id, 
                    $wishlist_item_id, 
                    $target_price
                ]);
                  if ($result) {
                    // Send confirmation email
                    sendAlertConfirmationEmail($item, $target_price, false);
                    
                    // Log price alert creation activity
                    require_once 'includes/activity_logger.php';
                    logAlertActivity($user_id, 'set', [
                        'wishlist_item_id' => $wishlist_item_id,
                        'product_name' => $item['product_name'],
                        'target_price' => $target_price,
                        'current_price' => $item['current_price'] ?? null,
                        'retailer' => $item['retailer'] ?? null
                    ]);
                    
                    $_SESSION['alert_message'] = 'Price alert set successfully';
                    $_SESSION['alert_success'] = true;
                } else {
                    $_SESSION['alert_message'] = 'Failed to set price alert';
                    $_SESSION['alert_success'] = false;
                }
            }
        } else {
            $_SESSION['alert_message'] = 'Missing required information';
            $_SESSION['alert_success'] = false;
        }
        break;
        
    case 'delete':
        // Remove price alert
        if (isset($_POST['alert_id'])) {
            $alert_id = intval($_POST['alert_id']);
            
            // Check if the alert belongs to the user
            $check_stmt = $pdo->prepare("SELECT id FROM price_alerts WHERE id = ? AND user_id = ?");
            $check_stmt->execute([$alert_id, $user_id]);
            
            if ($check_stmt->rowCount() > 0) {
                $stmt = $pdo->prepare("DELETE FROM price_alerts WHERE id = ? AND user_id = ?");
                $result = $stmt->execute([$alert_id, $user_id]);
                
                if ($result) {
                    $_SESSION['alert_message'] = 'Price alert removed successfully';
                    $_SESSION['alert_success'] = true;
                } else {
                    $_SESSION['alert_message'] = 'Failed to remove price alert';
                    $_SESSION['alert_success'] = false;
                }
            } else {
                $_SESSION['alert_message'] = 'Alert not found or does not belong to you';
                $_SESSION['alert_success'] = false;
            }
        } else {
            $_SESSION['alert_message'] = 'Missing alert ID';
            $_SESSION['alert_success'] = false;
        }
        break;
        
    default:
        $_SESSION['alert_message'] = 'Invalid action';
        $_SESSION['alert_success'] = false;
        break;
}

// Function to send email notification
function sendAlertConfirmationEmail($item, $targetPrice, $isUpdate) {
    global $config; // Access the config variable from the global scope
    $mail = new PHPMailer(true);
      try {
        // Server settings from config file
        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp_username'];
        $mail->Password   = $config['smtp_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['smtp_port'];
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['site_name']);
        $mail->addAddress($item['email'], $item['first_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $isUpdate ? 'Price Alert Updated - PricePrawl' : 'Price Alert Set - PricePrawl';
        
        // Format prices
        $currentPrice = '₹' . number_format($item['current_price'], 0);
        $targetPriceFormatted = '₹' . number_format($targetPrice, 0);
        $discountPercentage = round((($item['current_price'] - $targetPrice) / $item['current_price']) * 100);
        
        // Build email body
        $emailBody = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #865D36; color: #FAF6F2; padding: 15px; text-align: center; }
                .content { background-color: #FAF6F2; padding: 20px; border-radius: 0 0 5px 5px; }
                .product-info { display: flex; margin: 20px 0; }
                .product-image { width: 150px; height: 150px; object-fit: contain; margin-right: 20px; background-color: #fff; padding: 5px; border: 1px solid #ddd; }
                .product-details { flex: 1; }
                .price { font-size: 18px; font-weight: bold; margin: 10px 0; }
                .current-price { color: #865D36; }
                .target-price { color: #2E7D32; }
                .discount { display: inline-block; background-color: #f44336; color: white; padding: 2px 8px; border-radius: 12px; font-size: 14px; }
                .btn { display: inline-block; background-color: #865D36; color: #FAF6F2; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
                .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Price Alert ' . ($isUpdate ? 'Updated' : 'Set') . '</h1>
                </div>
                <div class="content">
                    <p>Hello ' . htmlspecialchars($item['first_name']) . ',</p>
                    <p>We\'ve ' . ($isUpdate ? 'updated your price alert' : 'set a new price alert') . ' for the following product:</p>
                    
                    <div class="product-info">
                        <img src="' . htmlspecialchars($item['product_image']) . '" alt="' . htmlspecialchars($item['product_name']) . '" class="product-image">
                        <div class="product-details">
                            <h3>' . htmlspecialchars($item['product_name']) . '</h3>
                            <p>From: ' . htmlspecialchars($item['retailer'] ?? 'Retailer') . '</p>
                            <div class="price current-price">Current Price: ' . $currentPrice . '</div>
                            <div class="price target-price">Target Price: ' . $targetPriceFormatted . ' <span class="discount">-' . $discountPercentage . '%</span></div>
                        </div>
                    </div>
                    
                    <p>We\'ll notify you as soon as the price drops to your target price or lower.</p>
                    
                    <a href="' . htmlspecialchars($item['product_url']) . '" class="btn">View Product</a>
                    <a href="http://localhost/dashboard/wishlist.php" class="btn" style="background-color: #555;">Manage Alerts</a>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' PricePrawl. All rights reserved.</p>
                    <p>If you didn\'t set this alert, please disregard this email.</p>
                </div>
            </div>
        </body>
        </html>';
        
        $mail->Body = $emailBody;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $emailBody));
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error but don't display to user
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Redirect back to wishlist page
header("Location: wishlist.php");
exit;
