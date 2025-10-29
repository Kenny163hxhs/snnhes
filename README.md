# SNNHES - Student National High School Enrollment System

A comprehensive web-based enrollment system for managing student registrations, courses, sections, and administrative tasks.

## 🌟 Features

### Student Management
- **Student Registration** - Complete registration form with document uploads
- **Selfie Capture** - Built-in camera functionality for student photos
- **Document Management** - Upload and manage student documents
- **Student Transfers** - Handle student transfer requests

### Course & Section Management
- **Course Management** - Add, edit, and delete courses
- **Section Management** - Create and manage class sections
- **Capacity Management** - Set maximum students per section (up to 500)
- **Academic Year Management** - Handle different academic periods

### Administrative Features
- **Admin Dashboard** - Comprehensive admin panel
- **User Management** - Admin profile management
- **Enrollment Tracking** - Monitor student enrollments
- **Report Generation** - Generate various reports

### Technical Features
- **Responsive Design** - Works on desktop and mobile
- **File Upload System** - Secure document and image uploads
- **Database Management** - MySQL database with proper relationships
- **Security** - Input validation and secure file handling

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/snnhes.git
   cd snnhes
   ```

2. **Set up the database**
   - Create a MySQL database
   - Import `database/snnhes_db.sql`
   - Configure database credentials in `config/database.php`

3. **Configure the application**
   - Update database settings
   - Set up file upload directories
   - Configure email settings (optional)

4. **Start the application**
   - Start your local web server (XAMPP, WAMP, etc.)
   - Access the application via web browser

## 📁 Project Structure

```
snnhes/
├── assets/                 # CSS and JavaScript files
├── config/                # Configuration files
├── database/              # Database schema and migrations
├── includes/              # Shared PHP includes
├── modules/               # Feature modules
│   ├── admin/            # Admin management
│   ├── courses/          # Course management
│   ├── enrollment/       # Enrollment processing
│   ├── sections/         # Section management
│   └── students/         # Student management
├── uploads/              # File uploads directory
├── api/                  # API endpoints
├── index.php            # Main dashboard
├── login.php            # Admin login
└── student_register.php # Student registration
```

## 🔧 Configuration

### Database Configuration
Update `config/database.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'snnhes_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### File Upload Configuration
Ensure the following directories are writable:
- `uploads/`
- `uploads/students/`
- `uploads/transfers/`

## 📊 Database Schema

The system uses a MySQL database with the following main tables:
- `students` - Student information
- `courses` - Course details
- `class_sections` - Class sections
- `enrollments` - Student enrollments
- `admin_users` - Admin accounts
- `email_verifications` - Email verification system

## 🎯 Usage

### For Students
1. Visit the student registration page
2. Fill out the registration form
3. Upload required documents
4. Take a selfie photo
5. Submit registration

### For Administrators
1. Login to the admin panel
2. Manage courses and sections
3. Process student registrations
4. Handle student transfers
5. Generate reports

## 🛠️ Development

### Adding New Features
1. Create new modules in the `modules/` directory
2. Add database migrations in `database/`
3. Update the admin navigation
4. Test thoroughly

### Database Migrations
- All database changes are in `database/` directory
- Run migrations in order
- Always backup before running migrations

## 📝 API Endpoints

- `api/send_otp.php` - Send OTP for email verification
- `api/verify_otp.php` - Verify OTP codes
- `get_sections.php` - Get sections for a course
- `check_enrollment.php` - Check enrollment status

## 🔒 Security Features

- Input validation and sanitization
- SQL injection prevention
- File upload security
- Session management
- CSRF protection

## 📱 Mobile Support

The system is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🚀 Getting Started

1. **Clone the repository**
2. **Set up XAMPP or similar local server**
3. **Import the database schema**
4. **Configure database settings**
5. **Start using the system**

## 📞 Support

For support and questions:
- Check the documentation
- Review error logs
- Test with sample data
- Contact the development team

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📈 Roadmap

- [ ] Email notification system
- [ ] Advanced reporting
- [ ] Mobile app
- [ ] API documentation
- [ ] Multi-language support

---

**Built with ❤️ for educational institutions**