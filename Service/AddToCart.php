<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

include '../connectDB.php';


$data = json_decode(file_get_contents("php://input"), true);


if (!$data || !isset($data['name']) || !isset($data['cost'])) {
    echo json_encode(["error" => "Name and cost are required"]);
    exit;
}

$name = $conn->real_escape_string($data['name']);
$cost = $conn->real_escape_string($data['cost']);
$description = isset($data['description']) ? $conn->real_escape_string($data['description']) : "";

// Ruaj në databazë
$sql = "INSERT INTO addservice (name, cost) VALUES ('$name', '$cost')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Service added to cart", "name" => $name, "cost" => $cost]);
} else {
    echo json_encode(["error" => "Error: " . $conn->error]);
}

$conn->close();
?>
