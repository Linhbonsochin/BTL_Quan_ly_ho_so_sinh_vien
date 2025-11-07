<?php
require_once __DIR__ . '/../functions/permissions.php';
requireAdmin();
require_once __DIR__ . '/../functions/user_functions.php';

// Create user
if ($_POST['action'] === 'create') {
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

// Update user
if ($_POST['action'] === 'edit') {
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

// Delete user
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $result = deleteUser($_GET['id']);
    if ($result[0]) {
        $_SESSION['success'] = $result[1];
    } else {
        $_SESSION['error'] = $result[1];
    }
    header('Location: ../views/user.php');
    exit();
}

// Invalid request
header('Location: ../views/user.php?error=Yêu cầu không hợp lệ');
exit();