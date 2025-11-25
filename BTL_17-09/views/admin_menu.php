<style>
    :root {
        --primary-color: #4361ee;
        --primary-hover: #3851d0;
        --secondary-color: #6b7280;
        --accent-color: #f59e0b;
        --success-color: #10b981;
        --background-light: #f8fafc;
        --background-white: #ffffff;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
        --transition: all 0.3s ease;
    }

    body {
        background: var(--background-light);
        font-family: 'Inter', sans-serif;
    }

    .sidebar {
        width: 280px;
        min-height: 100vh;
        background: var(--background-white);
        border-right: 1px solid var(--border-color);
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        transition: var(--transition);
    }

    .sidebar-header {
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid var(--border-color);
    }

    .sidebar-header img {
        width: 50px;
        height: auto;
        margin-bottom: 1rem;
        transition: var(--transition);
    }

    .sidebar-header h2 {
        font-size: 1.1rem;
        color: var(--text-dark);
        font-weight: 600;
        margin: 0;
    }

    .nav {
        padding: 1rem 0;
    }

    .nav-link {
        padding: 0.75rem 1.5rem;
        color: var(--text-light) !important;
        font-weight: 500;
        border-radius: 0.5rem;
        margin: 0.25rem 1rem;
        transition: var(--transition);
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .nav-link i {
        width: 1.5rem;
        text-align: center;
        font-size: 1.1rem;
        transition: var(--transition);
    }

    .nav-link:hover {
        color: var(--primary-color) !important;
        background: var(--background-light);
    }

    .nav-link.active {
        color: var(--primary-color) !important;
        background: rgba(67, 97, 238, 0.1);
        font-weight: 600;
    }

    .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 60%;
        width: 3px;
        background: var(--primary-color);
        border-radius: 0 3px 3px 0;
    }

    .nav-section-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--secondary-color);
        padding: 1.5rem 1.5rem 0.5rem;
        font-weight: 600;
    }

    .collapse {
        background: rgba(0,0,0,0.02);
        border-radius: 0.5rem;
        margin: 0.25rem 1rem;
    }

    .collapse .nav-link {
        margin: 0;
        padding: 0.6rem 1rem 0.6rem 3rem;
        font-size: 0.95rem;
    }

    /* Main Content Adjustment */
    .dashboard-main {
        margin-left: 280px;
        padding: 2rem;
        transition: var(--transition);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .sidebar {
            width: 240px;
            transform: translateX(-100%);
        }
        
        .sidebar.show {
            transform: translateX(0);
        }

        .dashboard-main {
            margin-left: 0;
        }
    }

    /* Animation Effects */
    .nav-link i {
        transition: transform 0.2s ease;
    }

    .nav-link:hover i {
        transform: scale(1.1);
    }

    .sidebar-header img:hover {
        transform: rotate(5deg);
    }
