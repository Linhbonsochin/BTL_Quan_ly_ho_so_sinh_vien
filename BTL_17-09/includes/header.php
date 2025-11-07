<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once __DIR__ . '/../functions/permissions.php';
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ thống quản lý sinh viên</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/BTL_17-09/css/main.css" rel="stylesheet">
    <?php
    // allow pages to inject extra <head> content by setting $extra_head before including header
    if (!empty($extra_head))
        echo $extra_head;
    ?>
</head>

<body>
    <?php include_once __DIR__ . '/../views/menu.php'; ?>
    <main class="container my-4">
        <?php include_once __DIR__ . '/flash.php'; ?>