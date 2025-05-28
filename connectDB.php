<?php
$host = "localhost"; 
$db_name = "opulent_designs"; 
$username = "root"; 
$password = ""; 

$conn = new mysqli($host, $username, $password, $db_name);


if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}
?>
