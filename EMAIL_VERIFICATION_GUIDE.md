# Email Verification with OTP System

## Overview
I've implemented a comprehensive email verification system with OTP (One-Time Password) functionality for student registration. This ensures that students provide valid email addresses and verify their ownership before completing registration.

## Features Implemented

### ✅ **Core Features**
- **OTP Generation**: 6-digit random codes with configurable expiry
- **Email Sending**: HTML-formatted emails with professional templates
- **Rate Limiting**: Maximum 3 attempts per email with tracking
- **Session Management**: Secure verification state tracking
- **Auto Cleanup**: Expired OTPs are automatically removed
- **Real-time UI**: Dynamic verification interface with status updates

### ✅ **Security Features**
- **Attempt Limiting**: Prevents brute force attacks
- **Time-based Expiry**: OTPs expire after 15 minutes
- **Session Validation**: Verification tied to user session
- **Input Sanitization**: All inputs are properly sanitized
- **CSRF Protection**: Session-based verification prevents tampering

## Files Created/Modified

### **New Files**
1. `database/create_email_verification_table.sql` - Database schema
2. `includes/email_service.php` - Email and OTP service classes
3. `api/send_otp.php` - API endpoint for sending OTPs
4. `api/verify_otp.php` - API endpoint for verifying OTPs
5. `setup_email_verification.php` - Setup and testing script
6. `EMAIL_VERIFICATION_GUIDE.md` - This documentation

### **Modified Files**
1. `student_register.php` - Added email verification UI and logic
2. `modules/students/register.php` - Updated for email verification

## How It Works

### **1. Student Registration Process**
1. Student enters email address
2. Clicks "Send Verification" button
3. System generates 6-digit OTP and sends email
4. Student receives email with verification code
5. Student enters OTP code in the form
6. System verifies the code
7. Email is marked as verified
8. Student can proceed with registration

### **2. Technical Flow**
```
Student Input → Email Validation → OTP Generation → Email Sending
     ↓
OTP Storage → Student Enters Code → Verification → Session Update
     ↓
Registration Proceeds → Database Storage → Success
```

## Setup Instructions

### **Step 1: Run Setup Script**
```bash
# Navigate to your project directory
cd /path/to/SNNHES

# Run the setup script
php setup_email_verification.php
```

### **Step 2: Configure Email Settings**
Edit `includes/email_service.php` and update the email configuration:

```php
// For Gmail (recommended for testing)
$this->smtpHost = 'smtp.gmail.com';
$this->smtpPort = 587;
$this->smtpUsername = 'your-email@gmail.com';
$this->smtpPassword = 'your-app-password';
$this->smtpEncryption = 'tls';

// For other providers
$this->smtpHost = 'your-smtp-server.com';
$this->smtpPort = 587; // or 465 for SSL
$this->smtpUsername = 'your-email@domain.com';
$this->smtpPassword = 'your-password';
$this->smtpEncryption = 'tls'; // or 'ssl'
```

### **Step 3: Test the System**
1. Go to the student registration page
2. Enter a valid email address
3. Click "Send Verification"
4. Check your email for the OTP code
5. Enter the code and verify
6. Complete the registration

## Email Configuration Options

### **Option 1: PHP mail() Function (Default)**
- Uses PHP's built-in mail() function
- Works with most hosting providers
- No additional dependencies required

### **Option 2: PHPMailer (Recommended for Production)**
- More reliable email delivery
- Better error handling
- Supports multiple email providers

To use PHPMailer:
1. Install via Composer: `composer require phpmailer/phpmailer`
2. Uncomment the PHPMailer code in `includes/email_service.php`
3. Update the `sendEmail()` method to use `sendEmailWithPHPMailer()`

## Database Schema

The system creates an `email_verifications` table with the following structure:

```sql
CREATE TABLE `email_verifications` (
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
```

## API Endpoints

### **POST /api/send_otp.php**
Sends OTP to email address

**Request:**
```json
{
  "email": "student@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Verification code sent to your email address",
  "email": "student@example.com"
}
```

### **POST /api/verify_otp.php**
Verifies OTP code

**Request:**
```json
{
  "email": "student@example.com",
  "otp": "123456"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Email verified successfully",
  "email": "student@example.com"
}
```

## User Interface

### **Email Input Section**
- Real-time email validation
- "Send Verification" button (enabled only for valid emails)
- Visual feedback for email format validation

### **Verification Section**
- Appears after OTP is sent
- 6-digit OTP input field
- "Verify Email" and "Resend" buttons
- Real-time status updates
- Success/error message display

### **Verified Status**
- Green success message when email is verified
- Prevents form submission until verification is complete

## Security Considerations

### **Rate Limiting**
- Maximum 3 verification attempts per email
- 15-minute OTP expiry
- Automatic cleanup of expired OTPs

### **Session Security**
- Verification state stored in session
- Session timeout after 1 hour
- Prevents unauthorized access

### **Input Validation**
- Email format validation
- OTP format validation (6 digits only)
- SQL injection prevention
- XSS protection

## Troubleshooting

### **Common Issues**

1. **Emails not being sent**
   - Check email configuration in `includes/email_service.php`
   - Verify SMTP credentials
   - Check server mail function availability

2. **OTP verification failing**
   - Check database connection
   - Verify OTP hasn't expired
   - Check attempt limits

3. **UI not responding**
   - Check browser console for JavaScript errors
   - Verify API endpoints are accessible
   - Check network connectivity

### **Debug Mode**
Enable debug mode by adding this to the top of API files:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Customization

### **OTP Expiry Time**
Change in `includes/email_service.php`:
```php
$otpCode = $otpService->generateOTP($email, 'registration', 30); // 30 minutes
```

### **Max Attempts**
Change in database schema or `OTPService` class:
```php
'max_attempts' => 5 // Allow 5 attempts instead of 3
```

### **Email Template**
Modify the `getOTPMessage()` method in `EmailService` class to customize the email template.

## Production Recommendations

1. **Use PHPMailer** for better email delivery
2. **Configure proper SMTP settings** for your hosting provider
3. **Set up email monitoring** to track delivery rates
4. **Implement logging** for OTP generation and verification
5. **Consider using Redis** for OTP storage in high-traffic scenarios
6. **Add CAPTCHA** to prevent automated abuse

## Support

If you encounter any issues:
1. Check the setup script output for errors
2. Verify email configuration
3. Test with a simple email first
4. Check server error logs
5. Ensure all files are properly uploaded

The system is designed to be robust and user-friendly while maintaining security best practices.
