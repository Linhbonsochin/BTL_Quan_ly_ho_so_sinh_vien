<?php
require_once __DIR__ . '/../../functions/permissions.php';
requireAdmin();
require_once __DIR__ . '/../../functions/grade_functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../grade.php?error=Thiếu ID');
    exit();
}
$id = (int) $_GET['id'];
$grade = getGradeById($id);
if (!$grade) {
    header('Location: ../grade.php?error=Không tìm thấy điểm');
    exit();
}

$students = getAllStudentsForDropdown();
$subjects = getAllSubjectsForDropdown();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Chỉnh sửa điểm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/home.css">
</head>

<body>
    <!-- menu removed for add/edit pages as requested -->
    <div class="container my-4">
        <h3>Chỉnh sửa điểm</h3>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="../../handle/grade_process.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $grade['id']; ?>">

            <div class="mb-3">
                <label for="student_id" class="form-label">Sinh viên</label>
                <select name="student_id" id="student_id" class="form-select" required>
                    <?php foreach ($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php if ($s['id'] == $grade['student_id'])
                               echo 'selected'; ?>><?php echo htmlspecialchars($s['student_code'] . ' - ' . $s['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="subject_id" class="form-label">Môn học</label>
                <select name="subject_id" id="subject_id" class="form-select" required>
                    <?php foreach ($subjects as $sub): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php if ($sub['id'] == $grade['subject_id'])
                               echo 'selected'; ?>>
                            <?php echo htmlspecialchars($sub['subject_code'] . ' - ' . $sub['subject_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="grade" class="form-label">Điểm</label>
                <input type="number" step="0.01" min="0" max="10" name="grade" id="grade" class="form-control"
                    value="<?php echo htmlspecialchars($grade['grade']); ?>" required>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Cập nhật</button>
                <a href="../grade.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>