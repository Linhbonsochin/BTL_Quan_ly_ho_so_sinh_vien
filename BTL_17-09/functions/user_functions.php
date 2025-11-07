<?php
require_once __DIR__ . '/db_connection.php';

function getAllUsers()
{
    global $conn;
    $stmt = $conn->query("SELECT id, username, role FROM users ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createUser($username, $password, $role)
{
    global $conn;

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        return [false, "Tên đăng nhập đã tồn tại"];
    }

    try {
        // Create new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$username, $hashedPassword, $role]);
        return [true, "Tạo tài khoản thành công"];
    } catch (PDOException $e) {
        return [false, "Lỗi khi tạo tài khoản: " . $e->getMessage()];
    }
}

function updateUser($id, $username, $password = null, $role)
{
    global $conn;

    // Check if username exists for other users
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $id]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        return [false, "Tên đăng nhập đã tồn tại"];
    }

    try {
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $hashedPassword, $role, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $role, $id]);
        }
        return [true, "Cập nhật tài khoản thành công"];
    } catch (PDOException $e) {
        return [false, "Lỗi khi cập nhật tài khoản: " . $e->getMessage()];
    }
}

function deleteUser($id)
{
    global $conn;
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return [true, "Xóa tài khoản thành công"];
    } catch (PDOException $e) {
        return [false, "Lỗi khi xóa tài khoản: " . $e->getMessage()];
    }
}