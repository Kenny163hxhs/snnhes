#!/bin/bash

echo "=== XAMPP Status Check and Start Script ==="
echo ""

# Check if XAMPP is already running
echo "Checking XAMPP status..."
/opt/lampp/lampp status

echo ""
echo "Attempting to start XAMPP..."
sudo /opt/lampp/lampp start

echo ""
echo "Waiting 5 seconds for services to start..."
sleep 5

echo ""
echo "Checking status again..."
/opt/lampp/lampp status

echo ""
echo "Testing database connection..."
php test_db.php

echo ""
echo "=== Script completed ==="
echo "If you see any errors, please check:"
echo "1. XAMPP installation: /opt/lampp/"
echo "2. Database configuration: config/database.php"
echo "3. Upload directory permissions: uploads/students/" 