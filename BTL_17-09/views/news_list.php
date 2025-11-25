<?php
require_once __DIR__ . '/../functions/static_functions.php';
require_once __DIR__ . '/../functions/permissions.php';

$currentUser = getCurrentUser();
if (!$currentUser) {
    header('Location: /BTL_17-09/index.php');
    exit();
}

$all = getStaticsByType('news');
$items = array_values(array_filter($all, function($r){ return !empty($r['published']); }));
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tin tức</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/user_menu.php'; ?>
    <div class="container my-4">
        <h3>Tin tức</h3>
        <?php if (empty($items)): ?>
            <div class="alert alert-info">Chưa có tin tức nào.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach($items as $it): ?>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start" href="static_view.php?type=news&id=<?php echo $it['id']; ?>">
                        <div>
                            <div class="fw-bold"><?php echo htmlspecialchars($it['title']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars(date('d/m/Y', strtotime($it['created_at'] ?? 'now'))); ?></div>
                        </div>
                        <div class="text-muted small">&nbsp;&raquo;</div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="mt-3"><a href="home.php" class="btn btn-outline-secondary">Quay lại</a></div>
    </div>
</body>
</html>