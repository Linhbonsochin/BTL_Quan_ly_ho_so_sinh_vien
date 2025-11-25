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

                <form action="../../handle/student_process.php" method="POST" enctype="multipart/form-data">
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
                        <div class="col-md-4">
                            <label class="form-label">Ảnh thẻ (ảnh đại diện)</label>
                            <input type="file" name="avatar" accept="image/*" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ảnh CCCD - Mặt trước</label>
                            <input type="file" name="cccd_front" accept="image/*" class="form-control" id="cccd_front_input">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ảnh CCCD - Mặt sau</label>
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
                            <input class="form-control" type="text" name="phone" placeholder="Số điện thoại">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" placeholder="Email sinh viên">
                        </div>
                    </div>

                    <!-- Expanded profile fields -->
                    <hr>
                    <h6>Thông tin CCCD / Nhân thân</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Số CCCD</label>
                            <input class="form-control" type="text" name="cccd_number" placeholder="Số CCCD / CMND">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nơi cấp CCCD</label>
                            <input class="form-control" type="text" name="cccd_place" placeholder="Nơi cấp">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày cấp CCCD</label>
                            <input class="form-control" type="date" name="cccd_issue_date">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label">Quốc tịch</label>
                            <input class="form-control" type="text" name="nationality" value="Việt Nam">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Dân tộc</label>
                            <input class="form-control" type="text" name="ethnicity">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tôn giáo</label>
                            <input class="form-control" type="text" name="religion">
                        </div>
                    </div>

                    <hr>
                    <h6>Học tập</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Hệ đào tạo</label>
                            <input class="form-control" type="text" name="education_system" placeholder="Ví dụ: Đại học-QC08">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chuyên ngành</label>
                            <input class="form-control" type="text" name="major" placeholder="Chuyên ngành">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Khóa học</label>
                            <input class="form-control" type="text" name="course_year" placeholder="2023-2027">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Khóa tuyển sinh</label>
                            <input class="form-control" type="text" name="admission_batch" placeholder="17">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label">Số điện thoại phụ</label>
                            <input class="form-control" type="text" name="phone_alt" placeholder="Số điện thoại phụ">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email cá nhân</label>
                            <input class="form-control" type="email" name="student_email" placeholder="Email sinh viên">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" name="is_union_member" id="is_union_member" value="1">
                                <label class="form-check-label" for="is_union_member">Đoàn viên</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_party_member" id="is_party_member" value="1">
                                <label class="form-check-label" for="is_party_member">Đảng viên</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label">Ngày vào Đoàn/Đảng</label>
                            <input class="form-control" type="date" name="join_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Học lực lớp 12</label>
                            <input class="form-control" type="text" name="grade12_academic">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hạnh kiểm lớp 12</label>
                            <input class="form-control" type="text" name="grade12_conduct">
                        </div>
                    </div>

                    <hr>
                    <h6>Địa chỉ</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tỉnh/Thành phố thường trú</label>
                            <input class="form-control" type="text" name="permanent_province">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Xã/Phường thường trú</label>
                            <input class="form-control" type="text" name="permanent_ward">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số nhà/thôn, xóm thường trú</label>
                            <input class="form-control" type="text" name="permanent_address">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label">Tỉnh/Thành phố hiện nay</label>
                            <input class="form-control" type="text" name="current_province">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Xã/Phường hiện nay</label>
                            <input class="form-control" type="text" name="current_ward">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số nhà/thôn, xóm nơi ở hiện nay</label>
                            <input class="form-control" type="text" name="current_address">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Tỉnh/Thành phố nơi sinh</label>
                            <input class="form-control" type="text" name="birth_province">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Xã/Phường nơi sinh</label>
                            <input class="form-control" type="text" name="birth_ward">
                        </div>
                    </div>

                    <hr>
                    <h6>Ngân hàng & Ký túc xá</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên ngân hàng</label>
                            <input class="form-control" type="text" name="bank_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số tài khoản ngân hàng</label>
                            <input class="form-control" type="text" name="bank_account">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label">Sinh viên hiện ở ngoại trú</label>
                            <select class="form-select" name="is_offcampus">
                                <option value="0">Không</option>
                                <option value="1">Có</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Địa chỉ ký túc xá</label>
                            <input class="form-control" type="text" name="dorm_address">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Học kỳ ở nội/ngoại trú</label>
                            <input class="form-control" type="text" name="residence_term">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Năm học ở nội/ngoại trú</label>
                            <input class="form-control" type="text" name="residence_year">
                        </div>
                    </div>

                    <hr>
                    <h6>Người báo tin / liên hệ</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Người báo tin</label>
                            <input class="form-control" type="text" name="reporter_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SĐT người báo tin</label>
                            <input class="form-control" type="text" name="reporter_phone">
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label class="form-label">Địa chỉ liên hệ</label>
                        <input class="form-control" type="text" name="contact_address">
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
    <script src="/BTL_17-09/js/ocr_helper.js"></script>
    <script>
        document.getElementById('ocrFromFront').addEventListener('click', function(){
            var inp = document.getElementById('cccd_front_input');
            if (!inp || !inp.files || inp.files.length === 0) { alert('Vui lòng chọn ảnh mặt trước CCCD trước'); return; }
            var f = inp.files[0];
            OCRHelper.processFile(f, function(parsed, raw){
                // try autofill common fields
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