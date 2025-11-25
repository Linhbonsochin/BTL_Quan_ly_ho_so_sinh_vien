
<?php
/*
========================================
 Chức năng: Xử lý các hành động quản trị nội dung tĩnh (thông báo, văn bản, tin tức), CRUD, upload file, xóa nhiều
========================================
*/
require_once __DIR__ . '/../functions/permissions.php';
require_once __DIR__ . '/../functions/static_functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $type = $_POST['type'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $published = isset($_POST['published']) ? 1 : 0;
        $imagePath = null;
        $filePath = null;
        $imgDir = __DIR__ . '/../image/static';
        $fileDir = __DIR__ . '/../image/static_files';
        if (!is_dir($imgDir)) @mkdir($imgDir, 0755, true);
        if (!is_dir($fileDir)) @mkdir($fileDir, 0755, true);
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fn = basename($_FILES['image']['name']);
            $ext = pathinfo($fn, PATHINFO_EXTENSION);
            $new = time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
            $dest = $imgDir . '/' . $new;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imagePath = 'image/static/' . $new;
            }
        }
        if (!empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $fn = basename($_FILES['attachment']['name']);
            $ext = pathinfo($fn, PATHINFO_EXTENSION);
            $new = time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
            $dest = $fileDir . '/' . $new;
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $dest)) {
                $filePath = 'image/static_files/' . $new;
            }
        }
        if ($type && $title) {
            $ok = addStatic($type, $title, $content, $published, $imagePath, $filePath);
            $_SESSION['success'] = $ok ? 'Thêm trang tĩnh thành công.' : 'Lỗi khi thêm.';
        } else {
            $_SESSION['error'] = 'Thiếu dữ liệu bắt buộc.';
        }
        $map = [
            'notification' => 'static_notifications.php',
            'document' => 'static_documents.php',
            'news' => 'static_news.php'
        ];
        $target = $map[$type] ?? 'static_news.php';
        header('Location: /BTL_17-09/views/' . $target);
        exit;
    }

    if ($action === 'bulk_delete') {
        $ids = $_POST['ids'] ?? [];
        $type = $_POST['type'] ?? 'news';
        $deleted = 0;
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $iid) {
                $iid = intval($iid);
                if ($iid > 0) {
                    if (deleteStatic($iid)) $deleted++;
                }
            }
        }
        $_SESSION['success'] = $deleted > 0 ? ("Đã xóa {$deleted} mục.") : 'Không có mục nào bị xóa.';
        $map = [
            'notification' => 'static_notifications.php',
            'document' => 'static_documents.php',
            'news' => 'static_news.php'
        ];
        $target = $map[$type] ?? 'static_news.php';
        header('Location: /BTL_17-09/views/' . $target);
        exit;
    }

    if ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $published = isset($_POST['published']) ? 1 : 0;
        $row = getStaticById($id);
        $imagePath = null;
        $filePath = null;
        $imgDir = __DIR__ . '/../image/static';
        $fileDir = __DIR__ . '/../image/static_files';
        if (!is_dir($imgDir)) @mkdir($imgDir, 0755, true);
        if (!is_dir($fileDir)) @mkdir($fileDir, 0755, true);
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fn = basename($_FILES['image']['name']);
            $ext = pathinfo($fn, PATHINFO_EXTENSION);
            $new = time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
            $dest = $imgDir . '/' . $new;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imagePath = 'image/static/' . $new;
            }
        }
        if (!empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $fn = basename($_FILES['attachment']['name']);
            $ext = pathinfo($fn, PATHINFO_EXTENSION);
            $new = time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
            $dest = $fileDir . '/' . $new;
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $dest)) {
                $filePath = 'image/static_files/' . $new;
            }
        }
        if ($row && $title) {
            $ok = updateStatic($id, $title, $content, $published, $imagePath, $filePath);
            $_SESSION['success'] = $ok ? 'Cập nhật thành công.' : 'Lỗi khi cập nhật.';
            $type = $row['type'];
        } else {
            $_SESSION['error'] = 'Bản ghi không tồn tại hoặc thiếu tiêu đề.';
            $type = $row['type'] ?? 'news';
        }
        $map = [
            'notification' => 'static_notifications.php',
            'document' => 'static_documents.php',
            'news' => 'static_news.php'
        ];
        $target = $map[$type] ?? 'static_news.php';
        header('Location: /BTL_17-09/views/' . $target);
        exit;
    }

}

// GET delete via query
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $row = getStaticById($id);
    if ($row) {
        deleteStatic($id);
        $_SESSION['success'] = 'Xóa thành công.';
    } else {
        $_SESSION['error'] = 'Bản ghi không tồn tại.';
    }
    $type = $row['type'] ?? 'news';
    $map = [
        'notification' => 'static_notifications.php',
        'document' => 'static_documents.php',
        'news' => 'static_news.php'
    ];
    $target = $map[$type] ?? 'static_news.php';
    header('Location: /BTL_17-09/views/' . $target);
    exit;
}

header('Location: /BTL_17-09/views/static_news.php');
exit;

?>
