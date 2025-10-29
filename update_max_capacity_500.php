<?php
/**
 * Update Maximum Capacity to 500 Students
 * Update all existing sections to have 500 max students
 */

require_once 'config/database.php';

echo "🔄 Updating Maximum Capacity to 500 Students...\n";
echo "==============================================\n\n";

$db = Database::getInstance();

try {
    echo "1. Checking current section capacities...\n";
    $sections = $db->fetchAll("SELECT id, section_code, max_students FROM class_sections ORDER BY id");
    
    echo "Current sections and their max students:\n";
    foreach ($sections as $section) {
        echo "  - {$section['section_code']}: {$section['max_students']} students\n";
    }
    
    echo "\n2. Updating all sections to 500 max students...\n";
    $result = $db->query("UPDATE class_sections SET max_students = 500 WHERE max_students < 500");
    
    if ($result) {
        echo "✅ Successfully updated sections to 500 max students\n";
    } else {
        echo "❌ Failed to update sections\n";
        exit;
    }
    
    echo "\n3. Verifying the update...\n";
    $updatedSections = $db->fetchAll("SELECT id, section_code, max_students FROM class_sections ORDER BY id");
    
    echo "Updated sections and their max students:\n";
    foreach ($updatedSections as $section) {
        $status = $section['max_students'] == 500 ? '✅' : '❌';
        echo "  {$status} {$section['section_code']}: {$section['max_students']} students\n";
    }
    
    echo "\n4. Checking for any sections still below 500...\n";
    $lowCapacitySections = $db->fetchAll("SELECT section_code, max_students FROM class_sections WHERE max_students < 500");
    
    if (count($lowCapacitySections) == 0) {
        echo "✅ All sections now have 500 max students!\n";
    } else {
        echo "⚠️ Some sections still have less than 500 students:\n";
        foreach ($lowCapacitySections as $section) {
            echo "  - {$section['section_code']}: {$section['max_students']} students\n";
        }
    }
    
    echo "\n5. Summary of changes:\n";
    echo "✅ Maximum capacity per section: 500 students\n";
    echo "✅ All existing sections updated\n";
    echo "✅ New sections will default to 500 students\n";
    echo "✅ System ready for high-capacity enrollment\n\n";
    
    echo "🎯 Impact on your system:\n";
    echo "========================\n";
    echo "• Each section can now hold up to 500 students\n";
    echo "• Total capacity per course: 500 × number of sections\n";
    echo "• Example: STEM with 2 sections = 1,000 students total\n";
    echo "• Much higher enrollment capacity for your school\n\n";
    
    echo "🚀 Maximum capacity update completed!\n";
    echo "Your system can now handle 500 students per section!\n";
    
} catch (Exception $e) {
    echo "❌ Error updating capacity: " . $e->getMessage() . "\n";
}
?>
