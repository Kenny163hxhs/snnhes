<?php
/**
 * Fix Courses Issue
 * Ensure courses are properly loaded in registration form
 */

require_once 'config/database.php';

echo "🔧 Fixing Courses Issue...\n";
echo "==========================\n\n";

$db = Database::getInstance();

echo "1. Checking current courses...\n";
$courses = $db->fetchAll("SELECT * FROM courses ORDER BY id");
echo "Found " . count($courses) . " courses in database\n\n";

if (count($courses) == 0) {
    echo "❌ No courses found! Creating sample courses...\n";
    
    $sampleCourses = [
        ['course_code' => 'STEM', 'course_name' => 'Science, Technology, Engineering, and Mathematics'],
        ['course_code' => 'ABM', 'course_name' => 'Accountancy, Business, and Management'],
        ['course_code' => 'HUMSS', 'course_name' => 'Humanities and Social Sciences'],
        ['course_code' => 'GAS', 'course_name' => 'General Academic Strand'],
        ['course_code' => 'TVL-ICT', 'course_name' => 'Technical-Vocational-Livelihood - Information and Communications Technology'],
        ['course_code' => 'TVL-HE', 'course_name' => 'Technical-Vocational-Livelihood - Home Economics'],
        ['course_code' => 'TVL-IA', 'course_name' => 'Technical-Vocational-Livelihood - Industrial Arts'],
        ['course_code' => 'TVL-AGRI', 'course_name' => 'Technical-Vocational-Livelihood - Agriculture']
    ];
    
    foreach ($sampleCourses as $course) {
        $db->insert('courses', [
            'course_code' => $course['course_code'],
            'course_name' => $course['course_name'],
            'description' => $course['course_name'] . ' Strand',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        echo "✅ Created course: {$course['course_name']}\n";
    }
} else {
    echo "✅ Courses found. Checking if they're active...\n";
    
    $inactiveCourses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 0");
    if (count($inactiveCourses) > 0) {
        echo "Found " . count($inactiveCourses) . " inactive courses. Activating them...\n";
        $db->query("UPDATE courses SET is_active = 1 WHERE is_active = 0");
        echo "✅ All courses are now active\n";
    } else {
        echo "✅ All courses are already active\n";
    }
}

echo "\n2. Verifying courses for registration...\n";
$activeCourses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");
echo "Active courses available for registration: " . count($activeCourses) . "\n\n";

if (count($activeCourses) > 0) {
    echo "✅ Courses are ready for registration!\n";
    echo "Available courses:\n";
    foreach ($activeCourses as $course) {
        echo "- {$course['course_name']} ({$course['course_code']})\n";
    }
} else {
    echo "❌ Still no active courses found!\n";
    echo "There might be a database issue.\n";
}

echo "\n3. Testing course loading...\n";
$testCourses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");
if (count($testCourses) > 0) {
    echo "✅ Course loading test passed!\n";
    echo "The registration form should now show courses.\n";
} else {
    echo "❌ Course loading test failed!\n";
    echo "Please check your database connection.\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. Go to: http://localhost/SNNHES/student_register.php\n";
echo "2. Check if courses are now showing in the dropdown\n";
echo "3. If still not showing, clear browser cache and refresh\n";
echo "4. Try adding a new course in admin panel\n\n";

echo "🚀 Courses issue should now be fixed!\n";
?>
