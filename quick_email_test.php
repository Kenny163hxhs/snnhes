<?php
/**
 * Quick Email Test - Direct SMTP Configuration
 */

echo "Quick Email Test with Direct Configuration\n";
echo "==========================================\n\n";

// Direct Gmail SMTP Configuration
$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpUsername = 'your-email@gmail.com'; // ⚠️ CHANGE THIS
$smtpPassword = 'your-app-password';    // ⚠️ CHANGE THIS
$fromEmail = 'your-email@gmail.com';    // ⚠️ CHANGE THIS
$fromName = 'SNNHES System';

$toEmail = 'your-email@gmail.com'; // ⚠️ CHANGE THIS TO YOUR EMAIL

echo "Configuration:\n";
echo "- SMTP Host: $smtpHost\n";
echo "- SMTP Port: $smtpPort\n";
echo "- Username: $smtpUsername\n";
echo "- From Email: $fromEmail\n";
echo "- To Email: $toEmail\n\n";

// Test SMTP connection
echo "Testing SMTP connection...\n";

$socket = fsockopen($smtpHost, $smtpPort, $errno, $errstr, 30);

if (!$socket) {
    echo "❌ Connection failed: $errstr ($errno)\n";
    echo "\nPossible solutions:\n";
    echo "1. Check your internet connection\n";
    echo "2. Make sure Gmail SMTP is not blocked by firewall\n";
    echo "3. Try using a different port (465 for SSL)\n";
    exit;
}

echo "✅ Connected to SMTP server\n";

// Read initial response
$response = fgets($socket, 512);
echo "Server response: " . trim($response) . "\n";

if (substr($response, 0, 3) != '220') {
    echo "❌ Server not ready\n";
    fclose($socket);
    exit;
}

// Send EHLO
fputs($socket, "EHLO localhost\r\n");
$response = fgets($socket, 512);
echo "EHLO response: " . trim($response) . "\n";

// Start TLS
echo "Starting TLS...\n";
fputs($socket, "STARTTLS\r\n");
$response = fgets($socket, 512);
echo "STARTTLS response: " . trim($response) . "\n";

if (substr($response, 0, 3) != '220') {
    echo "❌ TLS failed\n";
    fclose($socket);
    exit;
}

// Enable crypto
if (stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
    echo "✅ TLS enabled\n";
} else {
    echo "❌ TLS crypto failed\n";
    fclose($socket);
    exit;
}

// Send EHLO again
fputs($socket, "EHLO localhost\r\n");
$response = fgets($socket, 512);
echo "EHLO after TLS: " . trim($response) . "\n";

// Authenticate
echo "Authenticating...\n";
fputs($socket, "AUTH LOGIN\r\n");
$response = fgets($socket, 512);
echo "AUTH response: " . trim($response) . "\n";

fputs($socket, base64_encode($smtpUsername) . "\r\n");
$response = fgets($socket, 512);
echo "Username response: " . trim($response) . "\n";

fputs($socket, base64_encode($smtpPassword) . "\r\n");
$response = fgets($socket, 512);
echo "Password response: " . trim($response) . "\n";

if (substr($response, 0, 3) != '235') {
    echo "❌ Authentication failed!\n";
    echo "Make sure you're using:\n";
    echo "1. Your actual Gmail address\n";
    echo "2. An App Password (not your regular password)\n";
    echo "3. 2-Step Verification is enabled\n";
    fclose($socket);
    exit;
}

echo "✅ Authentication successful!\n";

// Send email
echo "Sending test email...\n";

fputs($socket, "MAIL FROM: <$fromEmail>\r\n");
$response = fgets($socket, 512);
echo "MAIL FROM response: " . trim($response) . "\n";

fputs($socket, "RCPT TO: <$toEmail>\r\n");
$response = fgets($socket, 512);
echo "RCPT TO response: " . trim($response) . "\n";

fputs($socket, "DATA\r\n");
$response = fgets($socket, 512);
echo "DATA response: " . trim($response) . "\n";

// Email content
$subject = "Test Email from SNNHES";
$message = "<h1>Test Email</h1><p>This is a test email from SNNHES system.</p><p>Time: " . date('Y-m-d H:i:s') . "</p>";

$emailData = "From: $fromName <$fromEmail>\r\n";
$emailData .= "To: $toEmail\r\n";
$emailData .= "Subject: $subject\r\n";
$emailData .= "MIME-Version: 1.0\r\n";
$emailData .= "Content-Type: text/html; charset=UTF-8\r\n";
$emailData .= "\r\n";
$emailData .= $message . "\r\n";
$emailData .= ".\r\n";

fputs($socket, $emailData);
$response = fgets($socket, 512);
echo "Email send response: " . trim($response) . "\n";

if (substr($response, 0, 3) == '250') {
    echo "✅ Email sent successfully!\n";
    echo "Check your inbox: $toEmail\n";
} else {
    echo "❌ Email sending failed\n";
}

// Quit
fputs($socket, "QUIT\r\n");
fclose($socket);

echo "\nNext steps:\n";
echo "1. If successful, update config/email_config.php with your real credentials\n";
echo "2. Test the registration form\n";
echo "3. If failed, check Gmail App Password setup\n";
?>
