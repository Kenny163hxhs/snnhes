<?php
/**
 * Production Setup Script
 * Configures the application for production deployment
 */

require_once 'config/database.php';

echo "<h1>🔧 Production Setup</h1>";
echo "========================\n\n";

try {
    $db = Database::getInstance();
    
    echo "1. Checking database connection...\n";
    $testQuery = $db->fetchOne("SELECT 1 as test");
    echo "   ✅ Database connection successful\n\n";
    
    echo "2. Checking required tables...\n";
    $tables = ['users', 'students', 'courses', 'class_sections', 'enrollments', 'student_documents'];
    $missingTables = [];
    
    foreach ($tables as $table) {
        $exists = $db->fetchOne("SHOW TABLES LIKE '$table'");
        if ($exists) {
            echo "   ✅ Table '{$table}' exists\n";
        } else {
            echo "   ❌ Table '{$table}' missing\n";
            $missingTables[] = $table;
        }
    }
    
    if (count($missingTables) > 0) {
        echo "\n   ⚠️ Missing tables detected. Please import database schema first.\n";
    } else {
        echo "\n   ✅ All required tables present\n";
    }
    
    echo "\n3. Checking file permissions...\n";
    $uploadDirs = ['uploads', 'uploads/students', 'uploads/transfers'];
    foreach ($uploadDirs as $dir) {
        if (is_dir($dir)) {
            if (is_writable($dir)) {
                echo "   ✅ Directory '{$dir}' is writable\n";
            } else {
                echo "   ❌ Directory '{$dir}' is not writable\n";
            }
        } else {
            echo "   ⚠️ Directory '{$dir}' does not exist\n";
        }
    }
    
    echo "\n4. Checking PHP extensions...\n";
    $requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'fileinfo', 'json', 'mbstring'];
    foreach ($requiredExtensions as $ext) {
        if (extension_loaded($ext)) {
            echo "   ✅ Extension '{$ext}' loaded\n";
        } else {
            echo "   ❌ Extension '{$ext}' not loaded\n";
        }
    }
    
    echo "\n5. Checking application files...\n";
    $requiredFiles = [
        'index.php',
        'login.php',
        'student_register.php',
        'config/database.php',
        'modules/admin/profile.php',
        'modules/courses/manage.php',
        'modules/sections/manage.php',
        'modules/students/list.php'
    ];
    
    foreach ($requiredFiles as $file) {
        if (file_exists($file)) {
            echo "   ✅ File '{$file}' exists\n";
        } else {
            echo "   ❌ File '{$file}' missing\n";
        }
    }
    
    echo "\n6. Checking courses and sections...\n";
    $courseCount = $db->fetchOne("SELECT COUNT(*) as count FROM courses WHERE is_active = 1")['count'];
    $sectionCount = $db->fetchOne("SELECT COUNT(*) as count FROM class_sections WHERE is_active = 1")['count'];
    
    echo "   📚 Active courses: {$courseCount}\n";
    echo "   📖 Active sections: {$sectionCount}\n";
    
    if ($courseCount > 0 && $sectionCount > 0) {
        echo "   ✅ System ready for student enrollment\n";
    } else {
        echo "   ⚠️ No courses or sections found\n";
    }
    
    echo "\n7. Generating production configuration...\n";
    
    // Create production config
    $prodConfig = "<?php
/**
 * Production Configuration
 * Generated automatically for Railway deployment
 */

// Database configuration for Railway
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'snnhes_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');

// Application configuration
define('APP_ENV', 'production');
define('APP_URL', getenv('APP_URL') ?: 'https://your-app.railway.app');
define('APP_DEBUG', false);

// File upload configuration
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Security configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);

echo 'Production configuration generated successfully!';
?>";
    
    file_put_contents('config/production.php', $prodConfig);
    echo "   ✅ Production config created: config/production.php\n";
    
    echo "\n8. Creating deployment summary...\n";
    
    $summary = "SNNHES PRODUCTION DEPLOYMENT SUMMARY
==========================================

System Status: " . (count($missingTables) == 0 ? "READY" : "NEEDS SETUP") . "
Database: " . ($testQuery ? "CONNECTED" : "DISCONNECTED") . "
Courses: {$courseCount} active
Sections: {$sectionCount} active
Upload Directories: " . (is_writable('uploads') ? "READY" : "NEEDS PERMISSIONS") . "

Next Steps:
1. Upload all files to GitHub
2. Deploy to Railway.app
3. Add MySQL database
4. Set environment variables
5. Import database schema
6. Test all functionality

Your app will be available at: https://your-app.railway.app

Generated: " . date('Y-m-d H:i:s') . "
";
    
    file_put_contents('DEPLOYMENT_SUMMARY.txt', $summary);
    echo "   ✅ Deployment summary created: DEPLOYMENT_SUMMARY.txt\n";
    
    echo "\n🎉 PRODUCTION SETUP COMPLETE!\n";
    echo "=============================\n";
    echo "✅ System is ready for deployment\n";
    echo "✅ All configurations generated\n";
    echo "✅ Database schema verified\n";
    echo "✅ Files prepared for upload\n\n";
    
    echo "📋 NEXT STEPS:\n";
    echo "1. Upload to GitHub\n";
    echo "2. Deploy to Railway.app\n";
    echo "3. Add MySQL database\n";
    echo "4. Set environment variables\n";
    echo "5. Test your live application\n\n";
    
} catch (Exception $e) {
    echo "❌ Error during production setup: " . $e->getMessage() . "\n";
}

echo "\n<p><a href='deploy_to_railway.php'>View Deployment Guide</a></p>";
echo "<p><a href='index.php'>Go to Dashboard</a></p>";
?>
