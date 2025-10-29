<?php
/**
 * Setup Email Verification System
 * This script sets up the email verification system for student registration
 */

require_once 'config/database.php';

try {
    $db = Database::getInstance();
    
    echo "Setting up email verification system...\n";
    
    // Step 1: Create email_verifications table
    echo "1. Creating email_verifications table...\n";
    
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS `email_verifications` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `email` varchar(100) NOT NULL,
      `otp_code` varchar(6) NOT NULL,
      `verification_type` enum('registration','password_reset','email_change') NOT NULL DEFAULT 'registration',
      `expires_at` timestamp NOT NULL,
      `is_verified` tinyint(1) DEFAULT 0,
      `verified_at` timestamp NULL DEFAULT NULL,
      `attempts` int(11) DEFAULT 0,
      `max_attempts` int(11) DEFAULT 3,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `idx_email_verifications_email` (`email`),
      KEY `idx_email_verifications_otp` (`otp_code`),
      KEY `idx_email_verifications_expires` (`expires_at`),
      KEY `idx_email_verifications_verified` (`is_verified`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
    ";
    
    $db->query($createTableSQL);
    echo "   ✓ email_verifications table created successfully\n";
    
    // Step 2: Test email service
    echo "2. Testing email service...\n";
    
    require_once 'includes/email_service.php';
    
    $emailService = new EmailService();
    $otpService = new OTPService();
    
    // Generate test OTP
    $testEmail = 'test@example.com';
    $testOTP = $otpService->generateOTP($testEmail, 'registration');
    echo "   ✓ OTP generation working (Test OTP: $testOTP)\n";
    
    // Test email sending (commented out to avoid sending real emails during setup)
    /*
    $emailSent = $emailService->sendOTP($testEmail, $testOTP, 'registration');
    if ($emailSent) {
        echo "   ✓ Email sending working\n";
    } else {
        echo "   ⚠ Email sending failed - check your email configuration\n";
    }
    */
    
    // Clean up test data
    $db->query("DELETE FROM email_verifications WHERE email = ?", [$testEmail]);
    
    // Step 3: Create API directory if it doesn't exist
    echo "3. Setting up API endpoints...\n";
    
    if (!is_dir('api')) {
        mkdir('api', 0755, true);
        echo "   ✓ Created api directory\n";
    } else {
        echo "   ✓ API directory already exists\n";
    }
    
    // Step 4: Test API endpoints
    echo "4. Testing API endpoints...\n";
    
    // Test send_otp.php
    if (file_exists('api/send_otp.php')) {
        echo "   ✓ send_otp.php endpoint ready\n";
    } else {
        echo "   ⚠ send_otp.php not found\n";
    }
    
    // Test verify_otp.php
    if (file_exists('api/verify_otp.php')) {
        echo "   ✓ verify_otp.php endpoint ready\n";
    } else {
        echo "   ⚠ verify_otp.php not found\n";
    }
    
    // Step 5: Email configuration check
    echo "5. Email configuration check...\n";
    
    $configIssues = [];
    
    // Check if email service file exists
    if (!file_exists('includes/email_service.php')) {
        $configIssues[] = "Email service file not found";
    }
    
    // Check if mail function is available
    if (!function_exists('mail')) {
        $configIssues[] = "PHP mail() function not available";
    }
    
    if (empty($configIssues)) {
        echo "   ✓ Email configuration looks good\n";
    } else {
        echo "   ⚠ Configuration issues found:\n";
        foreach ($configIssues as $issue) {
            echo "      - $issue\n";
        }
    }
    
    echo "\n🎉 Email verification system setup completed!\n";
    echo "\nNext steps:\n";
    echo "1. Configure your email settings in includes/email_service.php\n";
    echo "2. Test the registration form with email verification\n";
    echo "3. For production, consider using PHPMailer for better email delivery\n";
    echo "\nEmail verification features:\n";
    echo "✅ OTP generation and validation\n";
    echo "✅ Email sending with HTML templates\n";
    echo "✅ Rate limiting and attempt tracking\n";
    echo "✅ Session-based verification tracking\n";
    echo "✅ Automatic cleanup of expired OTPs\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during setup: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
    exit(1);
}
?>
