<?php
/**
 * Install PHPMailer for better email delivery
 */

echo "Installing PHPMailer...\n";

// Create composer.json if it doesn't exist
if (!file_exists('composer.json')) {
    $composerJson = [
        "require" => [
            "phpmailer/phpmailer" => "^6.8"
        ],
        "autoload" => [
            "psr-4" => [
                "App\\" => "src/"
            ]
        ]
    ];
    
    file_put_contents('composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));
    echo "✓ Created composer.json\n";
}

// Check if composer is available
$composerPath = '';
if (is_executable('/usr/local/bin/composer')) {
    $composerPath = '/usr/local/bin/composer';
} elseif (is_executable('/usr/bin/composer')) {
    $composerPath = '/usr/bin/composer';
} elseif (is_executable('composer')) {
    $composerPath = 'composer';
}

if ($composerPath) {
    echo "✓ Composer found at: $composerPath\n";
    echo "Running: $composerPath install\n";
    
    $output = shell_exec("$composerPath install 2>&1");
    echo $output;
    
    if (file_exists('vendor/autoload.php')) {
        echo "✓ PHPMailer installed successfully!\n";
    } else {
        echo "⚠ PHPMailer installation may have failed\n";
    }
} else {
    echo "⚠ Composer not found. Please install PHPMailer manually:\n";
    echo "1. Download PHPMailer from: https://github.com/PHPMailer/PHPMailer\n";
    echo "2. Extract to includes/PHPMailer/\n";
    echo "3. Or run: composer install\n";
}

echo "\nNext steps:\n";
echo "1. Configure your Gmail settings in includes/email_service.php\n";
echo "2. Test the email sending again\n";
?>
