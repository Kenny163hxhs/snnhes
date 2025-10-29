<?php
/**
 * Fix Sections Issue
 * Check why no sections are found for course and year level
 */

require_once 'config/database.php';

echo "🔍 Fixing Sections Issue...\n";
echo "===========================\n\n";

$db = Database::getInstance();

echo "1. Checking all courses...\n";
$courses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");
echo "Active courses found: " . count($courses) . "\n";
foreach ($courses as $course) {
    echo "  - {$course['course_name']} (ID: {$course['id']})\n";
}

echo "\n2. Checking all sections...\n";
$sections = $db->fetchAll("SELECT * FROM class_sections WHERE is_active = 1 ORDER BY course_id, year_level");
echo "Active sections found: " . count($sections) . "\n";
foreach ($sections as $section) {
    $courseName = $db->fetchOne("SELECT course_name FROM courses WHERE id = ?", [$section['course_id']]);
    echo "  - {$section['section_code']} | Course: {$courseName['course_name']} | Year: {$section['year_level']}\n";
}

echo "\n3. Checking course-section relationships...\n";
$courseSections = $db->fetchAll("
    SELECT c.course_name, c.id as course_id, cs.section_code, cs.year_level, cs.is_active
    FROM courses c
    LEFT JOIN class_sections cs ON c.id = cs.course_id AND cs.is_active = 1
    WHERE c.is_active = 1
    ORDER BY c.course_name, cs.year_level
");

foreach ($courseSections as $row) {
    if ($row['section_code']) {
        echo "  ✅ {$row['course_name']} -> {$row['section_code']} (Year {$row['year_level']})\n";
    } else {
        echo "  ❌ {$row['course_name']} -> NO SECTIONS FOUND\n";
    }
}

echo "\n4. Creating missing sections...\n";
$coursesWithoutSections = $db->fetchAll("
    SELECT c.* FROM courses c
    LEFT JOIN class_sections cs ON c.id = cs.course_id AND cs.is_active = 1
    WHERE c.is_active = 1 AND cs.id IS NULL
");

if (count($coursesWithoutSections) > 0) {
    echo "Found courses without sections:\n";
    foreach ($coursesWithoutSections as $course) {
        echo "  - {$course['course_name']} (ID: {$course['id']})\n";
        
        // Create sections for Grade 11 and 12
        $sectionsToCreate = [
            ['year' => 11, 'code' => $course['course_code'] . '-11A'],
            ['year' => 12, 'code' => $course['course_code'] . '-12A']
        ];
        
        foreach ($sectionsToCreate as $section) {
            $sectionData = [
                'section_code' => $section['code'],
                'section_name' => $course['course_name'] . ' Grade ' . $section['year'] . ' Section A',
                'course_id' => $course['id'],
                'year_level' => $section['year'],
                'academic_year' => '2024-2025',
                'max_students' => 500,
                'current_students' => 0,
                'is_active' => 1
            ];
            
            $db->insert('class_sections', $sectionData);
            echo "    ✅ Created: {$section['code']}\n";
        }
    }
} else {
    echo "All courses have sections.\n";
}

echo "\n5. Final verification...\n";
$finalSections = $db->fetchAll("
    SELECT c.course_name, cs.section_code, cs.year_level
    FROM courses c
    JOIN class_sections cs ON c.id = cs.course_id
    WHERE c.is_active = 1 AND cs.is_active = 1
    ORDER BY c.course_name, cs.year_level
");

echo "Total sections available: " . count($finalSections) . "\n";
foreach ($finalSections as $section) {
    echo "  - {$section['course_name']} -> {$section['section_code']} (Year {$section['year_level']})\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. Go to: http://localhost/SNNHES/student_register.php\n";
echo "2. Refresh the page (Ctrl+F5)\n";
echo "3. Select a course and year level\n";
echo "4. The error should be gone!\n\n";

echo "🚀 Sections issue should now be fixed!\n";
?>
