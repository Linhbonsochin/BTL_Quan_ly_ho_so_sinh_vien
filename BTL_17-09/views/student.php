<?php
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/student_functions.php';
requireAdmin();

$search = isset($_GET['q']) ? trim($_GET['q']) : null;
$students = getStudents($search);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý sinh viên - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background:#f6f8fa; }
        .sidebar { width:250px; min-height:100vh; background:#fff; border-right:1px solid #e6e9ee; }
        .sidebar .nav-link { color:#444; }
        .sidebar .nav-link:hover { background:#f0f4f8; }
        .card-ghost { border-radius:12px; box-shadow:0 6px 18px rgba(30,41,59,0.06); }
        .dashboard-main { padding:24px; }
        .search-form .form-control { max-width: 360px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/admin_menu.php'; ?>

        <div class="flex-grow-1 dashboard-main">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Quản lý sinh viên</h3>
                <div>
                    <span class="me-3 text-muted">Xin chào, <?php $u = getCurrentUser(); echo htmlspecialchars($u['username'] ?? ''); ?></span>
                    <a class="btn btn-outline-secondary btn-sm" href="/BTL_17-09/handle/logout_process.php">Đăng xuất</a>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <a href="student/create_student.php" class="btn btn-primary">Thêm sinh viên</a>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="student.php" class="search-form row g-2 align-items-center">
                        <div class="col-md-6 col-lg-5">
                            <div class="input-group">
                                <input type="search" name="q" id="q" class="form-control" placeholder="Tìm theo tên hoặc mã sinh viên" value="<?php echo htmlspecialchars($search ?? ''); ?>" autofocus>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearSearch">
                                    <i class="fa fa-times"></i> Xóa
                                </button>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="student.php" class="btn btn-outline-secondary">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>
                        <div class="col-12 mt-2 small text-muted">
                            <?php $count = is_array($students) ? count($students) : 0; echo "Kết quả: " . $count; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-ghost p-3">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <button id="toggleBulk" class="btn btn-outline-danger">Xóa nhiều</button>
                        <button id="bulkClear" class="btn btn-outline-secondary d-none ms-2">Hủy chọn</button>
                    </div>
                    <div class="text-muted" id="bulkInfo" style="display:none">Đã chọn <span id="bulkCount">0</span></div>
                </div>
                <form id="bulkForm" method="POST" action="../handle/student_process.php">
                    <input type="hidden" name="action" value="bulk_delete">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th class="bulk-col d-none" style="width:40px;"><input type="checkbox" id="selectAll" class="d-none"></th>
                                    <th>STT</th>
                                    <th>Mã</th>
                                    <th>Họ tên</th>
                                    <th>Mã lớp</th>
                                    <th>Mã khoa</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($students) === 0): ?>
                                    <tr><td colspan="7" class="text-center p-4">Không có dữ liệu</td></tr>
                                <?php else: foreach($students as $i => $s): ?>
                                    <tr>
                                        <td class="bulk-col d-none" style="vertical-align: middle;">
                                            <input type="checkbox" name="ids[]" value="<?php echo $s['id']; ?>" class="bulk-checkbox">
                                        </td>
                                            <td><?php echo $i+1; ?></td>
                                    <?php
                                        $code = htmlspecialchars($s['student_code']);
                                        $name = htmlspecialchars($s['full_name']);
                                        if (!empty($search)) {
                                            try {
                                                $pat = '/' . preg_quote($search, '/') . '/iu';
                                                $code = preg_replace($pat, '<mark>$0</mark>', $code);
                                                $name = preg_replace($pat, '<mark>$0</mark>', $name);
                                            } catch (Exception $e) {
                                                // ignore
                                            }
                                        }
                                    ?>
                                    <td><?php echo $code; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo htmlspecialchars($s['class_code'] ?? $s['class_name']); ?></td>
                                    <td><?php echo htmlspecialchars($s['department_code'] ?? $s['department_name']); ?></td>
                                        <td class="table-actions">
                                        <a href="student/edit_student.php?id=<?php echo $s['id']; ?>" class="text-primary" title="Sửa">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                                        <a href="../handle/student_process.php?delete=1&id=<?php echo $s['id']; ?>" class="text-danger ms-2" onclick="return confirm('Xóa sinh viên này?')" title="Xóa">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Confirmation modal -->
                    <div class="modal fade" id="bulkConfirmModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Xác nhận xóa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Bạn có chắc chắn muốn xóa <strong id="confirmCount">0</strong> sinh viên đã chọn?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <button type="button" id="confirmBulkDelete" class="btn btn-danger">OK, Xóa</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#clearSearch').click(function() {
                $('#q').val('');
                $('form.search-form').submit();
            });
            $('form.search-form').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true);
                return true;
            });

            // Bulk delete UI
            var bulkMode = false;
            function updateBulkCount() {
                var c = $('.bulk-checkbox:checked').length;
                $('#bulkCount').text(c);
                $('#confirmCount').text(c);
                $('#toggleBulk').text(bulkMode ? ('Xóa nhiều ('+c+')') : 'Xóa nhiều');
                if (c > 0) {
                    $('#bulkInfo').show();
                } else {
                    if (!bulkMode) $('#bulkInfo').hide();
                }
            }

            $('#toggleBulk').on('click', function(e){
                e.preventDefault();
                bulkMode = !bulkMode;
                if (bulkMode) {
                    $('.bulk-col, .bulk-checkbox, #selectAll').removeClass('d-none');
                    $('#bulkClear').removeClass('d-none');
                    $('#bulkInfo').show();
                    updateBulkCount();
                } else {
                    // if turning off and none selected, just hide; if some selected, prompt confirm
                    var c = $('.bulk-checkbox:checked').length;
                    if (c > 0) {
                        // show confirm modal
                        var myModal = new bootstrap.Modal(document.getElementById('bulkConfirmModal'));
                        myModal.show();
                    } else {
                        $('.bulk-col, .bulk-checkbox, #selectAll').addClass('d-none');
                        $('#bulkClear').addClass('d-none');
                        $('#bulkInfo').hide();
                        updateBulkCount();
                    }
                }
            });

            $('#bulkClear').on('click', function(e){
                e.preventDefault();
                bulkMode = false;
                $('.bulk-checkbox').prop('checked', false);
                $('.bulk-col, .bulk-checkbox, #selectAll').addClass('d-none');
                $('#bulkClear').addClass('d-none');
                $('#bulkInfo').hide();
                updateBulkCount();
            });

            $(document).on('change', '.bulk-checkbox', function(){ updateBulkCount(); });
            $(document).on('change', '#selectAll', function(){ $('.bulk-checkbox').prop('checked', this.checked); updateBulkCount(); });

            $('#confirmBulkDelete').on('click', function(){
                // submit the form
                $('#bulkForm').submit();
            });
        });
    </script>
</body>
</html>
