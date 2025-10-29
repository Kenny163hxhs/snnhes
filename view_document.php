<?php
/**
 * Document Viewer
 * National High School Enrollment System
 */

session_start();
require_once 'config/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = Database::getInstance();
$documentId = intval($_GET['id'] ?? 0);

if (!$documentId) {
    die('Document ID not provided');
}

$document = $db->fetchOne("SELECT * FROM student_documents WHERE id = ?", [$documentId]);

if (!$document) {
    die('Document not found');
}

$userRole = getUserRole();
$userId = $_SESSION['user_id'];

if ($userRole === 'student') {
    $currentStudent = $db->fetchOne("SELECT id FROM students WHERE user_id = ?", [$userId]);
    if (!$currentStudent || $currentStudent['id'] != $document['student_id']) {
        redirect('index.php');
    }
}

$filePath = $document['file_path'];

// Cross-platform path handling using helper functions
if (!isFileAccessible($filePath)) {
    // Try to find a similar file in the uploads directory
    $fileName = basename($filePath);
    $uploadDirs = ['uploads/students/', 'uploads/transfers/'];
    $foundFile = null;
    
    foreach ($uploadDirs as $dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && strpos($file, $fileName) !== false) {
                    $foundFile = $dir . $file;
                    break 2;
                }
            }
        }
    }
    
    if ($foundFile) {
        $filePath = $foundFile;
    } else {
        die('Access denied: File not found. Original path: ' . htmlspecialchars($filePath));
    }
}

$fullPath = getCrossPlatformFilePath($filePath);

// Check if file exists
if (!file_exists($fullPath)) {
    die('File not found: ' . $fullPath);
}

$fileInfo = pathinfo($fullPath);
$extension = strtolower($fileInfo['extension']);

$contentTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'txt' => 'text/plain'
];

$contentType = $contentTypes[$extension] ?? 'application/octet-stream';

$isDownload = isset($_GET['download']) && $_GET['download'] == 1;

// Clear any output buffer
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($fullPath));

if ($isDownload) {
    header('Content-Disposition: attachment; filename="' . $document['file_name'] . '"');
} else {
    header('Content-Disposition: inline; filename="' . $document['file_name'] . '"');
}

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($fullPath);
exit();
?>