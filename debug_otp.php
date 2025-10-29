<?php
/**
 * Debug OTP System
 * This will help us understand what's happening with the OTP verification
 */

require_once 'config/database.php';
require_once 'includes/email_service.php';

echo "Debugging OTP System...\n";
echo "========================\n\n";

try {
    $db = Database::getInstance();
    $otpService = new OTPService();
    
    // Test email
    $testEmail = 'debug@example.com';
    
    echo "1. Cleaning up any existing OTPs for test email...\n";
    $db->query("DELETE FROM email_verifications WHERE email = ?", [$testEmail]);
    echo "   ✓ Cleaned up\n";
    
    echo "2. Generating new OTP...\n";
    $otpCode = $otpService->generateOTP($testEmail, 'registration');
    echo "   ✓ OTP generated: $otpCode\n";
    
    echo "3. Checking what's in the database...\n";
    $storedOTP = $db->fetchOne(
        "SELECT * FROM email_verifications WHERE email = ? AND verification_type = ? ORDER BY created_at DESC LIMIT 1",
        [$testEmail, 'registration']
    );
    
    if ($storedOTP) {
        echo "   ✓ Found OTP in database:\n";
        echo "     - ID: " . $storedOTP['id'] . "\n";
        echo "     - Email: " . $storedOTP['email'] . "\n";
        echo "     - OTP Code: " . $storedOTP['otp_code'] . "\n";
        echo "     - Type: " . $storedOTP['verification_type'] . "\n";
        echo "     - Verified: " . ($storedOTP['is_verified'] ? 'Yes' : 'No') . "\n";
        echo "     - Expires: " . $storedOTP['expires_at'] . "\n";
        echo "     - Attempts: " . $storedOTP['attempts'] . "\n";
        echo "     - Max Attempts: " . $storedOTP['max_attempts'] . "\n";
    } else {
        echo "   ✗ No OTP found in database!\n";
    }
    
    echo "4. Testing OTP verification with correct code...\n";
    $isValid = $otpService->verifyOTP($testEmail, $otpCode, 'registration');
    if ($isValid) {
        echo "   ✓ OTP verification successful\n";
    } else {
        echo "   ✗ OTP verification failed\n";
        
        // Check what's in the database after failed verification
        $afterOTP = $db->fetchOne(
            "SELECT * FROM email_verifications WHERE email = ? AND verification_type = ? ORDER BY created_at DESC LIMIT 1",
            [$testEmail, 'registration']
        );
        
        if ($afterOTP) {
            echo "   Database after failed verification:\n";
            echo "     - Verified: " . ($afterOTP['is_verified'] ? 'Yes' : 'No') . "\n";
            echo "     - Attempts: " . $afterOTP['attempts'] . "\n";
        }
    }
    
    echo "5. Testing with wrong OTP code...\n";
    $isValidWrong = $otpService->verifyOTP($testEmail, '000000', 'registration');
    if ($isValidWrong) {
        echo "   ✗ Wrong OTP was accepted (this is bad!)\n";
    } else {
        echo "   ✓ Wrong OTP correctly rejected\n";
    }
    
    echo "6. Testing email verification status...\n";
    $isVerified = $otpService->isEmailVerified($testEmail, 'registration');
    if ($isVerified) {
        echo "   ✓ Email is marked as verified\n";
    } else {
        echo "   ✗ Email verification status failed\n";
    }
    
    // Clean up
    $db->query("DELETE FROM email_verifications WHERE email = ?", [$testEmail]);
    echo "\n✓ Cleaned up test data\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
