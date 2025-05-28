<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../connectDB.php';

$inputJSON = file_get_contents("php://input");
error_log("POST data: " . $inputJSON);

$data = json_decode($inputJSON, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Të dhënat janë në një format të gabuar"]);
    exit();
}

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

error_log("Username: $username");

if ($username === '' || $password === '') {
    echo json_encode(["status" => "error", "message" => "Të dhënat nuk mund të jenë bosh"]);
    exit();
}


$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
$stmt->bind_param("ss", $username, $username);

if ($stmt->execute()) {
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            echo json_encode([
                "status" => "success",
                "role" => $user['role'],
                "username" => $user['username']
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Fjalëkalimi i pasaktë"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Përdoruesi nuk ekziston"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Gabim gjatë ekzekutimit të kërkesës"]);
}

$stmt->close();
$conn->close();



