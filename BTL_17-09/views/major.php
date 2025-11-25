<?php
require_once __DIR__ . '/../functions/permissions.php';
requireAdmin();
require_once __DIR__ . '/../functions/major_functions.php';
$majors = getAllMajors();
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$total = count($majors);
$totalPages = $total > 0 ? intval(ceil($total / $perPage)) : 1;
if ($page > $totalPages)
    $page = $totalPages;
$start = ($page - 1) * $perPage;
$pageMajors = array_slice($majors, $start, $perPage);
?>
<!DOCTYPE html>
<html lang="vi">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Quản lý ngành - Admin</title>
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
                <h3 class="m-0">Quản lý ngành</h3>
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
                 <a href="major/major_create.php" class="btn btn-primary">Thêm ngành</a>
            </div>
            

            <!--
                Chú thích: Giao diện nút xóa nhiều ngành (bulk delete)
                Đây là chức năng QUAN TRỌNG cho quản trị ngành, giúp admin thao tác nhanh và hiệu quả
            -->
            <div class="card card-ghost p-3">
                <div class="mb-3 d-flex align-items-center gap-2">
                    <button id="toggleBulk" class="btn btn-outline-danger">Xóa nhiều</button>
                    <button id="bulkClear" class="btn btn-outline-secondary d-none ms-2">Hủy chọn</button>
                    <button id="selectAllBtn" class="btn btn-outline-primary d-none ms-2">Chọn tất cả</button>
                    <span class="ms-3 text-muted" id="bulkInfo" style="display:none">Đã chọn <span id="bulkCount">0</span></span>
                </div>
                <form id="bulkForm" method="POST" action="../handle/major_process.php">
                    <input type="hidden" name="action" value="bulk_delete">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th class="bulk-col d-none" style="width:40px;">
                                        <input type="checkbox" id="selectAllHeader" class="form-check-input"
                                            aria-label="Chọn tất cả">
                                    </th>
                                    <th>STT</th>
                                    <th>Mã ngành</th>
                                    <th>Tên ngành</th>
                                    <th>Khoa</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($majors) === 0): ?>
                                    <tr>
                                        <td colspan="6" class="text-center p-4">Không có dữ liệu</td>
                                    </tr>
                                <?php else:
                                    foreach ($pageMajors as $i => $m): ?>
                                        <tr>
                                            <td class="bulk-col d-none" style="vertical-align:middle;"><input type="checkbox"
                                                    name="ids[]" value="<?php echo $m['id']; ?>" class="bulk-checkbox"></td>
                                            <td><?php echo $start + $i + 1; ?></td>
                                            <td><?php echo htmlspecialchars($m['major_code']); ?></td>
                                            <td><?php echo htmlspecialchars($m['major_name']); ?></td>
                                            <td><?php echo htmlspecialchars($m['department_name']); ?></td>
                                            <td>
                                                <a href="major/major_edit.php?id=<?php echo $m['id']; ?>" class="text-primary"
                                                    title="Sửa"><i class="fa fa-pencil"></i></a>
                                                <a href="../handle/major_process.php?action=delete&id=<?php echo $m['id']; ?>"
                                                    class="text-danger ms-2" onclick="return confirm('Xóa ngành này?')"
                                                    title="Xóa"><i class="fa fa-trash"></i></a>
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
                                    Bạn có chắc chắn muốn xóa <strong id="confirmCount">0</strong> ngành đã chọn?
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
            <!-- Pagination -->
            <?php if ($total > $perPage): ?>
                <div class="container my-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php
                            $base = basename($_SERVER['PHP_SELF']);
                            $buildUrl = function ($p) use ($base) {
                                return $base . '?page=' . $p;
                            };
                            $visible = 7;
                            $startP = max(1, $page - intval($visible / 2));
                            $endP = $startP + $visible - 1;
                            if ($endP > $totalPages) {
                                $endP = $totalPages;
                                $startP = max(1, $endP - $visible + 1);
                            }
                            ?>
                            <li class="page-item <?php if ($page <= 1)
                                echo 'disabled'; ?>"><a class="page-link" href="<?php echo $buildUrl(1); ?>">«</a></li>
                            <li class="page-item <?php if ($page <= 1)
                                echo 'disabled'; ?>"><a class="page-link"
                                    href="<?php echo $buildUrl(max(1, $page - 1)); ?>">‹</a></li>

                            <?php if ($startP > 1): ?>
                                <li class="page-item"><a class="page-link" href="<?php echo $buildUrl(1); ?>">1</a></li>
                                <?php if ($startP > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                            <?php endif; ?>

                            <?php for ($p = $startP; $p <= $endP; $p++): ?>
                                <li class="page-item <?php if ($p == $page)
                                    echo 'active'; ?>"><a class="page-link"
                                        href="<?php echo $buildUrl($p); ?>"><?php echo $p; ?></a></li>
                            <?php endfor; ?>

                            <?php if ($endP < $totalPages): ?>
                                <?php if ($endP < $totalPages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                                <li class="page-item"><a class="page-link"
                                        href="<?php echo $buildUrl($totalPages); ?>"><?php echo $totalPages; ?></a></li>
                            <?php endif; ?>

                            <li class="page-item <?php if ($page >= $totalPages)
                                echo 'disabled'; ?>"><a class="page-link"
                                    href="<?php echo $buildUrl(min($totalPages, $page + 1)); ?>">›</a></li>
                            <li class="page-item <?php if ($page >= $totalPages)
                                echo 'disabled'; ?>"><a class="page-link"
                                    href="<?php echo $buildUrl($totalPages); ?>">»</a></li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
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
                if (selectAllHeader) { selectAllHeader.classList.add('d-none'); selectAllHeader.checked = false; }
                if (selectAllBtn) selectAllBtn.classList.add('d-none');
                updateBulkCount();
            });

            document.addEventListener('change', function (e) { if (e.target && e.target.classList && e.target.classList.contains('bulk-checkbox')) updateBulkCount(); });

            if (selectAllHeader) {
                selectAllHeader.addEventListener('change', function (e) {
                    var checked = !!e.target.checked;
                    document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = checked);
                    updateBulkCount();
                });
            }

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