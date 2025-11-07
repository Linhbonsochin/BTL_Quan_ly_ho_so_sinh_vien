<?php
// Import file chứa các hàm kiểm tra quyền
require_once __DIR__ . '/../functions/permissions.php';

// Lấy thông tin người dùng hiện tại từ session
$currentUser = getCurrentUser();

// Nếu chưa đăng nhập thì chuyển về trang đăng nhập
if (!$currentUser) {
    header('Location: /BTL_17-09/index.php');
    exit();
}
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
                        <a href="#" class="list-group-item news-link">
                            <div class="d-flex align-items-center mb-2">
                            </div>
                            <div>
                                Thông báo v/v: Nghỉ lễ Quốc Khánh 02/09/2025
                                <div class="news-date">13/03/2025</div>
                            </div>
                        </a>
                        <a href="#" class="list-group-item news-link">
                            <div class="d-flex align-items-center mb-2">
                            </div>
                            <div>
                                Thông báo V/v: Sinh viên khóa 14,15,16, 17,18 nộp học phí học kỳ I năm học 2025-2026
                                <div class="news-date">13/03/2025</div>
                            </div>
                        </a>
                    </div>
                    <div class="card-footer bg-transparent text-end">
                        <a href="#" class="view-all-link">Xem tất cả</a>
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
                        <a href="#" class="list-group-item news-link">
                            <div class="d-flex align-items-center mb-2">
                            </div>
                            <div>
                                Quy định công tác Sinh viên Đại học Đại Nam đối với Chương trình đào tạo đại học hệ
                                chính quy
                                <div class="news-date">18/08/2025</div>
                            </div>
                        </a>
                        <a href="#" class="list-group-item news-link">
                            <div class="d-flex align-items-center mb-2">
                            </div>
                            <div>
                                Quyết định ban hành Quy chế đào tạo trình độ đại học theo hệ thống tín chỉ của Trường
                                Đại học Đại Nam
                                <div class="news-date">11/08/2025</div>
                            </div>
                        </a>
                    </div>
                    <div class="card-footer bg-transparent text-end">
                        <a href="#" class="view-all-link">Xem tất cả</a>
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

                        <a href="#" class="list-group-item news-link">
                            <div class="d-flex align-items-center mb-2">

                            </div>
                            <div>
                                Ban hành quy định thi, kiểm tra và đánh giá kết quả học tập của sinh viên
                                <div class="news-date">05/08/2025</div>
                            </div>
                        </a>
                    </div>
                    <div class="card-footer bg-transparent text-end">
                        <a href="#" class="view-all-link">Xem tất cả</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>