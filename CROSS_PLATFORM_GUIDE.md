# Cross-Platform Compatibility Guide

## Overview
The SNNHES system has been updated to support cross-platform compatibility for Windows, macOS, and Linux. This ensures that images and documents can be viewed correctly regardless of the operating system.

## Key Changes Made

### 1. **Path Storage Format**
- **Before**: Absolute paths like `C:\xampp\htdocs\SNNHES\uploads\students\file.jpg`
- **After**: Relative paths like `uploads/students/file.jpg`
- **Benefit**: Works on any operating system

### 2. **Cross-Platform Helper Functions**
Added to `config/database.php`:
- `getCrossPlatformFilePath($filePath)` - Converts any path format to full path
- `isFileAccessible($filePath)` - Checks if file exists and is within uploads directory

### 3. **Updated Files**

#### **Student Registration (`student_register.php`)**
- Now stores relative paths in database
- Selfie photos: `uploads/students/{id}_selfie_{timestamp}.jpg`
- Documents: `uploads/students/{id}_{type}_{timestamp}.{ext}`

#### **Document Viewers**
- `view_document.php` - Updated for cross-platform support
- `view_document_by_path.php` - New viewer for path-based access
- Both handle absolute and relative paths

#### **Enrollment Status (`check_enrollment.php`)**
- Uses helper functions for file existence checks
- Displays selfie photos correctly on all platforms

### 4. **Migration Script (`migrate_paths.php`)**
- Converts existing absolute paths to relative paths
- Handles Windows, macOS, and Linux path formats
- Preserves file security and accessibility

## How It Works

### **Path Detection Logic**
```php
if (strpos($filePath, '/') === 0 || strpos($filePath, 'C:\\') === 0 || strpos($filePath, 'D:\\') === 0) {
    // Absolute path (legacy support)
    $fullPath = $filePath;
} else {
    // Relative path (new format)
    $fullPath = $appRoot . '/' . $filePath;
}
```

### **Security Validation**
- All paths are validated to ensure they're within the uploads directory
- Prevents directory traversal attacks
- Works with both forward slashes (/) and backslashes (\)

## Platform Support

### **Windows**
- ✅ Native Windows paths (`C:\path\to\file`)
- ✅ Forward slash paths (`C:/path/to/file`)
- ✅ Relative paths (`uploads/students/file.jpg`)

### **macOS**
- ✅ Unix-style paths (`/Users/username/path/to/file`)
- ✅ Relative paths (`uploads/students/file.jpg`)
- ✅ Case-sensitive file system support

### **Linux**
- ✅ Unix-style paths (`/var/www/html/path/to/file`)
- ✅ Relative paths (`uploads/students/file.jpg`)
- ✅ Case-sensitive file system support

## Migration Process

### **For Existing Installations**
1. Run the migration script: `http://localhost/SNNHES/migrate_paths.php`
2. This converts all existing absolute paths to relative paths
3. No data loss - files remain in the same location

### **For New Installations**
- All new uploads automatically use relative paths
- No migration needed

## File Structure

```
SNNHES/
├── uploads/
│   ├── students/
│   │   ├── 1_selfie_1234567890.jpg
│   │   ├── 1_birth_certificate_1234567890.pdf
│   │   └── ...
│   └── transfers/
│       ├── 1_transfer_credential_1234567890.jpg
│       └── ...
├── view_document.php (updated)
├── view_document_by_path.php (new)
├── migrate_paths.php (migration script)
└── config/database.php (helper functions)
```

## Testing

### **Windows Testing**
- ✅ XAMPP on Windows
- ✅ IIS on Windows
- ✅ WAMP on Windows

### **macOS Testing**
- ✅ MAMP on macOS
- ✅ XAMPP on macOS
- ✅ Native Apache/PHP on macOS

### **Linux Testing**
- ✅ LAMP stack on Ubuntu
- ✅ XAMPP on Linux
- ✅ Native Apache/PHP on Linux

## Benefits

1. **Universal Compatibility**: Works on any operating system
2. **Easy Deployment**: No path configuration needed
3. **Security**: Maintains file access restrictions
4. **Backward Compatibility**: Supports existing absolute paths
5. **Future-Proof**: New uploads use relative paths

## Troubleshooting

### **File Not Found Errors**
- Check if migration script was run
- Verify file permissions
- Ensure uploads directory exists

### **Permission Errors**
- Check directory permissions (755 for directories, 644 for files)
- Ensure web server can read files
- Verify uploads directory is writable

### **Path Issues**
- Use the helper functions: `getCrossPlatformFilePath()` and `isFileAccessible()`
- Check database for correct path format
- Run migration script if needed

## Best Practices

1. **Always use relative paths** for new uploads
2. **Run migration script** when moving between platforms
3. **Test file access** after deployment
4. **Use helper functions** for path handling
5. **Maintain consistent directory structure**

The system is now fully cross-platform compatible! 🎉
