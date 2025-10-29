#!/bin/bash
# Fix macOS Permissions for SNNHES Upload Directory
# Run this script on macOS to fix upload directory permissions

echo "🔧 Fixing macOS Permissions for SNNHES Upload Directory"
echo "=================================================="

# Get the current directory (should be SNNHES root)
CURRENT_DIR=$(pwd)
UPLOADS_DIR="$CURRENT_DIR/uploads"

echo "📁 Current directory: $CURRENT_DIR"
echo "📁 Uploads directory: $UPLOADS_DIR"

# Check if uploads directory exists
if [ ! -d "$UPLOADS_DIR" ]; then
    echo "❌ Uploads directory not found. Creating it..."
    mkdir -p "$UPLOADS_DIR"
    mkdir -p "$UPLOADS_DIR/students"
    mkdir -p "$UPLOADS_DIR/transfers"
    echo "✅ Uploads directory created"
else
    echo "✅ Uploads directory exists"
fi

# Create subdirectories if they don't exist
mkdir -p "$UPLOADS_DIR/students"
mkdir -p "$UPLOADS_DIR/transfers"

# Set proper permissions
echo "🔐 Setting permissions..."

# Set directory permissions (755 = rwxr-xr-x)
chmod 755 "$UPLOADS_DIR"
chmod 755 "$UPLOADS_DIR/students"
chmod 755 "$UPLOADS_DIR/transfers"

# Set ownership to current user and _www group (for XAMPP)
echo "👤 Setting ownership..."
sudo chown -R $(whoami):_www "$UPLOADS_DIR"

# Alternative: Set ownership to _www:_www (if running as web server)
# sudo chown -R _www:_www "$UPLOADS_DIR"

# Set group write permissions
chmod -R g+w "$UPLOADS_DIR"

echo "✅ Permissions set successfully!"
echo ""
echo "📋 Directory permissions:"
ls -la "$UPLOADS_DIR"
echo ""
echo "📋 Students directory permissions:"
ls -la "$UPLOADS_DIR/students"
echo ""
echo "📋 Transfers directory permissions:"
ls -la "$UPLOADS_DIR/transfers"
echo ""
echo "🎉 macOS permissions fixed! The upload directory should now be writable."
echo ""
echo "💡 If you still get permission errors, try:"
echo "   sudo chown -R _www:_www $UPLOADS_DIR"
echo "   sudo chmod -R 775 $UPLOADS_DIR"
