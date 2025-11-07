<?php
require_once __DIR__ . '/../../functions/permissions.php';
requireAdmin();
require_once __DIR__ . '/../../functions/class_functions.php';
require_once __DIR__ . '/../../functions/department_functions.php';
$departments = getAllDepartments();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../class.php?error=Thiếu ID');
    exit();
}

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
    <!-- menu removed for add/edit pages as requested -->
    <div class="container my-4">
        <h3>Chỉnh sửa lớp</h3>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="../../handle/class_process.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $class['id']; ?>">

            <div class="mb-3">
                <label for="class_code" class="form-label">Mã lớp</label>
                <input type="text" name="class_code" id="class_code" class="form-control"
                    value="<?php echo htmlspecialchars($class['class_code']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="class_name" class="form-label">Tên lớp</label>
                <input type="text" name="class_name" id="class_name" class="form-control"
                    value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="department_id" class="form-label">Khoa</label>
                <select name="department_id" id="department_id" class="form-select" required>
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['id']; ?>" <?php echo ($class['department_id'] == $d['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['department_name']); ?></option>
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