<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy số lượng sinh viên theo từng khoa
 */
function getStudentsByDepartment() {
    global $conn;
    $sql = "SELECT d.department_name, COUNT(s.id) as student_count 
            FROM departments d 
            LEFT JOIN students s ON d.id = s.department_id 
            GROUP BY d.id, d.department_name 
            ORDER BY d.department_name";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy phân bố điểm số của sinh viên
 */
function getGradeDistribution() {
    global $conn;
    $sql = "SELECT 
            CASE 
                WHEN grade < 4 THEN '0-4'
                WHEN grade >= 4 AND grade < 5 THEN '4-5'
                WHEN grade >= 5 AND grade < 6 THEN '5-6'
                WHEN grade >= 6 AND grade < 7 THEN '6-7'
                WHEN grade >= 7 AND grade < 8 THEN '7-8'
                WHEN grade >= 8 AND grade < 9 THEN '8-9'
                ELSE '9-10'
            END as grade_range,
            COUNT(*) as count
            FROM grades
            GROUP BY 
            CASE 
                WHEN grade < 4 THEN '0-4'
                WHEN grade >= 4 AND grade < 5 THEN '4-5'
                WHEN grade >= 5 AND grade < 6 THEN '5-6'
                WHEN grade >= 6 AND grade < 7 THEN '6-7'
                WHEN grade >= 7 AND grade < 8 THEN '7-8'
                WHEN grade >= 8 AND grade < 9 THEN '8-9'
                ELSE '9-10'
            END
            ORDER BY grade_range";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy điểm trung bình toàn trường
 */
function getOverallAverageGrade() {
    global $conn;
    $sql = "SELECT ROUND(AVG(grade), 2) as average FROM grades";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['average'] ?? 0;
}
?>