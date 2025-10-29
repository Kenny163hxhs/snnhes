<?php
/**
 * Fix Permissions Script
 * Run this from your web browser to fix upload directory permissions
 */

// Security check - only allow from localhost
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', 'localhost'])) {
    die('Access denied. This script can only be run from localhost.');
}

echo "<h2>🔧 Fix Upload Directory Permissions</h2>";

$baseDir = realpath(dirname(__FILE__));
$uploadsDir = $baseDir . '/uploads';

echo "<p><strong>Base Directory:</strong> " . htmlspecialchars($baseDir) . "</p>";
echo "<p><strong>Uploads Directory:</strong> " . htmlspecialchars($uploadsDir) . "</p>";

// Check current permissions
echo "<h3>Current Status:</h3>";
echo "<p><strong>Uploads directory exists:</strong> " . (is_dir($uploadsDir) ? 'Yes' : 'No') . "</p>";
echo "<p><strong>Uploads directory writable:</strong> " . (is_writable($uploadsDir) ? 'Yes' : 'No') . "</p>";

if (is_dir($uploadsDir)) {
    $perms = fileperms($uploadsDir);
    echo "<p><strong>Current permissions:</strong> " . substr(sprintf('%o', $perms), -4) . "</p>";
}

// Create directories if they don't exist
echo "<h3>Creating Directories:</h3>";

$directories = [
    $uploadsDir,
    $uploadsDir . '/students',
    $uploadsDir . '/transfers'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "<p style='color: green;'>✅ Created: " . htmlspecialchars($dir) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create: " . htmlspecialchars($dir) . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Already exists: " . htmlspecialchars($dir) . "</p>";
    }
}

// Try to fix permissions
echo "<h3>Fixing Permissions:</h3>";

$success = true;

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        // Try to set permissions
        if (chmod($dir, 0777)) {
            echo "<p style='color: green;'>✅ Set permissions for: " . htmlspecialchars($dir) . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Could not set permissions for: " . htmlspecialchars($dir) . "</p>";
            $success = false;
        }
    }
}

// Check if we're on macOS/Linux and try to fix ownership
if (PHP_OS_FAMILY === 'Darwin' || PHP_OS_FAMILY === 'Linux') {
    echo "<h3>Fixing Ownership (macOS/Linux):</h3>";
    
    $webUser = 'www-data';
    if (PHP_OS_FAMILY === 'Darwin') {
        $webUser = '_www';
    }
    
    echo "<p><strong>Web server user:</strong> $webUser</p>";
    
    // Try to change ownership
    $command = "chown -R $webUser:$webUser " . escapeshellarg($uploadsDir) . " 2>&1";
    $result = shell_exec($command);
    
    if ($result) {
        echo "<p style='color: blue;'>ℹ️ Ownership change result: " . htmlspecialchars($result) . "</p>";
    }
    
    // Try to set group write permissions
    $command = "chmod -R g+w " . escapeshellarg($uploadsDir) . " 2>&1";
    $result = shell_exec($command);
    
    if ($result) {
        echo "<p style='color: blue;'>ℹ️ Group permissions result: " . htmlspecialchars($result) . "</p>";
    }
}

// Final check
echo "<h3>Final Status:</h3>";

$allWritable = true;
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir);
        $status = $writable ? '✅ Writable' : '❌ Not writable';
        $color = $writable ? 'green' : 'red';
        echo "<p style='color: $color;'>$status: " . htmlspecialchars($dir) . "</p>";
        
        if (!$writable) {
            $allWritable = false;
        }
    }
}

if ($allWritable) {
    echo "<h3 style='color: green;'>🎉 Success! All directories are now writable.</h3>";
    echo "<p>You can now use the SNNHES system without permission errors.</p>";
} else {
    echo "<h3 style='color: red;'>❌ Some directories are still not writable.</h3>";
    echo "<p>Please run the following commands in Terminal:</p>";
    echo "<pre>";
    echo "cd " . htmlspecialchars($baseDir) . "\n";
    echo "sudo chown -R _www:_www uploads/\n";
    echo "sudo chmod -R 775 uploads/\n";
    echo "</pre>";
}

echo "<h3>Next Steps:</h3>";
echo "<p>1. <a href='index.php'>Go to SNNHES Home</a></p>";
echo "<p>2. <a href='student_register.php'>Test Student Registration</a></p>";
echo "<p>3. <a href='check_enrollment.php'>Test Enrollment Status</a></p>";

// Security: Remove this file after use
echo "<hr>";
echo "<p style='color: orange;'>⚠️ <strong>Security Note:</strong> Please delete this file (fix_permissions.php) after fixing permissions.</p>";
?>
