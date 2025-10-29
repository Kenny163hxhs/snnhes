# Course Management Fix Guide

## Problem Description
You were experiencing a foreign key constraint violation error when trying to add or edit courses/strands. The error message was:
```
Query failed: SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`snnhes_db`.`class_sections`, CONSTRAINT `class_sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE)
```

## Root Cause
The issue was caused by a database schema inconsistency:
1. The `class_sections` table had a `course_id` column defined as `NOT NULL`
2. However, the application code was trying to insert `NULL` values for `course_id` when creating sections without assigned courses
3. This created a conflict between the database constraint and the application logic

## Solution Applied

### 1. Database Schema Fix
- **File**: `database/fix_course_id_nullable.sql`
- **Change**: Modified the `course_id` column in `class_sections` table to allow `NULL` values
- **Impact**: Sections can now be created with or without being assigned to a specific course

### 2. Updated Database Schema
- **File**: `database/snnhes_db.sql`
- **Changes**:
  - Changed `course_id` from `int(11) NOT NULL` to `int(11) NULL`
  - Updated foreign key constraint to include `ON UPDATE CASCADE`

### 3. Enhanced Course Management
- **File**: `modules/courses/manage.php`
- **Improvements**:
  - Added validation for required fields
  - Added duplicate course code checking
  - Added safety checks before deleting courses (prevents deletion if sections or students exist)
  - Better error handling and user feedback

## How to Apply the Fix

### Option 1: Run the Fix Script (Recommended)
1. Open your web browser and navigate to: `http://localhost/SNNHES/fix_course_management.php`
2. The script will automatically:
   - Drop the existing foreign key constraint
   - Modify the `course_id` column to allow NULL values
   - Re-add the foreign key constraint with proper handling
   - Test the fix with sample data
   - Clean up test data

### Option 2: Manual Database Update
If you prefer to run the SQL commands manually:

1. Open phpMyAdmin or your MySQL client
2. Select your `snnhes_db` database
3. Run the following SQL commands in order:

```sql
-- Drop the existing foreign key constraint
ALTER TABLE `class_sections` DROP FOREIGN KEY `class_sections_ibfk_1`;

-- Modify the course_id column to allow NULL values
ALTER TABLE `class_sections` MODIFY COLUMN `course_id` int(11) NULL;

-- Re-add the foreign key constraint with proper NULL handling
ALTER TABLE `class_sections` 
ADD CONSTRAINT `class_sections_ibfk_1` 
FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;
```

## What This Fix Enables

### ✅ Now You Can:
1. **Add new courses/strands** without foreign key constraint errors
2. **Edit existing courses** without issues
3. **Create sections without assigning them to courses** (useful for general sections)
4. **Create sections and assign them to courses** (normal workflow)
5. **Delete courses safely** (with proper validation to prevent data loss)

### 🔒 Safety Features Added:
1. **Duplicate Prevention**: Cannot create courses with duplicate codes
2. **Deletion Protection**: Cannot delete courses that have associated sections or enrolled students
3. **Better Validation**: Required fields are properly validated
4. **Clear Error Messages**: Users get helpful feedback when operations fail

## Testing the Fix

After applying the fix, test these scenarios:

1. **Add a new course**:
   - Go to Course Management
   - Click "Add New Course"
   - Fill in the details and save
   - Should work without errors

2. **Edit an existing course**:
   - Click the edit button on any course
   - Modify details and save
   - Should work without errors

3. **Create a section without course**:
   - Go to Sections Management
   - Click "Add New Section"
   - Leave "Course" field as "No Course Assigned"
   - Should work without errors

4. **Create a section with course**:
   - Go to Sections Management
   - Click "Add New Section"
   - Select a course from the dropdown
   - Should work without errors

## Files Modified

1. `database/fix_course_id_nullable.sql` - SQL fix script
2. `database/snnhes_db.sql` - Updated schema
3. `modules/courses/manage.php` - Enhanced course management
4. `fix_course_management.php` - Automated fix script
5. `COURSE_MANAGEMENT_FIX.md` - This documentation

## Rollback (If Needed)

If you need to rollback the changes:

```sql
-- Restore the original constraint (this will fail if NULL values exist)
ALTER TABLE `class_sections` DROP FOREIGN KEY `class_sections_ibfk_1`;
ALTER TABLE `class_sections` MODIFY COLUMN `course_id` int(11) NOT NULL;
ALTER TABLE `class_sections` 
ADD CONSTRAINT `class_sections_ibfk_1` 
FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) 
ON DELETE CASCADE;
```

**Note**: You'll need to update any sections with NULL course_id values before rolling back.

## Support

If you encounter any issues after applying this fix, please check:
1. Database connection is working
2. All SQL commands executed successfully
3. No syntax errors in the modified files
4. Proper file permissions are set

The fix is designed to be safe and non-destructive, but always backup your database before making changes.
