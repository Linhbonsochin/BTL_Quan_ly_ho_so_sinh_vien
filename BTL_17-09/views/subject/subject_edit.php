<?php
require_once __DIR__ . '/../../functions/permissions.php';
requireAdmin();
require_once __DIR__ . '/../../functions/subject_functions.php';
require_once __DIR__ . '/../../functions/department_functions.php';
$departments = getAllDepartments();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: subject.php?error=Thiếu ID');
    exit();
}
$id = (int) $_GET['id'];
$s = getSubjectById($id);
if (!$s) {
    header('Location: subject.php?error=Không tìm thấy môn');
    exit();
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Chỉnh sửa môn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/home.css">
</head>

<body>
    <!-- menu removed for add/edit pages as requested -->
    <div class="container my-4">
        <h3>Chỉnh sửa môn</h3>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div><?php endif; ?>

        <form action="../../handle/subject_process.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Mã môn</label>
                <input name="subject_code" class="form-control" required
                    value="<?php echo htmlspecialchars($s['subject_code']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Tên môn</label>
                <input name="subject_name" class="form-control" required
                    value="<?php echo htmlspecialchars($s['subject_name']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Khoa</label>
                <select name="department_id" class="form-select" required>
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['id']; ?>" <?php echo ($s['department_id'] == $d['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['department_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Ngành</label>
                <select name="major_id" class="form-select" required>
                    <option value="">-- Chọn ngành --</option>
                    <?php 
                    require_once __DIR__ . '/../../functions/major_functions.php';
                    $majors = getAllMajorsForDropdown();
                    foreach ($majors as $m): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo ($s['major_id'] == $m['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['major_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><button class="btn btn-primary">Cập nhật</button> <a class="btn btn-secondary"
                    href="../subject.php">Hủy</a></div>
        </form>
    </div>
</body>

</html>