<?php
require_once __DIR__ . '/../functions/permissions.php';

// Yêu cầu phải là admin
requireAdmin('../index.php');

$currentUser = getCurrentUser();
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Hệ thống quản lý hồ sơ sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f8fa;
        }

        .sidebar {
            width: 400px;
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

        .metric {
            background: #fff;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 6px 18px rgba(30, 41, 59, 0.04);
        }

        .metric .value {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .metric .label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .dashboard-main {
            padding: 24px;
        }

        a.card-link {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar (shared) -->
        <?php include __DIR__ . '/admin_menu.php'; ?>

        <!-- Main content -->
        <div class="flex-grow-1 dashboard-main">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Trang chủ</h3>
                <div>
                    <span class="me-3 text-muted">Xin chào,
                        <?php echo htmlspecialchars($currentUser['username'] ?? ''); ?></span>
                    <a class="btn btn-outline-secondary btn-sm" href="/BTL_17-09/handle/logout_process.php">Đăng
                        xuất</a>
                </div>
            </div>

            <?php
            // Lấy thông tin thống kê
            require_once __DIR__ . '/../functions/student_functions.php';
            require_once __DIR__ . '/../functions/class_functions.php';
            require_once __DIR__ . '/../functions/department_functions.php';
            require_once __DIR__ . '/../functions/statistics_functions.php';
            
            $students = getStudents();
            $classes = getAllClasses();
            $departments = getAllDepartments();
            $averageGrade = getOverallAverageGrade();
            
            // Lấy dữ liệu cho biểu đồ
            $studentsByDept = getStudentsByDepartment();
            $gradeDistribution = getGradeDistribution();
            ?>

            <!-- Thống kê tổng quan -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="metric">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-user-graduate text-primary me-2"></i>
                            <div class="label">Tổng sinh viên</div>
                        </div>
                        <div class="value"><?php echo count($students); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-chalkboard text-success me-2"></i>
                            <div class="label">Tổng lớp học</div>
                        </div>
                        <div class="value"><?php echo count($classes); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-university text-info me-2"></i>
                            <div class="label">Tổng khoa</div>
                        </div>
                        <div class="value"><?php echo count($departments); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-chart-line text-warning me-2"></i>
                            <div class="label">Điểm trung bình toàn trường</div>
                        </div>
                        <div class="value"><?php echo number_format($averageGrade, 2); ?></div>
                    </div>
                </div>
            </div>

            <!-- Truy cập nhanh -->
            <h4 class="mb-3">Truy cập nhanh</h4>
            <div class="row g-4">
                <div class="col-md-4">
                    <a href="student.php" class="card-link">
                        <div class="card card-ghost">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-user-plus text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">Thêm sinh viên mới</h5>
                                        <p class="card-text text-muted mb-0">Quản lý hồ sơ sinh viên</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="grade.php" class="card-link">
                        <div class="card card-ghost">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-graduation-cap text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">Quản lý điểm</h5>
                                        <p class="card-text text-muted mb-0">Cập nhật và xem điểm</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="class.php" class="card-link">
                        <div class="card card-ghost">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-chalkboard-teacher text-info"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">Quản lý lớp</h5>
                                        <p class="card-text text-muted mb-0">Tổ chức lớp học</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

            <!-- Biểu đồ thống kê -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card card-ghost">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Phân bố sinh viên theo khoa</h5>
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-ghost">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Phân bố điểm</h5>
                            <canvas id="gradeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Chuẩn bị dữ liệu cho biểu đồ phân bố theo khoa
                const departmentData = <?php echo json_encode($studentsByDept); ?>;
                const deptLabels = departmentData.map(d => d.department_name);
                const deptCounts = departmentData.map(d => d.student_count);
                
                // Biểu đồ phân bố sinh viên theo khoa
                new Chart(document.getElementById('departmentChart'), {
                    type: 'pie',
                    data: {
                        labels: deptLabels,
                        datasets: [{
                            data: deptCounts,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(153, 102, 255, 0.8)',
                                'rgba(255, 159, 64, 0.8)',
                                'rgba(201, 203, 207, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let percentage = Math.round((value * 100) / sum);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Chuẩn bị dữ liệu cho biểu đồ phân bố điểm
                const gradeData = <?php echo json_encode($gradeDistribution); ?>;
                const gradeLabels = gradeData.map(g => g.grade_range);
                const gradeCounts = gradeData.map(g => g.count);

                // Biểu đồ phân bố điểm
                new Chart(document.getElementById('gradeChart'), {
                    type: 'bar',
                    data: {
                        labels: gradeLabels,
                        datasets: [{
                            label: 'Số sinh viên',
                            data: gradeCounts,
                            backgroundColor: 'rgba(54, 162, 235, 0.8)'
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Số sinh viên: ${context.raw}`;
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
</body>

</html>