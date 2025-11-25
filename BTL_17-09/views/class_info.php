<?php
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/student_functions.php';
requireStudent();

$current = getCurrentUser();
$student = getStudentByCode($current['username']);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Thông tin lớp</title>
    <link rel="stylesheet" href="../css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include __DIR__ . '/user_menu.php'; ?>
    <div class="container my-4">
        <h3>Thông tin lớp</h3>
        <?php if (!$student): ?>
            <div class="alert alert-warning">Không tìm thấy hồ sơ. Liên hệ quản trị viên.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <tr>
                    <th>Lớp</th>
                    <td><?php echo htmlspecialchars($student['class_name']); ?></td>
                </tr>
                <tr>
                    <th>Khoa</th>
                    <td><?php echo htmlspecialchars($student['department_name']); ?></td>
                </tr>
                <tr>
                    <th>Mã sinh viên</th>
                    <td><?php echo htmlspecialchars($student['student_code']); ?></td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>