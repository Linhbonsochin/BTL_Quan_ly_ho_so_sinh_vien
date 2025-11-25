<?php
require_once('../../functions/permissions.php');
require_once('../../functions/student_functions.php');
requireAdmin();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$student = getStudentById($id);
if (!$student) {
    $_SESSION['error'] = 'Sinh viên không tồn tại.';
    header('Location: ../student.php');
    exit();
}
$classes = getClasses();
$departments = getDepartments();
$users = getStudentUsers();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sửa sinh viên</title>
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
                <h4 class="card-title mb-3">Sửa sinh viên</h4>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error'];
                    unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form action="../../handle/student_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Mã sinh viên</label>
                        <input class="form-control" type="text" name="student_code"
                            value="<?php echo htmlspecialchars($student['student_code']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input class="form-control" type="text" name="full_name"
                            value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Ngày sinh</label>
                            <input class="form-control" type="date" name="birth_date"
                                value="<?php echo $student['birth_date']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giới tính</label>
                            <select class="form-select" name="gender">
                                <option value="">--Chọn--</option>
                                <option value="Nam" <?php echo ($student['gender'] === 'Nam') ? 'selected' : ''; ?>>Nam
                                </option>
                                <option value="Nữ" <?php echo ($student['gender'] === 'Nữ') ? 'selected' : ''; ?>>Nữ
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lớp</label>
                            <select class="form-select" name="class_id">
                                <option value="">--Chọn lớp--</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo ($student['class_id'] == $c['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['class_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Khoa</label>
                        <select class="form-select" name="department_id">
                            <option value="">--Chọn khoa--</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?php echo $d['id']; ?>" <?php echo ($student['department_id'] == $d['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['department_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Liên kết tài khoản (tùy chọn)</label>
                        <select class="form-select" name="user_id">
                            <option value="">--Không liên kết--</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo (isset($student['user_id']) && $student['user_id'] == $u['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="address"
                            rows="3"><?php echo htmlspecialchars($student['address']); ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Ảnh thẻ (ảnh đại diện)</label>
                            <?php if (!empty($student['avatar_path'])): ?>
                                <div class="mb-2 d-flex align-items-start gap-2">
                                    <img src="<?php echo htmlspecialchars($student['avatar_path']); ?>" alt="avatar" style="max-width:120px; max-height:120px; object-fit:cover; border:1px solid #ddd; padding:2px;">
                                    <form method="POST" action="../../handle/student_process.php" onsubmit="return confirm('Xác nhận xoá ảnh đại diện?');">
                                        <input type="hidden" name="action" value="remove_image">
                                        <input type="hidden" name="id" value="<?php echo intval($student['id']); ?>">
                                        <input type="hidden" name="field" value="avatar">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Xoá</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="avatar" accept="image/*" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ảnh CCCD - Mặt trước</label>
                            <?php if (!empty($student['cccd_front_path'])): ?>
                                <div class="mb-2 d-flex align-items-start gap-2">
                                    <img src="<?php echo htmlspecialchars($student['cccd_front_path']); ?>" alt="cccd_front" style="max-width:160px; max-height:120px; object-fit:cover; border:1px solid #ddd; padding:2px;">
                                    <form method="POST" action="../../handle/student_process.php" onsubmit="return confirm('Xác nhận xoá ảnh CCCD (mặt trước)?');">
                                        <input type="hidden" name="action" value="remove_image">
                                        <input type="hidden" name="id" value="<?php echo intval($student['id']); ?>">
                                        <input type="hidden" name="field" value="cccd_front">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Xoá</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="cccd_front" accept="image/*" class="form-control" id="cccd_front_input">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ảnh CCCD - Mặt sau</label>
                            <?php if (!empty($student['cccd_back_path'])): ?>
                                <div class="mb-2 d-flex align-items-start gap-2">
                                    <img src="<?php echo htmlspecialchars($student['cccd_back_path']); ?>" alt="cccd_back" style="max-width:160px; max-height:120px; object-fit:cover; border:1px solid #ddd; padding:2px;">
                                    <form method="POST" action="../../handle/student_process.php" onsubmit="return confirm('Xác nhận xoá ảnh CCCD (mặt sau)?');">
                                        <input type="hidden" name="action" value="remove_image">
                                        <input type="hidden" name="id" value="<?php echo intval($student['id']); ?>">
                                        <input type="hidden" name="field" value="cccd_back">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Xoá</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="cccd_back" accept="image/*" class="form-control" id="cccd_back_input">
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="button" id="ocrFromFront" class="btn btn-sm btn-outline-primary">Nhận dữ liệu từ ảnh CCCD (mặt trước)</button>
                        <button type="button" id="ocrFromBack" class="btn btn-sm btn-outline-secondary">Nhận dữ liệu từ ảnh CCCD (mặt sau)</button>
                        <span class="text-muted ms-2">(Tự động điền số CCCD, họ tên, ngày sinh nếu tìm thấy)</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input class="form-control" type="text" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>" placeholder="Số điện thoại">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" placeholder="Email sinh viên">
                        </div>
                    </div>

                        <!-- Expanded profile fields -->
                        <hr>
                        <h6>Thông tin CCCD / Nhân thân</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Số CCCD</label>
                                <input class="form-control" type="text" name="cccd_number" value="<?php echo htmlspecialchars($student['cccd_number'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nơi cấp CCCD</label>
                                <input class="form-control" type="text" name="cccd_place" value="<?php echo htmlspecialchars($student['cccd_place'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ngày cấp CCCD</label>
                                <input class="form-control" type="date" name="cccd_issue_date" value="<?php echo htmlspecialchars($student['cccd_issue_date'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Quốc tịch</label>
                                <input class="form-control" type="text" name="nationality" value="<?php echo htmlspecialchars($student['nationality'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Dân tộc</label>
                                <input class="form-control" type="text" name="ethnicity" value="<?php echo htmlspecialchars($student['ethnicity'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tôn giáo</label>
                                <input class="form-control" type="text" name="religion" value="<?php echo htmlspecialchars($student['religion'] ?? ''); ?>">
                            </div>
                        </div>

                        <hr>
                        <h6>Học tập</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Hệ đào tạo</label>
                                <input class="form-control" type="text" name="education_system" value="<?php echo htmlspecialchars($student['education_system'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Chuyên ngành</label>
                                <input class="form-control" type="text" name="major" value="<?php echo htmlspecialchars($student['major'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Khóa học</label>
                                <input class="form-control" type="text" name="course_year" value="<?php echo htmlspecialchars($student['course_year'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Khóa tuyển sinh</label>
                                <input class="form-control" type="text" name="admission_batch" value="<?php echo htmlspecialchars($student['admission_batch'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Số điện thoại phụ</label>
                                <input class="form-control" type="text" name="phone_alt" value="<?php echo htmlspecialchars($student['phone_alt'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email cá nhân</label>
                                <input class="form-control" type="email" name="student_email" value="<?php echo htmlspecialchars($student['student_email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" name="is_union_member" id="is_union_member" value="1" <?php if (!empty($student['is_union_member'])) echo 'checked'; ?>>
                                    <label class="form-check-label" for="is_union_member">Đoàn viên</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_party_member" id="is_party_member" value="1" <?php if (!empty($student['is_party_member'])) echo 'checked'; ?>>
                                    <label class="form-check-label" for="is_party_member">Đảng viên</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Ngày vào Đoàn/Đảng</label>
                                <input class="form-control" type="date" name="join_date" value="<?php echo htmlspecialchars($student['join_date'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Học lực lớp 12</label>
                                <input class="form-control" type="text" name="grade12_academic" value="<?php echo htmlspecialchars($student['grade12_academic'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hạnh kiểm lớp 12</label>
                                <input class="form-control" type="text" name="grade12_conduct" value="<?php echo htmlspecialchars($student['grade12_conduct'] ?? ''); ?>">
                            </div>
                        </div>

                        <hr>
                        <h6>Địa chỉ</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tỉnh/Thành phố thường trú</label>
                                <input class="form-control" type="text" name="permanent_province" value="<?php echo htmlspecialchars($student['permanent_province'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Xã/Phường thường trú</label>
                                <input class="form-control" type="text" name="permanent_ward" value="<?php echo htmlspecialchars($student['permanent_ward'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số nhà/thôn, xóm thường trú</label>
                                <input class="form-control" type="text" name="permanent_address" value="<?php echo htmlspecialchars($student['permanent_address'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Tỉnh/Thành phố hiện nay</label>
                                <input class="form-control" type="text" name="current_province" value="<?php echo htmlspecialchars($student['current_province'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Xã/Phường hiện nay</label>
                                <input class="form-control" type="text" name="current_ward" value="<?php echo htmlspecialchars($student['current_ward'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số nhà/thôn, xóm nơi ở hiện nay</label>
                                <input class="form-control" type="text" name="current_address" value="<?php echo htmlspecialchars($student['current_address'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Tỉnh/Thành phố nơi sinh</label>
                                <input class="form-control" type="text" name="birth_province" value="<?php echo htmlspecialchars($student['birth_province'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Xã/Phường nơi sinh</label>
                                <input class="form-control" type="text" name="birth_ward" value="<?php echo htmlspecialchars($student['birth_ward'] ?? ''); ?>">
                            </div>
                        </div>

                        <hr>
                        <h6>Ngân hàng & Ký túc xá</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tên ngân hàng</label>
                                <input class="form-control" type="text" name="bank_name" value="<?php echo htmlspecialchars($student['bank_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số tài khoản ngân hàng</label>
                                <input class="form-control" type="text" name="bank_account" value="<?php echo htmlspecialchars($student['bank_account'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Sinh viên hiện ở ngoại trú</label>
                                <select class="form-select" name="is_offcampus">
                                    <option value="0" <?php if (empty($student['is_offcampus'])) echo 'selected'; ?>>Không</option>
                                    <option value="1" <?php if (!empty($student['is_offcampus'])) echo 'selected'; ?>>Có</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Địa chỉ ký túc xá</label>
                                <input class="form-control" type="text" name="dorm_address" value="<?php echo htmlspecialchars($student['dorm_address'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Học kỳ ở nội/ngoại trú</label>
                                <input class="form-control" type="text" name="residence_term" value="<?php echo htmlspecialchars($student['residence_term'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Năm học ở nội/ngoại trú</label>
                                <input class="form-control" type="text" name="residence_year" value="<?php echo htmlspecialchars($student['residence_year'] ?? ''); ?>">
                            </div>
                        </div>

                        <hr>
                        <h6>Người báo tin / liên hệ</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Người báo tin</label>
                                <input class="form-control" type="text" name="reporter_name" value="<?php echo htmlspecialchars($student['reporter_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SĐT người báo tin</label>
                                <input class="form-control" type="text" name="reporter_phone" value="<?php echo htmlspecialchars($student['reporter_phone'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3 mt-2">
                            <label class="form-label">Địa chỉ liên hệ</label>
                            <input class="form-control" type="text" name="contact_address" value="<?php echo htmlspecialchars($student['contact_address'] ?? ''); ?>">
                        </div>
                                
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="../student.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/BTL_17-09/js/ocr_helper.js"></script>
    <script>
        document.getElementById('ocrFromFront').addEventListener('click', function(){
            var inp = document.getElementById('cccd_front_input');
            if (!inp || !inp.files || inp.files.length === 0) { alert('Vui lòng chọn ảnh mặt trước CCCD trước'); return; }
            var f = inp.files[0];
            OCRHelper.processFile(f, function(parsed, raw){
                var filled = OCRHelper.autofillToForm(parsed);
                if (filled === 0) {
                    alert('Không tìm thấy trường phù hợp tự động điền. Xem kết quả OCR trong console.');
                    console.log('OCR result:', parsed, raw);
                } else {
                    alert('Đã điền ' + filled + ' trường (kiểm tra và chỉnh sửa nếu cần)');
                }
            }, function(err){ alert('OCR lỗi: ' + err); });
        });

        document.getElementById('ocrFromBack').addEventListener('click', function(){
            var inp = document.getElementById('cccd_back_input');
            if (!inp || !inp.files || inp.files.length === 0) { alert('Vui lòng chọn ảnh mặt sau CCCD trước'); return; }
            var f = inp.files[0];
            OCRHelper.processFile(f, function(parsed, raw){
                var filled = OCRHelper.autofillToForm(parsed);
                if (filled === 0) {
                    alert('Không tìm thấy trường phù hợp tự động điền. Xem kết quả OCR trong console.');
                    console.log('OCR result:', parsed, raw);
                } else {
                    alert('Đã điền ' + filled + ' trường (kiểm tra và chỉnh sửa nếu cần)');
                }
            }, function(err){ alert('OCR lỗi: ' + err); });
        });
    </script>
</body>

</html>