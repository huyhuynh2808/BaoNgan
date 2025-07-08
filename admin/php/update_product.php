<?php
require 'db.php';

$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];
$description = $_POST['description'];

// Lấy tên file ảnh cũ từ DB
$stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($oldImage);
$stmt->fetch();
$stmt->close();

$imageFileName = $oldImage;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $imageFileName = time() . '_' . basename($_FILES['image']['name']);
    $targetPath = '../../assets/img/products/' . $imageFileName;
    move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
}

$stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, image=? WHERE id=?");
$stmt->bind_param("ssssi", $name, $price, $description, $imageFileName, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
?>
