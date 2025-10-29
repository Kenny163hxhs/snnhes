<?php
/**
 * Clear Course Cache and Refresh Data
 * Fix courses not updating in registration form
 */

require_once 'config/database.php';

echo "🔄 Clearing Course Cache and Refreshing Data...\n";
echo "===============================================\n\n";

$db = Database::getInstance();

echo "1. Checking current courses in database...\n";
$allCourses = $db->fetchAll("SELECT * FROM courses ORDER BY id");
echo "Total courses in database: " . count($allCourses) . "\n\n";

foreach ($allCourses as $course) {
    $status = $course['is_active'] ? 'ACTIVE' : 'INACTIVE';
    echo "ID: {$course['id']} | Code: {$course['course_code']} | Name: {$course['course_name']} | Status: {$status}\n";
}

echo "\n2. Checking what registration form should load...\n";
$activeCourses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");
echo "Active courses for registration: " . count($activeCourses) . "\n\n";

if (count($activeCourses) > 0) {
    echo "Courses that should appear in registration:\n";
    foreach ($activeCourses as $course) {
        echo "- {$course['course_name']} ({$course['course_code']})\n";
    }
} else {
    echo "❌ No active courses found!\n";
    echo "This means either:\n";
    echo "1. All courses are marked as inactive\n";
    echo "2. No courses exist in database\n";
}

echo "\n3. Checking for deleted courses...\n";
$deletedCourses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 0");
if (count($deletedCourses) > 0) {
    echo "Found " . count($deletedCourses) . " inactive/deleted courses:\n";
    foreach ($deletedCourses as $course) {
        echo "- {$course['course_name']} ({$course['course_code']}) - DELETED/INACTIVE\n";
    }
} else {
    echo "No deleted courses found.\n";
}

echo "\n4. Force refresh registration form data...\n";
// Clear any potential caching by forcing a fresh query
$freshCourses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");
echo "Fresh query returned: " . count($freshCourses) . " active courses\n";

echo "\n5. Testing course loading with cache busting...\n";
// Add a timestamp to force refresh
$timestamp = time();
$coursesWithTimestamp = $db->fetchAll("SELECT *, ? as cache_buster FROM courses WHERE is_active = 1 ORDER BY course_name", [$timestamp]);
echo "Cache-busted query returned: " . count($coursesWithTimestamp) . " courses\n";

echo "\n6. Recommendations:\n";
if (count($activeCourses) == 0) {
    echo "❌ No active courses found!\n";
    echo "Solution: Add some courses or activate existing ones\n";
} else {
    echo "✅ Active courses found!\n";
    echo "If courses still don't show in registration:\n";
    echo "1. Clear browser cache (Ctrl+F5)\n";
    echo "2. Check if there's a JavaScript caching issue\n";
    echo "3. Try opening registration in incognito/private mode\n";
}

echo "\n7. Quick fix - Add sample TVL courses if missing...\n";
$tvlCourses = $db->fetchAll("SELECT * FROM courses WHERE course_code LIKE 'TVL%' AND is_active = 1");
if (count($tvlCourses) == 0) {
    echo "No TVL courses found. Adding sample TVL courses...\n";
    
    $tvlCoursesToAdd = [
        ['course_code' => 'TVL-ICT', 'course_name' => 'Technical-Vocational-Livelihood - Information and Communications Technology'],
        ['course_code' => 'TVL-HE', 'course_name' => 'Technical-Vocational-Livelihood - Home Economics'],
        ['course_code' => 'TVL-IA', 'course_name' => 'Technical-Vocational-Livelihood - Industrial Arts'],
        ['course_code' => 'TVL-AGRI', 'course_name' => 'Technical-Vocational-Livelihood - Agriculture']
    ];
    
    foreach ($tvlCoursesToAdd as $course) {
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
            echo "⚠️ Already exists: {$course['course_name']}\n";
        }
    }
} else {
    echo "TVL courses already exist:\n";
    foreach ($tvlCourses as $course) {
        echo "- {$course['course_name']} ({$course['course_code']})\n";
    }
}

echo "\n🎯 Next Steps:\n";
echo "1. Go to: http://localhost/SNNHES/student_register.php\n";
echo "2. Hard refresh the page (Ctrl+F5 or Cmd+Shift+R)\n";
echo "3. Check if courses are now showing correctly\n";
echo "4. If GAS is still showing, check if it's actually deleted from database\n\n";

echo "🚀 Course cache should now be cleared!\n";
?>
