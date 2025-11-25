
<?php
/*
========================================
 Chức năng: Các hàm xử lý nội dung tĩnh (thông báo, văn bản, tin tức), CRUD, lấy danh sách, thêm, sửa, xóa
========================================
*/
require_once __DIR__ . '/db_connection.php';

/**
 * Ghi chú: file này giả định tồn tại bảng `static_pages` với cấu trúc tối thiểu:
 * id (INT AUTO_INCREMENT), type (VARCHAR) - one of: 'notification','document','news',
 * title (VARCHAR), content (TEXT), published (TINYINT 0/1), created_at (DATETIME), updated_at (DATETIME)
 * Nếu bạn muốn, tôi có thể thêm file SQL migration.
 */

function getStaticsByType($type)
{
    global $conn;
    try {
        ensureStaticTableExists();
        $sql = "SELECT * FROM static_pages WHERE type = :type ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('static_functions::getStaticsByType error: ' . $e->getMessage());
        return [];
    }
}

function getStaticById($id)
{
    global $conn;
    try {
        ensureStaticTableExists();
        $sql = "SELECT * FROM static_pages WHERE id = :id LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('static_functions::getStaticById error: ' . $e->getMessage());
        return false;
    }
}

function addStatic($type, $title, $content, $published = 0, $image_path = null, $file_path = null)
{
    global $conn;
    try {
        ensureStaticTableExists();
        $sql = "INSERT INTO static_pages (type, title, content, published, image_path, file_path, created_at, updated_at)
                VALUES (:type, :title, :content, :published, :image_path, :file_path, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':type'=>$type, ':title'=>$title, ':content'=>$content, ':published'=>$published, ':image_path'=>$image_path, ':file_path'=>$file_path]);
    } catch (PDOException $e) {
        error_log('static_functions::addStatic error: ' . $e->getMessage());
        return false;
    }
}

function updateStatic($id, $title, $content, $published = 0, $image_path = null, $file_path = null)
{
    global $conn;
    try {
        ensureStaticTableExists();
        // build SQL dynamically so we don't overwrite existing image/file paths unless provided
        $parts = ["title = :title", "content = :content", "published = :published", "updated_at = NOW()"];
        $params = [':title'=>$title, ':content'=>$content, ':published'=>$published, ':id'=>$id];
        if ($image_path !== null) {
            $parts[] = "image_path = :image_path";
            $params[':image_path'] = $image_path;
        }
        if ($file_path !== null) {
            $parts[] = "file_path = :file_path";
            $params[':file_path'] = $file_path;
        }
        $sql = "UPDATE static_pages SET " . implode(', ', $parts) . " WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log('static_functions::updateStatic error: ' . $e->getMessage());
        return false;
    }
}

function deleteStatic($id)
{
    global $conn;
    try {
        ensureStaticTableExists();
        $sql = "DELETE FROM static_pages WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':id'=>$id]);
    } catch (PDOException $e) {
        error_log('static_functions::deleteStatic error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Ensure the static_pages table exists. Attempts to create it if missing.
 */
function ensureStaticTableExists()
{
    global $conn;
    try {
        $sql = "CREATE TABLE IF NOT EXISTS static_pages (
            id INT NOT NULL AUTO_INCREMENT,
            type VARCHAR(32) NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT DEFAULT NULL,
            published TINYINT(1) NOT NULL DEFAULT 0,
            image_path VARCHAR(255) DEFAULT NULL,
            file_path VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY (type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $conn->exec($sql);
        // ensure columns exist for older DBs (MySQL supports ADD COLUMN IF NOT EXISTS on modern versions)
        try {
            $conn->exec("ALTER TABLE static_pages ADD COLUMN IF NOT EXISTS image_path VARCHAR(255) DEFAULT NULL");
            $conn->exec("ALTER TABLE static_pages ADD COLUMN IF NOT EXISTS file_path VARCHAR(255) DEFAULT NULL");
        } catch (PDOException $ex) {
            // ignore if ALTER not supported; changes may need manual migration
        }
    } catch (PDOException $e) {
        // log and continue; caller will handle empty results
        error_log('ensureStaticTableExists error: ' . $e->getMessage());
    }

}

?>
