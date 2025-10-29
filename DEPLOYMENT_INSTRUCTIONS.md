# 🚀 SNNHES Deployment to Railway.app

## 📋 Quick Start Guide

### Step 1: Upload to GitHub (5 minutes)
1. Go to [github.com](https://github.com) and sign in
2. Click "+" → "New repository"
3. Name it `snnhes` (or any name you prefer)
4. Click "Create repository"
5. Click "uploading an existing file"
6. Drag and drop ALL your SNNHES files
7. Add commit message: "Initial commit"
8. Click "Commit changes"

### Step 2: Deploy to Railway (10 minutes)
1. Go to [railway.app](https://railway.app)
2. Sign up with your GitHub account
3. Click "Deploy from GitHub repo"
4. Select your `snnhes` repository
5. Railway will automatically deploy your app

### Step 3: Add MySQL Database (5 minutes)
1. In Railway dashboard, click "+ New" → "Database" → "MySQL"
2. Wait for database to be created
3. Click on your database to get credentials
4. Copy the connection details

### Step 4: Configure Environment Variables (3 minutes)
1. Go to your app's settings in Railway
2. Click "Variables" tab
3. Add these environment variables:
   ```
   DB_HOST=your-railway-mysql-host
   DB_NAME=your-railway-database-name
   DB_USER=your-railway-username
   DB_PASS=your-railway-password
   DB_PORT=3306
   APP_ENV=production
   APP_URL=https://your-app.railway.app
   ```

### Step 5: Import Database Schema (5 minutes)
1. Go to your MySQL database in Railway
2. Click "Connect" → "MySQL Console"
3. Copy and paste the contents of `database/snnhes_db.sql`
4. Execute the SQL to create all tables
5. Verify tables were created

### Step 6: Test Your Application (2 minutes)
1. Visit your Railway app URL
2. Test student registration
3. Test admin login (default: admin/admin123)
4. Verify all features work

## 📁 Files Included in Deployment

### Core Application Files
- `index.php` - Main dashboard
- `login.php` - Login system
- `student_register.php` - Student registration
- `logout.php` - Logout functionality

### Configuration Files
- `config/database.php` - Database configuration
- `config/production.php` - Production settings
- `composer.json` - PHP dependencies

### Database Files
- `database/snnhes_db.sql` - Complete database schema
- `database/fix_course_id_nullable.sql` - Database fixes
- `database/update_selfie_support.sql` - Selfie support

### Module Files
- `modules/admin/` - Admin panel
- `modules/courses/` - Course management
- `modules/sections/` - Section management
- `modules/students/` - Student management
- `modules/enrollment/` - Enrollment system

### Assets
- `assets/css/style.css` - Styling
- `assets/js/script.js` - JavaScript functionality

### Upload Directories
- `uploads/students/` - Student documents
- `uploads/transfers/` - Transfer documents

### Deployment Configuration
- `railway.json` - Railway deployment config
- `Procfile` - Process configuration
- `nixpacks.toml` - Build configuration
- `.gitignore` - Git ignore rules

## 🔧 Production Configuration

### Environment Variables Required
```bash
DB_HOST=your-mysql-host
DB_NAME=your-database-name
DB_USER=your-username
DB_PASS=your-password
DB_PORT=3306
APP_ENV=production
APP_URL=https://your-app.railway.app
```

### Database Tables Created
- `users` - System users (admin, registrar, teacher)
- `students` - Student information
- `courses` - Available courses
- `class_sections` - Class sections
- `enrollments` - Student enrollments
- `student_documents` - Uploaded documents
- `student_transfers` - Transfer records
- `academic_records` - Academic performance
- `enrollment_documents` - Enrollment files

## 🎯 System Features

### Student Registration
- Online registration form
- Document upload (birth certificate, selfie)
- Course selection
- Section assignment
- Email validation

### Admin Panel
- Course management
- Section management
- Student management
- Enrollment processing
- Document viewing
- Transfer management

### Available Courses
1. **STEM** - Science, Technology, Engineering, Mathematics
2. **ABM** - Accountancy, Business, Management
3. **HUMSS** - Humanities and Social Sciences
4. **TVL-ICT** - Information and Communications Technology
5. **TVL-HE** - Home Economics
6. **TVL-EPAS** - Education, Public Administration, Social Services

## 📊 System Capacity
- **Total Courses**: 6 active courses
- **Total Sections**: 12 sections (2 per course)
- **Student Capacity**: 6,000 students (500 per section)
- **Academic Year**: 2025-2026

## 🔐 Default Login Credentials
- **Admin**: admin / admin123
- **Registrar**: registrar / registrar123
- **Teacher**: teacher / teacher123

## ⚠️ Important Notes
- Change default passwords after deployment
- Configure email settings for notifications
- Set up file upload limits
- Enable HTTPS in production
- Regular database backups recommended

## 🆘 Troubleshooting

### Common Issues
1. **Database Connection Error**: Check environment variables
2. **File Upload Issues**: Verify upload directory permissions
3. **Email Not Sending**: Configure SMTP settings
4. **Page Not Loading**: Check Railway deployment logs

### Support Resources
- [Railway Documentation](https://docs.railway.app)
- [PHP Documentation](https://php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## 🎉 Success!
Your SNNHES system will be live at: `https://your-app.railway.app`

Total deployment time: ~25 minutes
Total cost: Free (Railway free tier)
