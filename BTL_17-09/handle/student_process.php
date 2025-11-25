
<?php
/*
========================================
 Chức năng: Xử lý các hành động quản trị sinh viên (CRUD, xóa nhiều, thêm, sửa, upload ảnh, xác thực)
========================================
*/
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

    function handle_uploaded_image($fileArray, $fieldPrefix = 'file') {
        $allowedMime = ['image/jpeg','image/png','image/webp','image/gif'];
        $maxBytes = 5 * 1024 * 1024; // 5MB
        if (empty($fileArray) || $fileArray['error'] !== UPLOAD_ERR_OK) return null;
        if ($fileArray['size'] > $maxBytes) return ['error' => 'Kích thước file vượt quá 5MB'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileArray['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowedMime)) return ['error' => 'Định dạng file không được hỗ trợ'];

        $ext = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
        $studentImageDir = __DIR__ . '/../image/students/';
        if (!is_dir($studentImageDir)) @mkdir($studentImageDir, 0755, true);
        $newName = uniqid($fieldPrefix . '_') . '.' . $ext;
        $dest = $studentImageDir . $newName;
        if (move_uploaded_file($fileArray['tmp_name'], $dest)) {
            $webBase = '/BTL_17-09/image/students/';
            return ['path' => $webBase . $newName, 'disk' => $dest];
        }
        return ['error' => 'Không thể lưu file lên server'];
    }

    // Xử lý thêm sinh viên mới (admin)
    if ($action === 'add') {
        if (!$isAdmin) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
            header('Location: ../index.php'); exit();
        }
        // Thu thập dữ liệu từ form (lấy tất cả trường, kể cả trường mở rộng)
        $data = $_POST;
        $data['student_code'] = trim($_POST['student_code']);
        $data['full_name'] = trim($_POST['full_name']);
        $data['address'] = isset($_POST['address']) ? trim($_POST['address']) : null;
        $data['user_id'] = isset($_POST['user_id']) && $_POST['user_id'] !== '' ? intval($_POST['user_id']) : null;
        $data['class_id'] = isset($_POST['class_id']) && $_POST['class_id'] !== '' ? $_POST['class_id'] : null;
        $data['department_id'] = isset($_POST['department_id']) && $_POST['department_id'] !== '' ? $_POST['department_id'] : null;

        // Kiểm tra dữ liệu bắt buộc
        if (empty($data['student_code']) || empty($data['full_name'])) {
            $_SESSION['error'] = 'Mã sinh viên và họ tên là bắt buộc.';
            header('Location: ../views/student/create_student.php');
            exit();
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Địa chỉ email không hợp lệ.';
            header('Location: ../views/student/create_student.php');
            exit();
        }

        // Xử lý file ảnh (avatar, cccd_front, cccd_back) với validate
        $uploadedFields = ['avatar', 'cccd_front', 'cccd_back'];
        foreach ($uploadedFields as $field) {
            if (!empty($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $res = handle_uploaded_image($_FILES[$field], $field);
                if (isset($res['error'])) {
                    $_SESSION['error'] = 'Lỗi upload ảnh [' . $field . ']: ' . $res['error'];
                    header('Location: ../views/student/create_student.php'); exit();
                }
                if (isset($res['path'])) $data[$field . '_path'] = $res['path'];
            } else {
                // Nếu không upload, không thêm trường _path vào $data (để giữ giá trị mặc định NULL)
                if (isset($data[$field . '_path'])) unset($data[$field . '_path']);
            }
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
        
        // Thu thập dữ liệu từ form (lấy tất cả trường, kể cả trường mở rộng)
        $data = $_POST;
        $data['student_code'] = trim($_POST['student_code']);
        $data['full_name'] = trim($_POST['full_name']);
        $data['address'] = isset($_POST['address']) ? trim($_POST['address']) : null;
        $data['user_id'] = isset($_POST['user_id']) && $_POST['user_id'] !== '' ? intval($_POST['user_id']) : null;
        $data['class_id'] = isset($_POST['class_id']) && $_POST['class_id'] !== '' ? $_POST['class_id'] : null;
        $data['department_id'] = isset($_POST['department_id']) && $_POST['department_id'] !== '' ? $_POST['department_id'] : null;

        // Kiểm tra dữ liệu bắt buộc
        if (empty($data['student_code']) || empty($data['full_name'])) {
            $_SESSION['error'] = 'Mã sinh viên và họ tên là bắt buộc.';
            header('Location: ../views/student/edit_student.php?id=' . $id);
            exit();
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Địa chỉ email không hợp lệ.';
            header('Location: ../views/student/edit_student.php?id=' . $id);
            exit();
        }

        // Xử lý file ảnh (avatar, cccd_front, cccd_back) nếu được upload để cập nhật
        $uploadedFields = ['avatar', 'cccd_front', 'cccd_back'];
        $existing = getStudentById($id);
        foreach ($uploadedFields as $field) {
            if (!empty($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $res = handle_uploaded_image($_FILES[$field], $field);
                if (isset($res['error'])) {
                    $_SESSION['error'] = 'Lỗi upload ảnh [' . $field . ']: ' . $res['error'];
                    header('Location: ../views/student/edit_student.php?id=' . $id); exit();
                }
                if (isset($res['path'])) {
                    // attempt to remove old file on disk
                    $oldPath = $existing[$field . '_path'] ?? null;
                    if ($oldPath) {
                        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], "\\/");
                        $diskOld = $docRoot . $oldPath;
                        if (file_exists($diskOld)) @unlink($diskOld);
                    }
                    $data[$field . '_path'] = $res['path'];
                }
            }
            // Nếu không upload mới, không thêm/truyền trường _path vào $data (giữ nguyên DB)
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

    // Xử lý xóa nhiều sinh viên (admin)
    if ($action === 'bulk_delete') {
        if (!$isAdmin) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
            header('Location: ../index.php'); exit();
        }

        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids) || count($ids) === 0) {
            $_SESSION['error'] = 'Không có sinh viên nào được chọn để xóa.';
            header('Location: ../views/student.php'); exit();
        }

        // Thực hiện xóa an toàn: hàm sẽ bỏ qua sinh viên có điểm liên quan
        $totalRequested = count($ids);
        $deleted = deleteStudents($ids);
        $skipped = $totalRequested - $deleted;

        if ($deleted > 0) {
            $_SESSION['success'] = "Đã xóa thành công $deleted sinh viên.";
        }
        if ($skipped > 0) {
            // nếu có cả success và skipped, nối thông báo
            $msg = "Có $skipped sinh viên không thể xóa do có dữ liệu liên quan (ví dụ: điểm).";
            if (isset($_SESSION['success'])) $_SESSION['error'] = $msg; else $_SESSION['error'] = $msg;
        }

        header('Location: ../views/student.php');
        exit();
    }
    
    // Xử lý xoá ảnh (admin) - action remove_image
    if ($action === 'remove_image') {
        if (!$isAdmin) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
            header('Location: ../index.php'); exit();
        }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $field = isset($_POST['field']) ? trim($_POST['field']) : '';
        $allowed = ['avatar','cccd_front','cccd_back'];
        if ($id <= 0 || !in_array($field, $allowed)) {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ.';
            header('Location: ../views/student.php'); exit();
        }
        $student = getStudentById($id);
        if (!$student) {
            $_SESSION['error'] = 'Không tìm thấy sinh viên.';
            header('Location: ../views/student.php'); exit();
        }
        $col = $field . '_path';
        $old = $student[$col] ?? null;
        if ($old) {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], "\\/");
            $diskOld = $docRoot . $old;
            if (file_exists($diskOld)) @unlink($diskOld);
        }
        // update DB to set column to NULL
        $ok = updateStudent($id, [$col => null]);
        if ($ok) {
            $_SESSION['success'] = 'Đã xóa ảnh.';
        } else {
            $_SESSION['error'] = 'Lỗi khi cập nhật cơ sở dữ liệu.';
        }
        header('Location: ../views/student/edit_student.php?id=' . $id);
        exit();
    }

    // Xử lý xoá ảnh do chính sinh viên yêu cầu (self removal)
    if ($action === 'remove_image_self') {
        // User must be logged in (requireLogin already called) and own the profile
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $field = isset($_POST['field']) ? trim($_POST['field']) : '';
        $allowed = ['avatar','cccd_front','cccd_back'];
        if ($id <= 0 || !in_array($field, $allowed)) {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ.';
            header('Location: ../views/profile.php'); exit();
        }
        $student = getStudentById($id);
        if (!$student) {
            $_SESSION['error'] = 'Không tìm thấy sinh viên.';
            header('Location: ../views/profile.php'); exit();
        }
        $currentUser = getCurrentUser();
        $owns = $currentUser && isset($currentUser['id']) && $student['user_id'] == $currentUser['id'];
        if (!($owns || $isAdmin)) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
            header('Location: ../views/profile.php'); exit();
        }
        $col = $field . '_path';
        $old = $student[$col] ?? null;
        if ($old) {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], "\\/");
            $diskOld = $docRoot . $old;
            if (file_exists($diskOld)) @unlink($diskOld);
        }
        $ok = updateStudent($id, [$col => null]);
        if ($ok) {
            $_SESSION['success'] = 'Đã xóa ảnh.';
        } else {
            $_SESSION['error'] = 'Lỗi khi cập nhật cơ sở dữ liệu.';
        }
        header('Location: ../views/profile.php');
        exit();
    }

    // Xử lý sinh viên đề xuất/cập nhật thông tin cơ bản (self) - chỉ chủ hồ sơ hoặc admin

    if ($action === 'self_update_basic') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) { $_SESSION['error'] = 'Yêu cầu không hợp lệ.'; header('Location: ../views/profile.php'); exit(); }
        $student = getStudentById($id);
        if (!$student) { $_SESSION['error'] = 'Không tìm thấy hồ sơ.'; header('Location: ../views/profile.php'); exit(); }
        $currentUser = getCurrentUser();
        $owns = $currentUser && isset($currentUser['id']) && $student['user_id'] == $currentUser['id'];
        if (!($owns || $isAdmin)) { $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.'; header('Location: ../views/profile.php'); exit(); }

        // Lấy tất cả các trường từ form (trừ action, id)
        $data = [];
        foreach ($_POST as $k => $v) {
            if (in_array($k, ['action','id'])) continue;
            $data[$k] = is_string($v) ? trim($v) : $v;
        }

        // Validate required
        if (empty($data['full_name'])) { $_SESSION['error'] = 'Họ tên không được để trống.'; header('Location: ../views/profile.php'); exit(); }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $_SESSION['error'] = 'Email không hợp lệ.'; header('Location: ../views/profile.php'); exit(); }

        $ok = updateStudent($id, $data);
        if ($ok) {
            $_SESSION['success'] = 'Đã gửi đề xuất / cập nhật thông tin thành công.';
        } else {
            $_SESSION['error'] = 'Lỗi khi cập nhật thông tin.';
        }
        header('Location: ../views/profile.php');
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