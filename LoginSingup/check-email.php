<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; 
include '../connectDB.php'; 

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';

if (!$email) {
    echo json_encode(["status" => "error", "message" => "Emaili është i nevojshëm."]);
    exit();
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Emaili nuk ekziston."]);
    exit();
}


$code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
$code_hash = hash("sha256", $code);
$expiry = date("Y-m-d H:i:s", time() + 60 * 10); 

$update = $conn->prepare("UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?");
$update->bind_param("sss", $code_hash, $expiry, $email);
$update->execute();

if ($conn->affected_rows > 0) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bardhietna@gmail.com';
        $mail->Password = 'vrhv csvu iqvo gcxn'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('bardhi55etna@gmail.com', 'Opulent Design');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Kodi për Reset të Fjalëkalimit';
        $mail->Body = "Kodi juaj për të rivendosur fjalëkalimin është: <b>$code</b>. Ky kod skadon për 10 minuta.";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Kodi është dërguar me sukses në email."]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Dërgimi i emailit dështoi."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Nuk mund të ruhet kodi."]);
}


