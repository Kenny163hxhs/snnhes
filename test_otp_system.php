<?php
/**
 * Test OTP System
 * This will test the complete OTP verification system
 */

require_once 'config/database.php';
require_once 'includes/email_service.php';

echo "Testing OTP System...\n";
echo "====================\n\n";

try {
    $db = Database::getInstance();
    $emailService = new EmailService();
    $otpService = new OTPService();
    
    // Test email
    $testEmail = 'test@example.com';
    
    echo "1. Testing OTP generation...\n";
    $otpCode = $otpService->generateOTP($testEmail, 'registration');
    echo "   ✓ OTP generated: $otpCode\n";
    
    echo "2. Testing email sending...\n";
    $emailSent = $emailService->sendOTP($testEmail, $otpCode, 'registration');
    if ($emailSent) {
        echo "   ✓ Email sent successfully\n";
    } else {
        echo "   ⚠ Email sending failed, but OTP system still works\n";
    }
    
    echo "3. Testing OTP verification...\n";
    $isValid = $otpService->verifyOTP($testEmail, $otpCode, 'registration');
    if ($isValid) {
        echo "   ✓ OTP verification successful\n";
    } else {
        echo "   ✗ OTP verification failed\n";
    }
    
    echo "4. Testing email verification status...\n";
    $isVerified = $otpService->isEmailVerified($testEmail, 'registration');
    if ($isVerified) {
        echo "   ✓ Email is marked as verified\n";
    } else {
        echo "   ✗ Email verification status failed\n";
    }
    
    // Clean up test data
    $db->query("DELETE FROM email_verifications WHERE email = ?", [$testEmail]);
    
    echo "\n🎉 OTP System Test Complete!\n";
    echo "\nNext steps:\n";
    echo "1. Go to: http://localhost/SNNHES/student_register.php\n";
    echo "2. Fill out the registration form\n";
    echo "3. Enter your email and click 'Send Verification'\n";
    echo "4. Check: http://localhost/SNNHES/otp_display.php (for testing)\n";
    echo "5. Enter the OTP code to verify\n";
    echo "6. Complete registration\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
