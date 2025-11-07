<?php
// Import file kết nối database
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy danh sách tất cả các khoa
 * @return array Mảng chứa thông tin các khoa
 */
function getAllDepartments()
{
    global $conn;
    // Truy vấn lấy thông tin khoa, sắp xếp theo mã khoa
    $stmt = $conn->query("SELECT id, department_code, department_name FROM departments ORDER BY department_code");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy thông tin khoa theo ID
 * @param int $id ID của khoa cần lấy thông tin
 * @return array|false Thông tin khoa hoặc false nếu không tồn tại
 */
function getDepartmentById($id)
{
    global $conn;
    // Truy vấn lấy khoa theo ID với prepared statement
    $stmt = $conn->prepare("SELECT id, department_code, department_name FROM departments WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Thêm khoa mới
 * @param string $code Mã khoa
 * @param string $name Tên khoa
 * @return boolean true nếu thêm thành công, false nếu thất bại
 */
function addDepartment($code, $name)
{
    global $conn;
    // Câu lệnh INSERT với prepared statement
    $stmt = $conn->prepare("INSERT INTO departments (department_code, department_name) VALUES (:code, :name)");
    return $stmt->execute([':code' => $code, ':name' => $name]);
}

/**
 * Cập nhật thông tin khoa
 * @param int $id ID của khoa cần cập nhật
 * @param string $code Mã khoa mới
 * @param string $name Tên khoa mới
 * @return boolean true nếu cập nhật thành công, false nếu thất bại
 */
function updateDepartment($id, $code, $name)
{
    global $conn;
    // Câu lệnh UPDATE với prepared statement
    $stmt = $conn->prepare("UPDATE departments SET department_code = :code, department_name = :name WHERE id = :id");
    return $stmt->execute([':code' => $code, ':name' => $name, ':id' => $id]);
}

/**
 * Xóa khoa khỏi database
 * @param int $id ID của khoa cần xóa
 * @return boolean true nếu xóa thành công, false nếu thất bại
 * @note Cần xem xét kiểm tra sinh viên/lớp phụ thuộc trước khi xóa
 */
function deleteDepartment($id)
{
    global $conn;
    // Câu lệnh DELETE với prepared statement
    $stmt = $conn->prepare("DELETE FROM departments WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

?>