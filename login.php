<?php
/**
 * PricePrawl - Login Page
 */
$pageTitle = "Login";
$currentPage = basename($_SERVER['SCRIPT_NAME']);
include 'includes/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 py-8 md:py-12 max-w-md">
    <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50">
        <div class="p-6 sm:p-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary text-center mb-8">Login to Your Account</h1>
            
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
                            case 'invalid_credentials':
                                echo 'Invalid email or password. Please try again.';
                                break;
                            case 'empty_fields':
                                echo 'Please fill in all required fields.';
                                break;
                            case 'email_not_verified':
                                echo 'Your email has not been verified. Please check your email for the verification code.';
                                break;
                            case 'database_error':
                                echo 'There was a database error. Please try again later.';
                                break;
                            case 'auth_required':
                                echo 'You need to be logged in to access that page. Please log in.';
                                break;
                            default:
                                echo 'An error occurred. Please try again.';
                        }
                        ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['message'])): ?>
            <div class="mb-6 p-3 bg-green-100 dark:bg-green-500/10 border border-green-300 dark:border-green-500/30 text-green-800 dark:text-green-300 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm">
                        <?php 
                        $message = htmlspecialchars($_GET['message']);
                        switch ($message) {
                            case 'registered':
                                echo 'Registration successful! Please login with your credentials.';
                                break;
                            case 'verification_email_sent':
                                echo 'A verification email has been sent. Please check your inbox.';
                                break;
                            case 'password_reset':
                                echo 'Your password has been reset successfully. Please login with your new password.';
                                break;
                            case 'logged_out':
                                echo 'You have been successfully logged out.';
                                break;
                            default:
                                echo 'Operation completed successfully.';
                        }
                        ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            
            <form action="login_process.php" method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-brand-text-secondary dark:text-dark-brand-text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" placeholder="Your email address" class="w-full pl-11 pr-4 py-3 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200" required>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary">Password</label>
                        <a href="forgot-password.php" class="text-xs text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover transition-colors">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-brand-text-secondary dark:text-dark-brand-text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="Your password" class="w-full pl-11 pr-4 py-3 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200" required>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-brand-accent dark:text-dark-brand-accent focus:ring-brand-accent dark:focus:ring-dark-brand-accent border-brand-border dark:border-dark-brand-border rounded">
                    <label for="remember" class="ml-2 block text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">
                        Remember me
                    </label>
                </div>
                
                <div>
                    <button type="submit" class="w-full py-3 px-4 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg font-semibold transition-colors duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header">
                        Sign In
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">
                    Don't have an account?
                    <a href="register.php" class="text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover font-medium ml-1 transition-colors">
                        Sign Up
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 