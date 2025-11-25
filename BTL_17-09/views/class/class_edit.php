<?php
// Mục đích: Trang chỉnh sửa thông tin một lớp
// Yêu cầu: người dùng phải là admin (kiểm tra trong permissions.php)
require_once __DIR__ . '/../../functions/permissions.php';
// Kiểm tra quyền admin
requireAdmin();
require_once __DIR__ . '/../../functions/class_functions.php';
require_once __DIR__ . '/../../functions/department_functions.php';
// Lấy danh sách các khoa cho dropdown
$departments = getAllDepartments();

// Kiểm tra tham số id truyền vào (nếu không có thì chuyển hướng về danh sách lớp)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../class.php?error=Thiếu ID');
    exit();
}

// Lấy thông tin lớp theo id và kiểm tra tồn tại
$id = (int) $_GET['id'];
$class = getClassById($id);
if (!$class) {
    header('Location: ../class.php?error=Không tìm thấy lớp');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Chỉnh sửa lớp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/home.css">
</head>

<body>
    <!-- Ghi chú: menu được loại bỏ cho trang thêm/chỉnh sửa để bố cục gọn hơn -->
    <div class="container my-4">
        <h3>Chỉnh sửa lớp</h3>

        <!-- Thông báo lỗi: hiển thị khi có tham số error trong URL -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <!-- Form chỉnh sửa: gửi về handle/class_process.php với action=edit -->
        <form action="../../handle/class_process.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $class['id']; ?>">

            <div class="mb-3">
                <label for="class_code" class="form-label">Mã lớp</label>
                <!-- Hiện giá trị mã lớp hiện tại để admin có thể sửa -->
                <input type="text" name="class_code" id="class_code" class="form-control"
                    value="<?php echo htmlspecialchars($class['class_code']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="class_name" class="form-label">Tên lớp</label>
                <!-- Hiện giá trị tên lớp hiện tại để admin có thể sửa -->
                <input type="text" name="class_name" id="class_name" class="form-control"
                    value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="department_id" class="form-label">Khoa</label>
                <!-- Dropdown chọn khoa, chọn mặc định theo dữ liệu lớp hiện tại -->
                <select name="department_id" id="department_id" class="form-select" required>
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['id']; ?>" <?php echo ($class['department_id'] == $d['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['department_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="major_id" class="form-label">Ngành</label>
                <!-- Dropdown ngành: lấy danh sách ngành và chọn ngành hiện tại của lớp -->
                <select name="major_id" id="major_id" class="form-select" required>
                    <option value="">-- Chọn ngành --</option>
                    <?php 
                    require_once __DIR__ . '/../../functions/major_functions.php';
                    $majors = getAllMajorsForDropdown();
                    foreach ($majors as $m): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo ($class['major_id'] == $m['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['major_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Cập nhật</button>
                <a href="../class.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>