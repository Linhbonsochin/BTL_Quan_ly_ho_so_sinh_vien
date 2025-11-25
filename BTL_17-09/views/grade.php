<?php
// Import các file cần thiết
require_once __DIR__ . '/../functions/permissions.php'; // File kiểm tra quyền
require_once __DIR__ . '/../functions/grade_functions.php'; // File chứa các hàm xử lý điểm
require_once __DIR__ . '/../functions/subject_functions.php';

// Kiểm tra quyền admin - chỉ admin mới được truy cập trang này
requireAdmin();

// Lấy danh sách tất cả các điểm sinh viên
$allGrades = getAllGrades();

// Pagination settings
$perPage = 10;
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$filter_subject = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
$filter_score = isset($_GET['score']) ? floatval($_GET['score']) : '';

// Lọc theo môn học
$gradesBySubject = $allGrades;
if ($filter_subject) {
    $gradesBySubject = array_filter($allGrades, function($g) use ($filter_subject) {
        return $g['subject_id'] == $filter_subject;
    });
}

// Lọc theo điểm tổng kết (không liên quan đến lọc môn)
$gradesByScore = $allGrades;
if ($filter_score !== '') {
    $gradesByScore = array_filter($allGrades, function($g) use ($filter_score) {
        return isset($g['total_score']) && floatval($g['total_score']) == $filter_score;
    });
}

// Hiển thị dữ liệu theo bộ lọc nào được chọn
if ($filter_subject) {
    $showGrades = array_values($gradesBySubject);
} elseif ($filter_score !== '') {
    $showGrades = array_values($gradesByScore);
} else {
    $showGrades = $allGrades;
}

$total = is_array($showGrades) ? count($showGrades) : 0;
$totalPages = ($total > 0) ? (int) ceil($total / $perPage) : 1;
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;
$grades = array_slice($showGrades, $offset, $perPage);

