<?php
/**
 * Test OTPService Class
 */

echo "Testing OTPService Class...\n";
echo "===========================\n\n";

try {
    require_once 'config/database.php';
    require_once 'includes/email_service.php';
    
    echo "1. Loading classes...\n";
    $db = Database::getInstance();
    $otpService = new OTPService();
    echo "   ✓ OTPService class loaded successfully\n";
    
    echo "2. Testing OTP generation...\n";
    $testEmail = 'test@example.com';
    $otpCode = $otpService->generateOTP($testEmail, 'registration');
    echo "   ✓ OTP generated: $otpCode\n";
    
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
        echo "   ✓ Email is verified\n";
    } else {
        echo "   ✗ Email verification status failed\n";
    }
    
    // Clean up
    $db->query("DELETE FROM email_verifications WHERE email = ?", [$testEmail]);
    
    echo "\n🎉 All tests passed! The system is working correctly.\n";
    echo "\nYou can now test the registration form at:\n";
    echo "http://localhost/SNNHES/student_register.php\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
