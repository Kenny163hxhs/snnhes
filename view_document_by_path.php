<?php
/**
 * Document Viewer by File Path
 * National High School Enrollment System
 */

session_start();
require_once 'config/database.php';

// Get the file path from the URL parameter
$filePath = $_GET['path'] ?? '';

if (empty($filePath)) {
    die('File path not provided');
}

// Decode the URL-encoded path
$filePath = urldecode($filePath);

// Cross-platform path handling using helper functions
if (!isFileAccessible($filePath)) {
    die('Access denied: Invalid file path or file not found');
}

$fullPath = getCrossPlatformFilePath($filePath);

// Check if file exists
if (!file_exists($fullPath)) {
    die('File not found: ' . $fullPath . ' (Original: ' . $filePath . ')');
}

// Get file information
$fileInfo = pathinfo($fullPath);
$extension = strtolower($fileInfo['extension'] ?? '');

// Set content types
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

// Clear any output buffer
while (ob_get_level()) {
    ob_end_clean();
}

// Set headers
header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($fullPath));
header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output the file
readfile($fullPath);
exit();
?>
