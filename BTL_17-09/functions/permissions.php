<?php
// Import file xác thực người dùng
require_once __DIR__ . '/auth.php';

/**
 * Kiểm tra xem người dùng hiện tại có phải là admin không
 * @return boolean true nếu là admin, false nếu không phải
 */
function isAdmin()
{
    $u = getCurrentUser();
    return $u && isset($u['role']) && $u['role'] === 'admin';
}

/**
 * Kiểm tra xem người dùng hiện tại có phải là sinh viên không
 * @return boolean true nếu là sinh viên, false nếu không phải
 */
function isStudent()
{
    $u = getCurrentUser();
    return $u && isset($u['role']) && $u['role'] === 'student';
}

/**
 * Yêu cầu người dùng phải đăng nhập để truy cập
 * Nếu chưa đăng nhập sẽ chuyển hướng về trang đăng nhập
 * @param string $redirect đường dẫn trang đăng nhập (mặc định: '../index.php')
 */
function requireLogin($redirect = '../index.php')
{
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Bạn cần đăng nhập để truy cập trang này.';
        header('Location: ' . $redirect);
        exit();
    }
}

/**
 * Yêu cầu người dùng phải có quyền admin để truy cập
 * Nếu không phải admin sẽ chuyển hướng về trang được chỉ định
 * @param string $redirect đường dẫn chuyển hướng khi không đủ quyền (mặc định: '../index.php')
 */
function requireAdmin($redirect = '../index.php')
{
    requireLogin($redirect);
    if (!isAdmin()) {
        $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
        header('Location: ' . $redirect);
        exit();
    }
}

/**
 * Yêu cầu người dùng phải là sinh viên để truy cập
 * Nếu không phải sinh viên sẽ chuyển hướng về trang được chỉ định
 * @param string $redirect đường dẫn chuyển hướng khi không đủ quyền (mặc định: '../index.php')
 */
function requireStudent($redirect = '../index.php')
{
    requireLogin($redirect);
    if (!isStudent()) {
        $_SESSION['error'] = 'Chức năng chỉ dành cho sinh viên.';
        header('Location: ' . $redirect);
        exit();
    }
}

?>