<?php
/**
 * Database Connection Test
 * This script tests the database connection and provides troubleshooting information
 */

echo "<h2>Database Connection Test</h2>";
echo "<h3>XAMPP Status Check</h3>";
$mysqlSocket = '/opt/lampp/var/mysql/mysql.sock';
$apachePort = 80;

if (file_exists($mysqlSocket)) {
    echo "<p style='color: green;'>✓ MySQL socket exists: $mysqlSocket</p>";
} else {
    echo "<p style='color: red;'>✗ MySQL socket not found: $mysqlSocket</p>";
    echo "<p>XAMPP MySQL service may not be running. Please start XAMPP:</p>";
    echo "<code>sudo /opt/lampp/lampp start</code>";
}
$connection = @fsockopen('localhost', $apachePort, $errno, $errstr, 5);
if (is_resource($connection)) {
    echo "<p style='color: green;'>✓ Apache is running on port $apachePort</p>";
    fclose($connection);
} else {
    echo "<p style='color: red;'>✗ Apache is not running on port $apachePort</p>";
}
echo "<h3>Database Connection Test</h3>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    $result = $db->fetchAll("SELECT COUNT(*) as count FROM students");
    echo "<p>✓ Students table accessible. Count: {$result[0]['count']}</p>";
    $result = $db->fetchAll("SELECT COUNT(*) as count FROM student_documents");
    echo "<p>✓ Student documents table accessible. Count: {$result[0]['count']}</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<h4>Troubleshooting Steps:</h4>";
    echo "<ol>";
    echo "<li>Make sure XAMPP is running: <code>sudo /opt/lampp/lampp start</code></li>";
    echo "<li>Check if MySQL is running: <code>sudo /opt/lampp/lampp status</code></li>";
    echo "<li>Verify database exists: <code>mysql -u root -p -e 'SHOW DATABASES;'</code></li>";
    echo "<li>Check database configuration in config/database.php</li>";
    echo "</ol>";
}
echo "<h3>Upload Directory Check</h3>";
$uploadDir = 'uploads/students/';
if (is_dir($uploadDir)) {
    echo "<p style='color: green;'>✓ Upload directory exists: $uploadDir</p>";
    if (is_writable($uploadDir)) {
        echo "<p style='color: green;'>✓ Upload directory is writable</p>";
    } else {
        echo "<p style='color: red;'>✗ Upload directory is not writable</p>";
        echo "<p>Fix permissions: <code>chmod 755 $uploadDir</code></p>";
    }
} else {
    echo "<p style='color: red;'>✗ Upload directory does not exist: $uploadDir</p>";
    echo "<p>Create directory: <code>mkdir -p $uploadDir && chmod 755 $uploadDir</code></p>";
}

echo "<h3>PHP Upload Settings</h3>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>max_file_uploads: " . ini_get('max_file_uploads') . "</li>";
echo "<li>file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</li>";
echo "</ul>";

echo "<h3>Next Steps</h3>";
echo "<p>If the database connection is successful, you can now:</p>";
echo "<ol>";
echo "<li>Go to the student edit page</li>";
echo "<li>Upload documents using the form</li>";
echo "<li>View and manage uploaded documents</li>";
echo "</ol>";
?> 