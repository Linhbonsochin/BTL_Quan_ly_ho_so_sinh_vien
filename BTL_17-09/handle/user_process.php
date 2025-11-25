
<?php
/*
========================================
 Chức năng: Xử lý các hành động quản trị tài khoản người dùng (CRUD, xóa nhiều, thêm, sửa, xóa)
========================================
*/
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/user_functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireAdmin();

if (isset($_POST['action']) && $_POST['action'] === 'create') {
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['role'])) {
        header('Location: ../views/user/create_user.php?error=Vui lòng điền đầy đủ thông tin');
        exit();
    }

    $result = createUser($_POST['username'], $_POST['password'], $_POST['role']);
    if ($result[0]) {
        $_SESSION['success'] = $result[1];
        header('Location: ../views/user.php');
    } else {
        header('Location: ../views/user/create_user.php?error=' . urlencode($result[1]));
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    if (empty($_POST['id']) || empty($_POST['username']) || empty($_POST['role'])) {
        header('Location: ../views/user.php?error=Thiếu thông tin cần thiết');
        exit();
    }

    $result = updateUser(
        $_POST['id'],
        $_POST['username'],
        !empty($_POST['password']) ? $_POST['password'] : null,
        $_POST['role']
    );

    if ($result[0]) {
        $_SESSION['success'] = $result[1];
        header('Location: ../views/user.php');
    } else {
        header('Location: ../views/user/edit_user.php?id=' . $_POST['id'] . '&error=' . urlencode($result[1]));
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
    $ids = $_POST['ids'] ?? [];
    if (!is_array($ids) || empty($ids)) {
        $_SESSION['error'] = 'Không có tài khoản nào được chọn để xóa.';
        header('Location: ../views/user.php');
        exit();
    }

    $ids = array_map('intval', $ids);
    $ids = array_filter($ids, fn($v) => $v > 0);

    $deleted = 0;
    $skipped = [];
    $failed = 0;

    foreach ($ids as $id) {
        $user = getUserById($id);
        if (!$user) {
            $failed++;
            continue;
        }
        if (($user['role'] ?? '') === 'admin') {
            $skipped[] = $user['username'] ?? $id;
            continue;
        }

        $res = deleteUser($id);
        if ($res[0]) {
            $deleted++;
        } else {
            $failed++;
        }
    }

    if ($deleted > 0) $_SESSION['success'] = "Đã xóa $deleted tài khoản.";
    if ($failed > 0) $_SESSION['error'] = "Không thể xóa $failed tài khoản do lỗi.";
    if (!empty($skipped)) {
        $_SESSION['error'] = ($_SESSION['error'] ?? '') . ' Không xóa tài khoản quản trị: ' . implode(', ', $skipped);
    }

    header('Location: ../views/user.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $user = getUserById($id);
    if (!$user) {
        $_SESSION['error'] = 'Tài khoản không tồn tại.';
        header('Location: ../views/user.php');
        exit();
    }
    if (($user['role'] ?? '') === 'admin') {
        $_SESSION['error'] = 'Không thể xóa tài khoản quản trị.';
        header('Location: ../views/user.php');
        exit();
    }

    $result = deleteUser($id);
    if ($result[0]) {
        $_SESSION['success'] = $result[1];
    } else {
        $_SESSION['error'] = $result[1];
    }
    header('Location: ../views/user.php');
    exit();
}

header('Location: ../views/user.php?error=Yêu cầu không hợp lệ');
exit();