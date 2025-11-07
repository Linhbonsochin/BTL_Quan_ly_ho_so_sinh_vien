<?php
// Bắt đầu phiên làm việc
session_start();

// Import các file cần thiết
require_once __DIR__ . '/../functions/db_connection.php'; // File kết nối database
require_once __DIR__ . '/../functions/auth.php'; // File chứa các hàm xác thực

// Kiểm tra xem form đăng nhập đã được submit chưa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    handleLogin();
}

/**
 * Hàm xử lý đăng nhập người dùng
 */
function handleLogin() {
    // Kết nối database
    $conn = getDbConnection();
    
    // Lấy thông tin đăng nhập từ form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kiểm tra xem đã nhập đủ thông tin chưa
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng nhập đầy đủ username và password!';
        header('Location: ../index.php');
        exit();
    }

    // Xác thực thông tin đăng nhập
    $user = authenticateUser($conn, $username, $password);
    if ($user) {
        // Kiểm tra xem vai trò được chọn có khớp với vai trò trong database không
        $selectedRole = $_POST['role'] ?? '';
        if ($selectedRole && $selectedRole !== $user['role']) {
            $_SESSION['error'] = 'Vai trò đăng nhập không khớp với tài khoản.';
            mysqli_close($conn);
            header('Location: ../index.php');
            exit();
        }

        // Lưu thông tin người dùng vào session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Nếu người dùng là sinh viên thì lấy thêm student_id
        if ($user['role'] === 'student') {
            $sql = "SELECT id FROM students WHERE student_code = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $user['username']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($student = mysqli_fetch_assoc($result)) {
                    $_SESSION['student_id'] = $student['id'];
                }
                mysqli_stmt_close($stmt);
            }
        }
        
        // Thông báo đăng nhập thành công
        $_SESSION['success'] = 'Đăng nhập thành công!';
        mysqli_close($conn);

        // Chuyển hướng dựa theo vai trò
        if ($user['role'] === 'admin') {
            header('Location: /BTL_17-09/views/admin_dashboard.php');
        } else {
            header('Location: /BTL_17-09/views/home.php'); // Trang chủ cho sinh viên
        }
        exit();
    }

    // Thông báo lỗi nếu đăng nhập thất bại
    $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    mysqli_close($conn);
    header('Location: ../index.php');
    exit();
}
?>