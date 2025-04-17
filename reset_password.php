<?php
/**
 * PricePrawl - Reset Password Page
 */
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Verify token
$token = $_GET['token'] ?? '';
$validToken = false;
$userId = null;

if (!empty($token)) {
    try {
        // Check if token exists and is not expired
        $stmt = $pdo->prepare("
            SELECT user_id FROM password_reset_tokens 
            WHERE token = ? AND expires_at > NOW() 
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        
        if ($result) {
            $validToken = true;
            $userId = $result['user_id'];
            
            // Store token in session for the form submission
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_user_id'] = $userId;
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
}

$pageTitle = "Reset Password";
$currentPage = basename($_SERVER['SCRIPT_NAME']);
include 'includes/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 py-8 md:py-12 max-w-md">
    <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50">
        <div class="p-6 sm:p-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary text-center mb-8">Reset Your Password</h1>
            
            <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-3 bg-red-100 dark:bg-red-500/10 border border-red-300 dark:border-red-500/30 text-red-800 dark:text-red-300 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm">
                        <?php 
                        $error = htmlspecialchars($_GET['error']);
                        switch ($error) {
                            case 'passwords_mismatch':
                                echo 'Passwords do not match. Please try again.';
                                break;
                            case 'weak_password':
                                echo 'Password must be at least 8 characters and include a number and special character.';
                                break;
                            case 'expired_token':
                                echo 'The password reset link has expired. Please request a new one.';
                                break;
                            case 'invalid_token':
                                echo 'Invalid password reset link. Please request a new one.';
                                break;
                            default:
                                echo 'An error occurred. Please try again.';
                        }
                        ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!$validToken): ?>
            <div class="text-center py-6">
                <div class="mb-6 p-3 bg-red-100 dark:bg-red-500/10 border border-red-300 dark:border-red-500/30 text-red-800 dark:text-red-300 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm">The password reset link is invalid or has expired. Please request a new one.</span>
                    </div>
                </div>
                <a href="forgot-password.php" class="inline-flex justify-center items-center py-2 px-4 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg text-sm font-medium transition-colors duration-150 mt-4">
                    Request New Reset Link
                </a>
            </div>
            
            <?php else: ?>
            <form action="reset_password_process.php" method="POST" class="space-y-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">New Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-brand-text-secondary dark:text-dark-brand-text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="Create a new password" class="w-full pl-11 pr-4 py-3 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200" required>
                    </div>
                    <p class="mt-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">
                        Must be at least 8 characters and include a number and special character
                    </p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Confirm New Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-brand-text-secondary dark:text-dark-brand-text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" class="w-full pl-11 pr-4 py-3 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200" required>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="w-full py-3 px-4 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg font-semibold transition-colors duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header">
                        Reset Password
                    </button>
                </div>
            </form>
            <?php endif; ?>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">
                    <a href="login.php" class="text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover font-medium transition-colors">
                        Back to Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 