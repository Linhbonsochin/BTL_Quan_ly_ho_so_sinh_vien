<?php
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/student_functions.php';
requireAdmin();


require_once __DIR__ . '/../functions/class_functions.php';
require_once __DIR__ . '/../functions/department_functions.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : null;
$class_id = isset($_GET['class_id']) ? trim($_GET['class_id']) : '';
$department_id = isset($_GET['department_id']) ? trim($_GET['department_id']) : '';

// Lấy danh sách lớp và khoa cho dropdown
$classList = getClasses();
$departmentList = getDepartments();

// Lấy danh sách sinh viên với lọc
$students = array_filter(getStudents($search), function($s) use ($class_id, $department_id) {
    $ok = true;
    if ($class_id !== '' && $class_id !== null) {
        $ok = $ok && ($s['class_id'] == $class_id);
    }
    if ($department_id !== '' && $department_id !== null) {
        $ok = $ok && ($s['department_id'] == $department_id);
    }
    return $ok;
});

// Pagination (server-side slice). For larger datasets switch to DB LIMIT/OFFSET.
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$total = is_array($students) ? count($students) : 0;
$totalPages = $total > 0 ? intval(ceil($total / $perPage)) : 1;
if ($page > $totalPages) $page = $totalPages;
$start = ($page - 1) * $perPage;
$pageStudents = array_slice($students, $start, $perPage);
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
                        <div class="col-md-4 col-lg-3 mb-2 mb-md-0">
                            <input type="search" name="q" id="q" class="form-control" placeholder="Tìm theo tên hoặc mã sinh viên" value="<?php echo htmlspecialchars($search ?? ''); ?>" autofocus>
                        </div>
                        <div class="col-md-3 col-lg-2 mb-2 mb-md-0">
                            <select name="class_id" class="form-select">
                                <option value="">-- Lọc theo lớp --</option>
                                <?php foreach($classList as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php if($class_id !== '' && $class_id == $c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['class_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2 mb-2 mb-md-0">
                            <select name="department_id" class="form-select">
                                <option value="">-- Lọc theo khoa --</option>
                                <?php foreach($departmentList as $d): ?>
                                    <option value="<?php echo $d['id']; ?>" <?php if($department_id !== '' && $department_id == $d['id']) echo 'selected'; ?>><?php echo htmlspecialchars($d['department_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-lg-2 mb-2 mb-md-0">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                        <div class="col-md-2 col-lg-2 mb-2 mb-md-0">
                            <a href="student.php" class="btn btn-outline-secondary w-100">
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
                        <button id="selectAllBtn" class="btn btn-outline-primary d-none ms-2">Chọn tất cả</button>
                    </div>
                    <div class="text-muted" id="bulkInfo" style="display:none">Đã chọn <span id="bulkCount">0</span></div>
                </div>
                <form id="bulkForm" method="POST" action="../handle/student_process.php">
                    <input type="hidden" name="action" value="bulk_delete">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th class="bulk-col d-none" style="width:40px;">
                                        <input type="checkbox" id="selectAllHeader" class="form-check-input" aria-label="Chọn tất cả">
                                    </th>
                                    <th>STT</th>
                                    <th>Mã</th>
                                    <th>Họ tên</th>
                                    <th>Mã lớp</th>
                                    <th>Mã khoa</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($total === 0): ?>
                                    <tr><td colspan="7" class="text-center p-4">Không có dữ liệu</td></tr>
                                <?php else: foreach($pageStudents as $i => $s): ?>
                                    <tr>
                                        <td class="bulk-col d-none" style="vertical-align: middle;">
                                            <input type="checkbox" name="ids[]" value="<?php echo $s['id']; ?>" class="bulk-checkbox">
                                        </td>
                                            <td><?php echo $start + $i + 1; ?></td>
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
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="container my-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php
                                $base = basename($_SERVER['PHP_SELF']);
                                $buildUrl = function($p) use ($base, $search) { $q = $search ? '&q=' . urlencode($search) : ''; return $base . '?page=' . $p . $q; };

                                $visible = 7;
                                $startPage = max(1, $page - intval($visible/2));
                                $endPage = $startPage + $visible - 1;
                                if ($endPage > $totalPages) { $endPage = $totalPages; $startPage = max(1, $endPage - $visible + 1); }
                                ?>
                                <li class="page-item <?php if($page<=1) echo 'disabled'; ?>"><a class="page-link" href="<?php echo $buildUrl(1); ?>">«</a></li>
                                <li class="page-item <?php if($page<=1) echo 'disabled'; ?>"><a class="page-link" href="<?php echo $buildUrl(max(1,$page-1)); ?>">‹</a></li>

                                <?php if ($startPage > 1): ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $buildUrl(1); ?>">1</a></li>
                                    <?php if ($startPage > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                                <?php endif; ?>

                                <?php for($p = $startPage; $p <= $endPage; $p++): ?>
                                    <li class="page-item <?php if($p==$page) echo 'active'; ?>"><a class="page-link" href="<?php echo $buildUrl($p); ?>"><?php echo $p; ?></a></li>
                                <?php endfor; ?>

                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $buildUrl($totalPages); ?>"><?php echo $totalPages; ?></a></li>
                                <?php endif; ?>

                                <li class="page-item <?php if($page>=$totalPages) echo 'disabled'; ?>"><a class="page-link" href="<?php echo $buildUrl(min($totalPages,$page+1)); ?>">›</a></li>
                                <li class="page-item <?php if($page>=$totalPages) echo 'disabled'; ?>"><a class="page-link" href="<?php echo $buildUrl($totalPages); ?>">»</a></li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>

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

            // Bulk delete UI (adapted to match class template behavior)
            var bulkMode = false;
            var $selectHeader = $('#selectAllHeader');
            var $selectAllBtn = $('#selectAllBtn');

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
                var $cols = $('.bulk-col');
                var $checks = $('.bulk-checkbox');
                if (bulkMode) {
                    $cols.removeClass('d-none');
                    $checks.removeClass('d-none');
                    $('#bulkClear').removeClass('d-none');
                    if ($selectHeader.length) { $selectHeader.removeClass('d-none'); $selectHeader.prop('checked', false); }
                    if ($selectAllBtn.length) $selectAllBtn.removeClass('d-none');
                    $('#bulkInfo').show();
                    updateBulkCount();
                } else {
                    var c = $('.bulk-checkbox:checked').length;
                    if (c > 0) {
                        var myModal = new bootstrap.Modal(document.getElementById('bulkConfirmModal'));
                        myModal.show();
                    } else {
                        $cols.addClass('d-none');
                        $checks.addClass('d-none');
                        $('#bulkClear').addClass('d-none');
                        if ($selectHeader.length) { $selectHeader.addClass('d-none'); $selectHeader.prop('checked', false); }
                        if ($selectAllBtn.length) $selectAllBtn.addClass('d-none');
                        $('#bulkInfo').hide();
                        updateBulkCount();
                    }
                }
            });

            $('#bulkClear').on('click', function(e){
                e.preventDefault();
                bulkMode = false;
                $('.bulk-checkbox').prop('checked', false);
                $('.bulk-col, .bulk-checkbox').addClass('d-none');
                $('#bulkClear').addClass('d-none');
                $('#bulkInfo').hide();
                if ($selectHeader.length) { $selectHeader.addClass('d-none'); $selectHeader.prop('checked', false); }
                if ($selectAllBtn.length) $selectAllBtn.addClass('d-none');
                updateBulkCount();
            });

            $(document).on('change', '.bulk-checkbox', function(){ updateBulkCount(); });

            // header checkbox toggles all visible row checkboxes on the current page
            if ($selectHeader.length) {
                $selectHeader.on('change', function(){ $('.bulk-checkbox').prop('checked', this.checked); updateBulkCount(); });
            }

            // selectAllBtn toggles selection of visible rows (current page)
            if ($selectAllBtn.length) {
                $selectAllBtn.on('click', function(e){
                    e.preventDefault();
                    var $checks = $('.bulk-checkbox');
                    var anyUnchecked = $checks.toArray().some(function(cb){ return !cb.checked; });
                    $checks.prop('checked', anyUnchecked);
                    if ($selectHeader.length) $selectHeader.prop('checked', anyUnchecked);
                    updateBulkCount();
                    $selectAllBtn.text(anyUnchecked ? 'Bỏ chọn tất cả' : 'Chọn tất cả');
                });
            }

            $('#confirmBulkDelete').on('click', function(){
                $('#bulkForm').submit();
            });
        });
    </script>
</body>
</html>
