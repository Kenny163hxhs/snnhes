# Student Registration System

## Overview
The Student Registration System allows students to enroll themselves in the school without requiring admin access. This is a public-facing registration form that students can use to apply for enrollment.

## Features

### 1. Public Student Registration (`student_register.php`)
- **Purpose**: Allows students to register themselves for enrollment
- **Access**: Public (no login required)
- **Features**:
  - Personal information collection
  - Address information
  - Emergency contact details
  - Academic information (course selection, year level)
  - **Selfie photo capture** (required for completion)
  - Document upload functionality
  - Real-time section availability checking
  - Form validation

### 2. Enrollment Status Checker (`check_enrollment.php`)
- **Purpose**: Allows students to check their enrollment status
- **Access**: Public (no login required)
- **Features**:
  - Search by Student ID or Email
  - Display enrollment status (pending, approved, rejected)
  - Show assigned course and section
  - Display personal information

### 3. Section Availability API (`get_sections.php`)
- **Purpose**: AJAX endpoint for loading available sections
- **Access**: Internal API
- **Features**:
  - Returns sections based on course and year level
  - Shows current enrollment numbers
  - Filters out full sections

## How It Works

### Registration Process
1. Student visits the registration form
2. Fills out personal, address, and academic information
3. **Takes a selfie photo using the built-in camera**
4. Uploads required documents (birth certificate, report card, etc.)
5. Selects preferred course and year level
6. System automatically assigns to available section or allows manual selection
7. Student receives a unique Student ID
8. Enrollment status is set to "pending" for admin approval

### Status Checking
1. Student enters their Student ID or Email
2. System displays their current enrollment status
3. Shows assigned course and section (if approved)
4. Provides appropriate messages based on status

## File Structure
```
├── student_register.php          # Main registration form
├── check_enrollment.php         # Status checker
├── get_sections.php             # AJAX API for sections
├── index.php                    # Updated with new links
└── config/database.php          # Database configuration
```

## Database Integration
The system integrates with existing database tables:
- `students` - Stores student information
- `enrollments` - Tracks enrollment status
- `class_sections` - Available sections
- `courses` - Available courses
- `student_documents` - Uploaded documents and selfie photos

## Security Features
- Input sanitization and validation
- File upload restrictions (size and type)
- SQL injection prevention with prepared statements
- Email validation
- Duplicate student prevention
- Camera permission handling
- Selfie photo validation

## Selfie Photo Feature
The registration system now includes a mandatory selfie capture feature:

### How It Works
1. **Camera Access**: Students must grant camera permissions
2. **Photo Capture**: Uses device's front camera for selfie
3. **Photo Requirements**: Clear guidelines for acceptable photos
4. **Validation**: Photo is required to complete registration
5. **Storage**: Selfie is saved as a document in the database
6. **Display**: Selfie appears in enrollment status checker

### Technical Details
- Uses HTML5 `getUserMedia()` API
- Captures photo as base64 data
- Converts to JPEG format for storage
- Stores in `student_documents` table with type 'selfie'
- Displays in enrollment status for verification

## User Interface
- Responsive Bootstrap 5 design
- Modern, user-friendly interface
- Real-time form validation
- Dynamic section loading
- Clear status indicators
- Mobile-friendly design

## Navigation
The main index page now includes:
- "Student Registration" button for new students
- "Check Enrollment Status" button for existing students
- Links are prominently displayed for easy access

## Requirements
- PHP 7.4+
- MySQL/MariaDB
- XAMPP or similar local server
- Bootstrap 5 (CDN)
- Font Awesome (CDN)

## Usage Instructions

### For Students
1. Visit the school website
2. Click "Student Registration" to apply
3. Fill out the complete form
4. Upload required documents
5. Submit the application
6. Use "Check Enrollment Status" to track progress

### For Administrators
1. Access the admin panel to review pending enrollments
2. Approve or reject applications
3. Assign students to specific sections if needed
4. Monitor enrollment statistics

## Future Enhancements
- Email notifications for status updates
- SMS notifications
- Online payment integration
- Document verification system
- Advanced reporting features
