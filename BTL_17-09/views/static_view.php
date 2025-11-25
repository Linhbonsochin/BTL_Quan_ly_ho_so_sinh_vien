<?php
require_once __DIR__ . '/../functions/static_functions.php';
require_once __DIR__ . '/../functions/permissions.php';

// type and id from GET
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$item = null;
if ($id > 0) {
    $item = getStaticById($id);
}

// if not found or type mismatch, show 404-like message
if (!$item || ($type !== '' && $item['type'] !== $type)) {
    http_response_code(404);
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $item ? htmlspecialchars($item['title']) : 'Trang không tồn tại'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body{background:#f6f8fa}.dashboard-main{padding:24px}</style>
</head>
<body>
    <?php include __DIR__ . '/user_menu.php'; ?>
    <div class="container dashboard-main">
        <?php if (!$item): ?>
            <div class="alert alert-warning">Không tìm thấy nội dung hoặc trang không tồn tại.</div>
            <p><a href="home.php" class="btn btn-secondary">Quay lại trang chủ</a></p>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <div class="text-muted mb-3">Ngày: <?php echo htmlspecialchars(date('d/m/Y', strtotime($item['created_at']))); ?></div>
                    <div><?php echo nl2br(htmlspecialchars($item['content'])); ?></div>
                    <div class="mt-3"><a href="home.php" class="btn btn-outline-secondary">Quay lại</a></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
