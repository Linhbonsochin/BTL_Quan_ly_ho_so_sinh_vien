
<?php
/*
========================================
 Chức năng: Xử lý OCR (nhận diện ký tự từ ảnh), upload ảnh, xác thực, trả về kết quả JSON
========================================
*/
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/static_functions.php';
require_once __DIR__ . '/../functions/db_connection.php';
$current = getCurrentUser();
if (!$current) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'error'=>'Not authenticated']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok'=>false,'error'=>'No file uploaded or upload error']);
    exit;
}

$tmp = $_FILES['image']['tmp_name'];
$origName = basename($_FILES['image']['name']);
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
$allowed = ['jpg','jpeg','png','tif','tiff','bmp','gif','webp'];
if (!in_array($ext, $allowed)) {
    echo json_encode(['ok'=>false,'error'=>'Unsupported image format']);
    exit;
}

$workDir = __DIR__ . '/../image/ocr_tmp';
if (!is_dir($workDir)) @mkdir($workDir, 0755, true);
$uniq = time() . '_' . bin2hex(random_bytes(4));
$target = $workDir . '/' . $uniq . '.' . $ext;
if (!move_uploaded_file($tmp, $target)) {
    echo json_encode(['ok'=>false,'error'=>'Failed to store uploaded file']);
    exit;
}

$cmdCheck = null;
if (stripos(PHP_OS, 'WIN') !== false) {
    // windows
    $cmdCheck = 'where tesseract 2>&1';
} else {
    $cmdCheck = 'which tesseract 2>/dev/null';
}
$which = null;
@exec($cmdCheck, $out, $rc);
if (!empty($out) && is_array($out)) {
    $which = trim($out[0]);
}

if (!$which) {
    echo json_encode(['ok'=>false,'error'=>'Tesseract not found on server. Install tesseract OCR to enable automatic extraction.','tmp_path'=>$target]);
    exit;
}

$cmd = escapeshellarg($which) . ' ' . escapeshellarg($target) . ' stdout --psm 6 2>&1';
$output = '';
@exec($cmd, $lines, $rc2);
if (is_array($lines)) $output = implode("\n", $lines);

echo json_encode(['ok'=>true,'text'=>$output]);

exit;
?>