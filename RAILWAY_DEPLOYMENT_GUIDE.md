# 🚀 Railway Deployment Guide for SNNHES

## Quick Deployment Steps

### Step 1: Create Railway Account
1. Go to **https://railway.app/**
2. Click **"Sign Up"**
3. **Sign up with GitHub** (recommended)
4. **Verify your email**

### Step 2: Prepare Your Repository
1. **Create a GitHub repository** for your SNNHES project
2. **Upload all files** to the repository
3. **Make sure these files are included**:
   - `railway.json`
   - `composer.json`
   - `Procfile`
   - `config/database_railway.php`
   - All your PHP files

### Step 3: Deploy to Railway
1. **Login to Railway**
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. **Choose your SNNHES repository**
5. Click **"Deploy"**

### Step 4: Add MySQL Database
1. **In your Railway project dashboard**
2. Click **"+ New"**
3. Select **"Database"** → **"MySQL"**
4. **Wait for database to be created**
5. **Note down the connection details**

### Step 5: Configure Environment Variables
1. **Go to your project settings**
2. **Add these environment variables**:
   - `DATABASE_URL` (Railway will provide this)
   - `APP_NAME=SNNHES`
   - `APP_ENV=production`

### Step 6: Update Database Configuration
1. **Railway will automatically use `database_railway.php`**
2. **The database connection will be configured automatically**

### Step 7: Import Database Schema
1. **Go to your Railway MySQL database**
2. **Open the database in a MySQL client**
3. **Import `database/snnhes_db.sql`**

### Step 8: Test Your Deployment
1. **Visit your Railway URL** (e.g., `https://snnhes-production.up.railway.app`)
2. **Test student registration**
3. **Test admin login** (admin/admin123)

## Railway Advantages
- ✅ **Automatic scaling**
- ✅ **Built-in database**
- ✅ **Easy deployment**
- ✅ **Free tier available**
- ✅ **No server management**

## Troubleshooting
- **If deployment fails**: Check the logs in Railway dashboard
- **If database connection fails**: Verify environment variables
- **If files are missing**: Check GitHub repository

## Your Railway URL
After deployment, your site will be available at:
`https://your-project-name.up.railway.app`

## Default Admin Login
- **Username**: admin
- **Password**: admin123
- **⚠️ Change this after first login!**
