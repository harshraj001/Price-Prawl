<?php
/**
 * Activity Logger
 * 
 * Helper functions to log user activities consistently across the application
 */

// Include database connection if not already included
if (!isset($pdo)) {
    require_once __DIR__ . '/db_connect.php';
}

/**
 * Log a user activity
 * 
 * @param int $user_id User ID
 * @param string $activity_type Type of activity (search, wishlist_add, alert_set, auth_login, etc.)
 * @param string $description Human-readable description of the activity
 * @param array $details Additional details to store as JSON (optional)
 * @return bool True if logging was successful, false otherwise
 */
function logUserActivity($user_id, $activity_type, $description, $details = []) {
    global $pdo;
    
    try {
        // Get IP address and user agent
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        // Prepare JSON for details
        $details_json = !empty($details) ? json_encode($details) : null;
        
        // Insert into activity log
        $stmt = $pdo->prepare("
            INSERT INTO user_activity (user_id, activity_type, description, details, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $user_id,
            $activity_type,
            $description,
            $details_json,
            $ip_address,
            $user_agent
        ]);
    } catch (Exception $e) {
        // Silent fail - don't interrupt the user experience if logging fails
        error_log('Error logging user activity: ' . $e->getMessage());
        return false;
    }
}

/**
 * Log a search activity
 * 
 * @param int $user_id User ID
 * @param string $query Search query or product URL
 * @param array $additional_details Additional details (optional)
 */
function logSearchActivity($user_id, $query, $additional_details = []) {
    $details = array_merge(['query' => $query], $additional_details);
    logUserActivity(
        $user_id,
        'search_query',
        'Searched for a product',
        $details
    );
}

/**
 * Log a wishlist activity
 * 
 * @param int $user_id User ID
 * @param string $action Action performed (add, remove)
 * @param array $product_data Product information
 */
function logWishlistActivity($user_id, $action, $product_data) {
    $action_text = ($action === 'add') ? 'Added product to wishlist' : 'Removed product from wishlist';
    logUserActivity(
        $user_id,
        'wishlist_' . $action,
        $action_text,
        $product_data
    );
}

/**
 * Log a price alert activity
 * 
 * @param int $user_id User ID
 * @param string $action Action performed (set, remove, triggered)
 * @param array $alert_data Alert information
 */
function logAlertActivity($user_id, $action, $alert_data) {
    $action_texts = [
        'set' => 'Set price alert for product',
        'remove' => 'Removed price alert',
        'triggered' => 'Price alert triggered',
        'updated' => 'Updated price alert settings'
    ];
    
    $action_text = $action_texts[$action] ?? 'Price alert action';
    
    logUserActivity(
        $user_id,
        'alert_' . $action,
        $action_text,
        $alert_data
    );
}

/**
 * Log an authentication activity
 * 
 * @param int $user_id User ID
 * @param string $action Action performed (login, logout, register, etc.)
 * @param array $additional_details Additional details (optional)
 */
function logAuthActivity($user_id, $action, $additional_details = []) {
    $action_texts = [
        'login' => 'Logged in',
        'logout' => 'Logged out',
        'register' => 'Created account',
        'password_reset' => 'Reset password',
        'password_change' => 'Changed password',
        'email_verify' => 'Verified email address'
    ];
    
    $action_text = $action_texts[$action] ?? 'Account action';
    
    logUserActivity(
        $user_id,
        'auth_' . $action,
        $action_text,
        $additional_details
    );
}

/**
 * Log a profile update activity
 * 
 * @param int $user_id User ID
 * @param array $updated_fields Fields that were updated
 */
function logProfileActivity($user_id, $updated_fields = []) {
    $description = 'Updated profile information';
    
    if (!empty($updated_fields)) {
        $field_names = array_keys($updated_fields);
        if (count($field_names) === 1) {
            $description = 'Updated ' . $field_names[0];
        } else {
            $description = 'Updated ' . implode(', ', $field_names);
        }
    }
    
    logUserActivity(
        $user_id,
        'profile_update',
        $description,
        ['updated_fields' => $updated_fields]
    );
}

/**
 * Log a product view activity
 * 
 * @param int $user_id User ID
 * @param array $product_data Product information
 */
function logProductViewActivity($user_id, $product_data) {
    logUserActivity(
        $user_id,
        'product_view',
        'Viewed product details',
        $product_data
    );
}
?>
