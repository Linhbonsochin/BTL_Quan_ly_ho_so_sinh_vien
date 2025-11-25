<?php
session_start();
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/grade_functions.php';
require_once __DIR__ . '/../functions/student_functions.php';

requireLogin();

$current = getCurrentUser();
$student = null;
if ($current && isset($current['id'])) {
    $student = getStudentByUserId($current['id']);
}
if (!$student && $current && isset($current['username'])) {
    $student = getStudentByCode($current['username']);
}

if (!$student) {
    $_SESSION['error'] = 'Không tìm thấy thông tin sinh viên.';
    header('Location: /BTL_17-09/index.php');
    exit;
}

$student_id = $student['id'];

$grades = getGradesByStudentId($student_id);

// --- Xếp hạng ---
require_once __DIR__ . '/../functions/db_connection.php';
$class_id = $student['class_id'] ?? null;
$major = isset($student['major']) ? trim($student['major']) : null;
$department_id = $student['department_id'] ?? null;

function calc_student_gpa($grades) {
    if (!$grades || !is_array($grades) || count($grades) == 0) return 0;
    $sum = 0; $cnt = 0;
    foreach ($grades as $g) {
        if (isset($g['total_score'])) {
            $sum += floatval($g['total_score']);
            $cnt++;
        }
    }
    return $cnt > 0 ? $sum / $cnt : 0;
}

// Lấy danh sách sinh viên cùng lớp/ngành/khoa và tính GPA
function get_rank($pdo, $student_id, $scope, $value) {
    // $scope: 'class_id' (student.class_id), 'major' (student.major string), 'major_id' (classes.major_id), 'department_id' (student.department_id)
    // Bảo vệ scope để tránh SQL injection qua tên cột
    $allowed = ['class_id', 'major', 'major_id', 'department_id'];
    if (!in_array($scope, $allowed)) return [0,0];

    // Nếu scope là major_id (id ngành), thực hiện JOIN với bảng classes để tìm những sinh viên
    // thuộc các lớp có cùng major_id.
    if ($scope === 'major_id') {
        $sql = "SELECT s.id, s.full_name FROM students s JOIN classes c ON s.class_id = c.id WHERE c.major_id = :val";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['val' => $value]);
    } else {
        // cho các scope khác (class_id, major, department_id) dùng trực tiếp cột của students
        $sql = "SELECT s.id, s.full_name FROM students s WHERE s." . $scope . " = :val";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['val' => $value]);
    }
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $gpas = [];
    foreach ($students as $stu) {
        $grades = getGradesByStudentId($stu['id']);
        $gpas[] = [
            'id' => $stu['id'],
            'gpa' => calc_student_gpa($grades)
        ];
    }
    // Sắp xếp giảm dần theo GPA
    usort($gpas, function($a, $b) { return $b['gpa'] <=> $a['gpa']; });
    // Tìm vị trí của sinh viên hiện tại
    $rank = 0;
    foreach ($gpas as $i => $g) {
        if ($g['id'] == $student_id) {
            $rank = $i + 1;
            break;
        }
    }
    $total = count($gpas);
    return [$rank, $total];
}

$pdo = function_exists('getPDO') ? getPDO() : null;
if (!$pdo) {
    // Tự định nghĩa hàm getPDO nếu chưa có
    require_once __DIR__ . '/../functions/db_connection.php';
    if (function_exists('getPDO')) {
        $pdo = getPDO();
    } else {
        // fallback thủ công, lấy thông tin từ db_connection.php
        global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $port, $DB_CHARSET;
        $portPart = (isset($port) && is_numeric($port) && intval($port) > 0) ? ";port={$port}" : '';
        $dsn = "mysql:host={$DB_HOST}{$portPart};dbname={$DB_NAME};charset={$DB_CHARSET}";
        try {
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die('DB connection error: ' . htmlspecialchars($e->getMessage()));
        }
    }
}
$rank_class = $rank_major = $rank_dept = [0,0];
if ($class_id) $rank_class = get_rank($pdo, $student_id, 'class_id', $class_id);
// Biến chứa id ngành là $major_id — sửa điều kiện để tránh cảnh báo biến không định nghĩa
// Nếu có tên ngành trong trường students.major, tính xếp hạng theo cột `major` (chuỗi)
if ($major) $rank_major = get_rank($pdo, $student_id, 'major', $major);
// Nếu không có trường major ở student nhưng có class_id, thử lấy major_id từ lớp
if (!$major && $class_id) {
    // lấy thông tin lớp để tìm major_id (nếu cần để mở rộng sau này)
    try {
        $stmtC = $pdo->prepare('SELECT major_id FROM classes WHERE id = :id LIMIT 1');
        $stmtC->execute(['id' => $class_id]);
        $cls = $stmtC->fetch(PDO::FETCH_ASSOC);
        if ($cls && !empty($cls['major_id'])) {
            // nếu tìm thấy major_id trên lớp, xếp hạng theo major_id (tìm tất cả sinh viên thuộc các lớp có cùng major_id)
            $rank_major = get_rank($pdo, $student_id, 'major_id', $cls['major_id']);
            // Ngoài ra, nếu cần hiển thị tên ngành ở giao diện, có thể lấy major_name từ majors
        }
    } catch (Exception $e) {
        // Không làm gì, giữ giá trị rank_major mặc định [0,0]
    }
}
if ($department_id) $rank_dept = get_rank($pdo, $student_id, 'department_id', $department_id);
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
                                    <th style="width: 6%">STT</th>
                                    <th style="width: 14%">Mã môn học</th>
                                    <th style="width: 30%">Tên môn học</th>
                                    <th style="width: 10%">Quá trình</th>
                                    <th style="width: 10%">Giữa kỳ</th>
                                    <th style="width: 10%">Cuối kỳ</th>
                                    <th style="width: 10%">Tổng kết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                $count = count($grades);
                                $high = 0;
                                $low = 0;
                                foreach ($grades as $i => $grade):
                                    // Lấy từng đầu điểm nếu có
                                    // Lấy đúng các trường điểm từ bảng grades
                                    $attendance = isset($grade['attendance_score']) ? $grade['attendance_score'] : null;
                                    $midterm = isset($grade['midterm_score']) ? $grade['midterm_score'] : null;
                                    $final = isset($grade['final_score']) ? $grade['final_score'] : null;
                                    $total_grade = isset($grade['total_score']) ? $grade['total_score'] : null;

                                    // Thống kê tổng kết
                                    $grade_value = floatval($total_grade);
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
                                            <?php echo ($attendance !== null) ? htmlspecialchars(number_format($attendance, 1)) : '-'; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo ($midterm !== null) ? htmlspecialchars(number_format($midterm, 1)) : '-'; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo ($final !== null) ? htmlspecialchars(number_format($final, 1)) : '-'; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="grade-value <?php echo $grade_class; ?>">
                                                <?php echo ($total_grade !== null) ? htmlspecialchars(number_format($total_grade, 1)) : '-'; ?>
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
                        <div class="row mt-4">
                            <div class="col-md-4 col-12">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $rank_class[0] > 0 ? $rank_class[0] . ' / ' . $rank_class[1] : '-'; ?></div>
                                    <div class="stat-label">Xếp hạng trong lớp</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $rank_major[0] > 0 ? $rank_major[0] . ' / ' . $rank_major[1] : '-'; ?></div>
                                    <div class="stat-label">Xếp hạng ngành</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $rank_dept[0] > 0 ? $rank_dept[0] . ' / ' . $rank_dept[1] : '-'; ?></div>
                                    <div class="stat-label">Xếp hạng khoa</div>
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