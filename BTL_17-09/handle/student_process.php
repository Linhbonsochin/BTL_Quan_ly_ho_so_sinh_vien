<?php
// Bắt đầu phiên làm việc
session_start();

// Import các file cần thiết
require_once('../functions/db_connection.php'); // File kết nối database
require_once('../functions/student_functions.php'); // File chứa các hàm xử lý sinh viên
require_once('../functions/permissions.php');

// Yêu cầu người dùng đã đăng nhập
requireLogin();
$isAdmin = isAdmin();

// Xử lý các yêu cầu POST (thêm/sửa sinh viên)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy loại hành động từ form
    $action = $_POST['action'] ?? '';

    // Xử lý thêm sinh viên mới (admin)
    if ($action === 'add') {
        if (!$isAdmin) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
            header('Location: ../index.php'); exit();
        }
        // Thu thập dữ liệu từ form
        $data = [
            'student_code' => trim($_POST['student_code']),
            'full_name' => trim($_POST['full_name']),
            'birth_date' => $_POST['birth_date'] ?: null,
            'gender' => $_POST['gender'] ?: null,
            'address' => trim($_POST['address']) ?: null,
            'class_id' => $_POST['class_id'] ?: null,
            'department_id' => $_POST['department_id'] ?: null,
            'user_id' => isset($_POST['user_id']) && $_POST['user_id'] !== '' ? intval($_POST['user_id']) : null,
            'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : null,
            'email' => isset($_POST['email']) ? trim($_POST['email']) : null
        ];

        // Kiểm tra dữ liệu bắt buộc
        if (empty($data['student_code']) || empty($data['full_name'])) {
            $_SESSION['error'] = 'Mã sinh viên và họ tên là bắt buộc.';
            header('Location: ../views/student/create_student.php');
            exit();
        }
        // Validate email if provided
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Địa chỉ email không hợp lệ.';
            header('Location: ../views/student/create_student.php');
            exit();
        }

        // Thực hiện thêm sinh viên
        $ok = addStudent($data);
        if ($ok) {
            $_SESSION['success'] = 'Thêm sinh viên thành công.';
        } else {
            $_SESSION['error'] = 'Lỗi khi thêm sinh viên.';
        }
        header('Location: ../views/student.php');
        exit();
    }

    // Xử lý cập nhật thông tin sinh viên (admin)
    if ($action === 'edit') {
        if (!$isAdmin) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
            header('Location: ../index.php'); exit();
        }
        // Lấy ID sinh viên cần cập nhật
        $id = intval($_POST['id']);
        
        // Thu thập dữ liệu từ form
        $data = [
            'student_code' => trim($_POST['student_code']),
            'full_name' => trim($_POST['full_name']),
            'birth_date' => $_POST['birth_date'] ?: null,
            'gender' => $_POST['gender'] ?: null,
            'address' => trim($_POST['address']) ?: null,
            'class_id' => $_POST['class_id'] ?: null,
            'department_id' => $_POST['department_id'] ?: null,
            'user_id' => isset($_POST['user_id']) && $_POST['user_id'] !== '' ? intval($_POST['user_id']) : null,
            'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : null,
            'email' => isset($_POST['email']) ? trim($_POST['email']) : null
        ];

        // Kiểm tra dữ liệu bắt buộc
        if (empty($data['student_code']) || empty($data['full_name'])) {
            $_SESSION['error'] = 'Mã sinh viên và họ tên là bắt buộc.';
            header('Location: ../views/student/edit_student.php?id=' . $id);
            exit();
        }
        // Validate email if provided
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Địa chỉ email không hợp lệ.';
            header('Location: ../views/student/edit_student.php?id=' . $id);
            exit();
        }

        // Thực hiện cập nhật sinh viên
        $ok = updateStudent($id, $data);
        if ($ok) {
            $_SESSION['success'] = 'Cập nhật sinh viên thành công.';
        } else {
            $_SESSION['error'] = 'Lỗi khi cập nhật sinh viên.';
        }
        header('Location: ../views/student.php');
        exit();
    }

    // Xử lý sinh viên tự cập nhật thông tin liên hệ
    if ($action === 'self_edit') {
        // Người dùng phải đăng nhập (đã requireLogin trên)
        $uid = $_SESSION['user_id'] ?? null;
        if (!$uid) {
            $_SESSION['error'] = 'Bạn cần đăng nhập để thực hiện thao tác này.';
            header('Location: ../index.php'); exit();
        }
        $student = getStudentByUserId($uid);
        if (!$student) {
            $_SESSION['error'] = 'Không tìm thấy hồ sơ sinh viên liên kết với tài khoản.';
            header('Location: ../views/profile.php'); exit();
        }

        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
        $email = isset($_POST['email']) ? trim($_POST['email']) : null;
        $address = isset($_POST['address']) ? trim($_POST['address']) : null;
        // Validate email
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Địa chỉ email không hợp lệ.';
            header('Location: ../views/profile.php'); exit();
        }
        $ok = updateStudentContact($student['id'], $phone, $email, $address);
        if ($ok) {
            $_SESSION['success'] = 'Cập nhật thông tin liên hệ thành công.';
        } else {
            $_SESSION['error'] = 'Lỗi khi cập nhật thông tin.';
        }
        header('Location: ../views/profile.php');
        exit();
    }
}

// Xử lý yêu cầu xóa sinh viên (qua phương thức GET)
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $ok = deleteStudent($id);
    if ($ok) {
        $_SESSION['success'] = 'Xóa sinh viên thành công.';
    } else {
        $_SESSION['error'] = 'Lỗi khi xóa sinh viên.';
    }
    header('Location: ../views/student.php');
    exit();
}

// Chuyển hướng về trang quản lý sinh viên nếu không có hành động nào được thực hiện
header('Location: ../views/student.php');
exit();
?>