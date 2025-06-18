<?php
// Cho phép CORS và POST
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Đường dẫn chính xác đến thư viện PHPMailer
require __DIR__ . '/../vendor/PHPMailer/Exception.php';
require __DIR__ . '/../vendor/PHPMailer/PHPMailer.php';
require __DIR__ . '/../vendor/PHPMailer/SMTP.php';

// Cấu hình email
$receiving_email_address = 'huyhuynh28082002@gmail.com'; // Email nhận thông báo

// Kiểm tra và xử lý dữ liệu form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Kiểm tra dữ liệu
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin']);
        exit;
    }

    // Kiểm tra email hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Email không hợp lệ']);
        exit;
    }

    try {
        $mail = new PHPMailer(true);

        // Cấu hình server
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'huyhuynh28082002@gmail.com'; // Email Gmail của bạn
        $mail->Password = 'tcor oetu thjk dari'; // Mật khẩu ứng dụng Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Bật debug mode trong môi trường development
        // $mail->SMTPDebug = 2;

        // Người gửi và người nhận
        $mail->setFrom($email, $name);
        $mail->addAddress($receiving_email_address);

        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                    <h2 style='color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 10px;'>Thông tin liên hệ mới</h2>
                    <p><strong>Tên:</strong> " . htmlspecialchars($name) . "</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                    <p><strong>Chủ đề:</strong> " . htmlspecialchars($subject) . "</p>
                    <p><strong>Nội dung:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
                </div>
            </body>
            </html>";

        // Thêm plain text version
        $mail->AltBody = "Thông tin liên hệ mới\n\n" .
            "Tên: $name\n" .
            "Email: $email\n" .
            "Chủ đề: $subject\n" .
            "Nội dung:\n$message";

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Tin nhắn của bạn đã được gửi thành công!']);
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.',
            'debug' => $e->getMessage() // Chỉ hiển thị trong môi trường development
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ']);
}
?>
