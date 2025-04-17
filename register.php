<?php
/**
 * PricePrawl - Registration Page
 */
$pageTitle = "Register";
$currentPage = basename($_SERVER['SCRIPT_NAME']);
include 'includes/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 py-8 md:py-12 max-w-md">
    <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50">
        <div class="p-6 sm:p-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary text-center mb-4">Create an Account</h1>
            <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary text-center mb-8">Join PricePrawl to track prices and get alerts when prices drop.</p>
            
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
                            case 'email_exists':
                                echo 'Email already registered. Please use a different email or login.';
                                break;
                            case 'password_mismatch':
                                echo 'Passwords do not match. Please try again.';
                                break;
                            case 'empty_fields':
                                echo 'Please fill in all required fields.';
                                break;
                            case 'invalid_email':
                                echo 'Please enter a valid email address.';
                                break;
                            case 'weak_password':
                                echo 'Password must be at least 8 characters and include a number and special character.';
                                break;
                            case 'terms_required':
                                echo 'You must accept the Terms of Service and Privacy Policy to register.';
                                break;
                            case 'email_error':
                                echo 'There was an error sending verification email. Please try again later.';
                                break;
                            case 'database_error':
                                echo 'There was a database error. Please try again later.';
                                break;
                            case 'session_expired':
                                echo 'Your session has expired. Please try registering again.';
                                break;
                            default:
                                echo 'An error occurred. Please try again.';
                        }
                        ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            
            <form action="register_process.php" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">First Name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="First name" class="w-full px-4 py-3 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200" required>
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Last Name</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Last name" class="w-full px-4 py-3 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200" required>
                    </div>
                </div>
                
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
                    <p class="mt-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">
                        We'll send a verification code to this email address
                    </p>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-brand-text-secondary dark:text-dark-brand-text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="Create a password" class="w-full pl-11 pr-4 py-3 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200" required>
                    </div>
                    <p class="mt-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary">
                        Must be at least 8 characters and include a number and special character
                    </p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-brand-text-secondary dark:text-dark-brand-text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" class="w-full pl-11 pr-4 py-3 rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary text-sm transition duration-200" required>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" id="terms" name="terms" class="h-4 w-4 text-brand-accent dark:text-dark-brand-accent focus:ring-brand-accent dark:focus:ring-dark-brand-accent border-brand-border dark:border-dark-brand-border rounded" required>
                    </div>
                    <label for="terms" class="ml-2 block text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">
                        I agree to the 
                        <a href="terms.php" class="text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover transition-colors">
                            Terms of Service
                        </a> and 
                        <a href="privacy.php" class="text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover transition-colors">
                            Privacy Policy
                        </a>
                    </label>
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg font-semibold transition-colors duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header">
                        Create Account
                    </button>
                    <p class="mt-3 text-xs text-center text-brand-text-secondary dark:text-dark-brand-text-secondary">
                        By clicking "Create Account", a verification code will be sent to your email
                    </p>
                </div>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">
                    Already have an account?
                    <a href="login.php" class="text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover font-medium ml-1 transition-colors">
                        Sign In
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 