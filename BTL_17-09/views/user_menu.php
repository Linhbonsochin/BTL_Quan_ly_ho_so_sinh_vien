<?php
// Ensure session and helper functions are available
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();
require_once __DIR__ . '/../functions/permissions.php';
$currentUser = getCurrentUser();
?>
<nav class="navbar navbar-expand-lg" style="background: linear-gradient(135deg, #be93c5, #7bc6cc);">
    <div class="container">
        <a class="navbar-brand text-white" href="/BTL_17-09/views/home.php">
            <i class="fa fa-home me-1"></i>Trang chủ
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <?php if (isStudent()): ?>
                    <li class="nav-item"><a class="nav-link text-white" href="/BTL_17-09/views/profile.php">Hồ sơ sinh
                            viên</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/BTL_17-09/views/student_grades.php">Kết quả
                            học tập</a></li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($currentUser): ?>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Xin chào,
                            <?php echo htmlspecialchars($currentUser['username']); ?></a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/BTL_17-09/handle/logout_process.php">Đăng
                            xuất</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link text-white" href="/BTL_17-09/index.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>