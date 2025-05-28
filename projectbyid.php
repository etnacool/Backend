<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

include 'connectDB.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);  
    error_log("ID received: " . $id);  

    $sql = "SELECT * FROM projects_details WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $project = mysqli_fetch_assoc($result);
        echo json_encode($project);
    } else {
        echo json_encode(array("error" => "Project not found"));
    }
} else {
    echo json_encode(array("error" => "No ID provided"));
}