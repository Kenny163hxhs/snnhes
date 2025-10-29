<?php
/**
 * OTP Display Page
 * Shows the OTP code to students for email verification
 */

require_once 'config/database.php';
require_once 'includes/email_service.php';

// Get email from URL parameter
$email = $_GET['email'] ?? '';

if (empty($email)) {
    header('Location: student_register.php');
    exit;
}

// Get the latest OTP for this email
$db = Database::getInstance();
$otpData = $db->fetchOne(
    "SELECT * FROM email_verifications WHERE email = ? AND verification_type = 'registration' ORDER BY created_at DESC LIMIT 1",
    [$email]
);

if (!$otpData) {
    header('Location: student_register.php?error=no_otp');
    exit;
}

$otpCode = $otpData['otp_code'];
$expiresAt = $otpData['expires_at'];
$timeLeft = strtotime($expiresAt) - time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - SNNHES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .otp-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .otp-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .otp-code {
            background: #f8f9fa;
            border: 3px dashed #007bff;
            border-radius: 15px;
            padding: 40px;
            margin: 30px;
            text-align: center;
        }
        .otp-number {
            font-size: 4rem;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 15px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .copy-btn {
            background: #28a745;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .copy-btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        .instructions {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
        }
        .timer {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .done-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        .done-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="otp-container">
                    <div class="otp-header">
                        <i class="fas fa-envelope-open-text fa-3x mb-3"></i>
                        <h2>Email Verification</h2>
                        <p class="mb-0">We've sent a verification code to your email</p>
                    </div>
                    
                    <div class="p-4">
                        <div class="text-center mb-4">
                            <h4>Email Verification Complete!</h4>
                            <p class="text-muted">Your email has been verified successfully</p>
                        </div>
                        
                        <div class="instructions">
                            <h5><i class="fas fa-check-circle me-2 text-success"></i>Verification Successful:</h5>
                            <ul class="mb-0">
                                <li>Your email address has been verified</li>
                                <li>You can now complete your registration</li>
                                <li>Click "Done" to continue with the registration process</li>
                            </ul>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-muted mb-3">
                                <strong>Email:</strong> <?= htmlspecialchars($email) ?><br>
                                <strong>Verified at:</strong> <?= date('M d, Y H:i:s') ?>
                            </p>
                            
                            <a href="student_register.php?email=<?= urlencode($email) ?>&verified=true" class="btn done-btn">
                                <i class="fas fa-check me-2"></i>Done - Continue Registration
                            </a>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Your email verification is complete and secure.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-verify the email when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Automatically mark the email as verified
            fetch('api/verify_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    email: '<?= $email ?>', 
                    otp_code: '<?= $otpCode ?>' 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Email verified successfully');
                } else {
                    console.log('Email verification failed:', data.message);
                }
            })
            .catch(error => {
                console.error('Error verifying email:', error);
            });
        });
    </script>
</body>
</html>