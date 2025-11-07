<?php
// Import các file cần thiết
require_once __DIR__ . '/../functions/permissions.php'; // File kiểm tra quyền
require_once __DIR__ . '/../functions/grade_functions.php'; // File chứa các hàm xử lý điểm

// Kiểm tra quyền admin - chỉ admin mới được truy cập trang này
requireAdmin();

// Lấy danh sách tất cả các điểm sinh viên
$grades = getAllGrades();
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

            <div class="card card-ghost p-3">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <button id="toggleBulk" class="btn btn-outline-danger">Xóa nhiều</button>
                        <button id="bulkClear" class="btn btn-outline-secondary d-none ms-2">Hủy chọn</button>
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
                                    <th class="bulk-col d-none" style="width:40px;"></th>
                                    <th>STT</th>
                                    <th>Sinh viên</th>
                                    <th>Môn</th>
                                    <th>Điểm</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($grades)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center p-4">Chưa có điểm nào</td>
                                    </tr>
                                <?php else:
                                    foreach ($grades as $i => $g): ?>
                                        <tr>
                                            <td class="bulk-col d-none" style="vertical-align:middle;"><input type="checkbox"
                                                    name="ids[]" value="<?php echo $g['id']; ?>" class="bulk-checkbox"></td>
                                            <td><?php echo $i + 1; ?></td>
                                            <td><?php echo htmlspecialchars($g['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($g['subject_name']); ?></td>
                                            <td><?php echo htmlspecialchars(number_format($g['grade'], 1)); ?></td>
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
            document.getElementById('toggleBulk').addEventListener('click', function (e) { e.preventDefault(); bulkMode = !bulkMode; var cols = document.querySelectorAll('.bulk-col'); var checks = document.querySelectorAll('.bulk-checkbox'); if (bulkMode) { cols.forEach(c => c.classList.remove('d-none')); checks.forEach(c => c.classList.remove('d-none')); document.getElementById('bulkClear').classList.remove('d-none'); document.getElementById('bulkInfo').style.display = 'block'; updateBulkCount(); } else { var c = document.querySelectorAll('.bulk-checkbox:checked').length; if (c > 0) { var myModal = new bootstrap.Modal(document.getElementById('bulkConfirmModal')); myModal.show(); } else { cols.forEach(c => c.classList.add('d-none')); checks.forEach(c => c.classList.add('d-none')); document.getElementById('bulkClear').classList.add('d-none'); document.getElementById('bulkInfo').style.display = 'none'; updateBulkCount(); } } });
            document.getElementById('bulkClear').addEventListener('click', function (e) { e.preventDefault(); bulkMode = false; document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = false); document.querySelectorAll('.bulk-col').forEach(c => c.classList.add('d-none')); document.querySelectorAll('.bulk-checkbox').forEach(c => c.classList.add('d-none')); document.getElementById('bulkClear').classList.add('d-none'); document.getElementById('bulkInfo').style.display = 'none'; updateBulkCount(); });
            document.addEventListener('change', function (e) { if (e.target && e.target.classList && e.target.classList.contains('bulk-checkbox')) updateBulkCount(); });
            document.getElementById('confirmBulkDelete').addEventListener('click', function () { document.getElementById('bulkForm').submit(); });
        })();
    </script>
</body>

</html>