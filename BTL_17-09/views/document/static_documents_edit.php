<?php
require_once __DIR__ . '/../../functions/permissions.php';
require_once __DIR__ . '/../../functions/static_functions.php';
requireAdmin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: static_documents.php?error=Thiếu+ID');
    exit();
}

$id = intval($_GET['id']);
$item = getStaticById($id);
if (!$item) {
    header('Location: static_documents.php?error=Không+tìm+thấy');
    exit();
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chỉnh sửa văn bản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body{background:#f6f8fa}.dashboard-main{padding:24px}</style>
</head>
<body>
    <div class="container my-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Chỉnh sửa văn bản / biểu mẫu</h4>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form method="POST" action="../../handle/static_process.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nội dung / Liên kết</label>
                <textarea name="content" class="form-control" rows="8"><?php echo htmlspecialchars($item['content']); ?></textarea>
            </div>
            <div class="form-check mb-3"><input type="checkbox" name="published" class="form-check-input" id="pubd2" <?php echo $item['published'] ? 'checked' : ''; ?>><label for="pubd2" class="form-check-label">Đã xuất bản</label></div>

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
                    <div class="d-flex gap-2"><button class="btn btn-primary">Lưu</button><a href="../static_documents.php" class="btn btn-secondary">Hủy</a></div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
