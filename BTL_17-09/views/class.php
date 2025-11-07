<?php
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/class_functions.php';
requireAdmin();

$classes = getAllClasses();
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
                                    <th class="bulk-col d-none" style="width:40px;"></th>
                                    <th>STT</th>
                                    <th>Mã lớp</th>
                                    <th>Tên lớp</th>
                                    <th>Khoa</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($classes) === 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-center p-4">Không có dữ liệu</td>
                                    </tr>
                                <?php else:
                                    foreach ($classes as $i => $c): ?>
                                        <tr>
                                            <td class="bulk-col d-none" style="vertical-align:middle;"><input type="checkbox"
                                                    name="ids[]" value="<?php echo $c['id']; ?>" class="bulk-checkbox"></td>
                                            <td><?php echo $i + 1; ?></td>
                                            <td><?php echo htmlspecialchars($c['class_code']); ?></td>
                                            <td><?php echo htmlspecialchars($c['class_name']); ?></td>
                                            <td><?php echo htmlspecialchars($c['department_name'] ?? ''); ?></td>
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
            document.getElementById('toggleBulk').addEventListener('click', function (e) { e.preventDefault(); bulkMode = !bulkMode; var cols = document.querySelectorAll('.bulk-col'); var checks = document.querySelectorAll('.bulk-checkbox'); if (bulkMode) { cols.forEach(c => c.classList.remove('d-none')); checks.forEach(c => c.classList.remove('d-none')); document.getElementById('bulkClear').classList.remove('d-none'); document.getElementById('bulkInfo').style.display = 'block'; updateBulkCount(); } else { var c = document.querySelectorAll('.bulk-checkbox:checked').length; if (c > 0) { var myModal = new bootstrap.Modal(document.getElementById('bulkConfirmModal')); myModal.show(); } else { cols.forEach(c => c.classList.add('d-none')); checks.forEach(c => c.classList.add('d-none')); document.getElementById('bulkClear').classList.add('d-none'); document.getElementById('bulkInfo').style.display = 'none'; updateBulkCount(); } } });
            document.getElementById('bulkClear').addEventListener('click', function (e) { e.preventDefault(); bulkMode = false; document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = false); document.querySelectorAll('.bulk-col').forEach(c => c.classList.add('d-none')); document.querySelectorAll('.bulk-checkbox').forEach(c => c.classList.add('d-none')); document.getElementById('bulkClear').classList.add('d-none'); document.getElementById('bulkInfo').style.display = 'none'; updateBulkCount(); });
            document.addEventListener('change', function (e) { if (e.target && e.target.classList && e.target.classList.contains('bulk-checkbox')) updateBulkCount(); });
            document.getElementById('confirmBulkDelete').addEventListener('click', function () { document.getElementById('bulkForm').submit(); });
        })();
    </script>
</body>

</html>