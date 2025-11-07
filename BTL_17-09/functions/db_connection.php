<?php
// Cấu hình kết nối database
$DB_HOST = 'localhost';           // Địa chỉ host database
$DB_NAME = 'quanlyhososinhvien'; // Tên database
$DB_USER = 'root';               // Tên người dùng database
$DB_PASS = 'baolinh123';         // Mật khẩu database
$port = 3366;                    // Cổng kết nối database (thay đổi nếu cần)
$DB_CHARSET = 'utf8mb4';         // Bảng mã ký tự

// Tạo kết nối PDO (được sử dụng bởi các hàm dựa trên PDO)
try {
    // Nếu cổng không phải mặc định, thêm vào chuỗi kết nối DSN
    $portPart = isset($port) && is_numeric($port) && intval($port) > 0 ? ";port={$port}" : '';
    $dsn = "mysql:host={$DB_HOST}{$portPart};dbname={$DB_NAME};charset={$DB_CHARSET}";

    // Khởi tạo kết nối PDO với các tùy chọn
    $conn = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,           // Bật chế độ báo lỗi
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Kiểu trả về mặc định là mảng kết hợp
        PDO::ATTR_EMULATE_PREPARES => false,                   // Tắt chế độ mô phỏng prepared statements
    ]);
} catch (PDOException $e) {
    // Trong môi trường production nên ghi log thay vì hiển thị
    echo "Lỗi kết nối database: " . htmlspecialchars($e->getMessage());
    exit;
}

/**
 * Hàm tương thích cho các file Demo sử dụng mysqli
 * @return mysqli Đối tượng kết nối mysqli
 */
function getDbConnection()
{
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $port, $DB_CHARSET;

    // Kiểm tra và sử dụng cổng tùy chỉnh nếu có
    $portArg = (isset($port) && is_numeric($port) && intval($port) > 0) ? intval($port) : null;
    if ($portArg) {
        $mysqli = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $portArg);
    } else {
        $mysqli = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    }

    // Kiểm tra kết nối
    if (!$mysqli) {
        // Trong môi trường production nên ghi log thay vì die
        die('Lỗi kết nối MySQLi: ' . mysqli_connect_error());
    }

    // Thiết lập bảng mã ký tự (sử dụng cùng bảng mã với PDO)
    mysqli_set_charset($mysqli, $DB_CHARSET);
    return $mysqli;
}

?>