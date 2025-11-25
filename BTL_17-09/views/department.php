<?php
// Import các file cần thiết
require_once __DIR__ . '/../functions/permissions.php'; // File kiểm tra quyền
require_once __DIR__ . '/../functions/department_functions.php'; // File chứa các hàm xử lý khoa

// Kiểm tra quyền admin - chỉ admin mới được truy cập trang này
requireAdmin();

// Lấy danh sách tất cả các khoa
$departments = getAllDepartments();
// Pagination: server-side slicing (for small datasets). For large datasets
// consider switching to DB LIMIT/OFFSET in functions/department_functions.php
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$total = count($departments);
$totalPages = $total > 0 ? intval(ceil($total / $perPage)) : 1;
if ($page > $totalPages) $page = $totalPages;
$start = ($page - 1) * $perPage;
$pageDepartments = array_slice($departments, $start, $perPage);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý khoa - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>.table-actions .btn{margin-right:6px} body { background:#f6f8fa; } .sidebar { width:250px; min-height:100vh; background:#fff; border-right:1px solid #e6e9ee; } .sidebar .nav-link { color:#444; } .sidebar .nav-link:hover { background:#f0f4f8; } .card-ghost { border-radius:12px; box-shadow:0 6px 18px rgba(30,41,59,0.06); } .dashboard-main { padding:24px; }</style>
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/admin_menu.php'; ?>

        <div class="flex-grow-1 dashboard-main">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Quản lý khoa</h3>
                <div>
                    <span class="me-3 text-muted">Xin chào, <?php $u = getCurrentUser(); echo htmlspecialchars($u['username'] ?? ''); ?></span>
                    <a class="btn btn-outline-secondary btn-sm" href="/BTL_17-09/handle/logout_process.php">Đăng xuất</a>
                </div>
            </div>

            <div class="mb-3">
                <a href="department/create_department.php" class="btn btn-primary">Thêm khoa</a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="card card-ghost p-3">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <button id="toggleBulk" class="btn btn-outline-danger">Xóa nhiều</button>
                        <button id="bulkClear" class="btn btn-outline-secondary d-none ms-2">Hủy chọn</button>
                        <button id="selectAllBtn" class="btn btn-outline-primary d-none ms-2">Chọn tất cả</button>
                    </div>
                    <div class="text-muted" id="bulkInfo" style="display:none">Đã chọn <span id="bulkCount">0</span></div>
                </div>
                <form id="bulkForm" method="POST" action="../handle/department_process.php">
                    <input type="hidden" name="action" value="bulk_delete">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th class="bulk-col d-none" style="width:40px;">
                                        <input type="checkbox" id="selectAllHeader" class="form-check-input" aria-label="Chọn tất cả">
                                    </th>
                                    <th>STT</th>
                                    <th>Mã khoa</th>
                                    <th>Khoa</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                        <tbody>
                            <?php if (empty($departments)): ?>
                                <tr><td colspan="5" class="text-center p-4">Chưa có khoa</td></tr>
                            <?php else: foreach($pageDepartments as $i => $d): ?>
                                <tr>
                                    <td class="bulk-col d-none" style="vertical-align: middle;">
                                        <input type="checkbox" name="ids[]" value="<?php echo $d['id']; ?>" class="bulk-checkbox">
                                    </td>
                                    <td class="text-center"><?php echo $start + $i + 1; ?></td>
                                    <td><?php echo htmlspecialchars($d['department_code'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($d['department_name']); ?></td>
                                    <td class="table-actions text-center">
                                        <a href="department/edit_department.php?id=<?php echo $d['id']; ?>" class="text-primary" title="Sửa"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        <a href="../handle/department_process.php?delete=1&id=<?php echo $d['id']; ?>" class="text-danger ms-2" onclick="return confirm('Xóa khoa này?')" title="Xóa"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Confirmation modal -->
    <div class="modal fade" id="bulkConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa <strong id="confirmCount">0</strong> khoa đã chọn?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" id="confirmBulkDelete" class="btn btn-danger">OK, Xóa</button>
                </div>
            </div>
        </div>
    </div>

    <script>
            $(document).ready(function() {
            // Bulk delete UI
            var bulkMode = false;
            function updateBulkCount() {
                var c = $('.bulk-checkbox:checked').length;
                $('#bulkCount').text(c);
                $('#confirmCount').text(c);
                $('#toggleBulk').text(bulkMode ? ('Xóa nhiều ('+c+')') : 'Xóa nhiều');
                
                // Show bulk info if there are selected items or if in bulk mode
                if (c > 0 || bulkMode) {
                    $('#bulkInfo').show();
                } else {
                    $('#bulkInfo').hide();
                }
            }

            $('#toggleBulk').on('click', function(e){
                e.preventDefault();
                    var checkedCount = $('.bulk-checkbox:checked').length;

                    // If we're in bulk mode and have selections, show confirm modal
                    if (bulkMode && checkedCount > 0) {
                        var myModal = new bootstrap.Modal(document.getElementById('bulkConfirmModal'));
                        myModal.show();
                        return;
                    }

                    // Toggle bulk mode
                    bulkMode = !bulkMode;

                    var $cols = $('.bulk-col');
                    var $checks = $('.bulk-checkbox');
                    var $selectHeader = $('#selectAllHeader');
                    var $selectAllBtn = $('#selectAllBtn');

                    // Show/hide elements based on mode
                    if (bulkMode) {
                        $cols.removeClass('d-none');
                        $checks.prop('disabled', false).removeClass('d-none');
                        $('#bulkClear').removeClass('d-none');
                        if ($selectHeader.length) { $selectHeader.removeClass('d-none'); $selectHeader.prop('checked', false); }
                        if ($selectAllBtn.length) $selectAllBtn.removeClass('d-none');
                    } else {
                        $checks.prop('checked', false); // Uncheck all when exiting bulk mode
                        $cols.addClass('d-none');
                        $checks.prop('disabled', true).addClass('d-none');
                        $('#bulkClear').addClass('d-none');
                        if ($selectHeader.length) { $selectHeader.addClass('d-none'); $selectHeader.prop('checked', false); }
                        if ($selectAllBtn.length) $selectAllBtn.addClass('d-none');
                    }

                    updateBulkCount();
            });

            $('#bulkClear').on('click', function(e){
                e.preventDefault();
                // Just uncheck all boxes but stay in bulk mode
                $('.bulk-checkbox, #selectAllHeader').prop('checked', false);
                updateBulkCount();
            });

            $(document).on('change', '.bulk-checkbox', function(){ updateBulkCount(); });

            // header checkbox toggles all visible row checkboxes on the current page
            $(document).on('change', '#selectAllHeader', function(){ $('.bulk-checkbox').prop('checked', this.checked); updateBulkCount(); });

            // selectAllBtn toggles selection when visible
            var $selectAllBtn = $('#selectAllBtn');
            if ($selectAllBtn.length) {
                $selectAllBtn.on('click', function(e){
                    e.preventDefault();
                    var $checks = $('.bulk-checkbox');
                    var anyUnchecked = $checks.toArray().some(function(cb){ return !cb.checked; });
                    $checks.prop('checked', anyUnchecked);
                    var $sh = $('#selectAllHeader'); if ($sh.length) $sh.prop('checked', anyUnchecked);
                    updateBulkCount();
                    $selectAllBtn.text(anyUnchecked ? 'Bỏ chọn tất cả' : 'Chọn tất cả');
                });
            }

            $('#confirmBulkDelete').on('click', function(){
                // Close modal first
                var modal = bootstrap.Modal.getInstance(document.getElementById('bulkConfirmModal'));
                modal.hide();
                // Then submit the form
                $('#bulkForm').submit();
            });
            
            // Additional debug
            $('#bulkForm').on('submit', function(e) {
                var ids = [];
                $('.bulk-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });
                console.log('Submitting form with ids:', ids);
            });
        });
    </script>
</body>
</html>
