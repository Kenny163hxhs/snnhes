<?php
/**
 * Deploy to Railway.app
 * Prepares and deploys SNNHES to Railway
 */

echo "<h1>🚀 Deploying SNNHES to Railway.app</h1>";
echo "==========================================\n\n";

echo "📋 DEPLOYMENT CHECKLIST:\n";
echo "========================\n\n";

echo "✅ Step 1: Prepare Files for Upload\n";
echo "   • All PHP files are ready\n";
echo "   • Database schema is prepared\n";
echo "   • Configuration files are set\n";
echo "   • Upload directories are created\n\n";

echo "✅ Step 2: Create Railway Account\n";
echo "   • Go to: https://railway.app\n";
echo "   • Sign up with GitHub account\n";
echo "   • Connect your repository\n\n";

echo "✅ Step 3: Deploy Application\n";
echo "   • Click 'Deploy from GitHub repo'\n";
echo "   • Select your SNNHES repository\n";
echo "   • Railway will auto-deploy\n\n";

echo "✅ Step 4: Add MySQL Database\n";
echo "   • Click '+ New' → 'Database' → 'MySQL'\n";
echo "   • Copy database credentials\n";
echo "   • Add environment variables\n\n";

echo "✅ Step 5: Configure Environment\n";
echo "   • Add these variables in Railway:\n";
echo "     - DB_HOST: (from Railway MySQL)\n";
echo "     - DB_NAME: (from Railway MySQL)\n";
echo "     - DB_USER: (from Railway MySQL)\n";
echo "     - DB_PASS: (from Railway MySQL)\n";
echo "     - DB_PORT: (from Railway MySQL)\n\n";

echo "✅ Step 6: Import Database\n";
echo "   • Use Railway's MySQL console\n";
echo "   • Import database/snnhes_db.sql\n";
echo "   • Run any migration scripts\n\n";

echo "🎯 QUICK DEPLOYMENT STEPS:\n";
echo "==========================\n\n";

echo "1. 📁 Upload to GitHub:\n";
echo "   • Create new repository: 'snnhes'\n";
echo "   • Upload all files from your project\n";
echo "   • Commit and push to main branch\n\n";

echo "2. 🚀 Deploy to Railway:\n";
echo "   • Go to railway.app\n";
echo "   • Click 'Deploy from GitHub'\n";
echo "   • Select 'snnhes' repository\n";
echo "   • Wait for deployment to complete\n\n";

echo "3. 🗄️ Setup Database:\n";
echo "   • Add MySQL database in Railway\n";
echo "   • Copy connection details\n";
echo "   • Update environment variables\n";
echo "   • Import database schema\n\n";

echo "4. ✅ Test Your App:\n";
echo "   • Visit your Railway URL\n";
echo "   • Test student registration\n";
echo "   • Test admin login\n";
echo "   • Verify all features work\n\n";

echo "🔧 PRODUCTION CONFIGURATION:\n";
echo "============================\n\n";

echo "📝 Environment Variables to Set:\n";
echo "DB_HOST=your-railway-mysql-host\n";
echo "DB_NAME=your-railway-database-name\n";
echo "DB_USER=your-railway-username\n";
echo "DB_PASS=your-railway-password\n";
echo "DB_PORT=3306\n";
echo "APP_ENV=production\n";
echo "APP_URL=https://your-app.railway.app\n\n";

echo "📁 Files to Upload to GitHub:\n";
echo "• All .php files\n";
echo "• database/ folder with .sql files\n";
echo "• assets/ folder with CSS/JS\n";
echo "• uploads/ folder (empty but present)\n";
echo "• config/ folder\n";
echo "• modules/ folder\n";
echo "• includes/ folder\n";
echo "• composer.json\n";
echo "• .gitignore\n";
echo "• README.md\n\n";

echo "⚠️ IMPORTANT NOTES:\n";
echo "==================\n";
echo "• Make sure uploads/ folder is writable\n";
echo "• Test all file uploads work\n";
echo "• Verify email sending (if configured)\n";
echo "• Check all admin functions work\n";
echo "• Test student registration flow\n\n";

echo "🎉 YOUR APP WILL BE LIVE AT:\n";
echo "============================\n";
echo "https://your-app-name.railway.app\n\n";

echo "💡 Need Help?\n";
echo "=============\n";
echo "• Railway Docs: https://docs.railway.app\n";
echo "• GitHub: Upload your code there first\n";
echo "• Database: Use Railway's MySQL service\n";
echo "• Support: Check Railway's support center\n\n";

echo "🚀 Ready to deploy? Follow the steps above!\n";
?>
