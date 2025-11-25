
<?php

/*
========================================
 Chức năng: Các hàm xử lý lớp học (CRUD, lấy danh sách, thêm, sửa, xóa)
========================================
*/
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy tất cả lớp
 * @return array
 */
function getAllClasses()
{
    $conn = getDbConnection();

    $sql = "SELECT c.id, c.class_code, c.class_name, c.department_id, d.department_name, c.major_id, m.major_name " .
        "FROM classes c LEFT JOIN departments d ON c.department_id = d.id LEFT JOIN majors m ON c.major_id = m.id ORDER BY c.id DESC";
    $result = mysqli_query($conn, $sql);

    $classes = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($r = mysqli_fetch_assoc($result)) {
            $classes[] = $r;
        }
    }

    mysqli_close($conn);
    return $classes;
}

/**
 * Thêm lớp mới
 * @param string $class_code
 * @param string $class_name
 * @return bool
 */
function addClass($class_code, $class_name, $department_id)
{
    $conn = getDbConnection();
    $sql = "INSERT INTO classes (class_code, class_name, department_id) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $class_code, $class_name, $department_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Lấy lớp theo ID
 * @param int $id
 * @return array|null
 */
function getClassById($id)
{
    $conn = getDbConnection();
    $sql = "SELECT c.id, c.class_code, c.class_name, c.department_id, d.department_name, c.major_id, m.major_name " .
        "FROM classes c LEFT JOIN departments d ON c.department_id = d.id LEFT JOIN majors m ON c.major_id = m.id WHERE c.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $student = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $student;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật lớp
 * @param int $id
 * @param string $class_code
 * @param string $class_name
 * @return bool
 */
function updateClass($id, $class_code, $class_name, $department_id)
{
    $conn = getDbConnection();
    $sql = "UPDATE classes SET class_code = ?, class_name = ?, department_id = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssii", $class_code, $class_name, $department_id, $id);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Xóa lớp
 * @param int $id
 * @return bool
 */
function deleteClass($id)
{
    $conn = getDbConnection();

    $sql = "DELETE FROM classes WHERE id = ?";
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

?>