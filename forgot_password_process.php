<?php
/**
 * PricePrawl - Forgot Password Process
 * This script handles sending password reset links via email
 */

// Start the session
session_start();

// Include database connection and configuration
require_once 'includes/db_connect.php';
require_once 'includes/config.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to generate a secure reset token
function generateResetToken() {
    return bin2hex(random_bytes(32)); // 64 character hex string
}

// Function to send reset password email
function sendResetEmail($email, $firstName, $resetToken) {
    global $config;
    
    // Generate reset URL
    $resetUrl = $config['site_url'] . '/reset_password.php?token=' . $resetToken;
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp_username'];
        $mail->Password   = $config['smtp_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['smtp_port'];
        
        // Recipients
        $mail->setFrom($config['from_email'], 'PricePrawl');
        $mail->addAddress($email);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your PricePrawl Password';
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background-color: #865D36; padding: 15px; text-align: center; color: white; font-weight: bold; font-size: 22px; border-radius: 5px 5px 0 0;">
                    PricePrawl
                </div>
                <div style="border: 1px solid #ddd; border-top: none; padding: 20px; border-radius: 0 0 5px 5px;">
                    <p>Hello ' . htmlspecialchars($firstName) . ',</p>
                    <p>You recently requested to reset your password for your PricePrawl account. Click the button below to reset it:</p>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="' . $resetUrl . '" style="background-color: #865D36; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-weight: bold; display: inline-block;">Reset Your Password</a>
                    </div>
                    <p>If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
                    <p>This password reset link is only valid for the next 30 minutes.</p>
                    <p>If you\'re having trouble clicking the button, copy and paste the URL below into your web browser:</p>
                    <p style="word-break: break-all; font-size: 14px; color: #666;">' . $resetUrl . '</p>
                    <p>Best regards,<br>The PricePrawl Team</p>
                </div>
                <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #777;">
                    &copy; ' . date('Y') . ' PricePrawl. All rights reserved.
                </div>
            </div>
        ';
        $mail->AltBody = 'Hello ' . $firstName . ', You recently requested to reset your password for your PricePrawl account. Please visit this link to reset your password: ' . $resetUrl . '. This link is valid for 30 minutes.';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Reset email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    
    // Validate email
    if (empty($email)) {
        header("Location: forgot-password.php?error=empty_email");
        exit();
    }
    
    if (!isValidEmail($email)) {
        header("Location: forgot-password.php?error=invalid_email");
        exit();
    }
    
    try {
        // Check if email exists in the database
        $stmt = $pdo->prepare("SELECT id, first_name, email, email_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // User not found
            header("Location: forgot-password.php?error=email_not_found");
            exit();
        }
        
        // Check if email is verified
        if (!$user['email_verified']) {
            header("Location: forgot-password.php?error=email_not_verified");
            exit();
        }
        
        // Generate password reset token
        $resetToken = generateResetToken();
        $tokenExpiry = date('Y-m-d H:i:s', strtotime('+30 minutes')); // Token expires in 30 minutes
        
        // Store token in database
        $stmt = $pdo->prepare("
            INSERT INTO password_reset_tokens (user_id, token, expires_at) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user['id'], $resetToken, $tokenExpiry]);
        
        // Send email with reset link
        if (sendResetEmail($user['email'], $user['first_name'], $resetToken)) {
            // Success, redirect with success message
            header("Location: forgot-password.php?message=reset_link_sent");
            exit();
        } else {
            // Email sending failed
            header("Location: forgot-password.php?error=email_error");
            exit();
        }
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header("Location: forgot-password.php?error=database_error");
        exit();
    }
} else {
    // If not POST request, redirect to forgot password page
    header("Location: forgot-password.php");
    exit();
} 