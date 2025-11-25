
<?php
/*
========================================
 Chức năng: Các hàm xử lý sinh viên (CRUD, lấy danh sách, tìm kiếm, thêm, sửa, xóa, ảnh)
========================================
*/
require_once __DIR__ . '/db_connection.php';


try {
    $conn->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS avatar_path VARCHAR(255) DEFAULT NULL");
    $conn->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS cccd_front_path VARCHAR(255) DEFAULT NULL");
    $conn->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS cccd_back_path VARCHAR(255) DEFAULT NULL");
} catch (PDOException $e) {

}

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

    // Câu lệnh INSERT with expanded profile fields
    $sql = "INSERT INTO students (
        student_code, full_name, birth_date, gender, address, class_id, department_id, user_id,
        phone, phone_alt, email, student_email,
        nationality, ethnicity, religion, cccd_number, cccd_place, cccd_issue_date,
        education_system, major, course_year, admission_batch,
        is_union_member, is_party_member, join_date,
        grade12_academic, grade12_conduct,
        permanent_province, permanent_ward, permanent_address,
        current_province, current_ward, current_address,
        birth_province, birth_ward,
        bank_name, bank_account,
        is_offcampus, dorm_address, residence_term, residence_year,
        reporter_name, reporter_phone, contact_address,
        avatar_path, cccd_front_path, cccd_back_path
    ) VALUES (
        :student_code, :full_name, :birth_date, :gender, :address, :class_id, :department_id, :user_id,
        :phone, :phone_alt, :email, :student_email,
        :nationality, :ethnicity, :religion, :cccd_number, :cccd_place, :cccd_issue_date,
        :education_system, :major, :course_year, :admission_batch,
        :is_union_member, :is_party_member, :join_date,
        :grade12_academic, :grade12_conduct,
        :permanent_province, :permanent_ward, :permanent_address,
        :current_province, :current_ward, :current_address,
        :birth_province, :birth_ward,
        :bank_name, :bank_account,
        :is_offcampus, :dorm_address, :residence_term, :residence_year,
        :reporter_name, :reporter_phone, :contact_address,
        :avatar_path, :cccd_front_path, :cccd_back_path
    )";

    $stmt = $conn->prepare($sql);

    return $stmt->execute([
        ':student_code' => isset($data['student_code']) ? $data['student_code'] : null,
        ':full_name' => isset($data['full_name']) ? $data['full_name'] : null,
        ':birth_date' => isset($data['birth_date']) && $data['birth_date'] !== '' ? $data['birth_date'] : null,
        ':gender' => isset($data['gender']) && $data['gender'] !== '' ? $data['gender'] : null,
        ':address' => isset($data['address']) ? $data['address'] : null,
        ':class_id' => isset($data['class_id']) && $data['class_id'] !== '' ? $data['class_id'] : null,
        ':department_id' => isset($data['department_id']) && $data['department_id'] !== '' ? $data['department_id'] : null,
        ':user_id' => isset($data['user_id']) && $data['user_id'] !== '' ? $data['user_id'] : null,
        ':phone' => isset($data['phone']) ? $data['phone'] : null,
        ':phone_alt' => isset($data['phone_alt']) ? $data['phone_alt'] : null,
        ':email' => isset($data['email']) ? $data['email'] : null,
        ':student_email' => isset($data['student_email']) ? $data['student_email'] : null,
        ':nationality' => isset($data['nationality']) ? $data['nationality'] : null,
        ':ethnicity' => isset($data['ethnicity']) ? $data['ethnicity'] : null,
        ':religion' => isset($data['religion']) ? $data['religion'] : null,
        ':cccd_number' => isset($data['cccd_number']) ? $data['cccd_number'] : null,
        ':cccd_place' => isset($data['cccd_place']) ? $data['cccd_place'] : null,
        ':cccd_issue_date' => isset($data['cccd_issue_date']) && $data['cccd_issue_date'] !== '' ? $data['cccd_issue_date'] : null,
        ':education_system' => isset($data['education_system']) ? $data['education_system'] : null,
        ':major' => isset($data['major']) ? $data['major'] : null,
        ':course_year' => isset($data['course_year']) ? $data['course_year'] : null,
        ':admission_batch' => isset($data['admission_batch']) ? $data['admission_batch'] : null,
        ':is_union_member' => !empty($data['is_union_member']) ? 1 : 0,
        ':is_party_member' => !empty($data['is_party_member']) ? 1 : 0,
        ':join_date' => isset($data['join_date']) && $data['join_date'] !== '' ? $data['join_date'] : null,
        ':grade12_academic' => isset($data['grade12_academic']) ? $data['grade12_academic'] : null,
        ':grade12_conduct' => isset($data['grade12_conduct']) ? $data['grade12_conduct'] : null,
        ':permanent_province' => isset($data['permanent_province']) ? $data['permanent_province'] : null,
        ':permanent_ward' => isset($data['permanent_ward']) ? $data['permanent_ward'] : null,
        ':permanent_address' => isset($data['permanent_address']) ? $data['permanent_address'] : null,
        ':current_province' => isset($data['current_province']) ? $data['current_province'] : null,
        ':current_ward' => isset($data['current_ward']) ? $data['current_ward'] : null,
        ':current_address' => isset($data['current_address']) ? $data['current_address'] : null,
        ':birth_province' => isset($data['birth_province']) ? $data['birth_province'] : null,
        ':birth_ward' => isset($data['birth_ward']) ? $data['birth_ward'] : null,
        ':bank_name' => isset($data['bank_name']) ? $data['bank_name'] : null,
        ':bank_account' => isset($data['bank_account']) ? $data['bank_account'] : null,
        ':is_offcampus' => !empty($data['is_offcampus']) ? 1 : 0,
        ':dorm_address' => isset($data['dorm_address']) ? $data['dorm_address'] : null,
        ':residence_term' => isset($data['residence_term']) ? $data['residence_term'] : null,
        ':residence_year' => isset($data['residence_year']) ? $data['residence_year'] : null,
        ':reporter_name' => isset($data['reporter_name']) ? $data['reporter_name'] : null,
        ':reporter_phone' => isset($data['reporter_phone']) ? $data['reporter_phone'] : null,
        ':contact_address' => isset($data['contact_address']) ? $data['contact_address'] : null,
        ':avatar_path' => isset($data['avatar_path']) ? $data['avatar_path'] : null,
        ':cccd_front_path' => isset($data['cccd_front_path']) ? $data['cccd_front_path'] : null,
        ':cccd_back_path' => isset($data['cccd_back_path']) ? $data['cccd_back_path'] : null
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
    // Load existing record and merge with provided data so missing keys keep old values
    $existing = getStudentById($id);
    if (!$existing) {
        throw new Exception("Student not found: $id");
    }

    // Build update with optional file columns
        // Build update with optional file columns and expanded profile fields
        $parts = [
            'student_code = :student_code', 'full_name = :full_name', 'birth_date = :birth_date', 'gender = :gender',
            'address = :address', 'class_id = :class_id', 'department_id = :department_id', 'user_id = :user_id',
            'phone = :phone', 'phone_alt = :phone_alt', 'email = :email', 'student_email = :student_email',
            'nationality = :nationality', 'ethnicity = :ethnicity', 'religion = :religion',
            'cccd_number = :cccd_number', 'cccd_place = :cccd_place', 'cccd_issue_date = :cccd_issue_date',
            'education_system = :education_system', 'major = :major', 'course_year = :course_year', 'admission_batch = :admission_batch',
            'is_union_member = :is_union_member', 'is_party_member = :is_party_member', 'join_date = :join_date',
            'grade12_academic = :grade12_academic', 'grade12_conduct = :grade12_conduct',
            'permanent_province = :permanent_province', 'permanent_ward = :permanent_ward', 'permanent_address = :permanent_address',
            'current_province = :current_province', 'current_ward = :current_ward', 'current_address = :current_address',
            'birth_province = :birth_province', 'birth_ward = :birth_ward',
            'bank_name = :bank_name', 'bank_account = :bank_account',
            'is_offcampus = :is_offcampus', 'dorm_address = :dorm_address', 'residence_term = :residence_term', 'residence_year = :residence_year',
            'reporter_name = :reporter_name', 'reporter_phone = :reporter_phone', 'contact_address = :contact_address'
        ];

    $params = [
        ':student_code' => array_key_exists('student_code', $data) && $data['student_code'] !== '' ? $data['student_code'] : $existing['student_code'],
        ':full_name' => array_key_exists('full_name', $data) && $data['full_name'] !== '' ? $data['full_name'] : $existing['full_name'],
        ':birth_date' => array_key_exists('birth_date', $data) ? ($data['birth_date'] ?: null) : ($existing['birth_date'] ?: null),
        ':gender' => array_key_exists('gender', $data) ? ($data['gender'] ?: null) : ($existing['gender'] ?: null),
        ':address' => array_key_exists('address', $data) ? ($data['address'] ?: null) : ($existing['address'] ?: null),
        ':class_id' => array_key_exists('class_id', $data) && $data['class_id'] !== '' ? $data['class_id'] : ($existing['class_id'] ?: null),
        ':department_id' => array_key_exists('department_id', $data) && $data['department_id'] !== '' ? $data['department_id'] : ($existing['department_id'] ?: null),
        ':user_id' => array_key_exists('user_id', $data) && $data['user_id'] !== '' ? $data['user_id'] : ($existing['user_id'] ?: null),
        ':phone' => array_key_exists('phone', $data) ? ($data['phone'] ?: null) : ($existing['phone'] ?: null),
        ':phone_alt' => array_key_exists('phone_alt', $data) ? ($data['phone_alt'] ?: null) : (isset($existing['phone_alt']) ? $existing['phone_alt'] : null),
        ':email' => array_key_exists('email', $data) ? ($data['email'] ?: null) : ($existing['email'] ?: null),
        ':student_email' => array_key_exists('student_email', $data) ? ($data['student_email'] ?: null) : (isset($existing['student_email']) ? $existing['student_email'] : null),
        ':nationality' => array_key_exists('nationality', $data) ? ($data['nationality'] ?: null) : (isset($existing['nationality']) ? $existing['nationality'] : null),
        ':ethnicity' => array_key_exists('ethnicity', $data) ? ($data['ethnicity'] ?: null) : (isset($existing['ethnicity']) ? $existing['ethnicity'] : null),
        ':religion' => array_key_exists('religion', $data) ? ($data['religion'] ?: null) : (isset($existing['religion']) ? $existing['religion'] : null),
        ':cccd_number' => array_key_exists('cccd_number', $data) ? ($data['cccd_number'] ?: null) : (isset($existing['cccd_number']) ? $existing['cccd_number'] : null),
        ':cccd_place' => array_key_exists('cccd_place', $data) ? ($data['cccd_place'] ?: null) : (isset($existing['cccd_place']) ? $existing['cccd_place'] : null),
        ':cccd_issue_date' => array_key_exists('cccd_issue_date', $data) ? ($data['cccd_issue_date'] ?: null) : (isset($existing['cccd_issue_date']) ? $existing['cccd_issue_date'] : null),
        ':education_system' => array_key_exists('education_system', $data) ? ($data['education_system'] ?: null) : (isset($existing['education_system']) ? $existing['education_system'] : null),
        ':major' => array_key_exists('major', $data) ? ($data['major'] ?: null) : (isset($existing['major']) ? $existing['major'] : null),
        ':course_year' => array_key_exists('course_year', $data) ? ($data['course_year'] ?: null) : (isset($existing['course_year']) ? $existing['course_year'] : null),
        ':admission_batch' => array_key_exists('admission_batch', $data) ? ($data['admission_batch'] ?: null) : (isset($existing['admission_batch']) ? $existing['admission_batch'] : null),
        ':is_union_member' => array_key_exists('is_union_member', $data) ? (!empty($data['is_union_member']) ? 1 : 0) : (!empty($existing['is_union_member']) ? 1 : 0),
        ':is_party_member' => array_key_exists('is_party_member', $data) ? (!empty($data['is_party_member']) ? 1 : 0) : (!empty($existing['is_party_member']) ? 1 : 0),
        ':join_date' => array_key_exists('join_date', $data) ? ($data['join_date'] ?: null) : (isset($existing['join_date']) ? $existing['join_date'] : null),
        ':grade12_academic' => array_key_exists('grade12_academic', $data) ? ($data['grade12_academic'] ?: null) : (isset($existing['grade12_academic']) ? $existing['grade12_academic'] : null),
        ':grade12_conduct' => array_key_exists('grade12_conduct', $data) ? ($data['grade12_conduct'] ?: null) : (isset($existing['grade12_conduct']) ? $existing['grade12_conduct'] : null),
        ':permanent_province' => array_key_exists('permanent_province', $data) ? ($data['permanent_province'] ?: null) : (isset($existing['permanent_province']) ? $existing['permanent_province'] : null),
        ':permanent_ward' => array_key_exists('permanent_ward', $data) ? ($data['permanent_ward'] ?: null) : (isset($existing['permanent_ward']) ? $existing['permanent_ward'] : null),
        ':permanent_address' => array_key_exists('permanent_address', $data) ? ($data['permanent_address'] ?: null) : (isset($existing['permanent_address']) ? $existing['permanent_address'] : null),
        ':current_province' => array_key_exists('current_province', $data) ? ($data['current_province'] ?: null) : (isset($existing['current_province']) ? $existing['current_province'] : null),
        ':current_ward' => array_key_exists('current_ward', $data) ? ($data['current_ward'] ?: null) : (isset($existing['current_ward']) ? $existing['current_ward'] : null),
        ':current_address' => array_key_exists('current_address', $data) ? ($data['current_address'] ?: null) : (isset($existing['current_address']) ? $existing['current_address'] : null),
        ':birth_province' => array_key_exists('birth_province', $data) ? ($data['birth_province'] ?: null) : (isset($existing['birth_province']) ? $existing['birth_province'] : null),
        ':birth_ward' => array_key_exists('birth_ward', $data) ? ($data['birth_ward'] ?: null) : (isset($existing['birth_ward']) ? $existing['birth_ward'] : null),
        ':bank_name' => array_key_exists('bank_name', $data) ? ($data['bank_name'] ?: null) : (isset($existing['bank_name']) ? $existing['bank_name'] : null),
        ':bank_account' => array_key_exists('bank_account', $data) ? ($data['bank_account'] ?: null) : (isset($existing['bank_account']) ? $existing['bank_account'] : null),
        ':is_offcampus' => array_key_exists('is_offcampus', $data) ? (!empty($data['is_offcampus']) ? 1 : 0) : (!empty($existing['is_offcampus']) ? 1 : 0),
        ':dorm_address' => array_key_exists('dorm_address', $data) ? ($data['dorm_address'] ?: null) : (isset($existing['dorm_address']) ? $existing['dorm_address'] : null),
        ':residence_term' => array_key_exists('residence_term', $data) ? ($data['residence_term'] ?: null) : (isset($existing['residence_term']) ? $existing['residence_term'] : null),
        ':residence_year' => array_key_exists('residence_year', $data) ? ($data['residence_year'] ?: null) : (isset($existing['residence_year']) ? $existing['residence_year'] : null),
        ':reporter_name' => array_key_exists('reporter_name', $data) ? ($data['reporter_name'] ?: null) : (isset($existing['reporter_name']) ? $existing['reporter_name'] : null),
        ':reporter_phone' => array_key_exists('reporter_phone', $data) ? ($data['reporter_phone'] ?: null) : (isset($existing['reporter_phone']) ? $existing['reporter_phone'] : null),
        ':contact_address' => array_key_exists('contact_address', $data) ? ($data['contact_address'] ?: null) : (isset($existing['contact_address']) ? $existing['contact_address'] : null),
        ':id' => $id
    ];
    if (array_key_exists('avatar_path', $data)) { $parts[] = 'avatar_path = :avatar_path'; $params[':avatar_path'] = $data['avatar_path']; }
    if (array_key_exists('cccd_front_path', $data)) { $parts[] = 'cccd_front_path = :cccd_front_path'; $params[':cccd_front_path'] = $data['cccd_front_path']; }
    if (array_key_exists('cccd_back_path', $data)) { $parts[] = 'cccd_back_path = :cccd_back_path'; $params[':cccd_back_path'] = $data['cccd_back_path']; }

    $sql = "UPDATE students SET " . implode(', ', $parts) . " WHERE id = :id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute($params);
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
 * Xóa nhiều sinh viên an toàn: bỏ qua các sinh viên có bản ghi điểm liên quan
 * @param array $ids Mảng id (có thể là string/number)
 * @return int Số bản ghi đã xóa
 */
function deleteStudents(array $ids)
{
    global $conn;
    if (empty($ids)) return 0;

    // Lọc id số nguyên dương
    $ids = array_values(array_filter($ids, function($v){ return is_numeric($v) && intval($v) > 0; }));
    if (empty($ids)) return 0;

    try {
        // Tìm các student_id có điểm liên quan
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT DISTINCT student_id FROM grades WHERE student_id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($ids);
        $withGrades = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        // Xác định các id được phép xóa (không có điểm liên quan)
        $withGrades = array_map('intval', $withGrades ?: []);
        $deletable = array_values(array_diff($ids, $withGrades));

        if (empty($deletable)) {
            return 0;
        }

        // Xóa các sinh viên có thể xóa
        $place2 = implode(',', array_fill(0, count($deletable), '?'));
        $sql2 = "DELETE FROM students WHERE id IN ($place2)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute($deletable);
        return $stmt2->rowCount();
    } catch (PDOException $e) {
        error_log('Database error in deleteStudents: ' . $e->getMessage());
        return 0;
    }
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