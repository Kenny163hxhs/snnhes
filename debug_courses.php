<?php
/**
 * Debug Courses Issue
 * Check why courses are not showing in registration
 */

require_once 'config/database.php';

echo "🔍 Debugging Courses Issue...\n";
echo "=============================\n\n";

$db = Database::getInstance();

echo "1. Checking all courses in database...\n";
$allCourses = $db->fetchAll("SELECT * FROM courses ORDER BY id");
echo "Total courses found: " . count($allCourses) . "\n\n";

foreach ($allCourses as $course) {
    echo "ID: {$course['id']} | Code: {$course['course_code']} | Name: {$course['course_name']} | Active: {$course['is_active']}\n";
}

echo "\n2. Checking active courses (what registration form loads)...\n";
$activeCourses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");
echo "Active courses found: " . count($activeCourses) . "\n\n";

foreach ($activeCourses as $course) {
    echo "ID: {$course['id']} | Code: {$course['course_code']} | Name: {$course['course_name']}\n";
}

echo "\n3. Testing course loading in registration form...\n";
$courses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");
echo "Courses loaded for registration: " . count($courses) . "\n\n";

if (count($courses) > 0) {
    echo "✅ Courses are loading correctly!\n";
    echo "Available courses for registration:\n";
    foreach ($courses as $course) {
        echo "- {$course['course_name']} ({$course['course_code']})\n";
    }
} else {
    echo "❌ No courses found for registration!\n";
    echo "This means either:\n";
    echo "1. No courses exist in database\n";
    echo "2. All courses are marked as inactive (is_active = 0)\n";
    echo "3. There's a database connection issue\n";
}

echo "\n4. Checking course management...\n";
echo "Go to: http://localhost/SNNHES/modules/courses/manage.php\n";
echo "Check if you can see and manage courses there.\n\n";

echo "5. Quick fix suggestions:\n";
echo "- Make sure courses are marked as active (is_active = 1)\n";
echo "- Check if course_code and course_name are not empty\n";
echo "- Try adding a new course and see if it appears\n";
echo "- Clear browser cache and refresh registration page\n\n";

echo "🔧 If courses are not showing, try this SQL:\n";
echo "UPDATE courses SET is_active = 1 WHERE is_active = 0;\n";
echo "This will activate all courses.\n";
?>
