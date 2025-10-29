<?php
/**
 * Comprehensive Path Migration Script
 * Handles migration from any platform to current platform
 */

require_once 'config/database.php';

$db = Database::getInstance();
$appRoot = dirname(__FILE__);

echo "<h2>Comprehensive Path Migration Script</h2>";

// Get all documents with absolute paths
$documents = $db->fetchAll("
    SELECT id, file_path, file_name 
    FROM student_documents 
    WHERE file_path LIKE 'C:%' OR file_path LIKE 'D:%' OR file_path LIKE '/opt/%' OR file_path LIKE '/var/%' OR file_path LIKE '/Users/%'
");

echo "<p>Found " . count($documents) . " documents with absolute paths.</p>";

$updated = 0;
$errors = 0;
$skipped = 0;

foreach ($documents as $doc) {
    $oldPath = $doc['file_path'];
    $fileName = $doc['file_name'];
    
    // Determine the relative path
    $relativePath = '';
    
    // Handle different path formats
    if (strpos($oldPath, 'uploads\\students\\') !== false) {
        $relativePath = 'uploads/students/' . $fileName;
    } elseif (strpos($oldPath, 'uploads\\transfers\\') !== false) {
        $relativePath = 'uploads/transfers/' . $fileName;
    } elseif (strpos($oldPath, 'uploads/students/') !== false) {
        $relativePath = 'uploads/students/' . $fileName;
    } elseif (strpos($oldPath, 'uploads/transfers/') !== false) {
        $relativePath = 'uploads/transfers/' . $fileName;
    } else {
        // Try to extract from absolute path using regex
        if (preg_match('/uploads[\/\\\\](students|transfers)[\/\\\\]([^\/\\\\]+)$/', $oldPath, $matches)) {
            $relativePath = 'uploads/' . $matches[1] . '/' . $matches[2];
        } else {
            echo "<p style='color: red;'>Could not determine relative path for: $oldPath</p>";
            $errors++;
            continue;
        }
    }
    
    // Check if the file exists at the original absolute path
    if (file_exists($oldPath)) {
        // Update the database with relative path
        $db->query("UPDATE student_documents SET file_path = ? WHERE id = ?", [$relativePath, $doc['id']]);
        echo "<p style='color: green;'>Updated: $oldPath → $relativePath</p>";
        $updated++;
    } else {
        // Check if file exists with the new relative path
        $newFullPath = $appRoot . '/' . $relativePath;
        if (file_exists($newFullPath)) {
            // File exists at new location, update database
            $db->query("UPDATE student_documents SET file_path = ? WHERE id = ?", [$relativePath, $doc['id']]);
            echo "<p style='color: blue;'>Found at new location: $oldPath → $relativePath</p>";
            $updated++;
        } else {
            // Check if it's a cross-platform path issue (Linux path on Windows)
            if (strpos($oldPath, '/opt/') === 0 || strpos($oldPath, '/var/') === 0 || strpos($oldPath, '/Users/') === 0) {
                // This is a Linux/macOS path on Windows - try to find the equivalent file
                $fileName = basename($oldPath);
                $possiblePaths = [
                    $appRoot . '/uploads/students/' . $fileName,
                    $appRoot . '/uploads/transfers/' . $fileName
                ];
                
                $found = false;
                foreach ($possiblePaths as $possiblePath) {
                    if (file_exists($possiblePath)) {
                        // Determine the correct relative path
                        if (strpos($possiblePath, '/uploads/students/') !== false) {
                            $relativePath = 'uploads/students/' . $fileName;
                        } elseif (strpos($possiblePath, '/uploads/transfers/') !== false) {
                            $relativePath = 'uploads/transfers/' . $fileName;
                        }
                        
                        // Update the database
                        $db->query("UPDATE student_documents SET file_path = ? WHERE id = ?", [$relativePath, $doc['id']]);
                        echo "<p style='color: purple;'>Cross-platform migration: $oldPath → $relativePath</p>";
                        $updated++;
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    echo "<p style='color: orange;'>File not found (cross-platform): $oldPath</p>";
                    $skipped++;
                }
            } else {
                echo "<p style='color: orange;'>File not found: $oldPath</p>";
                $errors++;
            }
        }
    }
}

echo "<h3>Migration Complete</h3>";
echo "<p>Updated: $updated documents</p>";
echo "<p>Errors: $errors documents</p>";
echo "<p>Skipped: $skipped documents</p>";

if ($errors > 0 || $skipped > 0) {
    echo "<p style='color: red;'>Some files could not be migrated. This is normal for cross-platform migrations.</p>";
} else {
    echo "<p style='color: green;'>All paths successfully migrated to relative format!</p>";
}
?>
