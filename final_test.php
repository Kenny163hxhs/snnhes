<?php
/**
 * Final Document Viewer Test
 */

require_once 'config/database.php';

$db = Database::getInstance();
$documentId = 19;

// Get document
$document = $db->fetchOne("SELECT * FROM student_documents WHERE id = ?", [$documentId]);

if (!$document) {
    die('Document not found');
}

$filePath = $document['file_path'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Document Viewer Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        img { max-width: 300px; border: 2px solid #ccc; margin: 10px 0; }
        .test-link { display: inline-block; margin: 10px; padding: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .test-link:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Final Document Viewer Test</h1>
    
    <div class="test-section">
        <h3>Document Information</h3>
        <p><strong>ID:</strong> <?= $document['id'] ?></p>
        <p><strong>File Path:</strong> <?= htmlspecialchars($filePath) ?></p>
        <p><strong>File Name:</strong> <?= htmlspecialchars($document['file_name']) ?></p>
        <p><strong>Document Type:</strong> <?= htmlspecialchars($document['document_type']) ?></p>
    </div>
    
    <div class="test-section">
        <h3>Test Methods</h3>
        
        <h4>1. Direct File Access</h4>
        <a href="<?= htmlspecialchars($filePath) ?>" target="_blank" class="test-link">Direct File Link</a>
        <br>
        <img src="<?= htmlspecialchars($filePath) ?>" alt="Direct file access" style="max-width: 200px;">
        
        <h4>2. Document Viewer by Path</h4>
        <a href="view_document_by_path.php?path=<?= urlencode($filePath) ?>" target="_blank" class="test-link">View by Path</a>
        <br>
        <img src="view_document_by_path.php?path=<?= urlencode($filePath) ?>" alt="Document viewer by path" style="max-width: 200px;">
        
        <h4>3. Document Viewer by ID</h4>
        <a href="view_document.php?id=<?= $documentId ?>" target="_blank" class="test-link">View by ID</a>
        <br>
        <img src="view_document.php?id=<?= $documentId ?>" alt="Document viewer by ID" style="max-width: 200px;">
    </div>
    
    <div class="test-section">
        <h3>Debug Information</h3>
        <?php
        $fullPath = getCrossPlatformFilePath($filePath);
        $isAccessible = isFileAccessible($filePath);
        $fileExists = file_exists($fullPath);
        ?>
        <p><strong>Full Path:</strong> <?= htmlspecialchars($fullPath) ?></p>
        <p><strong>File Exists:</strong> <?= $fileExists ? 'Yes' : 'No' ?></p>
        <p><strong>Is Accessible:</strong> <?= $isAccessible ? 'Yes' : 'No' ?></p>
        <?php if ($fileExists): ?>
            <p><strong>File Size:</strong> <?= filesize($fullPath) ?> bytes</p>
            <p><strong>File Permissions:</strong> <?= substr(sprintf('%o', fileperms($fullPath)), -4) ?></p>
        <?php endif; ?>
    </div>
    
    <div class="test-section">
        <h3>Instructions</h3>
        <p>1. <strong>Direct File Access</strong> should show the selfie image</p>
        <p>2. <strong>Document Viewer by Path</strong> should show the selfie image</p>
        <p>3. <strong>Document Viewer by ID</strong> should show the selfie image</p>
        <p>If any of the images above show as broken or blank, there's still an issue with the document viewers.</p>
    </div>
</body>
</html>

