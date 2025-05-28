<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include '../connectDB.php'; 


$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$newPassword = $data['newPassword'] ?? '';

if (empty($email) || empty($newPassword)) {
    echo json_encode(["status" => "error", "message" => "Të dhënat janë bosh"]);
    exit();
}

$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);


$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashedPassword, $email);


if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Fjalëkalimi u ndryshua me sukses"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gabim gjatë ndryshimit"]);
}
?>


