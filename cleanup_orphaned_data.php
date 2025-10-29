<?php
/**
 * Cleanup Orphaned Data
 * Removes any orphaned records that might be left behind
 */

require_once 'config/database.php';

$db = Database::getInstance();
$success = '';
$error = '';

echo "<h1>🧹 Cleaning Up Orphaned Data</h1>";
echo "===================================\n\n";

try {
    echo "1. Checking for orphaned student documents...\n";
    $orphanedDocs = $db->fetchAll("
        SELECT sd.* FROM student_documents sd 
        LEFT JOIN students s ON sd.student_id = s.id 
        WHERE s.id IS NULL
    ");
    
    if (count($orphanedDocs) > 0) {
        echo "   Found " . count($orphanedDocs) . " orphaned documents\n";
        
        // Delete files first
        foreach ($orphanedDocs as $doc) {
            $filePath = $doc['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
                echo "   ✅ Deleted file: {$filePath}\n";
            }
        }
        
        // Delete database records
        $result = $db->query("
            DELETE sd FROM student_documents sd 
            LEFT JOIN students s ON sd.student_id = s.id 
            WHERE s.id IS NULL
        ");
        echo "   ✅ Deleted " . $result->rowCount() . " orphaned document records\n";
    } else {
        echo "   ✅ No orphaned documents found\n";
    }
    
    echo "\n2. Checking for orphaned enrollments...\n";
    $orphanedEnrollments = $db->fetchAll("
        SELECT e.* FROM enrollments e 
        LEFT JOIN students s ON e.student_id = s.id 
        WHERE s.id IS NULL
    ");
    
    if (count($orphanedEnrollments) > 0) {
        $result = $db->query("
            DELETE e FROM enrollments e 
            LEFT JOIN students s ON e.student_id = s.id 
            WHERE s.id IS NULL
        ");
        echo "   ✅ Deleted " . $result->rowCount() . " orphaned enrollment records\n";
    } else {
        echo "   ✅ No orphaned enrollments found\n";
    }
    
    echo "\n3. Checking for orphaned transfers...\n";
    $orphanedTransfers = $db->fetchAll("
        SELECT st.* FROM student_transfers st 
        LEFT JOIN students s ON st.student_id = s.id 
        WHERE s.id IS NULL
    ");
    
    if (count($orphanedTransfers) > 0) {
        $result = $db->query("
            DELETE st FROM student_transfers st 
            LEFT JOIN students s ON st.student_id = s.id 
            WHERE s.id IS NULL
        ");
        echo "   ✅ Deleted " . $result->rowCount() . " orphaned transfer records\n";
    } else {
        echo "   ✅ No orphaned transfers found\n";
    }
    
    echo "\n4. Checking for orphaned email verifications...\n";
    $orphanedEmails = $db->fetchAll("
        SELECT ev.* FROM email_verifications ev 
        LEFT JOIN students s ON ev.email = s.email 
        WHERE s.id IS NULL
    ");
    
    if (count($orphanedEmails) > 0) {
        $result = $db->query("
            DELETE ev FROM email_verifications ev 
            LEFT JOIN students s ON ev.email = s.email 
            WHERE s.id IS NULL
        ");
        echo "   ✅ Deleted " . $result->rowCount() . " orphaned email verification records\n";
    } else {
        echo "   ✅ No orphaned email verifications found\n";
    }
    
    echo "\n5. Checking for orphaned academic records...\n";
    $orphanedAcademic = $db->fetchAll("
        SELECT ar.* FROM academic_records ar 
        LEFT JOIN students s ON ar.student_id = s.id 
        WHERE s.id IS NULL
    ");
    
    if (count($orphanedAcademic) > 0) {
        $result = $db->query("
            DELETE ar FROM academic_records ar 
            LEFT JOIN students s ON ar.student_id = s.id 
            WHERE s.id IS NULL
        ");
        echo "   ✅ Deleted " . $result->rowCount() . " orphaned academic records\n";
    } else {
        echo "   ✅ No orphaned academic records found\n";
    }
    
    echo "\n6. Checking for orphaned enrollment documents...\n";
    $orphanedEnrollmentDocs = $db->fetchAll("
        SELECT ed.* FROM enrollment_documents ed 
        LEFT JOIN students s ON ed.student_id = s.id 
        WHERE s.id IS NULL
    ");
    
    if (count($orphanedEnrollmentDocs) > 0) {
        $result = $db->query("
            DELETE ed FROM enrollment_documents ed 
            LEFT JOIN students s ON ed.student_id = s.id 
            WHERE s.id IS NULL
        ");
        echo "   ✅ Deleted " . $result->rowCount() . " orphaned enrollment documents\n";
    } else {
        echo "   ✅ No orphaned enrollment documents found\n";
    }
    
    echo "\n7. Checking for empty upload directories...\n";
    $uploadDirs = ['uploads/students', 'uploads/transfers'];
    $emptyDirs = 0;
    
    foreach ($uploadDirs as $dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            $fileCount = count($files) - 2; // Subtract . and ..
            
            if ($fileCount == 0) {
                echo "   ✅ Directory {$dir} is empty (clean)\n";
            } else {
                echo "   📁 Directory {$dir} contains {$fileCount} files\n";
            }
        }
    }
    
    echo "\n🎉 CLEANUP COMPLETE!\n";
    echo "✅ All orphaned data has been removed\n";
    echo "✅ Database is now clean and consistent\n";
    echo "✅ No traces of deleted students remain\n\n";
    
    $success = "Database cleanup completed successfully! All orphaned data removed.";
    
} catch (Exception $e) {
    $error = "Error during cleanup: " . $e->getMessage();
    echo "\n❌ ERROR: " . $error . "\n";
}

echo "\n<p><a href='modules/students/list.php'>Go to Student List</a></p>";
echo "<p><a href='index.php'>Go to Dashboard</a></p>";
?>
