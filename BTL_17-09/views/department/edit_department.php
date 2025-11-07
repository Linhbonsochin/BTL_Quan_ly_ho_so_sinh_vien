<?php
require_once __DIR__ . '/../../functions/permissions.php';
require_once __DIR__ . '/../../functions/department_functions.php';
requireAdmin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$dept = getDepartmentById($id);
if (!$dept) {
    $_SESSION['error'] = 'Khoa không tồn tại.';
    header('Location: ../department.php');
    exit();
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sửa khoa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/home.css">
</head>

<body>
    <!-- menu removed for add/edit pages as requested -->
    <div class="container my-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-3">Sửa khoa</h4>
                <form action="../../handle/department_process.php" method="post">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $dept['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Mã khoa</label>
                        <input class="form-control" type="text" name="department_code"
                            value="<?php echo htmlspecialchars($dept['department_code'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên khoa</label>
                        <input class="form-control" type="text" name="department_name"
                            value="<?php echo htmlspecialchars($dept['department_name']); ?>" required>
                    </div>
                    <div>
                        <button class="btn btn-primary">Lưu</button>
                        <a class="btn btn-secondary" href="../department.php">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>