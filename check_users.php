<?php
/**
 * Check Users
 * This script checks what users exist in the database
 */

require_once 'config/database.php';

echo "<h2>Database Users Check</h2>";

try {
    $db = Database::getInstance();
    $result = $db->fetchAll("SHOW TABLES LIKE 'users'");
    if (empty($result)) {
        echo "<p style='color: red;'>✗ Users table does not exist</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✓ Users table exists</p>";
    $users = $db->fetchAll("SELECT id, username, email, role, first_name, last_name, is_active FROM users");
    
    if (empty($users)) {
        echo "<p style='color: orange;'>⚠ No users found in database</p>";
        echo "<h3>Create Test User</h3>";
        echo "<p>You need to create a user to log in. Here's a test user you can create:</p>";
        
        $testUser = [
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'email' => 'admin@school.com',
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'is_active' => 1
        ];
        
        try {
            $db->insert('users', $testUser);
            echo "<p style='color: green;'>✓ Test user created successfully!</p>";
            echo "<p><strong>Login Credentials:</strong></p>";
            echo "<ul>";
            echo "<li><strong>Username:</strong> admin</li>";
            echo "<li><strong>Password:</strong> admin123</li>";
            echo "<li><strong>Role:</strong> admin</li>";
            echo "</ul>";
            echo "<p><a href='login.php' class='btn btn-primary'>Go to Login Page</a></p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Failed to create test user: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p style='color: green;'>✓ Found " . count($users) . " user(s) in database</p>";
        
        echo "<h3>Existing Users:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Name</th><th>Active</th>";
        echo "</tr>";
        
        foreach ($users as $user) {
            $status = $user['is_active'] ? 'Active' : 'Inactive';
            $statusColor = $user['is_active'] ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['first_name']} {$user['last_name']}</td>";
            echo "<td style='color: $statusColor;'>$status</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Login Instructions:</h3>";
        echo "<p>Use any of the above usernames to log in. If you don't know the password, you can reset it in the database.</p>";
        echo "<p><a href='login.php' class='btn btn-primary'>Go to Login Page</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?> 