<?php
require_once __DIR__ . '/../../functions/permissions.php';
require_once __DIR__ . '/../../functions/static_functions.php';
requireAdmin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: static_notifications.php?error=Thiếu+ID');
    exit();
}

$id = intval($_GET['id']);
$item = getStaticById($id);
if (!$item) {
    header('Location: static_notifications.php?error=Không+tìm+thấy+thông+báo');
    exit();
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chỉnh sửa thông báo</title>
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
    <?php include __DIR__ . '/../admin_menu.php'; ?>
    <div class="dashboard-main container">
        <h3>Chỉnh sửa thông báo</h3>
        <?php if (isset($_SESSION['success'])): ?><div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div><?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?><div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div><?php endif; ?>

        <form method="POST" action="../../handle/static_process.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nội dung</label>
                <textarea name="content" class="form-control" rows="8"><?php echo htmlspecialchars($item['content']); ?></textarea>
            </div>
            <div class="form-check mb-3"><input type="checkbox" name="published" class="form-check-input" id="pub" <?php echo $item['published'] ? 'checked' : ''; ?>><label for="pub" class="form-check-label">Đã xuất bản</label></div>
            <div class="mb-3">
                <label class="form-label">Ảnh hiện tại</label>
                <div>
                    <?php if (!empty($item['image_path'])): ?>
                        <a href="/BTL_17-09/<?php echo htmlspecialchars($item['image_path']); ?>" target="_blank">Xem ảnh</a>
                    <?php else: ?>
                        <span class="text-muted">Chưa có</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Thay ảnh (tùy chọn)</label>
                <input type="file" name="image" accept="image/*" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">File đính kèm hiện tại</label>
                <div>
                    <?php if (!empty($item['file_path'])): ?>
                        <a href="/BTL_17-09/<?php echo htmlspecialchars($item['file_path']); ?>" target="_blank">Tải file</a>
                    <?php else: ?>
                        <span class="text-muted">Chưa có</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Thay file đính kèm (tùy chọn)</label>
                <input type="file" name="attachment" class="form-control">
            </div>
            <div class="d-flex gap-2"><button class="btn btn-primary">Lưu</button><a href="../static_notifications.php" class="btn btn-secondary">Hủy</a></div>
        </form>
    </div>
</body>
</html>
