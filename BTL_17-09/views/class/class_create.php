<?php
require_once __DIR__ . '/../../functions/permissions.php';
requireAdmin();
require_once __DIR__ . '/../../functions/class_functions.php';
require_once __DIR__ . '/../../functions/department_functions.php';
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
    <!-- menu removed for add/edit pages as requested -->
    <div class="container my-4">
        <h3>Thêm lớp</h3>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

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
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Thêm</button>
                <a href="../class.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>