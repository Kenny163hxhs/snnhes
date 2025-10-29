# SNNHES Deployment Guide

## 🚀 Quick Deployment Steps

### Step 1: Upload to GitHub (5 minutes)
1. Go to [github.com](https://github.com) and sign in
2. Click "+" → "New repository"
3. Name it `snnhes` (or any name you prefer)
4. Click "Create repository"
5. Click "uploading an existing file"
6. Drag and drop ALL your SNNHES files (including the new ones)
7. Add commit message: "Initial commit"
8. Click "Commit changes"

### Step 2: Deploy to Railway (10 minutes)
1. Go to [railway.app](https://railway.app)
2. Sign up with your GitHub account
3. Click "Deploy from GitHub repo"
4. Select your `snnhes` repository
5. Railway will automatically deploy your app

### Step 3: Set Up Database (5 minutes)
1. In Railway dashboard, click "+ New" → "Database" → "MySQL"
2. Copy the database credentials
3. Add these environment variables in your Railway project:
   ```
   DB_HOST=your_mysql_host
   DB_NAME=your_database_name
   DB_USER=your_username
   DB_PASS=your_password
   ```
4. Import your database schema from `database/snnhes_db.sql`

### Step 4: Test (2 minutes)
1. Get your app URL from Railway dashboard
2. Visit the URL to test your application

## 📋 Files Ready for Deployment

### Core Application Files:
- ✅ `index.php` - Main dashboard
- ✅ `login.php` - Admin login
- ✅ `student_register.php` - Student registration
- ✅ `config/database.php` - Database configuration
- ✅ `modules/` - All admin modules
- ✅ `assets/` - CSS and JavaScript files

### Database Files:
- ✅ `database/snnhes_db.sql` - Main database schema
- ✅ `database/create_email_verification_table.sql` - Email verification table
- ✅ `database/fix_course_id_nullable.sql` - Course management fixes

### New Features Added:
- ✅ Course management (add/edit/delete courses)
- ✅ Section management (max 500 students)
- ✅ Student registration system
- ✅ File upload system for documents
- ✅ Selfie capture functionality

## 🔧 Environment Variables Needed

Add these to your Railway project settings:

```
DB_HOST=your_mysql_host
DB_NAME=your_database_name
DB_USER=your_username
DB_PASS=your_password
```

## 📁 File Structure for Upload

Make sure to upload these directories and files:
```
snnhes/
├── assets/
├── config/
├── database/
├── includes/
├── modules/
├── uploads/
├── api/
├── index.php
├── login.php
├── student_register.php
├── composer.json
└── README.md
```

## ⚠️ Important Notes

1. **Database**: Make sure to import `snnhes_db.sql` first
2. **File Permissions**: Railway handles this automatically
3. **Email**: Currently uses testing mode - configure SMTP for production
4. **Uploads**: The `uploads/` folder will be created automatically

## 🎯 After Deployment

1. Test the admin login
2. Test student registration
3. Test course management
4. Test file uploads
5. Configure email settings if needed

## 🆘 Troubleshooting

- **Database Connection**: Check environment variables
- **File Uploads**: Ensure `uploads/` folder exists
- **Email**: Configure SMTP settings for production
- **Permissions**: Railway handles this automatically

## 📞 Support

If you encounter any issues:
1. Check Railway logs
2. Verify database connection
3. Check file permissions
4. Review error logs

---

**Total Time Needed: ~20 minutes**
**The technical preparation is done - now you just need to follow the steps to get it online!**
