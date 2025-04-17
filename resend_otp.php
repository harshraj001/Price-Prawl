<?php
/**
 * PricePrawl - Resend OTP
 * This script handles resending of OTP for registration verification
 */

// Start the session
session_start();

// Include necessary files
require_once 'includes/db_connect.php';
require_once 'includes/config.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Check if registration data exists in session
if (!isset($_SESSION['registration']) || empty($_SESSION['registration'])) {
    header("Location: register.php?error=session_expired");
    exit();
}

// Function to generate OTP
function generateOTP($length = 6) {
    $otp = "";
    for ($i = 0; $i < $length; $i++) {
        $otp .= mt_rand(0, 9);
    }
    return $otp;
}

// Function to send OTP email
function sendOTPEmail($email, $firstName, $otp) {
    global $config;
    
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
        $mail->Subject = 'Your New PricePrawl Verification Code';
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background-color: #865D36; padding: 15px; text-align: center; color: white; font-weight: bold; font-size: 22px; border-radius: 5px 5px 0 0;">
                    PricePrawl
                </div>
                <div style="border: 1px solid #ddd; border-top: none; padding: 20px; border-radius: 0 0 5px 5px;">
                    <p>Hello ' . htmlspecialchars($firstName) . ',</p>
                    <p>You requested a new verification code. Please use the code below to complete your registration:</p>
                    <div style="background-color: #f8f8f8; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0; border-radius: 5px;">
                        ' . $otp . '
                    </div>
                    <p>This code will expire in 10 minutes.</p>
                    <p>If you did not request this code, please ignore this email.</p>
                    <p>Best regards,<br>The PricePrawl Team</p>
                </div>
                <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #777;">
                    &copy; ' . date('Y') . ' PricePrawl. All rights reserved.
                </div>
            </div>
        ';
        $mail->AltBody = 'Hello ' . $firstName . ', Your new PricePrawl verification code is: ' . $otp . '. This code will expire in 10 minutes.';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Get registration data
$email = $_SESSION['registration']['email'];
$firstName = $_SESSION['registration']['first_name'];

// Generate new OTP
$otp = generateOTP();
$otpExpiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

// Update OTP in session
$_SESSION['registration']['otp'] = $otp;
$_SESSION['registration']['otp_expiry'] = $otpExpiry;

// Send new OTP via email
if (sendOTPEmail($email, $firstName, $otp)) {
    // Redirect back to OTP verification page with success message
    header("Location: verify_otp.php?message=otp_resent");
    exit();
} else {
    // Email sending failed
    header("Location: verify_otp.php?error=email_error");
    exit();
} 