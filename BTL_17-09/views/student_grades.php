<?php
session_start();
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/grade_functions.php';
require_once __DIR__ . '/../functions/student_functions.php';

requireLogin();

$current = getCurrentUser();
$student = null;
if ($current && isset($current['id'])) {
    // Prefer lookup by user_id if available
    $student = getStudentByUserId($current['id']);
}
// Fallback: try to match student_code == username (legacy behavior)
if (!$student && $current && isset($current['username'])) {
    $student = getStudentByCode($current['username']);
}

if (!$student) {
    $_SESSION['error'] = 'Không tìm thấy thông tin sinh viên.';
    header('Location: /BTL_17-09/index.php');
    exit;
}

$student_id = $student['id'];

// Get grades for the logged-in student
$grades = getGradesByStudentId($student_id);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kết quả học tập</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/user-theme.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .grades-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(190, 147, 197, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .grades-header {
            background: linear-gradient(135deg, #be93c5, #7bc6cc);
            color: white;
            padding: 2.5rem 2rem;
            position: relative;
            text-align: center;
        }

        .student-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .student-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .student-avatar i {
            font-size: 2.8rem;
            color: #be93c5;
        }

        .student-details h3 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .student-code {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-size: 1rem;
            display: inline-block;
        }

        .grades-body {
            padding: 2rem;
        }

        .section-title {
            color: #495057;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .grade-table {
            margin-bottom: 0;
        }

        .grade-table th {
            background: linear-gradient(135deg, #be93c5, #7bc6cc);
            color: white;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            border: none;
            padding: 1rem;
            font-size: 0.95rem;
        }

        .grade-table td {
            vertical-align: middle;
            padding: 0.8rem;
            border-color: #eee;
            text-align: center;
        }

        .grade-value {
            font-weight: bold;
            padding: 0.4rem 1rem;
            border-radius: 6px;
            display: inline-block;
            min-width: 3.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .grade-value.high {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .grade-value.medium {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .grade-value.low {
            background-color: #ffebee;
            color: #c62828;
        }

        .grade-stats {
            background: linear-gradient(135deg, rgba(190, 147, 197, 0.05), rgba(123, 198, 204, 0.05));
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #be93c5, #7bc6cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #495057;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .empty-grades {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-grades i {
            font-size: 5rem;
            color: #be93c5;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }

        .empty-grades h4 {
            color: #495057;
            margin-bottom: 1rem;
        }

        .empty-grades p {
            color: #6c757d;
        }

        tr:hover {
            background-color: rgba(190, 147, 197, 0.05) !important;
        }

        @media (max-width: 768px) {
            .grades-header {
                padding: 2rem 1rem;
            }

            .student-avatar {
                width: 80px;
                height: 80px;
            }

            .student-details h3 {
                font-size: 1.5rem;
            }

            .grades-body {
                padding: 1.5rem;
            }

            .grade-stats {
                padding: 1.5rem;
            }

            .stat-item {
                margin-bottom: 1.5rem;
            }

            .stat-value {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body class="bg-light">
    <?php include __DIR__ . '/user_menu.php'; ?>
    <div class="container my-4">
        <div class="grades-card">
            <div class="grades-header">
                <div class="student-info">
                    <div class="student-avatar">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="student-details">
                        <h3><?php echo htmlspecialchars($student['full_name']); ?></h3>
                        <div class="student-code">
                            <i class="fas fa-id-card me-2"></i>MSSV:
                            <?php echo htmlspecialchars($student['student_code']); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grades-body">
                <?php if (empty($grades)): ?>
                    <div class="empty-grades">
                        <i class="fas fa-clipboard-list"></i>
                        <h4>Chưa có điểm</h4>
                        <p>Hiện tại chưa có thông tin điểm nào được cập nhật.</p>
                    </div>
                <?php else: ?>
                    <h4 class="section-title">
                        <i class="fas fa-graduation-cap me-2"></i>Bảng điểm chi tiết
                    </h4>
                    <div class="table-responsive">
                        <table class="table grade-table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10%">STT</th>
                                    <th style="width: 20%">Mã môn học</th>
                                    <th style="width: 50%">Tên môn học</th>
                                    <th style="width: 20%">Điểm</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                $count = count($grades);
                                $high = 0;
                                $low = 0;
                                foreach ($grades as $i => $grade):
                                    $grade_value = $grade['grade'] ?? 0;
                                    $total += $grade_value;
                                    if ($grade_value >= 8)
                                        $high++;
                                    if ($grade_value < 5)
                                        $low++;

                                    $grade_class = '';
                                    if ($grade_value >= 8)
                                        $grade_class = 'high';
                                    elseif ($grade_value >= 5)
                                        $grade_class = 'medium';
                                    else
                                        $grade_class = 'low';
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i + 1; ?></td>
                                        <td class="text-center">
                                            <?php echo htmlspecialchars($grade['subject_code'] ?? '(Chưa cập nhật)'); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo htmlspecialchars($grade['subject_name'] ?? '(Chưa cập nhật)'); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="grade-value <?php echo $grade_class; ?>">
                                                <?php echo htmlspecialchars(number_format($grade_value, 1)); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="section-title mt-4">
                        <i class="fas fa-chart-line me-2"></i>Thống kê kết quả học tập
                    </h4>
                    <div class="grade-stats">
                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo number_format($total / $count, 2); ?></div>
                                    <div class="stat-label">Điểm trung bình</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $count; ?></div>
                                    <div class="stat-label">Tổng số môn</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $high; ?></div>
                                    <div class="stat-label">Điểm >= 8.0</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $low; ?></div>
                                    <div class="stat-label">Điểm < 5.0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>