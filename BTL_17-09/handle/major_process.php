
<?php
require_once __DIR__ . '/../functions/major_functions.php'; // Import các hàm ngành
require_once __DIR__ . '/../functions/permissions.php'; // Kiểm tra quyền admin
requireAdmin(); // Chỉ admin mới được thao tác

/*
========================================
 Chức năng: Xử lý các hành động quản trị ngành học (CRUD, xóa nhiều, thêm, sửa, xóa)
========================================
*/
$action = '';
// Lấy loại hành động từ request (GET hoặc POST)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}


// Xử lý các hành động chính của ngành
switch ($action) {
    case 'create':
        handleCreateMajor(); // Thêm ngành mới
        break;
    case 'edit':
        handleEditMajor(); // Sửa ngành
        break;
    case 'delete':
        handleDeleteMajor(); // Xóa ngành đơn lẻ
        break;
    case 'bulk_delete':
        handleBulkDeleteMajors(); // Xóa nhiều ngành cùng lúc (QUAN TRỌNG)
        break;
}

function handleCreateMajor() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../views/major.php?error=Phương thức không hợp lệ');
        exit();
    }
    if (!isset($_POST['major_code'], $_POST['major_name'], $_POST['department_id'])) {
        header('Location: ../views/major/major_create.php?error=Thiếu thông tin');
        exit();
    }
    $major_code = trim($_POST['major_code']);
    $major_name = trim($_POST['major_name']);
    $department_id = intval($_POST['department_id']);
    if ($major_code === '' || $major_name === '' || $department_id <= 0) {
        header('Location: ../views/major/major_create.php?error=Vui lòng điền đầy đủ thông tin');
        exit();
    }
    if (addMajor($major_code, $major_name, $department_id)) {
        header('Location: ../views/major.php?success=Thêm ngành thành công');
    } else {
        header('Location: ../views/major/major_create.php?error=Thêm ngành thất bại');
    }
    exit();
}


/**
 * Xử lý xóa nhiều ngành cùng lúc (bulk delete)
 * Nhận mảng ids từ form, gọi hàm xóa nhiều ngành
 * Đây là chức năng QUAN TRỌNG cho quản trị ngành
 */
function handleBulkDeleteMajors() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../views/major.php?error=Phương thức không hợp lệ');
        exit();
    }
    if (!isset($_POST['ids']) || !is_array($_POST['ids']) || count($_POST['ids']) === 0) {
        header('Location: ../views/major.php?error=Không có ngành nào được chọn');
        exit();
    }
    $ids = array_map('intval', $_POST['ids']);
    require_once __DIR__ . '/../functions/major_functions.php';
    if (deleteMajorsBulk($ids)) {
        header('Location: ../views/major.php?success=Đã xóa các ngành đã chọn');
    } else {
        header('Location: ../views/major.php?error=Xóa nhiều ngành thất bại');
    }
    exit();
}

function handleEditMajor() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../views/major.php?error=Phương thức không hợp lệ');
        exit();
    }
    if (!isset($_POST['id'], $_POST['major_code'], $_POST['major_name'], $_POST['department_id'])) {
        header('Location: ../views/major.php?error=Thiếu thông tin');
        exit();
    }
    $id = intval($_POST['id']);
    $major_code = trim($_POST['major_code']);
    $major_name = trim($_POST['major_name']);
    $department_id = intval($_POST['department_id']);
    if ($id <= 0 || $major_code === '' || $major_name === '' || $department_id <= 0) {
        header('Location: ../views/major/major_edit.php?id=' . $id . '&error=Vui lòng điền đầy đủ thông tin');
        exit();
    }
    if (updateMajor($id, $major_code, $major_name, $department_id)) {
        header('Location: ../views/major.php?success=Cập nhật ngành thành công');
    } else {
        header('Location: ../views/major/major_edit.php?id=' . $id . '&error=Cập nhật ngành thất bại');
    }
    exit();
}

function handleDeleteMajor() {
    if (!isset($_GET['id'])) {
        header('Location: ../views/major.php?error=Thiếu ID ngành');
        exit();
    }
    $id = intval($_GET['id']);
    if ($id <= 0) {
        header('Location: ../views/major.php?error=ID ngành không hợp lệ');
        exit();
    }
    if (deleteMajor($id)) {
        header('Location: ../views/major.php?success=Xóa ngành thành công');
    } else {
        header('Location: ../views/major.php?error=Xóa ngành thất bại');
    }
    exit();
}
