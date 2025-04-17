<?php
/**
 * PricePrawl - Registration Process
 * This script handles initial user registration and OTP sending
 */

// Start the session
session_start();

// Include database connection
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
        $mail->Subject = 'Your PricePrawl Account Verification Code';
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background-color: #865D36; padding: 15px; text-align: center; color: white; font-weight: bold; font-size: 22px; border-radius: 5px 5px 0 0;">
                    PricePrawl
                </div>
                <div style="border: 1px solid #ddd; border-top: none; padding: 20px; border-radius: 0 0 5px 5px;">
                    <p>Hello ' . htmlspecialchars($firstName) . ',</p>
                    <p>Thank you for registering with PricePrawl! To complete your registration, please use the verification code below:</p>
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
        $mail->AltBody = 'Hello ' . $firstName . ', Your PricePrawl verification code is: ' . $otp . '. This code will expire in 10 minutes.';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']) ? 1 : 0;
    
    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        header("Location: register.php?error=empty_fields");
        exit();
    }
    
    // Validate email format
    if (!isValidEmail($email)) {
        header("Location: register.php?error=invalid_email");
        exit();
    }
    
    // Validate password match
    if ($password !== $confirmPassword) {
        header("Location: register.php?error=password_mismatch");
        exit();
    }
    
    // Validate password strength (at least 8 characters with at least 1 number and 1 special character)
    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        header("Location: register.php?error=weak_password");
        exit();
    }
    
    // Validate terms acceptance
    if (!$terms) {
        header("Location: register.php?error=terms_required");
        exit();
    }
    
    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            header("Location: register.php?error=email_exists");
            exit();
        }
        
        // Generate OTP
        $otp = generateOTP();
        $otpExpiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Store user data and OTP in session
        $_SESSION['registration'] = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'otp' => $otp,
            'otp_expiry' => $otpExpiry
        ];
        
        // Send OTP via email
        if (sendOTPEmail($email, $firstName, $otp)) {
            // Redirect to OTP verification page
            header("Location: verify_otp.php");
            exit();
        } else {
            // Email sending failed
            header("Location: register.php?error=email_error");
            exit();
        }
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header("Location: register.php?error=database_error");
        exit();
    }
} else {
    // If not POST request, redirect to registration page
    header("Location: register.php");
    exit();
} 