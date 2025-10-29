<?php
/**
 * List Files for GitHub Upload
 * Shows all files that need to be uploaded to GitHub
 */

echo "<h1>📁 Files to Upload to GitHub</h1>";
echo "===================================\n\n";

echo "🎯 ESSENTIAL FILES (Must Upload):\n";
echo "==================================\n\n";

// Root directory files
$rootFiles = [
    'index.php',
    'login.php',
    'logout.php',
    'student_register.php',
    'check_enrollment.php',
    'check_users.php',
    'verify_admin_login.php',
    'view_document.php',
    'view_document_by_path.php',
    'final_test.php',
    'debug_edit.php',
    'test_db.php',
    'test_profile.php',
    'test_student_persistence.php',
    'test_success_alert.php',
    'migrate_paths.php',
    'migrate_paths_comprehensive.php',
    'fix_permissions.php',
    'install.php',
    'composer.json',
    'Procfile',
    'railway.json',
    'nixpacks.toml',
    '.gitignore',
    'README.md',
    'DEPLOYMENT_INSTRUCTIONS.md',
    'GITHUB_UPLOAD_GUIDE.md'
];

echo "📄 Root Directory Files:\n";
foreach ($rootFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ {$file}\n";
    } else {
        echo "   ❌ {$file} (missing)\n";
    }
}

echo "\n📂 Essential Folders:\n";
$essentialFolders = [
    'assets',
    'config',
    'database',
    'includes',
    'modules',
    'uploads',
    'api'
];

foreach ($essentialFolders as $folder) {
    if (is_dir($folder)) {
        $fileCount = count(glob($folder . '/*'));
        echo "   ✅ {$folder}/ ({$fileCount} files)\n";
    } else {
        echo "   ❌ {$folder}/ (missing)\n";
    }
}

echo "\n📋 Documentation Files:\n";
$docFiles = [
    'COURSE_MANAGEMENT_FIX.md',
    'MIGRATION_SUCCESS.md',
    'SELFIE_INSTRUCTIONS.md',
    'STUDENT_REGISTRATION_GUIDE.md',
    'TRANSFER_GUIDE.md',
    'TRANSFER_PROCESS.md',
    'MACOS_PERMISSIONS_GUIDE.md',
    'CROSS_PLATFORM_GUIDE.md',
    'EMAIL_VERIFICATION_GUIDE.md',
    'DEPLOYMENT_GUIDE.md'
];

foreach ($docFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ {$file}\n";
    } else {
        echo "   ⚠️ {$file} (optional)\n";
    }
}

echo "\n🧪 Test Files (Optional - Can Skip):\n";
$testFiles = [
    'test_permanent_deletion.php',
    'cleanup_orphaned_data.php',
    'update_max_capacity_500.php',
    'fix_sections_issue.php',
    'production_setup.php',
    'deploy_to_railway.php',
    'deployment_test.php',
    'fix_gd_issue.php',
    'debug_courses.php',
    'fix_courses_issue.php',
    'clear_course_cache.php',
    'refresh_courses.php',
    'explain_course_units_vs_capacity.php',
    'max_capacity_500_summary.php',
    'otp_display.php',
    'test_email.php',
    'test_simple_email.php',
    'test_otp_system.php',
    'debug_otp.php',
    'test_fixed_otp.php',
    'test_complete_flow.php',
    'test_otp_class.php',
    'debug_form_submission.php',
    'test_form_preservation.php',
    'test_no_email_verification.php',
    'quick_email_test.php',
    'install_phpmailer.php',
    'setup_email_verification.php'
];

$testFileCount = 0;
foreach ($testFiles as $file) {
    if (file_exists($file)) {
        echo "   📄 {$file}\n";
        $testFileCount++;
    }
}

echo "\n📊 UPLOAD SUMMARY:\n";
echo "==================\n";

$totalFiles = 0;
$missingFiles = 0;

// Count root files
foreach ($rootFiles as $file) {
    if (file_exists($file)) {
        $totalFiles++;
    } else {
        $missingFiles++;
    }
}

// Count folder files
foreach ($essentialFolders as $folder) {
    if (is_dir($folder)) {
        $files = glob($folder . '/*');
        $totalFiles += count($files);
    }
}

echo "✅ Total files to upload: {$totalFiles}\n";
echo "❌ Missing files: {$missingFiles}\n";
echo "📄 Test files (optional): {$testFileCount}\n";

echo "\n🎯 UPLOAD INSTRUCTIONS:\n";
echo "=======================\n";
echo "1. Go to github.com and create new repository\n";
echo "2. Click 'uploading an existing file'\n";
echo "3. Select ALL files and folders listed above\n";
echo "4. Add commit message: 'Initial commit - SNNHES system'\n";
echo "5. Click 'Commit changes'\n\n";

echo "⚠️ IMPORTANT:\n";
echo "=============\n";
echo "• Upload ALL files from root directory\n";
echo "• Upload ALL folders (assets, config, database, etc.)\n";
echo "• Make sure uploads/ folder is included (even if empty)\n";
echo "• Don't forget composer.json and deployment files\n\n";

echo "🚀 After upload, go to railway.app to deploy!\n";
?>
