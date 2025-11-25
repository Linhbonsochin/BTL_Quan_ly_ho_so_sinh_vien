
<?php
/*
========================================
 Chức năng: Các hàm xử lý ngành học (majors), CRUD, lấy danh sách, thêm, sửa, xóa
========================================
*/
require_once __DIR__ . '/db_connection.php';

// Lấy tất cả ngành
function getAllMajors() {
    $conn = getDbConnection();
    $sql = "SELECT m.*, d.department_name FROM majors m LEFT JOIN departments d ON m.department_id = d.id ORDER BY m.major_name";
    $majors = [];
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $majors[] = $row;
        }
    }
    mysqli_close($conn);
    return $majors;
}

// Thêm ngành mới
function addMajor($major_code, $major_name, $department_id) {
    $conn = getDbConnection();
    $sql = "INSERT INTO majors (major_code, major_name, department_id) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $major_code, $major_name, $department_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

// Lấy ngành theo ID
function getMajorById($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM majors WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $major = null;
        if ($result && mysqli_num_rows($result) > 0) {
            $major = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $major;
    }
    mysqli_close($conn);
    return null;
}

// Cập nhật ngành
function updateMajor($id, $major_code, $major_name, $department_id) {
    $conn = getDbConnection();
    $sql = "UPDATE majors SET major_code = ?, major_name = ?, department_id = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssii", $major_code, $major_name, $department_id, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

// Xóa ngành
function deleteMajor($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM majors WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

/**
 * Xóa nhiều ngành cùng lúc (bulk delete)
 * Chức năng QUAN TRỌNG cho quản trị ngành
 * @param array $ids Mảng id ngành cần xóa
 * @return bool Thành công hoặc thất bại
 */
function deleteMajorsBulk($ids) {
    if (!is_array($ids) || count($ids) === 0) return false;
    $conn = getDbConnection();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE FROM majors WHERE id IN ($placeholders)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        $types = str_repeat('i', count($ids));
        mysqli_stmt_bind_param($stmt, $types, ...$ids);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

// Lấy tất cả ngành cho dropdown
function getAllMajorsForDropdown() {
    $conn = getDbConnection();
    $sql = "SELECT id, major_code, major_name FROM majors ORDER BY major_name";
    $majors = [];
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $majors[] = $row;
        }
    }
    mysqli_close($conn);
    return $majors;
}
