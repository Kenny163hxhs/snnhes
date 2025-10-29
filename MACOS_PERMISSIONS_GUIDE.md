# macOS Permissions Fix Guide

## 🚨 Problem
When running SNNHES on macOS, you get the error:
```
Upload directory is not writable: /Applications/XAMPP/xamppfiles/htdocs/SNNHES 4/uploads/students/
```

## 🔧 Solution

### **Method 1: Automated Script (Recommended)**

1. **Open Terminal** on your MacBook Pro
2. **Navigate to your SNNHES directory**:
   ```bash
   cd "/Applications/XAMPP/xamppfiles/htdocs/SNNHES 4"
   ```
3. **Make the script executable**:
   ```bash
   chmod +x fix_macos_permissions.sh
   ```
4. **Run the script**:
   ```bash
   ./fix_macos_permissions.sh
   ```

### **Method 2: Manual Commands**

If the script doesn't work, run these commands manually:

```bash
# Navigate to your SNNHES directory
cd "/Applications/XAMPP/xamppfiles/htdocs/SNNHES 4"

# Create upload directories
mkdir -p uploads/students
mkdir -p uploads/transfers

# Set proper permissions
chmod 755 uploads
chmod 755 uploads/students
chmod 755 uploads/transfers

# Set ownership to web server user
sudo chown -R _www:_www uploads/

# Set group write permissions
chmod -R g+w uploads/
```

### **Method 3: Alternative Ownership**

If the above doesn't work, try:

```bash
# Set ownership to your user and _www group
sudo chown -R $(whoami):_www uploads/

# Or set to _www user and group
sudo chown -R _www:_www uploads/

# Set permissions
chmod -R 775 uploads/
```

## 🔍 Verification

After running the fix, verify the permissions:

```bash
ls -la uploads/
ls -la uploads/students/
ls -la uploads/transfers/
```

You should see something like:
```
drwxrwxr-x  3 _www  _www  102 Oct 20 10:30 uploads
drwxrwxr-x  2 _www  _www   68 Oct 20 10:30 students
drwxrwxr-x  2 _www  _www   68 Oct 20 10:30 transfers
```

## 🚨 Troubleshooting

### **If you get "Permission denied" errors:**

1. **Check if you're in the right directory**:
   ```bash
   pwd
   ls -la
   ```

2. **Check current ownership**:
   ```bash
   ls -la uploads/
   ```

3. **Try with sudo**:
   ```bash
   sudo chown -R _www:_www uploads/
   sudo chmod -R 775 uploads/
   ```

### **If XAMPP is not running as _www:**

1. **Check XAMPP user**:
   ```bash
   ps aux | grep httpd
   ```

2. **Set ownership to the correct user** (replace `_www` with the actual user):
   ```bash
   sudo chown -R [actual_user]:[actual_group] uploads/
   ```

## 🎯 Expected Results

After fixing permissions:
- ✅ No more "Upload directory is not writable" errors
- ✅ Student registration with selfie capture works
- ✅ Document uploads work
- ✅ Image viewing works across all platforms

## 📋 Platform-Specific Notes

### **macOS (XAMPP)**
- Default web server user: `_www`
- Default web server group: `_www`
- Permissions: `775` (rwxrwxr-x)

### **Linux (LAMP)**
- Default web server user: `www-data`
- Default web server group: `www-data`
- Permissions: `775` (rwxrwxr-x)

### **Windows (XAMPP)**
- No permission issues (Windows handles this automatically)
- Permissions: `777` (rwxrwxrwx)

## 🔄 Cross-Platform Compatibility

The system now automatically:
1. **Detects the operating system**
2. **Sets appropriate permissions**
3. **Handles ownership correctly**
4. **Provides helpful error messages**

## 🎉 Success!

Once permissions are fixed, the SNNHES system will work perfectly on macOS with full cross-platform compatibility!
