<?php
require_once __DIR__ . '/../../functions/permissions.php';
requireAdmin();
require_once __DIR__ . '/../../functions/major_functions.php';
require_once __DIR__ . '/../../functions/department_functions.php';
$departments = getAllDepartmentsForDropdown();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Thêm ngành</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <h3>Thêm ngành</h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <form action="../../handle/major_process.php" method="POST">
        <input type="hidden" name="action" value="create">
        <div class="mb-3">
            <label for="major_code" class="form-label">Mã ngành</label>
            <input type="text" name="major_code" id="major_code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="major_name" class="form-label">Tên ngành</label>
            <input type="text" name="major_name" id="major_name" class="form-control" required>
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
        <button class="btn btn-primary">Thêm</button>
        <a href="../major.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
</html>
