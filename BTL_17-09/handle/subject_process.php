
<?php
/*
========================================
 Chức năng: Xử lý các hành động quản trị môn học (CRUD, xóa nhiều, thêm, sửa, xóa)
========================================
*/
require_once __DIR__ . '/../functions/subject_functions.php';
require_once __DIR__ . '/../functions/permissions.php';

requireAdmin();

require_once __DIR__ . '/../functions/department_functions.php';

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create': handleCreateSubject(); break;
    case 'edit': handleEditSubject(); break;
    case 'delete': handleDeleteSubject(); break;
    case 'bulk_delete': handleBulkDelete(); break;
}

function handleCreateSubject() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../views/subject.php?error=Phương thức không hợp lệ'); exit();
    }
    $code = trim($_POST['subject_code'] ?? '');
    $name = trim($_POST['subject_name'] ?? '');
    $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;
    $dept = getDepartmentById($department_id);
    if (!$dept) {
        header('Location: ../views/subject/subject_create.php?error=Vui lòng chọn khoa hợp lệ'); exit();
    }
    if ($code === '' || $name === '') {
        header('Location: ../views/subject/subject_create.php?error=Vui lòng điền đầy đủ thông tin'); exit();
    }
    $ok = addSubject($code, $name, $department_id, null);
    if ($ok) header('Location: ../views/subject.php?success=Thêm môn thành công');
    else header('Location: ../views/subject/subject_create.php?error=Có lỗi khi thêm môn');
    exit();
}

function handleEditSubject() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../views/subject.php?error=Phương thức không hợp lệ'); exit(); }
    $id = (int)($_POST['id'] ?? 0);
    $code = trim($_POST['subject_code'] ?? '');
    $name = trim($_POST['subject_name'] ?? '');
    $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;
    if ($id <= 0 || $code === '' || $name === '') { header('Location: ../views/subject/subject_edit.php?id=' . $id . '&error=Thiếu thông tin'); exit(); }
    $dept = getDepartmentById($department_id);
    if (!$dept) { header('Location: ../views/subject/subject_edit.php?id=' . $id . '&error=Khoa không hợp lệ'); exit(); }
    $ok = updateSubject($id, $code, $name, $department_id, null);
    if ($ok) header('Location: ../views/subject.php?success=Cập nhật thành công');
    else header('Location: ../views/subject/subject_edit.php?id=' . $id . '&error=Cập nhật thất bại');
    exit();
}

function handleBulkDelete() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../views/subject.php?error=Phương thức không hợp lệ');
        exit();
    }

    if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
        header('Location: ../views/subject.php?error=Không có môn học nào được chọn để xóa');
        exit();
    }

    $ids = array_map('intval', $_POST['ids']);
    
    // Kiểm tra xem có điểm liên kết với các môn này không
    $conn = getDbConnection();
    $idList = implode(',', $ids);
    $sqlChk = "SELECT subject_id, COUNT(*) as cnt FROM grades WHERE subject_id IN ($idList) GROUP BY subject_id";
    $result = mysqli_query($conn, $sqlChk);
    
    if ($result) {
        $hasGrades = false;
        while ($row = mysqli_fetch_assoc($result)) {
            $hasGrades = true;
            break;
        }
        
        if ($hasGrades) {
            header('Location: ../views/subject.php?error=Không thể xóa môn học có điểm số liên kết');
            exit();
        }
    }
    mysqli_close($conn);
    
    // Thực hiện xóa nhiều môn học
    $success = true;
    foreach ($ids as $id) {
        if (!deleteSubject($id)) {
            $success = false;
            break;
        }
    }

    if ($success) {
        header('Location: ../views/subject.php?success=Xóa các môn học đã chọn thành công');
    } else {
        header('Location: ../views/subject.php?error=Có lỗi xảy ra khi xóa môn học');
    }
    exit();
}

function handleDeleteSubject() {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) { header('Location: ../views/subject.php?error=ID không hợp lệ'); exit(); }

    $conn = getDbConnection();
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt FROM grades WHERE subject_id = ?");
    $count = 0;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
            $row = mysqli_fetch_assoc($res);
            $count = intval($row['cnt'] ?? 0);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);

    if ($count > 0) { header('Location: ../views/subject.php?error=Không thể xóa môn có điểm liên quan'); exit(); }

    $ok = deleteSubject($id);
    if ($ok) header('Location: ../views/subject.php?success=Xóa thành công');
    else header('Location: ../views/subject.php?error=Xóa thất bại');
    exit();
}

?>