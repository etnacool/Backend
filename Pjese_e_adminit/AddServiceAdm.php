<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include '../connectDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$data = json_decode(file_get_contents("php://input"), true);

if (
    isset($data['name']) &&
    isset($data['description']) &&
    isset($data['delivery_time']) &&
    isset($data['cost']) &&
    is_numeric($data['cost'])
) {
    $name = trim($data['name']);
    $description = trim($data['description']);
    $delivery_time = trim($data['delivery_time']);
    $cost = floatval($data['cost']);

    try {
        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO service1 (name, description, delivery_time, cost) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $name, $description, $delivery_time, $cost);
        $stmt->execute();
        $last_id = $conn->insert_id;
        $stmt->close();


        $conn->commit();

        echo json_encode([
            "success" => true,
            "message" => "Shërbimi u shtua me sukses",
            "id" => $last_id
        ]);
    } catch (Exception $e) {
        
        $conn->rollback();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Gabim: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Të dhënat janë të paplota ose të pasakta"]);
}

$conn->close();
?>
