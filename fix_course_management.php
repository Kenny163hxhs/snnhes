<?php
/**
 * Fix Course Management Database Issues
 * This script fixes the foreign key constraint issue that prevents adding/editing courses
 */

require_once 'config/database.php';

try {
    $db = Database::getInstance();
    
    echo "Starting database fix...\n";
    
    // Step 1: Drop the existing foreign key constraint
    echo "1. Dropping existing foreign key constraint...\n";
    try {
        $db->query("ALTER TABLE `class_sections` DROP FOREIGN KEY `class_sections_ibfk_1`");
        echo "   ✓ Foreign key constraint dropped successfully\n";
    } catch (Exception $e) {
        echo "   ⚠ Warning: Could not drop foreign key constraint: " . $e->getMessage() . "\n";
    }
    
    // Step 2: Modify the course_id column to allow NULL values
    echo "2. Modifying course_id column to allow NULL values...\n";
    try {
        $db->query("ALTER TABLE `class_sections` MODIFY COLUMN `course_id` int(11) NULL");
        echo "   ✓ course_id column modified successfully\n";
    } catch (Exception $e) {
        echo "   ✗ Error modifying course_id column: " . $e->getMessage() . "\n";
        throw $e;
    }
    
    // Step 3: Re-add the foreign key constraint with proper NULL handling
    echo "3. Adding foreign key constraint with proper NULL handling...\n";
    try {
        $db->query("ALTER TABLE `class_sections` ADD CONSTRAINT `class_sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        echo "   ✓ Foreign key constraint added successfully\n";
    } catch (Exception $e) {
        echo "   ✗ Error adding foreign key constraint: " . $e->getMessage() . "\n";
        throw $e;
    }
    
    // Step 4: Verify the fix by testing course operations
    echo "4. Testing course operations...\n";
    
    // Clean up any existing test data first
    $db->query("DELETE FROM class_sections WHERE section_code LIKE 'TEST-SEC-%'");
    $db->query("DELETE FROM courses WHERE course_code = 'TEST001'");
    
    // Test adding a course
    $testCourseData = [
        'course_code' => 'TEST001',
        'course_name' => 'Test Course',
        'description' => 'Test course for verification',
        'duration_years' => 2,
        'total_units' => 60,
        'is_active' => 1
    ];
    
    $testCourseId = $db->insert('courses', $testCourseData);
    echo "   ✓ Test course added successfully (ID: $testCourseId)\n";
    
    // Test adding a section without course (NULL course_id)
    $testSectionData = [
        'section_code' => 'TEST-SEC-001',
        'section_name' => 'Test Section Without Course',
        'course_id' => null,
        'year_level' => 11,
        'academic_year' => '2024-2025',
        'max_students' => 30,
        'current_students' => 0,
        'is_active' => 1
    ];
    
    $db->insert('class_sections', $testSectionData);
    echo "   ✓ Test section without course added successfully\n";
    
    // Test adding a section with course
    $testSectionWithCourseData = [
        'section_code' => 'TEST-SEC-002',
        'section_name' => 'Test Section With Course',
        'course_id' => $testCourseId,
        'year_level' => 12,
        'academic_year' => '2024-2025',
        'max_students' => 25,
        'current_students' => 0,
        'is_active' => 1
    ];
    
    $db->insert('class_sections', $testSectionWithCourseData);
    echo "   ✓ Test section with course added successfully\n";
    
    // Clean up test data
    echo "5. Cleaning up test data...\n";
    $db->query("DELETE FROM class_sections WHERE section_code LIKE 'TEST-SEC-%'");
    $db->query("DELETE FROM courses WHERE course_code = 'TEST001'");
    echo "   ✓ Test data cleaned up\n";
    
    echo "\n🎉 Database fix completed successfully!\n";
    echo "You can now add and edit courses without foreign key constraint errors.\n";
    echo "Sections can now be created with or without being assigned to a course.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during database fix: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
    exit(1);
}
?>
