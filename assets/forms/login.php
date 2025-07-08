<?php
session_start();
require_once '../../admin/php/db.php'; // Đường dẫn tùy vị trí file db.php

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Truy vấn kiểm tra user theo email và password
    $sql = "SELECT * FROM accounts WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($user['role'] === 'admin') {
            // Đăng nhập thành công với quyền admin
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $user['role'];
            header("Location: ../../admin/index.html"); // hoặc index.php nếu có
            exit();
        } else {
            // Không phải admin
            echo "<script>alert('Bạn không có quyền admin!'); window.history.back();</script>";
            exit();
        }
    } else {
        // Đăng nhập thất bại
        echo "<script>alert('Sai email hoặc mật khẩu!'); window.history.back();</script>";
        exit();
    }
}
?>