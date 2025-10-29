# Gmail SMTP Setup Guide

## Quick Setup (5 minutes)

### Step 1: Configure Your Gmail Settings
1. Open `config/email_config.php`
2. Replace `your-email@gmail.com` with your actual Gmail address
3. Replace `your-app-password` with your Gmail App Password (see Step 2)

### Step 2: Create Gmail App Password
1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Click **Security** in the left sidebar
3. Enable **2-Step Verification** if not already enabled
4. Under **2-Step Verification**, click **App passwords**
5. Select **Mail** as the app
6. Click **Generate**
7. Copy the 16-character password (it looks like: `abcd efgh ijkl mnop`)
8. Paste this password in `config/email_config.php`

### Step 3: Test the Email System
1. Go to: `http://localhost/SNNHES/test_email.php`
2. Edit line 19 to put your Gmail address
3. Run the test

## Example Configuration

```php
// In config/email_config.php
define('SMTP_USERNAME', 'john.doe@gmail.com');
define('SMTP_PASSWORD', 'abcd efgh ijkl mnop'); // Your 16-character app password
define('FROM_EMAIL', 'john.doe@gmail.com');
```

## Troubleshooting

### "Authentication Failed"
- Make sure you're using the App Password, not your regular Gmail password
- Ensure 2-Step Verification is enabled on your Google account

### "Connection Failed"
- Check your internet connection
- Make sure Gmail SMTP is not blocked by your firewall

### "Email Not Received"
- Check your spam/junk folder
- Wait a few minutes for delivery
- Try with a different email address

## Security Notes
- Never commit your real email credentials to version control
- Use App Passwords instead of your main Gmail password
- Consider using a dedicated Gmail account for the system

## Alternative: Use a Different Email Provider

If Gmail doesn't work, you can use other providers:

### Outlook/Hotmail
```php
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@outlook.com');
define('SMTP_PASSWORD', 'your-password');
```

### Yahoo
```php
define('SMTP_HOST', 'smtp.mail.yahoo.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@yahoo.com');
define('SMTP_PASSWORD', 'your-app-password');
```

Once configured, your email verification system will work perfectly! 🚀
