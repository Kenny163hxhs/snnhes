<?php
/**
 * Get sections for a specific course and year level
 * National High School Enrollment System
 */

require_once 'config/database.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    
    $courseId = $_GET['course_id'] ?? '';
    $yearLevel = $_GET['year_level'] ?? '';
    
    if (empty($courseId) || empty($yearLevel)) {
        throw new Exception('Course ID and Year Level are required');
    }
    
    // Get current academic year
    $currentPeriod = $db->fetchOne("SELECT * FROM enrollment_periods WHERE is_active = 1 ORDER BY start_date DESC LIMIT 1");
    $currentAcademicYear = $currentPeriod['academic_year'] ?? date('Y') . '-' . (date('Y') + 1);
    
    // Get available sections for the course and year level
    $sections = $db->fetchAll("
        SELECT cs.*, 
               (cs.max_students - cs.current_students) as available_slots
        FROM class_sections cs 
        WHERE cs.course_id = ? 
        AND cs.year_level = ? 
        AND cs.academic_year = ?
        AND cs.is_active = 1
        AND cs.current_students < cs.max_students
        ORDER BY cs.section_name
    ", [$courseId, $yearLevel, $currentAcademicYear]);
    
    echo json_encode($sections);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
