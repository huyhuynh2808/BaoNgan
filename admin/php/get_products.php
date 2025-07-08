<?php
require_once 'db.php';

$sql = "SELECT * FROM products"; // Đổi tên bảng nếu khác
$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
echo json_encode($products);
?>