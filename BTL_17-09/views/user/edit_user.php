<?php
require_once __DIR__ . '/../../functions/permissions.php';
require_once __DIR__ . '/../../functions/user_functions.php';
requireAdmin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../user.php?error=Thiếu ID');
    exit();
}

$id = (int) $_GET['id'];
$user = getUserById($id);
if (!$user) {
    header('Location: ../user.php?error=Không tìm thấy tài khoản');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chỉnh sửa tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/home.css">
</head>

<body>
    <!-- menu removed for add/edit pages as requested -->
    <div class="container my-4">
        <h3>Chỉnh sửa tài khoản</h3>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="../../handle/user_process.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" id="username" class="form-control"
                    value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Vai trò</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="student" <?php echo $user['role'] === 'student' ? 'selected' : ''; ?>>Sinh viên
                    </option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Quản trị viên
                    </option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="../user.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>