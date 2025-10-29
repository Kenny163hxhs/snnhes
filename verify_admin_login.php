<?php
/**
 * Verify Admin Login Script
 * This script verifies that the admin login credentials work correctly
 */

require_once 'config/database.php';

try {
    $db = Database::getInstance();
    
    // Test admin credentials
    $username = 'admin';
    $password = 'admin123';
    
    // Get admin user from database
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE username = ? AND is_active = 1",
        [$username]
    );
    
    if (!$user) {
        echo "❌ Admin user not found or inactive!\n";
        exit(1);
    }
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        echo "✅ Admin login credentials are working correctly!\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
        echo "Role: " . $user['role'] . "\n";
        echo "Status: " . ($user['is_active'] ? 'Active' : 'Inactive') . "\n";
    } else {
        echo "❌ Admin password verification failed!\n";
        echo "The password 'admin123' does not match the stored hash.\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?> 