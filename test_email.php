<?php
/**
 * Test Email Sending
 * This will help us debug the email issue
 */

require_once 'config/database.php';
require_once 'includes/email_service.php';

echo "Testing email sending...\n\n";

// Test 1: Check if mail function is available
echo "1. Checking PHP mail function...\n";
if (function_exists('mail')) {
    echo "   ✓ mail() function is available\n";
} else {
    echo "   ✗ mail() function is NOT available\n";
    exit;
}

// Test 2: Check email configuration
echo "2. Checking email configuration...\n";
$emailService = new EmailService();
echo "   SMTP Host: " . ($emailService->smtpHost ?? 'Not set') . "\n";
echo "   From Email: " . ($emailService->fromEmail ?? 'Not set') . "\n";

// Test 3: Try sending a test email
echo "3. Sending test email...\n";
$testEmail = 'your-email@gmail.com'; // Replace with your email
$testOTP = '123456';

echo "   Sending to: $testEmail\n";
echo "   OTP Code: $testOTP\n";
echo "   SMTP Host: " . ($emailService->smtpHost ?? 'Not set') . "\n";
echo "   SMTP Username: " . ($emailService->smtpUsername ?? 'Not set') . "\n";

$result = $emailService->sendOTP($testEmail, $testOTP, 'registration');

if ($result) {
    echo "   ✓ Email sent successfully!\n";
    echo "   Check your inbox (and spam folder)\n";
} else {
    echo "   ✗ Email sending failed\n";
    echo "   This might be due to:\n";
    echo "   - Server email configuration\n";
    echo "   - Gmail blocking emails from localhost\n";
    echo "   - Missing SMTP configuration\n";
}

// Test 4: Alternative method
echo "\n4. Trying alternative email method...\n";

$to = $testEmail;
$subject = 'Test Email from SNNHES';
$message = '<h1>Test Email</h1><p>This is a test email from SNNHES system.</p>';
$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: SNNHES System <noreply@snnhes.edu>',
    'Reply-To: noreply@snnhes.edu',
    'X-Mailer: PHP/' . phpversion()
];

$altResult = mail($to, $subject, $message, implode("\r\n", $headers));

if ($altResult) {
    echo "   ✓ Alternative method worked!\n";
} else {
    echo "   ✗ Alternative method also failed\n";
}

echo "\nTroubleshooting tips:\n";
echo "1. Check your spam/junk folder\n";
echo "2. Try with a different email provider (not Gmail)\n";
echo "3. Check if your hosting provider allows mail() function\n";
echo "4. Consider using a real SMTP service\n";
?>
