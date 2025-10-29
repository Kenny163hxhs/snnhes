<?php
/**
 * Refresh Courses Data
 * Force refresh courses in registration form
 */

require_once 'config/database.php';

echo "🔄 Refreshing Courses Data...\n";
echo "=============================\n\n";

$db = Database::getInstance();

echo "1. Current courses in database:\n";
$allCourses = $db->fetchAll("SELECT * FROM courses ORDER BY course_name");
foreach ($allCourses as $course) {
    $status = $course['is_active'] ? '✅ ACTIVE' : '❌ INACTIVE';
    echo "{$status} - {$course['course_name']} ({$course['course_code']})\n";
}

echo "\n2. Refreshing registration form data...\n";
// Force a completely fresh query
$freshCourses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");
echo "Active courses found: " . count($freshCourses) . "\n";

if (count($freshCourses) > 0) {
    echo "Courses that should appear in registration:\n";
    foreach ($freshCourses as $course) {
        echo "- {$course['course_name']} ({$course['course_code']})\n";
    }
} else {
    echo "❌ No active courses found!\n";
    echo "Adding sample courses...\n";
    
    $sampleCourses = [
        ['course_code' => 'STEM', 'course_name' => 'Science, Technology, Engineering, and Mathematics'],
        ['course_code' => 'ABM', 'course_name' => 'Accountancy, Business, and Management'],
        ['course_code' => 'HUMSS', 'course_name' => 'Humanities and Social Sciences'],
        ['course_code' => 'TVL-ICT', 'course_name' => 'Technical-Vocational-Livelihood - ICT'],
        ['course_code' => 'TVL-HE', 'course_name' => 'Technical-Vocational-Livelihood - Home Economics'],
        ['course_code' => 'TVL-IA', 'course_name' => 'Technical-Vocational-Livelihood - Industrial Arts']
    ];
    
    foreach ($sampleCourses as $course) {
        $existing = $db->fetchOne("SELECT id FROM courses WHERE course_code = ?", [$course['course_code']]);
        if (!$existing) {
            $db->insert('courses', [
                'course_code' => $course['course_code'],
                'course_name' => $course['course_name'],
                'description' => $course['course_name'],
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            echo "✅ Added: {$course['course_name']}\n";
        } else {
            // Activate existing course
            $db->query("UPDATE courses SET is_active = 1 WHERE course_code = ?", [$course['course_code']]);
            echo "✅ Activated: {$course['course_name']}\n";
        }
    }
}

echo "\n3. Final verification...\n";
$finalCourses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");
echo "Total active courses: " . count($finalCourses) . "\n\n";

echo "🎯 Next Steps:\n";
echo "1. Go to: http://localhost/SNNHES/student_register.php\n";
echo "2. Hard refresh the page (Ctrl+F5)\n";
echo "3. Check if courses are now showing correctly\n";
echo "4. GAS should be gone, TVL courses should appear\n\n";

echo "🚀 Courses data refreshed!\n";
?>
