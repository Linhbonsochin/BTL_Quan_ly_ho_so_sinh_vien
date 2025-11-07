<?php
require_once('../../functions/permissions.php');
require_once('../../functions/student_functions.php');
requireAdmin();
$classes = getClasses();
$departments = getDepartments();
$users = getStudentUsers();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Thêm sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/login.css">
    <meta name="robots" content="noindex,nofollow">
    <style>
        .form-label {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <!-- menu removed for add/edit pages as requested -->
    <div class="container my-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Thêm sinh viên</h4>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error'];
                    unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form action="../../handle/student_process.php" method="POST">
                    <input type="hidden" name="action" value="add">

                    <div class="mb-3">
                        <label class="form-label">Mã sinh viên</label>
                        <input class="form-control" type="text" name="student_code" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input class="form-control" type="text" name="full_name" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Ngày sinh</label>
                            <input class="form-control" type="date" name="birth_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giới tính</label>
                            <select class="form-select" name="gender">
                                <option value="">--Chọn--</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lớp</label>
                            <select class="form-select" name="class_id">
                                <option value="">--Chọn lớp--</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo htmlspecialchars($c['class_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Khoa</label>
                        <select class="form-select" name="department_id">
                            <option value="">--Chọn khoa--</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?php echo $d['id']; ?>">
                                    <?php echo htmlspecialchars($d['department_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Liên kết tài khoản (tùy chọn)</label>
                        <select class="form-select" name="user_id">
                            <option value="">--Không liên kết--</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="address" rows="3"></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input class="form-control" type="text" name="phone" placeholder="Số điện thoại">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" placeholder="Email sinh viên">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Thêm</button>
                        <a href="../student.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>