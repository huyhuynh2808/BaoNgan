<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cho phép CORS và POST
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Kiểm tra PHP extensions cần thiết
$required_extensions = ['openssl', 'mbstring', 'curl'];
$missing_extensions = [];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server thiếu các extension cần thiết: ' . implode(', ', $missing_extensions)
    ]);
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    // Kiểm tra và load PHPMailer files
    $phpmailer_path = __DIR__ . '/../vendor/PHPMailer/';
    
    if (!file_exists($phpmailer_path . 'Exception.php')) {
        throw new Exception('PHPMailer Exception.php not found');
    }
    if (!file_exists($phpmailer_path . 'PHPMailer.php')) {
        throw new Exception('PHPMailer PHPMailer.php not found');
    }
    if (!file_exists($phpmailer_path . 'SMTP.php')) {
        throw new Exception('PHPMailer SMTP.php not found');
    }
    
    require $phpmailer_path . 'Exception.php';
    require $phpmailer_path . 'PHPMailer.php';
    require $phpmailer_path . 'SMTP.php';
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Không thể load PHPMailer: ' . $e->getMessage()
    ]);
    exit;
}

// Cấu hình email
$receiving_email_address = 'congty.baongan2025@gmail.com';

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
        $mail->Username = 'congty.baongan2025@gmail.com';
        $mail->Password = 'high gcuf qcrg ynvf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Tăng timeout để tránh lỗi kết nối
        $mail->Timeout = 30;
        $mail->SMTPKeepAlive = true;

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
        
        // Kiểm tra loại lỗi để đưa ra thông báo phù hợp
        $error_message = 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.';
        
        if (strpos($e->getMessage(), 'SMTP connect() failed') !== false) {
            $error_message = 'Không thể kết nối đến máy chủ email. Vui lòng liên hệ admin.';
        } elseif (strpos($e->getMessage(), 'authentication') !== false) {
            $error_message = 'Lỗi xác thực email. Vui lòng liên hệ admin.';
        } elseif (strpos($e->getMessage(), 'timeout') !== false) {
            $error_message = 'Kết nối bị timeout. Vui lòng thử lại sau.';
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => $error_message,
            'debug' => $e->getMessage() // Chỉ hiển thị trong môi trường development
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ']);
}
?>