</style><!-- Shared admin sidebar/menu -->
<div class="sidebar">
    <div class="sidebar-header">
        <img src="/BTL_17-09/image/sutu.png" alt="logo">
        <h2>Quản lý sinh viên</h2>
    </div>

    <nav class="nav flex-column">
        <!-- Dashboard -->
        <div class="nav-section">
            <div class="nav-section-title">Tổng quan</div>
            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'admin_dashboard.php') !== false ? 'active' : ''; ?>" 
               href="/BTL_17-09/views/admin_dashboard.php">
                <i class="fas fa-home"></i>
                <span>Trang chủ</span>
            </a>
        </div>

        <!-- Quản lý dữ liệu -->
        <div class="nav-section">
            <div class="nav-section-title">Quản lý dữ liệu</div>
            <a class="nav-link" href="#tablesSub" data-bs-toggle="collapse">
                <i class="fas fa-database"></i>
                <span>Dữ liệu sinh viên</span>
            </a>
            <div class="collapse <?php echo (strpos($_SERVER['PHP_SELF'], 'student.php') !== false || strpos($_SERVER['PHP_SELF'], 'grade.php') !== false) ? 'show' : ''; ?>" 
                 id="tablesSub">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'student.php') !== false ? 'active' : ''; ?>" 
                   href="/BTL_17-09/views/student.php">
                    <i class="fas fa-user-graduate"></i>
                    <span>Danh sách sinh viên</span>
                </a>
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'grade.php') !== false ? 'active' : ''; ?>" 
                   href="/BTL_17-09/views/grade.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Quản lý điểm</span>
                </a>
            </div>
        </div>

        <!-- Quản lý đào tạo -->
        <div class="nav-section">
            <div class="nav-section-title">Quản lý đào tạo</div>
            <a class="nav-link" href="#trainingSub" data-bs-toggle="collapse">
                <i class="fas fa-graduation-cap"></i>
                <span>Đào tạo</span>
            </a>
            <div class="collapse <?php echo (strpos($_SERVER['PHP_SELF'], 'department.php') !== false || 
                                          strpos($_SERVER['PHP_SELF'], 'subject.php') !== false || 
                                          strpos($_SERVER['PHP_SELF'], 'class.php') !== false) ? 'show' : ''; ?>" 
                 id="trainingSub">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'department.php') !== false ? 'active' : ''; ?>" 
                   href="/BTL_17-09/views/department.php">
                    <i class="fas fa-building"></i>
                    <span>Quản lý khoa</span>
                </a>
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'major.php') !== false ? 'active' : ''; ?>" 
                   href="/BTL_17-09/views/major.php">
                    <i class="fas fa-layer-group"></i>
                    <span>Quản lý ngành</span>
                </a>
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'subject.php') !== false ? 'active' : ''; ?>" 
                   href="/BTL_17-09/views/subject.php">
                    <i class="fas fa-book"></i>
                    <span>Quản lý môn học</span>
                </a>
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'class.php') !== false ? 'active' : ''; ?>" 
                   href="/BTL_17-09/views/class.php">
                    <i class="fas fa-users"></i>
                    <span>Quản lý lớp học</span>
                </a>
            </div>
        </div>

        <!-- Quản lý trang tĩnh -->
        <div class="nav-section">
            <div class="nav-section-title">Quản lý trang tĩnh</div>
            <a class="nav-link" href="#staticSub" data-bs-toggle="collapse">
                <i class="fas fa-file-alt"></i>
                <span>Trang tĩnh</span>
            </a>
                <div class="collapse <?php echo (strpos($_SERVER['PHP_SELF'], 'static_notifications') !== false ||
                                                        strpos($_SERVER['PHP_SELF'], 'static_documents') !== false ||
                                                        strpos($_SERVER['PHP_SELF'], 'static_news') !== false) ? 'show' : ''; ?>" 
                 id="staticSub">
                     <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'static_notifications') !== false ? 'active' : ''; ?>" 
                         href="/BTL_17-09/views/static_notifications.php">
                    <i class="fas fa-bell"></i>
                    <span>Quản lý thông báo</span>
                </a>
                     <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'static_documents') !== false ? 'active' : ''; ?>" 
                         href="/BTL_17-09/views/static_documents.php">
                    <i class="fas fa-file-signature"></i>
                    <span>Quản lý văn bản/biểu mẫu</span>
                </a>
                     <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'static_news') !== false ? 'active' : ''; ?>" 
                         href="/BTL_17-09/views/static_news.php">
                    <i class="fas fa-newspaper"></i>
                    <span>Quản lý tin tức mới</span>
                </a>
            </div>
        </div>

        <!-- Hệ thống -->
        <div class="nav-section">
            <div class="nav-section-title">Hệ thống</div>
            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'user.php') !== false ? 'active' : ''; ?>" 
               href="/BTL_17-09/views/user.php">
                <i class="fas fa-user-shield"></i>
                <span>Quản lý tài khoản</span>
            </a>
            
        </div>
    </nav>
</div>