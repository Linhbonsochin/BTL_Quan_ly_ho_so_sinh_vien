<?php
// Import các file cần thiết
require_once __DIR__ . '/../functions/grade_functions.php'; // File chứa các hàm xử lý điểm
require_once __DIR__ . '/../functions/permissions.php'; // File kiểm tra quyền

// Kiểm tra quyền admin - chỉ admin mới được quản lý điểm
requireAdmin();

// Lấy loại hành động từ request (GET hoặc POST)
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

// Xử lý các hành động tương ứng
switch ($action) {
    case 'create':
        handleCreateGrade(); // Xử lý thêm điểm mới
        break;
    case 'edit':
        handleEditGrade(); // Xử lý sửa điểm
        break;
    case 'delete':
        handleDeleteGrade(); // Xử lý xóa điểm
        break;
}

/**
 * Lấy danh sách tất cả các điểm
 * @return array Mảng chứa thông tin điểm của các sinh viên
 */
function handleGetAllGrades() {
    return getAllGrades();
}

/**
 * Lấy thông tin điểm theo ID
 * @param int $id ID của điểm cần lấy thông tin
 * @return array|false Thông tin điểm hoặc false nếu không tồn tại
 */
function handleGetGradeById($id) {
    return getGradeById($id);
}

/**
 * Xử lý thêm điểm mới cho sinh viên
 */
function handleCreateGrade() {
    // Kiểm tra phương thức request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/grade.php?error=Phương thức không hợp lệ");
        exit();
    }

    // Kiểm tra đầy đủ thông tin đầu vào
    if (!isset($_POST['student_id']) || !isset($_POST['subject_id']) || !isset($_POST['grade'])) {
        header("Location: ../views/grade_create.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    // Lấy và làm sạch dữ liệu đầu vào
    $student_id = trim($_POST['student_id']);
    $subject_id = trim($_POST['subject_id']);
    $grade = trim($_POST['grade']);

    // Kiểm tra dữ liệu không được để trống
    if ($student_id === '' || $subject_id === '' || $grade === '') {
        header("Location: ../views/grade_create.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Kiểm tra điểm hợp lệ (0-10)
    if (!is_numeric($grade) || $grade < 0 || $grade > 10) {
        header("Location: ../views/grade_create.php?error=Điểm phải là số từ 0 đến 10");
        exit();
    }

    // Kiểm tra điểm đã tồn tại chưa
    if (checkGradeExists($student_id, $subject_id)) {
        header("Location: ../views/grade_create.php?error=Sinh viên đã có điểm môn học này");
        exit();
    }

    // Thực hiện thêm điểm
    $success = addGrade($student_id, $subject_id, $grade);
    if ($success) {
        header("Location: ../views/grade.php?success=Thêm điểm thành công");
    } else {
        header("Location: ../views/grade_create.php?error=Có lỗi xảy ra khi thêm điểm");
    }
    exit();
}

/**
 * Xử lý cập nhật điểm cho sinh viên
 */
function handleEditGrade() {
    // Kiểm tra phương thức request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/grade.php?error=Phương thức không hợp lệ");
        exit();
    }

    // Kiểm tra đầy đủ thông tin đầu vào
    if (!isset($_POST['id']) || !isset($_POST['student_id']) || !isset($_POST['subject_id']) || !isset($_POST['grade'])) {
        header("Location: ../views/grade.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    // Lấy và làm sạch dữ liệu đầu vào
    $id = (int)$_POST['id'];
    $student_id = trim($_POST['student_id']);
    $subject_id = trim($_POST['subject_id']);
    $grade = trim($_POST['grade']);

    // Kiểm tra dữ liệu không được để trống
    if ($student_id === '' || $subject_id === '' || $grade === '') {
        header("Location: ../views/grade_edit.php?id=$id&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Kiểm tra điểm hợp lệ (0-10)
    if (!is_numeric($grade) || $grade < 0 || $grade > 10) {
        header("Location: ../views/grade_edit.php?id=$id&error=Điểm phải là số từ 0 đến 10");
        exit();
    }

    // Kiểm tra điểm có tồn tại không
    $current = getGradeById($id);
    if (!$current) {
        header("Location: ../views/grade.php?error=Điểm không tồn tại");
        exit();
    }

    // Kiểm tra trùng điểm nếu thay đổi sinh viên hoặc môn học
    if ($current['student_id'] != $student_id || $current['subject_id'] != $subject_id) {
        if (checkGradeExists($student_id, $subject_id)) {
            header("Location: ../views/grade_edit.php?id=$id&error=Sinh viên đã có điểm môn học này");
            exit();
        }
    }

    // Thực hiện cập nhật điểm
    $success = updateGrade($id, $student_id, $subject_id, $grade);
    if ($success) {
        header("Location: ../views/grade.php?success=Cập nhật điểm thành công");
    } else {
        header("Location: ../views/grade_edit.php?id=$id&error=Có lỗi xảy ra khi cập nhật điểm");
    }
    exit();
}

/**
 * Xử lý xóa điểm của sinh viên
 */
function handleDeleteGrade() {
    // Kiểm tra có ID điểm không
    if (!isset($_GET['id'])) {
        header("Location: ../views/grade.php?error=Thiếu ID điểm");
        exit();
    }

    // Lấy và kiểm tra ID
    $id = (int)$_GET['id'];
    $grade = getGradeById($id);
    if (!$grade) {
        header("Location: ../views/grade.php?error=Điểm không tồn tại");
        exit();
    }

    // Thực hiện xóa điểm
    $success = deleteGrade($id);
    if ($success) {
        header("Location: ../views/grade.php?success=Xóa điểm thành công");
    } else {
        header("Location: ../views/grade.php?error=Có lỗi xảy ra khi xóa điểm");
    }
    exit();
}

?>
