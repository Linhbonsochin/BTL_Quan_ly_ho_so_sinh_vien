<?php
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/class_functions.php';
requireAdmin();

$classes = getAllClasses();

// Pagination: server-side slicing (for small datasets). For large datasets
// consider switching to DB LIMIT/OFFSET in functions/class_functions.php
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$total = count($classes);
$totalPages = $total > 0 ? intval(ceil($total / $perPage)) : 1;
if ($page > $totalPages) $page = $totalPages;
$start = ($page - 1) * $perPage;
$pageClasses = array_slice($classes, $start, $perPage);
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý lớp - Admin</title>
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
                <h3 class="m-0">Quản lý lớp</h3>
                <div>
                    <span class="me-3 text-muted">Xin chào,
                        <?php $u = getCurrentUser();
                        echo htmlspecialchars($u['username'] ?? ''); ?></span>
                    <a class="btn btn-outline-secondary btn-sm" href="/BTL_17-09/handle/logout_process.php">Đăng
                        xuất</a>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <a href="class/class_create.php" class="btn btn-primary">Thêm lớp</a>
            </div>

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
                <form id="bulkForm" method="POST" action="/BTL_17-09/handle/class_process.php">
                    <input type="hidden" name="action" value="bulk_delete">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="text-align: center;">
                            <thead>
                                    <tr>
                                    <th class="bulk-col d-none" style="width:40px;">
                                        <input type="checkbox" id="selectAllHeader" class="form-check-input" aria-label="Chọn tất cả">
                                    </th>
                                    <th>STT</th>
                                    <th>Mã lớp</th>
                                    <th>Tên lớp</th>
                                    <th>Khoa</th>
                                    <th>Ngành</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($total === 0): ?>
                                    <tr>
                                        <td colspan="7" class="text-center p-4">Không có dữ liệu</td>
                                    </tr>
                                <?php else:
                                    foreach ($pageClasses as $i => $c): ?>
                                        <tr>
                                            <td class="bulk-col d-none" style="vertical-align:middle;"><input type="checkbox"
                                                    name="ids[]" value="<?php echo $c['id']; ?>" class="bulk-checkbox"></td>
                                            <td><?php echo $start + $i + 1; ?></td>
                                            <td><?php echo htmlspecialchars($c['class_code']); ?></td>
                                            <td><?php echo htmlspecialchars($c['class_name']); ?></td>
                                            <td><?php echo htmlspecialchars($c['department_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($c['major_name'] ?? ''); ?></td>
                                            <td>
                                                <a href="class/class_edit.php?id=<?php echo $c['id']; ?>" class="text-primary"
                                                    title="Sửa"><i class="fa fa-pencil"></i></a>
                                                <a href="../handle/class_process.php?delete=1&id=<?php echo $c['id']; ?>"
                                                    class="text-danger ms-2" onclick="return confirm('Xóa lớp này?')"
                                                    title="Xóa"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center mt-3">
                                <?php
                                // first / prev
                                $prev = $page - 1;
                                $next = $page + 1;
                                $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
                                $lastPrinted = 0;
                                ?>
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=1" aria-label="First">&laquo;</a>
                                </li>
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo max(1, $prev); ?>" aria-label="Previous">&lsaquo;</a>
                                </li>
                                <?php
                                for ($p = 1; $p <= $totalPages; $p++) {
                                    // only show first, last, and neighbors around current
                                    if ($p == 1 || $p == $totalPages || ($p >= $page - 2 && $p <= $page + 2)) {
                                        if ($p - $lastPrinted > 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                        $active = $p == $page ? ' active' : '';
                                        echo '<li class="page-item' . $active . '"><a class="page-link" href="?page=' . $p . '">' . $p . '</a></li>';
                                        $lastPrinted = $p;
                                    }
                                }
                                ?>
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo min($totalPages, $next); ?>" aria-label="Next">&rsaquo;</a>
                                </li>
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $totalPages; ?>" aria-label="Last">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>

                    <div class="modal fade" id="bulkConfirmModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Xác nhận xóa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Bạn có chắc chắn muốn xóa <strong id="confirmCount">0</strong> lớp đã chọn?
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
                if (selectAllBtn) selectAllBtn.classList.add('d-none');
                document.getElementById('bulkInfo').style.display = 'none';
                if (selectAllHeader) selectAllHeader.checked = false;
                updateBulkCount();
            });

            document.addEventListener('change', function (e) {
                if (e.target && e.target.classList && e.target.classList.contains('bulk-checkbox')) updateBulkCount();
            });

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