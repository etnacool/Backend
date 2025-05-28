<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "../connectDB.php";


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$data = json_decode(file_get_contents("php://input"), true);

if (
    isset($data['title']) &&
    isset($data['a_sentence']) &&
    isset($data['photo_path']) &&
    isset($data['description'])
) {
    $title = $data['title'];
    $sentence = $data['a_sentence'];
    $photoPath = $data['photo_path'];
    $description = $data['description'];

    try {
     
        $conn->begin_transaction();

       
        $stmt1 = $conn->prepare("INSERT INTO projects (title, a_sentence, photo_path) VALUES (?, ?, ?)");
        $stmt1->bind_param("sss", $title, $sentence, $photoPath);
        $stmt1->execute();
        $last_id = $conn->insert_id;
        $stmt1->close();

        $stmt2 = $conn->prepare("INSERT INTO projects_details (id, title, photo_path, description) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $last_id, $title, $photoPath, $description);
        $stmt2->execute();
        $stmt2->close();

      
        $conn->commit();

        echo json_encode(["success" => true, "message" => "Projekti u shtua me sukses"]);
    } catch (Exception $e) {
        $conn->rollback(); 
        echo json_encode(["error" => "Gabim: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Të dhëna të paplota"]);
}

$conn->close();
?>
