<?php
session_start();
header("Content-Type: application/json");

if (isset($_SESSION['user']) && isset($_SESSION['role'])) {
    
    echo json_encode([
        "status" => "success", 
        "user" => $_SESSION['user'], 
        "role" => $_SESSION['role']
    ]);
} else {
    
    echo json_encode([
        "status" => "error", 
        "message" => "Përdoruesi nuk është loguar"
    ]);
}
?>
