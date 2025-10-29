<?php
/**
 * Deployment Test Script
 * Run this to check if your system is ready for deployment
 */

echo "🚀 SNNHES Deployment Readiness Test\n";
echo "====================================\n\n";

$checks = [];
$allPassed = true;

// Check 1: Core files exist
echo "1. Checking core files...\n";
$coreFiles = [
    'index.php',
    'login.php', 
    'student_register.php',
    'config/database.php',
    'modules/admin/profile.php',
    'modules/courses/manage.php',
    'modules/sections/manage.php',
    'modules/students/register.php'
];

foreach ($coreFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ $file\n";
        $checks[] = "Core file: $file";
    } else {
        echo "   ❌ $file - MISSING!\n";
        $allPassed = false;
    }
}

// Check 2: Database files
echo "\n2. Checking database files...\n";
$dbFiles = [
    'database/snnhes_db.sql',
    'database/create_email_verification_table.sql',
    'database/fix_course_id_nullable.sql'
];

foreach ($dbFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ $file\n";
        $checks[] = "Database file: $file";
    } else {
        echo "   ❌ $file - MISSING!\n";
        $allPassed = false;
    }
}

// Check 3: Upload directories
echo "\n3. Checking upload directories...\n";
$uploadDirs = [
    'uploads',
    'uploads/students',
    'uploads/transfers'
];

foreach ($uploadDirs as $dir) {
    if (is_dir($dir)) {
        echo "   ✅ $dir/\n";
        $checks[] = "Upload directory: $dir";
    } else {
        echo "   ⚠️  $dir/ - Will be created automatically\n";
    }
}

// Check 4: PHP extensions
echo "\n4. Checking PHP extensions...\n";
$extensions = ['pdo', 'pdo_mysql', 'gd', 'fileinfo', 'json'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext\n";
        $checks[] = "PHP extension: $ext";
    } else {
        echo "   ❌ $ext - REQUIRED!\n";
        $allPassed = false;
    }
}

// Check 5: File permissions
echo "\n5. Checking file permissions...\n";
$writableDirs = ['uploads', 'uploads/students', 'uploads/transfers'];
foreach ($writableDirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "   ✅ $dir/ is writable\n";
            $checks[] = "Writable directory: $dir";
        } else {
            echo "   ⚠️  $dir/ needs write permissions\n";
        }
    }
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 DEPLOYMENT READINESS SUMMARY\n";
echo str_repeat("=", 50) . "\n";

if ($allPassed) {
    echo "🎉 ALL CHECKS PASSED!\n";
    echo "✅ Your system is ready for deployment!\n\n";
    
    echo "📋 Next Steps:\n";
    echo "1. Upload all files to GitHub\n";
    echo "2. Deploy to Railway\n";
    echo "3. Set up MySQL database\n";
    echo "4. Import database schema\n";
    echo "5. Test your application\n\n";
    
    echo "⏱️  Estimated time: 20 minutes\n";
    echo "🚀 You're ready to go live!\n";
} else {
    echo "❌ SOME CHECKS FAILED!\n";
    echo "⚠️  Please fix the issues above before deploying.\n\n";
    
    echo "🔧 Common fixes:\n";
    echo "- Create missing files\n";
    echo "- Install required PHP extensions\n";
    echo "- Set proper file permissions\n";
}

echo "\n📁 Files to upload to GitHub:\n";
echo "- All .php files\n";
echo "- All .sql files in database/\n";
echo "- All .css and .js files in assets/\n";
echo "- All .md files (documentation)\n";
echo "- composer.json\n";

echo "\n🎯 Your SNNHES system is ready for the world! 🌍\n";
?>
