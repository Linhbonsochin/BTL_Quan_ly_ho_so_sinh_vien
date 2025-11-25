
<?php
/*
========================================
Chức năng: Các hàm xử lý môn học (subjects), CRUD, lấy danh sách, thêm, sửa, xóa
========================================
*/
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy danh sách tất cả các môn học
 * @return array Mảng chứa thông tin các môn học
 */
function getAllSubjects()
{
    $conn = getDbConnection();
    // Truy vấn lấy thông tin môn học cùng khoa, sắp xếp theo tên
    $sql = "SELECT s.id, s.subject_code, s.subject_name, s.credits, s.department_id, d.department_name, s.major_id, m.major_name " .
        "FROM subjects s LEFT JOIN departments d ON s.department_id = d.id LEFT JOIN majors m ON s.major_id = m.id ORDER BY s.subject_name";
    $items = [];
    try {
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $items[] = $row;
            }
        }
    } catch (mysqli_sql_exception $ex) {
        // Bảng subjects có thể chưa tồn tại - trả về mảng rỗng
        // Có thể log lỗi nếu cần: $ex->getMessage()
    }
    mysqli_close($conn);
    return $items;
}

/**
 * Thêm môn học mới
 * @param string $subject_code Mã môn học
 * @param string $subject_name Tên môn học
 * @return boolean true nếu thêm thành công, false nếu thất bại
 */
function addSubject($subject_code, $subject_name, $department_id = null, $credits = null)
{
    $conn = getDbConnection();
    // Câu lệnh INSERT với prepared statement
    $sql = "INSERT INTO subjects (subject_code, subject_name, credits, department_id) VALUES (?, ?, ?, ?)";
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            // types: subject_code (s), subject_name (s), credits (i), department_id (i)
            mysqli_stmt_bind_param($stmt, "ssii", $subject_code, $subject_name, $credits, $department_id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $ok;
        }
    } catch (mysqli_sql_exception $ex) {
        // Bỏ qua lỗi và trả về false
    }
    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin môn học theo ID
 * @param int $id ID của môn học cần lấy thông tin
 * @return array|null Thông tin môn học hoặc null nếu không tồn tại
 */
function getSubjectById($id)
{
    $conn = getDbConnection();
    // Truy vấn lấy môn học theo ID
    $sql = "SELECT s.id, s.subject_code, s.subject_name, s.credits, s.department_id, d.department_name, s.major_id, m.major_name " .
        "FROM subjects s LEFT JOIN departments d ON s.department_id = d.id LEFT JOIN majors m ON s.major_id = m.id WHERE s.id = ? LIMIT 1";
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res && mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $row;
            }
            mysqli_stmt_close($stmt);
        }
    } catch (mysqli_sql_exception $ex) {
        // Bảng không tồn tại hoặc lỗi khác - trả về null
    }
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin môn học
 * @param int $id ID của môn học cần cập nhật
 * @param string $subject_code Mã môn học mới
 * @param string $subject_name Tên môn học mới
 * @return boolean true nếu cập nhật thành công, false nếu thất bại
 */
function updateSubject($id, $subject_code, $subject_name, $department_id = null, $credits = null)
{
    $conn = getDbConnection();
    // Câu lệnh UPDATE với prepared statement
    $sql = "UPDATE subjects SET subject_code = ?, subject_name = ?, credits = ?, department_id = ? WHERE id = ?";
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            // types: subject_code (s), subject_name (s), credits (i), department_id (i), id (i)
            mysqli_stmt_bind_param($stmt, "ssiii", $subject_code, $subject_name, $credits, $department_id, $id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $ok;
        }
    } catch (mysqli_sql_exception $ex) {
        // Bỏ qua lỗi
    }
    mysqli_close($conn);
    return false;
}

/**
 * Xóa môn học khỏi database
 * @param int $id ID của môn học cần xóa
 * @return boolean true nếu xóa thành công, false nếu thất bại
 */
function deleteSubject($id)
{
    $conn = getDbConnection();
    // Câu lệnh DELETE với prepared statement
    $sql = "DELETE FROM subjects WHERE id = ?";
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $ok;
        }
    } catch (mysqli_sql_exception $ex) {
        // Bỏ qua lỗi
    }
    mysqli_close($conn);
    return false;
}

?>