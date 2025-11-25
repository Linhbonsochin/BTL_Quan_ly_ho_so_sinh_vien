<?php
require_once __DIR__ . '/../../functions/permissions.php';
requireAdmin();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm thông báo</title>
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
    <!-- using the compact card-style add/edit layout like student create pages -->
    <div class="container my-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Thêm thông báo</h4>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form action="../../handle/static_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="type" value="notification">

                    <div class="mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input class="form-control" type="text" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ảnh (tùy chọn)</label>
                        <input type="file" name="image" accept="image/*" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File đính kèm (tùy chọn)</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nội dung</label>
                        <textarea class="form-control" name="content" rows="6"></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="published" class="form-check-input" id="pub">
                        <label for="pub" class="form-check-label">Đã xuất bản</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Thêm</button>
                        <a href="../static_notifications.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
