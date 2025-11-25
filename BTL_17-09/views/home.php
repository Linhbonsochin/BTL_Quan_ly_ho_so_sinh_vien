<?php
// Import file chứa các hàm kiểm tra quyền
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/static_functions.php';

// Lấy thông tin người dùng hiện tại từ session
$currentUser = getCurrentUser();

// Nếu chưa đăng nhập thì chuyển về trang đăng nhập
if (!$currentUser) {
	header('Location: /BTL_17-09/index.php');
	exit();
}

// Lấy nội dung tĩnh (chỉ lấy mục đã xuất bản)
$newsAll = getStaticsByType('news');
$notificationsAll = getStaticsByType('notification');
$documentsAll = getStaticsByType('document');

// filter published and take top N
$take = 3;
$news = array_slice(array_values(array_filter($newsAll, function($r){ return !empty($r['published']); })), 0, $take);
$notifications = array_slice(array_values(array_filter($notificationsAll, function($r){ return !empty($r['published']); })), 0, $take);
$documents = array_slice(array_values(array_filter($documentsAll, function($r){ return !empty($r['published']); })), 0, $take);
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Trang chủ - Hệ thống quản lý sinh viên</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/home.css">
	<style>
		.news-card {
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			height: 100%;
		}

		.news-card .card-header {
			background: linear-gradient(135deg, #be93c5, #7bc6cc);
			color: white;
			font-weight: bold;
			padding: 12px 15px;
			border-radius: 8px 8px 0 0;
		}

		.news-card .list-group-item {
			border: none;
			border-bottom: 1px solid #eee;
			padding: 12px 15px;
		}

		.news-card .list-group-item:last-child {
			border-bottom: none;
		}

		.news-date {
			color: #666;
			font-size: 0.85rem;
		}

		.university-logo {
			height: 40px;
			width: auto;
		}

		.news-link {
			text-decoration: none;
			color: #333;
		}

		.news-link:hover {
			color: #be93c5;
		}

		.view-all-link {
			display: inline-block;
			padding: 8px 15px;
			text-decoration: none;
			color: #be93c5;
			font-weight: 500;
		}

		.view-all-link:hover {
			background-color: #f8f9fa;
			border-radius: 4px;
		}
	</style>
</head>

<body>
	<?php include __DIR__ . '/user_menu.php'; ?>

	<div class="container my-4">
		<div class="row g-4">
			<!-- Tin tức mới nhất -->
			<div class="col-md-4">
				<div class="card news-card">
					<div class="card-header d-flex align-items-center">
						<i class="fa fa-newspaper me-2"></i>
						<span>TIN TỨC MỚI NHẤT</span>
					</div>
					<div class="list-group list-group-flush">
						<?php if (empty($news)): ?>
							<div class="list-group-item">Chưa có tin tức nào.</div>
						<?php else: foreach($news as $n): ?>
							<a href="static_view.php?type=news&id=<?php echo $n['id']; ?>" class="list-group-item news-link">
								<div>
									<?php echo htmlspecialchars($n['title']); ?>
									<div class="news-date"><?php echo htmlspecialchars(date('d/m/Y', strtotime($n['created_at'] ?? 'now'))); ?></div>
								</div>
							</a>
						<?php endforeach; endif; ?>
					</div>
					<div class="card-footer bg-transparent text-end">
						<a href="news_list.php" class="view-all-link">Xem tất cả</a>
					</div>
				</div>
			</div>

			<!-- Thông báo -->
			<div class="col-md-4">
				<div class="card news-card">
					<div class="card-header d-flex align-items-center">
						<i class="fa fa-bell me-2"></i>
						<span>THÔNG BÁO</span>
					</div>
					<div class="list-group list-group-flush">
						<?php if (empty($notifications)): ?>
							<div class="list-group-item">Chưa có thông báo nào.</div>
						<?php else: foreach($notifications as $n): ?>
							<a href="static_view.php?type=notification&id=<?php echo $n['id']; ?>" class="list-group-item news-link">
								<div>
									<?php echo htmlspecialchars($n['title']); ?>
									<div class="news-date"><?php echo htmlspecialchars(date('d/m/Y', strtotime($n['created_at'] ?? 'now'))); ?></div>
								</div>
							</a>
						<?php endforeach; endif; ?>
					</div>
					<div class="card-footer bg-transparent text-end">
						<a href="notifications_list.php" class="view-all-link">Xem tất cả</a>
					</div>
				</div>
			</div>

			<!-- Văn bản, biểu mẫu -->
			<div class="col-md-4">
				<div class="card news-card">
					<div class="card-header d-flex align-items-center">
						<i class="fa fa-file-alt me-2"></i>
						<span>VĂN BẢN, BIỂU MẪU</span>
					</div>
					<div class="list-group list-group-flush">
						<?php if (empty($documents)): ?>
							<div class="list-group-item">Chưa có văn bản/biểu mẫu nào.</div>
						<?php else: foreach($documents as $d): ?>
							<a href="static_view.php?type=document&id=<?php echo $d['id']; ?>" class="list-group-item news-link">
								<div>
									<?php echo htmlspecialchars($d['title']); ?>
									<div class="news-date"><?php echo htmlspecialchars(date('d/m/Y', strtotime($d['created_at'] ?? 'now'))); ?></div>
								</div>
							</a>
						<?php endforeach; endif; ?>
					</div>
					<div class="card-footer bg-transparent text-end">
						<a href="documents_list.php" class="view-all-link">Xem tất cả</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>