<?php
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/static_functions.php';
requireAdmin();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
$allItems = getStaticsByType('news');

if ($q !== '') {
    $allItems = array_values(array_filter($allItems, function ($r) use ($q) {
        return stripos($r['title'] ?? '', $q) !== false;
    }));
}

// date range filter
if ($dateFrom !== '' || $dateTo !== '') {
    $fromTs = $dateFrom !== '' ? strtotime($dateFrom . ' 00:00:00') : null;
    $toTs = $dateTo !== '' ? strtotime($dateTo . ' 23:59:59') : null;
    $allItems = array_values(array_filter($allItems, function($r) use ($fromTs, $toTs) {
        if (empty($r['created_at'])) return false;
        $ts = strtotime($r['created_at']);
        if ($ts === false) return false;
        if ($fromTs !== null && $ts < $fromTs) return false;
        if ($toTs !== null && $ts > $toTs) return false;
        return true;
    }));
}

// Pagination
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$total = count($allItems);
$totalPages = $total > 0 ? intval(ceil($total / $perPage)) : 1;
if ($page > $totalPages) $page = $totalPages;
$start = ($page - 1) * $perPage;
$items = array_slice($allItems, $start, $perPage);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý tin tức mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body{background:#f6f8fa}.container{margin-top:20px}</style>
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/admin_menu.php'; ?>

        <div class="flex-grow-1 dashboard-main">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Quản lý tin tức mới</h3>
                <div>
                    <span class="me-3 text-muted">Xin chào, <?php $u = getCurrentUser(); echo htmlspecialchars($u['username'] ?? ''); ?></span>
                    <a class="btn btn-outline-secondary btn-sm" href="/BTL_17-09/handle/logout_process.php">Đăng xuất</a>
                </div>
            </div>
            <?php if (isset($_SESSION['success'])): ?><div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div><?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?><div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div><?php endif; ?>

            <div class="mb-3">
                <a href="news/static_news_create.php" class="btn btn-primary">Thêm tin tức</a>
            </div>
            <div class="mb-3">
                <form method="GET" action="static_news.php" class="p-3 bg-white rounded-3 shadow-sm">
                    <div class="d-flex gap-2 align-items-center">
                        <input type="search" name="q" class="form-control" placeholder="Tìm theo tiêu đề"
                            value="<?php echo htmlspecialchars($q); ?>">

                        <input type="date" name="date_from" class="form-control" style="max-width:160px;" value="<?php echo htmlspecialchars($dateFrom); ?>" title="Từ ngày">
                        <input type="date" name="date_to" class="form-control" style="max-width:160px;" value="<?php echo htmlspecialchars($dateTo); ?>" title="Đến ngày">

                        <button class="btn btn-primary">Tìm kiếm</button>
                        <a class="btn btn-outline-secondary" href="static_news.php">Reset</a>
                    </div>
                    <div class="ms-auto text-muted">Kết quả: <?php echo $total; ?></div>
                </form>
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

                <form id="bulkForm" method="POST" action="/BTL_17-09/handle/static_process.php">
                    <input type="hidden" name="action" value="bulk_delete">
                    <input type="hidden" name="type" value="news">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="text-align:center;">
                            <thead>
                                <tr>
                                    <th class="bulk-col d-none" style="width:40px;"><input type="checkbox" id="selectAllHeader" class="form-check-input"></th>
                                    <th>STT</th>
                                    <th>Tiêu đề</th>
                                    <th>Ngày</th>
                                    <th>Xuất bản</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center p-4">Không có dữ liệu</td>
                                    </tr>
                                <?php else:
                                    foreach ($items as $i => $r): ?>
                                        <tr>
                                            <td class="bulk-col d-none" style="vertical-align:middle;"><input type="checkbox"
                                                    name="ids[]" value="<?php echo $r['id']; ?>" class="bulk-checkbox"></td>
                                            <td><?php echo $start + $i + 1; ?></td>
                                            <td><?php echo htmlspecialchars($r['title']); ?></td>
                                            <td><?php echo htmlspecialchars($r['created_at']); ?></td>
                                            <td><?php echo $r['published'] ? 'Đã xuất bản' : 'Chưa xuất bản'; ?></td>
                                            <td>
                                                <?php if (!empty($r['image_path'])): ?>
                                                    <a class="text-success me-2" href="/<?php echo 'BTL_17-09/' . ltrim($r['image_path'], '/'); ?>" target="_blank" title="Ảnh"><i class="fa fa-image"></i></a>
                                                <?php endif; ?>
                                                <?php if (!empty($r['file_path'])): ?>
                                                    <a class="text-info me-2" href="/<?php echo 'BTL_17-09/' . ltrim($r['file_path'], '/'); ?>" target="_blank" title="File"><i class="fa fa-file"></i></a>
                                                <?php endif; ?>
                                                <a class="text-primary" href="news/static_news_edit.php?id=<?php echo $r['id']; ?>" title="Sửa"><i class="fa fa-pencil"></i></a>
                                                <a class="text-danger ms-2" href="/BTL_17-09/handle/static_process.php?delete=1&id=<?php echo $r['id']; ?>" onclick="return confirm('Xóa?')" title="Xóa"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination moved below like notifications -->

                    <!-- Bulk confirm modal -->
                    <div class="modal fade" id="bulkConfirmModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Xác nhận xóa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">Bạn có chắc chắn muốn xóa <strong id="confirmCount">0</strong>
                                    mục đã chọn?</div>
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

    <!-- Pagination (notifications-style) -->
    <?php if ($total > $perPage): ?>
    <div class="container my-3">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                $base = basename($_SERVER['PHP_SELF']);
                $buildUrl = function($p) use ($base, $q, $dateFrom, $dateTo) {
                    $qpart = $q ? '&q=' . urlencode($q) : '';
                    $df = $dateFrom !== '' ? '&date_from=' . urlencode($dateFrom) : '';
                    $dt = $dateTo !== '' ? '&date_to=' . urlencode($dateTo) : '';
                    return $base . '?page=' . $p . $qpart . $df . $dt;
                };

                $visible = 7; // visible page links
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
