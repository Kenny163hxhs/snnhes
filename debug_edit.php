<?php
/**
 * Debug Edit Page
 * This script helps debug issues with the edit page
 */

session_start();
require_once 'config/database.php';

echo "<h2>Debug Edit Page</h2>";

// Check session
echo "<h3>Session Check</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✓ User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p style='color: green;'>✓ User Role: " . ($_SESSION['user_role'] ?? 'Not set') . "</p>";
} else {
    echo "<p style='color: red;'>✗ No user session found</p>";
}

// Check if user is logged in
echo "<h3>Login Check</h3>";
if (isLoggedIn()) {
    echo "<p style='color: green;'>✓ User is logged in</p>";
    if (in_array(getUserRole(), ['admin', 'registrar'])) {
        echo "<p style='color: green;'>✓ User has permission to edit students</p>";
    } else {
        echo "<p style='color: red;'>✗ User does not have permission to edit students</p>";
    }
} else {
    echo "<p style='color: red;'>✗ User is not logged in</p>";
}

// Check database connection
echo "<h3>Database Check</h3>";
try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check if students exist
    $students = $db->fetchAll("SELECT id, first_name, last_name FROM students LIMIT 5");
    echo "<p>✓ Found " . count($students) . " students in database</p>";
    
    if (!empty($students)) {
        echo "<h4>Sample Students:</h4>";
        echo "<ul>";
        foreach ($students as $student) {
            echo "<li>ID: {$student['id']} - {$student['first_name']} {$student['last_name']}</li>";
        }
        echo "</ul>";
        
        // Test edit link
        $testStudent = $students[0];
        echo "<h4>Test Edit Link:</h4>";
        echo "<p><a href='modules/students/edit.php?id={$testStudent['id']}' target='_blank'>Edit {$testStudent['first_name']} {$testStudent['last_name']}</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Check file permissions
echo "<h3>File Permissions Check</h3>";
$editFile = 'modules/students/edit.php';
if (file_exists($editFile)) {
    echo "<p style='color: green;'>✓ Edit file exists: $editFile</p>";
    if (is_readable($editFile)) {
        echo "<p style='color: green;'>✓ Edit file is readable</p>";
    } else {
        echo "<p style='color: red;'>✗ Edit file is not readable</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Edit file does not exist: $editFile</p>";
}

// Check upload directory
echo "<h3>Upload Directory Check</h3>";
$uploadDir = 'uploads/students/';
if (is_dir($uploadDir)) {
    echo "<p style='color: green;'>✓ Upload directory exists: $uploadDir</p>";
    if (is_writable($uploadDir)) {
        echo "<p style='color: green;'>✓ Upload directory is writable</p>";
    } else {
        echo "<p style='color: red;'>✗ Upload directory is not writable</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Upload directory does not exist: $uploadDir</p>";
}

echo "<h3>Next Steps</h3>";
echo "<p>If all checks pass, try clicking the edit link above to test the edit page.</p>";
echo "<p>If there are any issues, they will be shown in the browser's developer console or error logs.</p>";
?> 