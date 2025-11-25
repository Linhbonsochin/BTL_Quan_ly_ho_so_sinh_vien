
<?php
/*
========================================
 Chức năng: Các hàm xử lý điểm số (grades), CRUD, lấy danh sách, thêm, sửa, xóa
========================================
*/
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy tất cả danh sách grades từ database với thông tin sinh viên và môn học
 * @return array Danh sách grades
 */
function getAllGrades()
{
    $conn = getDbConnection();
    $sql = "SELECT g.id, g.student_id, g.subject_id, g.attendance_score, g.midterm_score, g.final_score, g.total_score, 
                   s.student_code, s.full_name AS student_name, sub.subject_code, sub.subject_name 
            FROM grades g
            LEFT JOIN students s ON g.student_id = s.id
            LEFT JOIN subjects sub ON g.subject_id = sub.id
            ORDER BY g.id";
    $grades = [];
    try {
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $grades[] = $row;
            }
        }
    } catch (mysqli_sql_exception $ex) {}
    mysqli_close($conn);
    return $grades;
}

/**
 * Thêm grade mới
 * @param int $student_id ID sinh viên
 * @param int $subject_id ID môn học
 * @param float $attendance_score Điểm danh
 * @param float $midterm_score Điểm giữa kỳ
 * @param float $final_score Điểm cuối kỳ
 * @param float $total_score Tổng điểm
 * @return bool True nếu thành công, False nếu thất bại
 */
function addGrade($student_id, $subject_id, $attendance_score, $midterm_score, $final_score, $total_score)
{
    $conn = getDbConnection();
    $sql = "INSERT INTO grades (student_id, subject_id, attendance_score, midterm_score, final_score, total_score) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iidddd", $student_id, $subject_id, $attendance_score, $midterm_score, $final_score, $total_score);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

/**
 * Lấy grade theo ID
 * @param int $id ID của grade
 * @return array|null Thông tin grade hoặc null nếu không tồn tại
 */
function getGradeById($id)
{
    $conn = getDbConnection();
    $sql = "SELECT g.id, g.student_id, g.subject_id, g.attendance_score, g.midterm_score, g.final_score, g.total_score,
                   s.student_code, s.full_name AS student_name, sub.subject_code, sub.subject_name
            FROM grades g
            LEFT JOIN students s ON g.student_id = s.id
            LEFT JOIN subjects sub ON g.subject_id = sub.id
            WHERE g.id = ?";
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $grade = null;
            if ($result && mysqli_num_rows($result) > 0) {
                $grade = mysqli_fetch_assoc($result);
            }
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $grade;
        }
    } catch (mysqli_sql_exception $ex) {}
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật grade
 * @param int $id ID của grade
 * @param int $student_id ID sinh viên
 * @param int $subject_id ID môn học
 * @param float $attendance_score Điểm danh
 * @param float $midterm_score Điểm giữa kỳ
 * @param float $final_score Điểm cuối kỳ
 * @param float $total_score Tổng điểm
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateGrade($id, $student_id, $subject_id, $attendance_score, $midterm_score, $final_score, $total_score)
{
    $conn = getDbConnection();
    $sql = "UPDATE grades SET student_id = ?, subject_id = ?, attendance_score = ?, midterm_score = ?, final_score = ?, total_score = ? WHERE id = ?";
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iiddddi", $student_id, $subject_id, $attendance_score, $midterm_score, $final_score, $total_score, $id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $success;
        }
    } catch (mysqli_sql_exception $ex) {}
    mysqli_close($conn);
    return false;
}

/**
 * Xóa grade
 * @param int $id ID của grade
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteGrade($id)
{
    $conn = getDbConnection();

    $sql = "DELETE FROM grades WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    try {
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            $success = mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $success;
        }
    } catch (mysqli_sql_exception $ex) {
        // ignore
    }

    mysqli_close($conn);
    return false;
}

/**
 * Kiểm tra xem sinh viên đã có điểm môn học này chưa
 * @param int $student_id ID sinh viên
 * @param int $subject_id ID môn học
 * @return bool True nếu đã tồn tại, False nếu chưa
 */
function checkGradeExists($student_id, $subject_id)
{
    $conn = getDbConnection();
    $sql = "SELECT id FROM grades WHERE student_id = ? AND subject_id = ?";
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $student_id, $subject_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $exists = mysqli_num_rows($result) > 0;

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $exists;
        }
    } catch (mysqli_sql_exception $ex) {
        // If query fails (e.g., missing table), treat as not exists
    }

    mysqli_close($conn);
    return false;
}

/**
 * Lấy tất cả sinh viên để hiển thị trong dropdown
 * @return array Danh sách sinh viên
 */
function getAllStudentsForDropdown()
{
    $conn = getDbConnection();

    $sql = "SELECT id, student_code, full_name FROM students ORDER BY full_name";
    $result = mysqli_query($conn, $sql);

    $students = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
    }

    mysqli_close($conn);
    return $students;
}

/**
 * Lấy tất cả môn học để hiển thị trong dropdown
 * @return array Danh sách môn học
 */
function getAllSubjectsForDropdown()
{
    $conn = getDbConnection();
    $subjects = [];
    $sql = "SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_name";
    try {
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $subjects[] = $row;
            }
        }
    } catch (mysqli_sql_exception $ex) {
        // subjects table may not exist yet — return empty list
    }

    mysqli_close($conn);
    return $subjects;
}

/**
 * Lấy điểm của một sinh viên cụ thể
 * @param int $student_id ID của sinh viên
 * @return array Danh sách điểm của sinh viên
 */
function getStudentGrades($student_id)
{
    $conn = getDbConnection();

    $sql = "SELECT g.id, g.grade, s.subject_code, s.subject_name 
            FROM grades g
            LEFT JOIN subjects s ON g.subject_id = s.id
            WHERE g.student_id = ?
            ORDER BY s.subject_name";

    $grades = [];
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $student_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $grades[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
    } catch (mysqli_sql_exception $ex) {
        // Handle error silently
    }

    mysqli_close($conn);
    return $grades;
}
?>