<?php
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$username = $data['username'];
$password = $data['password'];
$email = $data['email'];
$role = $data['role'];

$sql = "UPDATE accounts SET username=?, password=?, email=?, role=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $username, $password, $email, $role, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
?>
