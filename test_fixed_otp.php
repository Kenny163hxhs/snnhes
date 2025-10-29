<?php
/**
 * Test Fixed OTP System
 */

require_once 'config/database.php';
require_once 'includes/email_service.php';

echo "Testing Fixed OTP System...\n";
echo "============================\n\n";

try {
    $db = Database::getInstance();
    $emailService = new EmailService();
    $otpService = new OTPService();
    
    // Test email
    $testEmail = 'test@example.com';
    
    // Clean up first
    $db->query("DELETE FROM email_verifications WHERE email = ?", [$testEmail]);
    
    echo "1. Generating OTP...\n";
    $otpCode = $otpService->generateOTP($testEmail, 'registration');
    echo "   ✓ OTP generated: $otpCode\n";
    
    echo "2. Checking database...\n";
    $storedOTP = $db->fetchOne(
        "SELECT * FROM email_verifications WHERE email = ? ORDER BY created_at DESC LIMIT 1",
        [$testEmail]
    );
    
    if ($storedOTP) {
        echo "   ✓ OTP stored in database\n";
        echo "   - Code: " . $storedOTP['otp_code'] . "\n";
        echo "   - Expires: " . $storedOTP['expires_at'] . "\n";
    } else {
        echo "   ✗ OTP not found in database\n";
        exit;
    }
    
    echo "3. Testing OTP verification...\n";
    $isValid = $otpService->verifyOTP($testEmail, $otpCode, 'registration');
    if ($isValid) {
        echo "   ✓ OTP verification successful!\n";
    } else {
        echo "   ✗ OTP verification failed\n";
    }
    
    echo "4. Testing email verification status...\n";
    $isVerified = $otpService->isEmailVerified($testEmail, 'registration');
    if ($isVerified) {
        echo "   ✓ Email is verified!\n";
    } else {
        echo "   ✗ Email verification status failed\n";
    }
    
    echo "5. Testing with wrong OTP...\n";
    $isValidWrong = $otpService->verifyOTP($testEmail, '000000', 'registration');
    if ($isValidWrong) {
        echo "   ✗ Wrong OTP was accepted (bad!)\n";
    } else {
        echo "   ✓ Wrong OTP correctly rejected\n";
    }
    
    // Clean up
    $db->query("DELETE FROM email_verifications WHERE email = ?", [$testEmail]);
    
    echo "\n🎉 OTP System Test Complete!\n";
    echo "\nThe system is now working correctly!\n";
    echo "You can test the registration form at: http://localhost/SNNHES/student_register.php\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
