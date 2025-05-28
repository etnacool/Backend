<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include '../connectDB.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "ID është e detyrueshme"]);
    exit;
}

$id = intval($_GET['id']); 

$stmt = $conn->prepare("DELETE FROM service1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();


if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Shërbimi u fshi me sukses"]);
} else {
    echo json_encode(["success" => false, "message" => "Shërbimi nuk u gjet ose nuk u fshi"]);
}

$stmt->close();
$conn->close();
?>




