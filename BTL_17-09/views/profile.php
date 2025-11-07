<?php
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/student_functions.php';
// allow both students and admins to view this page (admins may need to create missing student records)
requireLogin();

$current = getCurrentUser();
$student = null;
if ($current && isset($current['id'])) {
    // Prefer lookup by user_id if available
    $student = getStudentByUserId($current['id']);
}
// Fallback: try to match student_code == username (legacy behavior)
if (!$student && $current && isset($current['username'])) {
    $student = getStudentByCode($current['username']);
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Hồ sơ sinh viên</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/user-theme.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #be93c5, #7bc6cc);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto 1rem;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-avatar i {
            font-size: 4rem;
            color: #be93c5;
        }

        .profile-body {
            padding: 2rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }

        .info-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: #333;
        }

        .department-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .profile-header {
                padding: 1.5rem;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
            }

            .profile-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body class="bg-light">
    <?php include __DIR__ . '/user_menu.php'; ?>
    <div class="container my-4">

        <?php if (!$student): ?>
            <div class="alert alert-warning">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-exclamation-triangle me-3" style="font-size: 2rem; color: #856404;"></i>
                    <h4 class="mb-0">Không tìm thấy hồ sơ sinh viên</h4>
                </div>
                <p>Không tìm thấy hồ sơ sinh viên tương ứng với tài khoản
                    <strong><?php echo htmlspecialchars($current['username'] ?? ''); ?></strong>.
                </p>
                <p><i class="fas fa-info-circle me-2"></i>Nguyên nhân thường gặp: tài khoản người dùng chưa được liên kết
                    với hồ sơ sinh viên (mã sinh viên khác nhau).</p>
                <p><i class="fas fa-hand-point-right me-2"></i>Vui lòng liên hệ quản trị viên để tạo hoặc cập nhật hồ sơ.
                </p>
                <?php if (function_exists('isAdmin') && isAdmin()): ?>
                    <a href="student/create_student.php" class="btn btn-primary mt-3">
                        <i class="fas fa-user-plus me-2"></i>Tạo hồ sơ sinh viên
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3 class="mb-2"><?php echo htmlspecialchars($student['full_name']); ?></h3>
                    <p class="mb-2">Mã sinh viên: <?php echo htmlspecialchars($student['student_code']); ?></p>
                    <div class="department-badge">
                        <i class="fas fa-building me-2"></i>
                        <?php echo htmlspecialchars($student['department_name']); ?>

                    </div>
                </div>
                <div class="profile-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label"><i class="fas fa-calendar me-2"></i>Ngày sinh</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['birth_date']); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label"><i class="fas fa-venus-mars me-2"></i>Giới tính</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['gender']); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label"><i class="fas fa-phone me-2"></i>Số điện thoại</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['phone'] ?? ''); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label"><i class="fas fa-envelope me-2"></i>Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['email'] ?? ''); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label"><i class="fas fa-users me-2"></i>Lớp</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['class_name']); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['address']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit contact modal -->
            <div class="modal fade" id="editContactModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Sửa thông tin liên hệ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="../handle/student_process.php">
                            <input type="hidden" name="action" value="self_edit">
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Địa chỉ</label>
                                    <textarea name="address" class="form-control"
                                        rows="3"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>