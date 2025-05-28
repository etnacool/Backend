<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include '../connectDB.php';  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    $newpassword = $data['newpassword'] ?? '';
    $email = $data['email'] ?? '';



    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Të dhënat nuk mund të jenë bosh"]);
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result && $result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Përdoruesi ekziston"]);
        exit();
    }

    
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    
    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashedPassword, $email);
    

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Përdoruesi u regjistrua me sukses"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gabim gjatë ruajtjes në bazën e të dhënave"]);
    }

    $stmt->close(); 
    $conn->close(); 
} else {
    echo json_encode(["status" => "error", "message" => "Metoda kërkesës duhet të jetë POST"]);
}
?>


