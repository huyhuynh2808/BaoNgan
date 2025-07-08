<?php
require_once '../../admin/php/db.php'; // Đường dẫn tới file kết nối CSDL

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM products WHERE id = $id LIMIT 1";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($product);
?>