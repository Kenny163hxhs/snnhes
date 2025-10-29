<?php
/**
 * Simple Email Test - No SMTP, just basic mail()
 */

// Test with basic PHP mail() function
$to = 'your-email@gmail.com'; // Change this to your email
$subject = 'Test Email from SNNHES';
$message = '<h1>Test Email</h1><p>This is a test email from SNNHES system.</p>';
$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: SNNHES System <noreply@snnhes.edu>',
    'Reply-To: noreply@snnhes.edu',
    'X-Mailer: PHP/' . phpversion()
];

echo "Sending test email to: $to\n";

$result = mail($to, $subject, $message, implode("\r\n", $headers));

if ($result) {
    echo "✅ Email sent successfully!\n";
    echo "Check your inbox and spam folder.\n";
} else {
    echo "❌ Email sending failed.\n";
    echo "This is normal for XAMPP - it doesn't have a mail server.\n";
    echo "For testing, you can:\n";
    echo "1. Use a real web hosting service\n";
    echo "2. Set up a local mail server\n";
    echo "3. Use the SMTP method (which we'll configure)\n";
}

echo "\nTo test the registration form:\n";
echo "1. Go to: http://localhost/SNNHES/student_register.php\n";
echo "2. Fill out the form\n";
echo "3. Enter your email and click 'Send Verification'\n";
echo "4. Check your email for the OTP code\n";
echo "5. Enter the code and verify\n";
echo "6. Complete registration\n";
?>
