<?php
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/department_functions.php';
requireAdmin();

session_start();

// Add department
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $code = trim($_POST['department_code'] ?? '');
        $name = trim($_POST['department_name'] ?? '');
        if ($code === '' || $name === '') {
            $_SESSION['error'] = 'Mã khoa và tên khoa không được để trống.';
            header('Location: ../views/department/create_department.php');
            exit();
        }
        try {
            if (addDepartment($code, $name)) {
                $_SESSION['success'] = 'Thêm khoa thành công.';
            } else {
                $_SESSION['error'] = 'Không thể thêm khoa.';
            }
        } catch (PDOException $e) {
            // likely duplicate code
            $_SESSION['error'] = 'Lỗi khi thêm: ' . $e->getMessage();
        }
        header('Location: ../views/department.php');
        exit();
    }
    if ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $code = trim($_POST['department_code'] ?? '');
        $name = trim($_POST['department_name'] ?? '');
        if ($id <= 0 || $code === '' || $name === '') {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
            header('Location: ../views/department.php');
            exit();
        }
        try {
            if (updateDepartment($id, $code, $name)) {
                $_SESSION['success'] = 'Cập nhật khoa thành công.';
            } else {
                $_SESSION['error'] = 'Không thể cập nhật khoa.';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Lỗi khi cập nhật: ' . $e->getMessage();
        }
        header('Location: ../views/department.php');
        exit();
    }
    
    // Bulk delete action
    if ($action === 'bulk_delete') {
        $ids = $_POST['ids'] ?? [];
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, function($id) { return $id > 0; });
        
        if (empty($ids)) {
            $_SESSION['error'] = 'Không có khoa nào được chọn để xóa.';
            header('Location: ../views/department.php');
            exit();
        }

        $success = 0;
        $failed = 0;
        foreach ($ids as $id) {
            if (deleteDepartment($id)) {
                $success++;
            } else {
                $failed++;
            }
        }

        if ($success > 0) {
            $_SESSION['success'] = "Đã xóa thành công $success khoa.";
        }
        if ($failed > 0) {
            $_SESSION['error'] = "Không thể xóa $failed khoa do đang có liên kết.";
        }
        
        header('Location: ../views/department.php');
        exit();
    }
}

// Delete via GET (simple)
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id > 0) {
        if (deleteDepartment($id)) {
            $_SESSION['success'] = 'Xóa khoa thành công.';
        } else {
            $_SESSION['error'] = 'Không thể xóa khoa. Có thể đang có liên kết.';
        }
    } else {
        $_SESSION['error'] = 'ID không hợp lệ.';
    }
}

header('Location: ../views/department.php');
exit();
