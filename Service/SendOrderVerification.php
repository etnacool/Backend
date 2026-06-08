<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include '../connectDB.php';

$data = json_decode(file_get_contents("php://input"), true);

$name     = $data['name'] ?? '';
$email    = $data['email'] ?? '';
$phone    = $data['phone'] ?? '';
$services = $data['services'] ?? [];
$total    = $data['total'] ?? 0;

if (!$name || !$email || !$phone || empty($services)) {
    echo json_encode(["status" => "error", "message" => "Të dhënat janë të pakompletuara."]);
    exit();
}


$serviceList = "";
foreach ($services as $s) {
    $serviceList .= "<li>{$s['name']} - {$s['cost']} €</li>";
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'bardhietna@gmail.com';
    $mail->Password = 'vrhv csvu iqvo gcxn';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('bardhietna@gmail.com', 'Opulent Design');
    $mail->addAddress($email, $name);
    $mail->isHTML(true);
    $mail->Subject = 'Konfirmim i Porosisë - Opulent Design';
    $mail->Body = "
        <h2>Faleminderit, $name!</h2>
        <p>Porosia juaj u konfirmua me sukses.</p>
        <h3>Shërbimet e zgjedhura:</h3>
        <ul>$serviceList</ul>
        <h3>Totali: <strong>$total €</strong></h3>
        <p>Do t'ju kontaktojmë së shpejti në numrin: <strong>$phone</strong></p>
        <br>
        <p>Me respekt,<br><strong>Opulent Design</strong></p>
    ";

    $mail->send();
    echo json_encode(["status" => "success", "message" => "Email konfirmimi u dërgua."]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Dërgimi dështoi: " . $mail->ErrorInfo]);
}
?>