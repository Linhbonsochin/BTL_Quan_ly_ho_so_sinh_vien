<?php
require_once __DIR__ . '/../../functions/permissions.php';
requireAdmin();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm văn bản / biểu mẫu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body{background:#f6f8fa}.dashboard-main{padding:24px}</style>
</head>
<body>
    <div class="container my-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Thêm văn bản / biểu mẫu</h4>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form method="POST" action="../../handle/static_process.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="type" value="document">
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nội dung / Liên kết</label>
                <textarea name="content" class="form-control" rows="6"></textarea>
            </div>
           
                    <div class="mb-3">
                        <label class="form-label">Ảnh (tùy chọn)</label>
                        <input type="file" name="image" accept="image/*" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File đính kèm (tùy chọn)</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                     <div class="form-check mb-3"><input type="checkbox" name="published" class="form-check-input" id="pubd"><label for="pubd" class="form-check-label">Đã xuất bản</label></div>
                    <div class="d-flex gap-2"><button class="btn btn-primary">Thêm</button><a href="../static_documents.php" class="btn btn-secondary">Hủy</a></div>
                </form>
            </div>
            
        </div>
        
    </div>
</body>
</html>
