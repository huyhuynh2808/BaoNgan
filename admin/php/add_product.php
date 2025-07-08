<?php
// Kết nối database
require 'db.php';

// Lấy dữ liệu từ FormData
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$description = $_POST['description'] ?? '';
$imageFileName = null;

// Xử lý upload file ảnh
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $targetDir = '../../assets/img/products/';
    // Đảm bảo thư mục tồn tại
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    // Đổi tên file cho tránh trùng (nếu muốn)
    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $targetFile = $targetDir . $fileName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imageFileName = $fileName;
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi upload ảnh']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Chưa chọn ảnh hoặc ảnh lỗi']);
    exit;
}

// Thêm vào database
$stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $price, $description, $imageFileName);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
?>
