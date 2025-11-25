
<?php
/*
========================================
 Chức năng: Xử lý các hành động quản trị lớp học (CRUD, xóa nhiều, lấy danh sách, ...)
========================================
*/
require_once __DIR__ . '/../functions/class_functions.php';
require_once __DIR__ . '/../functions/permissions.php';

require_once __DIR__ . '/../functions/department_functions.php';


requireAdmin();
session_start();

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateClass();
        break;
    case 'edit':
        handleEditClass();
        break;
    case 'delete':
        handleDeleteClass();
        break;
    case 'bulk_delete':
        handleBulkDelete();
        break;
}

/**
 * Lấy tất cả danh sách lớp
 */
function handleGetAllClasses()
{
    return getAllClasses();
}

function handleGetClassById($id)
{
    return getClassById($id);
}

/**
 * Xử lý tạo lớp mới
 */
function handleCreateClass()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_POST['class_code']) || !isset($_POST['class_name'])) {
        header("Location: ../views/class/class_create.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $class_code = trim($_POST['class_code']);
    $class_name = trim($_POST['class_name']);
    $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;


    $dept = getDepartmentById($department_id);
    if (!$dept) {
        header("Location: ../views/class/class_create.php?error=Vui lòng chọn khoa hợp lệ");
        exit();
    }

    // Validate dữ liệu
    if (empty($class_code) || empty($class_name)) {
        header("Location: ../views/class/class_create.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Gọi hàm thêm lớp
    $result = addClass($class_code, $class_name, $department_id);

    if ($result) {
        header("Location: ../views/class.php?success=Thêm lớp thành công");
    } else {
        header("Location: ../views/class/class_create.php?error=Có lỗi xảy ra khi thêm lớp");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa lớp
 */
function handleEditClass()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_POST['id']) || !isset($_POST['class_code']) || !isset($_POST['class_name']) || !isset($_POST['department_id'])) {
        header("Location: ../views/class.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $id = (int) $_POST['id'];
    $class_code = trim($_POST['class_code']);
    $class_name = trim($_POST['class_name']);
    $department_id = (int) ($_POST['department_id'] ?? 0);

    $dept = getDepartmentById($department_id);
    if (!$dept) {
        header("Location: ../views/class/class_edit.php?id=" . $id . "&error=Khoa không hợp lệ");
        exit();
    }

    // Validate dữ liệu
        if (empty($class_code) || empty($class_name)) {
        header("Location: ../views/class/class_edit.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Gọi function để cập nhật lớp
    $result = updateClass($id, $class_code, $class_name, $department_id);

    if ($result) {
        header("Location: ../views/class.php?success=Cập nhật lớp thành công");
    } else {
        header("Location: ../views/class/class_edit.php?id=" . $id . "&error=Cập nhật lớp thất bại");
    }
    exit();
}

/**
 * Xử lý xóa lớp
 */
/**
 * Xử lý xóa nhiều lớp
 */
function handleBulkDelete() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
        header("Location: ../views/class.php?error=Không có lớp nào được chọn để xóa");
        exit();
    }

    $ids = array_map('intval', $_POST['ids']);
    
    // Kiểm tra xem có sinh viên liên kết với các lớp này không (an toàn)
    $conn = getDbConnection();

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $sqlChk = "SELECT class_id, COUNT(*) as cnt FROM students WHERE class_id IN ($placeholders) GROUP BY class_id";
    $stmt = mysqli_prepare($conn, $sqlChk);
    if ($stmt) {
        $params = array_merge([$types], $ids);
        $refs = [];
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }
        call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $refs));
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $classesWithStudents = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $classesWithStudents[] = (int)$row['class_id'];
        }
        mysqli_stmt_close($stmt);
    } else {
        $classesWithStudents = [];
    }
    mysqli_close($conn);

    if (!empty($classesWithStudents)) {
        $_SESSION['error'] = 'Không thể xóa các lớp có sinh viên liên kết: ' . implode(', ', $classesWithStudents);
        header('Location: ../views/class.php');
        exit();
    }

    // Thực hiện xóa nhiều lớp
    $deleted = 0;
    $failed = 0;
    foreach ($ids as $id) {
        if (deleteClass($id)) {
            $deleted++;
        } else {
            $failed++;
        }
    }

    if ($deleted > 0) {
        $_SESSION['success'] = "Đã xóa $deleted lớp thành công.";
    }
    if ($failed > 0) {
        $_SESSION['error'] = "Không thể xóa $failed lớp (có liên kết hoặc lỗi).";
    }

    header('Location: ../views/class.php');
    exit();
}

function handleDeleteClass()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/class.php?error=Không tìm thấy ID lớp");
        exit();
    }

    $id = $_GET['id'];

    // Validate ID là số
    if (!is_numeric($id)) {
        header("Location: ../views/class.php?error=ID lớp không hợp lệ");
        exit();
    }

    // Kiểm tra xem có sinh viên liên kết với lớp này không
    $connChk = getDbConnection();
    $sqlChk = "SELECT COUNT(*) as cnt FROM students WHERE class_id = ?";
    $stmtChk = mysqli_prepare($connChk, $sqlChk);
    if ($stmtChk) {
        mysqli_stmt_bind_param($stmtChk, "i", $id);
        mysqli_stmt_execute($stmtChk);
        $resChk = mysqli_stmt_get_result($stmtChk);
        $row = mysqli_fetch_assoc($resChk);
        $count = intval($row['cnt'] ?? 0);
        mysqli_stmt_close($stmtChk);
    } else {
        $count = 0;
    }
    mysqli_close($connChk);

    if ($count > 0) {
        header("Location: ../views/class.php?error=Không thể xóa lớp có sinh viên liên kết ($count)");
        exit();
    }

    // Gọi function để xóa lớp
    $result = deleteClass($id);

    if ($result) {
        header("Location: ../views/class.php?success=Xóa lớp thành công");
    } else {
        header("Location: ../views/class.php?error=Xóa lớp thất bại");
    }
    exit();
}
?>