$subjects = [];
try {
    $subjects = getAllSubjects();
} catch (Exception $e) {
    $subjects = [];
}
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý điểm - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f8fa;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #fff;
            border-right: 1px solid #e6e9ee;
        }

        .sidebar .nav-link {
            color: #444;
        }

        .sidebar .nav-link:hover {
            background: #f0f4f8;
        }

        .card-ghost {
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(30, 41, 59, 0.06);
        }

        .dashboard-main {
            padding: 24px;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php include __DIR__ . '/admin_menu.php'; ?>

        <div class="flex-grow-1 dashboard-main">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Quản lý điểm</h3>
                <div>
                    <span class="me-3 text-muted">Xin chào,
                        <?php $u = getCurrentUser();
                        echo htmlspecialchars($u['username'] ?? ''); ?></span>
                    <a class="btn btn-outline-secondary btn-sm" href="/BTL_17-09/handle/logout_process.php">Đăng
                        xuất</a>
                </div>
            </div>

            <div class="mb-3">
                <a href="grade/grade_create.php" class="btn btn-primary">Thêm điểm</a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form class="row g-2 mb-3" method="get" action="">
                <div class="col-md-5">
                    <select name="subject_id" class="form-select" onchange="this.form.submit()">
                        <option value="0">-- Lọc theo môn học --</option>
                        <?php foreach ($subjects as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php if($filter_subject == $s['id']) echo 'selected'; ?>><?php echo htmlspecialchars($s['subject_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="grade.php" class="btn btn-outline-secondary w-100">Bỏ lọc môn</a>
                </div>
            </form>

            <form class="row g-2 mb-3" method="get" action="">
                <div class="col-md-5">
                    <input type="number" step="0.1" min="0" max="10" name="score" class="form-control" placeholder="Lọc theo điểm tổng kết" value="<?php echo $filter_score !== '' ? htmlspecialchars($filter_score) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Lọc điểm</button>
                </div>
                <div class="col-md-2">
                    <a href="grade.php" class="btn btn-outline-secondary w-100">Bỏ lọc điểm</a>
                </div>
            </form>

            <div class="card card-ghost p-3">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <button id="toggleBulk" class="btn btn-outline-danger">Xóa nhiều</button>
                        <button id="bulkClear" class="btn btn-outline-secondary d-none ms-2">Hủy chọn</button>
                        <button id="selectAllBtn" class="btn btn-outline-primary d-none ms-2">Chọn tất cả</button>
                    </div>
                    <div class="text-muted" id="bulkInfo" style="display:none">Đã chọn <span id="bulkCount">0</span>
                    </div>
                </div>
                <form id="bulkForm" method="POST" action="../handle/grade_process.php">
                    <input type="hidden" name="action" value="bulk_delete">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th class="bulk-col d-none" style="width:40px;">
                                        <input type="checkbox" id="selectAllHeader" class="form-check-input" aria-label="Chọn tất cả">
                                    </th>
                                    <th>STT</th>
                                    <th>Sinh viên</th>
                                    <th>Môn</th>
                                    <th>Chuyên cần</th>
                                    <th>Giữa kỳ</th>
                                    <th>Cuối kỳ</th>
                                    <th>Tổng kết</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($grades)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center p-4">Chưa có điểm nào</td>
                                    </tr>
                                <?php else:
                                    foreach ($grades as $i => $g): ?>
                                        <tr>
                                            <td class="bulk-col d-none" style="vertical-align:middle;"><input type="checkbox"
                                                    name="ids[]" value="<?php echo $g['id']; ?>" class="bulk-checkbox"></td>
                                            <td><?php echo $offset + $i + 1; ?></td>
                                            <td><?php echo htmlspecialchars($g['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($g['subject_name']); ?></td>
                                            <td><?php echo isset($g['attendance_score']) ? htmlspecialchars(number_format($g['attendance_score'], 1)) : '-'; ?></td>
                                            <td><?php echo isset($g['midterm_score']) ? htmlspecialchars(number_format($g['midterm_score'], 1)) : '-'; ?></td>
                                            <td><?php echo isset($g['final_score']) ? htmlspecialchars(number_format($g['final_score'], 1)) : '-'; ?></td>
                                            <td><?php echo isset($g['total_score']) ? htmlspecialchars(number_format($g['total_score'], 1)) : '-'; ?></td>
                                            <td class="table-actions">
                                                <a href="grade/grade_edit.php?id=<?php echo $g['id']; ?>" class="text-primary"
                                                    title="Sửa"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                <a href="../handle/grade_process.php?action=delete&id=<?php echo $g['id']; ?>"
                                                    class="text-danger ms-2"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa điểm này?')"
                                                    title="Xóa"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="modal fade" id="bulkConfirmModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Xác nhận xóa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Bạn có chắc chắn muốn xóa <strong id="confirmCount">0</strong> mục đã chọn?
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
    <!-- Pagination -->
    <?php if ($total > $perPage): ?>
    <div class="container my-3">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                // helper to build page url
                $base = basename($_SERVER['PHP_SELF']);
                $buildUrl = function($p) use ($base){ return $base . '?p=' . $p; };

                $visible = 7; // visible page links
                $start = max(1, $page - intval($visible/2));
                $end = $start + $visible - 1;
                if ($end > $totalPages) { $end = $totalPages; $start = max(1, $end - $visible + 1); }
                ?>
                <li class="page-item <?php if($page<=1) echo 'disabled'; ?>"><a class="page-link" href="<?php echo $buildUrl(1); ?>">«</a></li>
                <li class="page-item <?php if($page<=1) echo 'disabled'; ?>"><a class="page-link" href="<?php echo $buildUrl(max(1,$page-1)); ?>">‹</a></li>

                <?php if ($start > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?php echo $buildUrl(1); ?>">1</a></li>
                    <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                <?php endif; ?>

                <?php for($p = $start; $p <= $end; $p++): ?>
                    <li class="page-item <?php if($p==$page) echo 'active'; ?>"><a class="page-link" href="<?php echo $buildUrl($p); ?>"><?php echo $p; ?></a></li>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                    <li class="page-item"><a class="page-link" href="<?php echo $buildUrl($totalPages); ?>"><?php echo $totalPages; ?></a></li>
                <?php endif; ?>

                <li class="page-item <?php if($page>=$totalPages) echo 'disabled'; ?>"><a class="page-link" href="<?php echo $buildUrl(min($totalPages,$page+1)); ?>">›</a></li>
                <li class="page-item <?php if($page>=$totalPages) echo 'disabled'; ?>"><a class="page-link" href="<?php echo $buildUrl($totalPages); ?>">»</a></li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            var bulkMode = false;
            function updateBulkCount() {
                var c = document.querySelectorAll('.bulk-checkbox:checked').length;
                document.getElementById('bulkCount').textContent = c;
                var conf = document.getElementById('confirmCount'); if (conf) conf.textContent = c;
                var info = document.getElementById('bulkInfo'); if (c > 0) info.style.display = 'block'; else if (!bulkMode) info.style.display = 'none';
            }

            var selectAllHeader = document.getElementById('selectAllHeader');
            var selectAllBtn = document.getElementById('selectAllBtn');

            document.getElementById('toggleBulk').addEventListener('click', function (e) {
                e.preventDefault();
                bulkMode = !bulkMode;
                var cols = document.querySelectorAll('.bulk-col');
                var checks = document.querySelectorAll('.bulk-checkbox');
                if (bulkMode) {
                    cols.forEach(c => c.classList.remove('d-none'));
                    checks.forEach(c => c.classList.remove('d-none'));
                    document.getElementById('bulkClear').classList.remove('d-none');
                    if (selectAllHeader) { selectAllHeader.classList.remove('d-none'); selectAllHeader.checked = false; }
                    if (selectAllBtn) selectAllBtn.classList.remove('d-none');
                    document.getElementById('bulkInfo').style.display = 'block';
                    updateBulkCount();
                } else {
                    var c = document.querySelectorAll('.bulk-checkbox:checked').length;
                    if (c > 0) {
                        var myModal = new bootstrap.Modal(document.getElementById('bulkConfirmModal'));
                        myModal.show();
                    } else {
                        cols.forEach(c => c.classList.add('d-none'));
                        checks.forEach(c => c.classList.add('d-none'));
                        document.getElementById('bulkClear').classList.add('d-none');
                        if (selectAllHeader) { selectAllHeader.classList.add('d-none'); selectAllHeader.checked = false; }
                        if (selectAllBtn) selectAllBtn.classList.add('d-none');
                        document.getElementById('bulkInfo').style.display = 'none';
                        updateBulkCount();
                    }
                }
            });

            document.getElementById('bulkClear').addEventListener('click', function (e) {
                e.preventDefault();
                bulkMode = false;
                document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = false);
                document.querySelectorAll('.bulk-col').forEach(c => c.classList.add('d-none'));
                document.querySelectorAll('.bulk-checkbox').forEach(c => c.classList.add('d-none'));
                document.getElementById('bulkClear').classList.add('d-none');
                document.getElementById('bulkInfo').style.display = 'none';
                // hide selectAll controls
                if (selectAllHeader) { selectAllHeader.classList.add('d-none'); selectAllHeader.checked = false; }
                if (selectAllBtn) selectAllBtn.classList.add('d-none');
                updateBulkCount();
            });

            document.addEventListener('change', function (e) { if (e.target && e.target.classList && e.target.classList.contains('bulk-checkbox')) updateBulkCount(); });

            // header checkbox toggles all visible row checkboxes on the current page
            if (selectAllHeader) {
                selectAllHeader.addEventListener('change', function (e) {
                    var checked = !!e.target.checked;
                    document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = checked);
                    updateBulkCount();
                });
            }

            // selectAllBtn toggles selection of visible rows (current page)
            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var all = Array.from(document.querySelectorAll('.bulk-checkbox'));
                    var anyUnchecked = all.some(cb => !cb.checked);
                    all.forEach(cb => cb.checked = anyUnchecked);
                    if (selectAllHeader) selectAllHeader.checked = anyUnchecked;
                    updateBulkCount();
                    selectAllBtn.textContent = anyUnchecked ? 'Bỏ chọn tất cả' : 'Chọn tất cả';
                });
            }

            document.getElementById('confirmBulkDelete').addEventListener('click', function () { document.getElementById('bulkForm').submit(); });
        })();
    </script>
</body>

</html>