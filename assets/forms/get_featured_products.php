<?php
require_once '../../admin/php/db.php'; // Đường dẫn tới file kết nối CSDL

$sql = "SELECT id, name, price, image FROM products LIMIT 4"; // Lấy 4 sản phẩm nổi bật
$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
header('Content-Type: application/json');
echo json_encode($products);
?>