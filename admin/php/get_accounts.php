<?php
require_once 'db.php'; // Đảm bảo file này kết nối đúng CSDL

$sql = "SELECT * FROM accounts"; // Đúng tên bảng
$result = $conn->query($sql);

if (!$result) {
    // In lỗi SQL ra màn hình để debug
    die("Lỗi truy vấn: " . $conn->error);
}

$accounts = [];
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}
header('Content-Type: application/json');
echo json_encode($accounts);
?>