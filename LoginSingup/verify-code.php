<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require '../connectDB.php';  

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';  
$code = $data['code'] ?? '';   

error_log("Email: $email, Kodi nga përdoruesi: $code");


if (empty($email) || empty($code)) {
    echo json_encode(["status" => "error", "message" => "Email ose kod i munguar."]);
    exit();
}

$stmt = $conn->prepare("SELECT reset_token_hash FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Emaili nuk ekziston."]);
    exit();
}

$row = $result->fetch_assoc();
$token_hash = $row['reset_token_hash'] ?? '';  

error_log("Token Hash nga DB: $token_hash");


if (hash('sha256', $code) === $token_hash) {
    echo json_encode(["status" => "success", "message" => "Kodi është i saktë!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Kodi është i pasaktë!"]);
}
?>

