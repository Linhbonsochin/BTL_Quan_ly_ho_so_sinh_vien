<?php
require_once __DIR__ . '/../../functions/permissions.php';
requireAdmin();
require_once __DIR__ . '/../../functions/grade_functions.php';

$students = getAllStudentsForDropdown();
$subjects = getAllSubjectsForDropdown();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Thêm điểm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/home.css">
</head>

<body>
    <!-- menu removed for add/edit pages as requested -->
    <div class="container my-4">
        <h3>Thêm điểm</h3>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="../../handle/grade_process.php" method="POST">
            <input type="hidden" name="action" value="create">
            <div class="mb-3">
                <label for="student_id" class="form-label">Sinh viên</label>
                <select name="student_id" id="student_id" class="form-select" required>
                    <option value="">-- Chọn sinh viên --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>">
                            <?php echo htmlspecialchars($s['student_code'] . ' - ' . $s['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="subject_id" class="form-label">Môn học</label>
                <select name="subject_id" id="subject_id" class="form-select" required>
                    <option value="">-- Chọn môn --</option>
                    <?php foreach ($subjects as $sub): ?>
                        <option value="<?php echo $sub['id']; ?>">
                            <?php echo htmlspecialchars($sub['subject_code'] . ' - ' . $sub['subject_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="attendance_score" class="form-label">Điểm chuyên cần</label>
                <input type="number" step="0.01" min="0" max="10" name="attendance_score" id="attendance_score" class="form-control" required oninput="calcTotalScore()">
            </div>
            <div class="mb-3">
                <label for="midterm_score" class="form-label">Điểm giữa kỳ</label>
                <input type="number" step="0.01" min="0" max="10" name="midterm_score" id="midterm_score" class="form-control" required oninput="calcTotalScore()">
            </div>
            <div class="mb-3">
                <label for="final_score" class="form-label">Điểm cuối kỳ</label>
                <input type="number" step="0.01" min="0" max="10" name="final_score" id="final_score" class="form-control" required oninput="calcTotalScore()">
            </div>
            <div class="mb-3">
                <label for="total_score" class="form-label">Điểm tổng kết</label>
                <input type="number" step="0.01" min="0" max="10" name="total_score" id="total_score" class="form-control" required readonly>
            </div>
            <script>
                function calcTotalScore() {
                    var cc = parseFloat(document.getElementById('attendance_score').value) || 0;
                    var gk = parseFloat(document.getElementById('midterm_score').value) || 0;
                    var ck = parseFloat(document.getElementById('final_score').value) || 0;
                    var total = (cc * 0.1 + gk * 0.4 + ck * 0.5).toFixed(2);
                    document.getElementById('total_score').value = total;
                }
            </script>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Thêm</button>
                <a href="../grade.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>