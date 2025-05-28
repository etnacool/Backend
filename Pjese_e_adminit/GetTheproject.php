<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

include '../connectDB.php';

if (!isset($_GET['id'])) {
   
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'ID nuk u gjet']);
    exit;
}

$id = intval($_GET['id']);

$sql = "
    SELECT 
        p.id, 
        p.title, 
        p.photo_path, 
        p.a_sentence, 
        pd.description
    FROM projects p
    LEFT JOIN projects_details pd ON p.id = pd.id
    WHERE p.id = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Gabim ne përgatitjen e query: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Projekti nuk u gjet']);
    exit;
}

$project = $result->fetch_assoc();

$stmt->close();
$conn->close();

ob_clean();

echo json_encode([
    'success' => true,
    'project' => $project
]);
exit;
