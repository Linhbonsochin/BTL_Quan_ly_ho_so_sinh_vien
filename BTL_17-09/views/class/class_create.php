<?php
// Mục đích: Trang thêm mới một lớp học
// Yêu cầu: người dùng phải là admin để truy cập (kiểm tra trong permissions.php)
// Chú ý: File này hiển thị một form gửi về handle/class_process.php để xử lý tạo lớp
require_once __DIR__ . '/../../functions/permissions.php';
// Kiểm tra quyền admin, nếu không có sẽ chuyển hướng
requireAdmin();
require_once __DIR__ . '/../../functions/class_functions.php';
require_once __DIR__ . '/../../functions/department_functions.php';
// Lấy danh sách các khoa để hiển thị trong dropdown
$departments = getAllDepartments();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Thêm lớp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/home.css">
</head>

<body>
    <!-- Ghi chú: menu được loại bỏ cho trang thêm/chỉnh sửa để bố cục gọn hơn -->
    <div class="container my-4">
        <h3>Thêm lớp</h3>
        <!-- Thông báo lỗi: hiển thị khi có tham số error trong URL -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <!-- Form gửi dữ liệu về handle/class_process.php để thực hiện hành động tạo lớp -->
        <form action="../../handle/class_process.php" method="POST">
            <input type="hidden" name="action" value="create">
            <div class="mb-3">
                <label for="class_code" class="form-label">Mã lớp</label>
                <input type="text" name="class_code" id="class_code" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="class_name" class="form-label">Tên lớp</label>
                <input type="text" name="class_name" id="class_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="department_id" class="form-label">Khoa</label>
                <select name="department_id" id="department_id" class="form-select" required>
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['department_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="major_id" class="form-label">Ngành</label>
                <select name="major_id" id="major_id" class="form-select" required>
                    <option value="">-- Chọn ngành --</option>
                    <?php 
                    require_once __DIR__ . '/../../functions/major_functions.php';
                    // Lấy danh sách ngành để hiển thị trong dropdown (hàm helper)
                    $majors = getAllMajorsForDropdown();
                    // Duyệt danh sách ngành và xuất option
                    foreach ($majors as $m): ?>
                        <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['major_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Thêm</button>
                <a href="../class.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>