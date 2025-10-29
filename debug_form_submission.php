<?php
/**
 * Debug Form Submission
 * This will help us understand what's happening when the form is submitted
 */

require_once 'config/database.php';
require_once 'includes/email_service.php';

echo "Debug Form Submission...\n";
echo "========================\n\n";

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "✅ POST request received\n";
    echo "POST data:\n";
    print_r($_POST);
    echo "\n";
    
    // Check email verification
    if (!empty($_POST['email'])) {
        $otpService = new OTPService();
        $isVerified = $otpService->isEmailVerified($_POST['email'], 'registration');
        echo "Email verification status: " . ($isVerified ? 'VERIFIED' : 'NOT VERIFIED') . "\n";
        
        if (!$isVerified) {
            echo "❌ This is why the form is failing - email is not verified!\n";
            echo "The user needs to click 'Send Verification' first.\n";
        }
    }
} else {
    echo "❌ Not a POST request\n";
    echo "This should be accessed via form submission\n";
}

echo "\nTo test the form:\n";
echo "1. Go to: http://localhost/SNNHES/student_register.php\n";
echo "2. Fill out the form\n";
echo "3. Enter an email and click 'Send Verification'\n";
echo "4. Go to the verification page and click 'Done'\n";
echo "5. Return to registration and try to submit\n";
?>
