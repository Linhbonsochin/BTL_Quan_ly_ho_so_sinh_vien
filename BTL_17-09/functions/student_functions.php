<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy danh sách sinh viên từ database
 * @param string|null $search Từ khóa tìm kiếm (tìm theo tên, mã SV, tên lớp, tên khoa)
 * @return array Mảng chứa thông tin của các sinh viên
 */
function getStudents($search = null)
{
    global $conn;

    // Câu truy vấn cơ bản lấy thông tin sinh viên kèm tên lớp và khoa
    $sql = "SELECT s.*, c.class_name, c.class_code, d.department_name, d.department_code, s.phone, s.email FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        LEFT JOIN departments d ON s.department_id = d.id";
    $params = [];

    // Nếu có từ khóa tìm kiếm thì thêm điều kiện WHERE
    if ($search !== null && trim($search) !== '') {
        $search = trim($search);
        $sql .= " WHERE (s.full_name LIKE :kw1 OR s.student_code LIKE :kw2 
                 OR c.class_name LIKE :kw3 OR d.department_name LIKE :kw4)";
        $searchTerm = "%" . $search . "%";
        $params[':kw1'] = $searchTerm; // Tìm theo tên sinh viên
        $params[':kw2'] = $searchTerm; // Tìm theo mã sinh viên
        $params[':kw3'] = $searchTerm; // Tìm theo tên lớp
        $params[':kw4'] = $searchTerm; // Tìm theo tên khoa
    }

    // Sắp xếp kết quả theo mã sinh viên
    $sql .= " ORDER BY s.student_code ASC";

    try {
        // Thực hiện truy vấn an toàn với PDO
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Ghi log lỗi và trả về mảng rỗng
        error_log("Database error in getStudents: " . $e->getMessage());
        return [];
    }
}

/**
 * Lấy danh sách users có role = 'student' để admin có thể liên kết tài khoản
 */
function getStudentUsers()
{
    global $conn;
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'student' ORDER BY username");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudentById($id)
{
    global $conn;
    $sql = "SELECT * FROM students WHERE id = :id LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Lấy sinh viên theo user_id
 */
function getStudentByUserId($userId)
{
    global $conn;
    $sql = "SELECT s.*, c.class_name, d.department_name FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN departments d ON s.department_id = d.id
            WHERE s.user_id = :uid LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':uid' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Thêm sinh viên mới vào database
 * @param array $data Mảng chứa thông tin sinh viên cần thêm
 * @return boolean true nếu thêm thành công, false nếu thất bại
 */
function addStudent($data)
{
    global $conn;

    // Câu lệnh INSERT với prepared statement
    $sql = "INSERT INTO students (student_code, full_name, birth_date, gender, address, class_id, department_id, user_id, phone, email)
        VALUES (:student_code, :full_name, :birth_date, :gender, :address, :class_id, :department_id, :user_id, :phone, :email)";

    $stmt = $conn->prepare($sql);

    // Thực thi câu lệnh với các tham số
    return $stmt->execute([
        ':student_code' => $data['student_code'],    // Mã sinh viên
        ':full_name' => $data['full_name'],         // Họ tên sinh viên
        ':birth_date' => $data['birth_date'] ?: null, // Ngày sinh (có thể null)
        ':gender' => $data['gender'] ?: null,        // Giới tính (có thể null)
        ':address' => $data['address'] ?: null,      // Địa chỉ (có thể null)
        ':class_id' => $data['class_id'] ?: null,    // ID lớp (có thể null)
        ':department_id' => $data['department_id'] ?: null, // ID khoa (có thể null)
        ':user_id' => isset($data['user_id']) && $data['user_id'] !== '' ? $data['user_id'] : null, // ID user liên kết (có thể null)
        ':phone' => isset($data['phone']) ? $data['phone'] : null,
        ':email' => isset($data['email']) ? $data['email'] : null
    ]);
}

/**
 * Cập nhật thông tin sinh viên
 * @param int $id ID của sinh viên cần cập nhật
 * @param array $data Mảng chứa thông tin mới của sinh viên
 * @return boolean true nếu cập nhật thành công, false nếu thất bại
 */
function updateStudent($id, $data)
{
    global $conn;

    // Câu lệnh UPDATE với prepared statement
    $sql = "UPDATE students SET student_code = :student_code, full_name = :full_name, birth_date = :birth_date,
        gender = :gender, address = :address, class_id = :class_id, department_id = :department_id, user_id = :user_id, phone = :phone, email = :email
        WHERE id = :id";

    $stmt = $conn->prepare($sql);

    // Thực thi câu lệnh với các tham số
    return $stmt->execute([
        ':student_code' => $data['student_code'],
        ':full_name' => $data['full_name'],
        ':birth_date' => $data['birth_date'] ?: null,
        ':gender' => $data['gender'] ?: null,
        ':address' => $data['address'] ?: null,
        ':class_id' => $data['class_id'] ?: null,
        ':department_id' => $data['department_id'] ?: null,
        ':user_id' => isset($data['user_id']) && $data['user_id'] !== '' ? $data['user_id'] : null,
        ':phone' => isset($data['phone']) ? $data['phone'] : null,
        ':email' => isset($data['email']) ? $data['email'] : null,
        ':id' => $id
    ]);
}

/**
 * Cập nhật thông tin liên hệ cho sinh viên (dùng khi sinh viên tự cập nhật)
 */
function updateStudentContact($id, $phone, $email, $address)
{
    global $conn;
    $sql = "UPDATE students SET phone = :phone, email = :email, address = :address WHERE id = :id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([':phone' => $phone ?: null, ':email' => $email ?: null, ':address' => $address ?: null, ':id' => $id]);
}

/**
 * Xóa sinh viên khỏi database
 * @param int $id ID của sinh viên cần xóa
 * @return boolean true nếu xóa thành công, false nếu thất bại
 */
function deleteStudent($id)
{
    global $conn;
    $sql = "DELETE FROM students WHERE id = :id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([':id' => $id]);
}

/**
 * Lấy danh sách các lớp học từ database
 * @return array Mảng chứa thông tin của các lớp
 */
function getClasses()
{
    global $conn;
    $stmt = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy danh sách các khoa từ database
 * @return array Mảng chứa thông tin của các khoa
 */
function getDepartments()
{
    global $conn;
    $stmt = $conn->query("SELECT id, department_name FROM departments ORDER BY department_name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy sinh viên theo mã (student_code)
 */
function getStudentByCode($studentCode)
{
    global $conn;
    $sql = "SELECT s.*, c.class_name, d.department_name FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN departments d ON s.department_id = d.id
            WHERE s.student_code = :code LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':code' => $studentCode]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Lấy điểm theo student_id
 */
function getGradesByStudentId($studentId)
{
    global $conn;
    $sql = "SELECT g.*, s.subject_code, s.subject_name 
            FROM grades g
            LEFT JOIN subjects s ON g.subject_id = s.id 
            WHERE g.student_id = :id 
            ORDER BY s.subject_name";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $studentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>