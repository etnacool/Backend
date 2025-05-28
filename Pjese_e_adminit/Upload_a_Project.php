<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../connectDB.php';


$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT') {
 
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID nuk është dhënë']);
        exit;
    }

    $id = intval($data['id']);
    $title = $data['title'] ?? '';
    $a_sentence = $data['a_sentence'] ?? '';
    $description = $data['description'] ?? '';
    $photo_path = $data['photo_path'] ?? '';

    $sql = "UPDATE projects p
            LEFT JOIN projects_details pd ON p.id = pd.id
            SET p.title = ?, p.a_sentence = ?, pd.description = ?, p.photo_path = ?
            WHERE p.id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Gabim në përgatitjen e query-t: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssssi", $title, $a_sentence, $description, $photo_path, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Projekti u përditësua me sukses']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gabim gjatë përditësimit: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}


?>

