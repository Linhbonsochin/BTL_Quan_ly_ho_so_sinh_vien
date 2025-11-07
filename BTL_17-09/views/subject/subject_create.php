<?php
require_once __DIR__ . '/../../functions/permissions.php';
requireAdmin();
require_once __DIR__ . '/../../functions/department_functions.php';
$departments = getAllDepartments();
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Thêm môn học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/home.css">
</head>

<body>
    <!-- menu removed for add/edit pages as requested -->
    <div class="container my-4">
        <h3>Thêm môn học</h3>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div><?php endif; ?>

        <form action="../../handle/subject_process.php" method="POST">
            <input type="hidden" name="action" value="create">
            <div class="mb-3">
                <label class="form-label">Mã môn</label>
                <input name="subject_code" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên môn</label>
                <input name="subject_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Khoa</label>
                <select name="department_id" class="form-select" required>
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['department_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><button class="btn btn-primary">Thêm</button> <a class="btn btn-secondary"
                    href="../subject.php">Hủy</a></div>
        </form>
    </div>
</body>

</html>