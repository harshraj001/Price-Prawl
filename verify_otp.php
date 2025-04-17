<?php
/**
 * PricePrawl - OTP Verification Page
 */
session_start();

// Check if registration data exists in session
if (!isset($_SESSION['registration']) || empty($_SESSION['registration'])) {
    header("Location: register.php?error=session_expired");
    exit();
}

$pageTitle = "Verify Your Email";
$currentPage = basename($_SERVER['SCRIPT_NAME']);
include 'includes/header.php';

// Extract data from session
$email = $_SESSION['registration']['email'] ?? '';
$firstName = $_SESSION['registration']['first_name'] ?? '';
?>

<div class="container mx-auto px-4 sm:px-6 py-8 md:py-12 max-w-md">
    <div class="bg-brand-header dark:bg-dark-brand-header rounded-xl overflow-hidden shadow-card dark:shadow-dark-card border border-brand-border/30 dark:border-dark-brand-border/50">
        <div class="p-6 sm:p-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary text-center mb-4">Verify Your Email</h1>
            <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary text-center mb-6">We've sent a verification code to<br><strong class="text-brand-text-primary dark:text-dark-brand-text-primary"><?= htmlspecialchars($email) ?></strong></p>
            
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
                            case 'invalid_otp':
                                echo 'The verification code you entered is incorrect. Please try again.';
                                break;
                            case 'expired_otp':
                                echo 'The verification code has expired. Please request a new one.';
                                break;
                            case 'empty_otp':
                                echo 'Please enter the verification code.';
                                break;
                            default:
                                echo 'An error occurred. Please try again.';
                        }
                        ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['message']) && $_GET['message'] == 'otp_resent'): ?>
            <div class="mb-6 p-3 bg-green-100 dark:bg-green-500/10 border border-green-300 dark:border-green-500/30 text-green-800 dark:text-green-300 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm">A new verification code has been sent to your email.</span>
                </div>
            </div>
            <?php endif; ?>
            
            <form action="verify_otp_process.php" method="POST" class="space-y-6">
                <div class="flex flex-col items-center">
                    <div class="w-full max-w-xs">
                        <label for="otp" class="block text-sm font-medium text-brand-text-primary dark:text-dark-brand-text-primary mb-1 text-center">Enter Verification Code</label>
                        <div class="mt-2">
                            <input type="text" id="otp" name="otp" placeholder="Enter 6-digit code" 
                                class="w-full px-4 py-3 text-center text-lg tracking-widest font-medium rounded-lg bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/30 border border-brand-border/50 dark:border-dark-brand-border/50 focus:border-brand-accent dark:focus:border-dark-brand-accent focus:ring-1 focus:ring-brand-accent dark:focus:ring-dark-brand-accent focus:outline-none text-brand-text-primary dark:text-dark-brand-text-primary placeholder-brand-text-secondary dark:placeholder-dark-brand-text-secondary transition duration-200" 
                                maxlength="6" 
                                pattern="[0-9]{6}" 
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                required>
                        </div>
                        <p class="mt-2 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary text-center">
                            Please enter the 6-digit verification code sent to your email
                        </p>
                    </div>
                </div>
                
                <div class="flex justify-center">
                    <button type="submit" class="w-full max-w-xs py-3 px-4 bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent rounded-lg font-semibold transition-colors duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent dark:focus:ring-offset-dark-brand-header">
                        Verify & Create Account
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary">
                    Didn't receive the code?
                    <a href="resend_otp.php" class="text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover font-medium ml-1 transition-colors">
                        Resend code
                    </a>
                </p>
                <p class="text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary mt-2">
                    <a href="register.php" class="text-brand-accent dark:text-dark-brand-accent hover:text-brand-accent-hover dark:hover:text-dark-brand-accent-hover font-medium transition-colors">
                        ← Back to registration
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 