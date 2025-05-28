<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include '../connectDB.php';

$sql = "SELECT * FROM addservice";
$result = $conn->query($sql);

$cartItems = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
    echo json_encode($cartItems);
} else {
    echo json_encode(["error" => "Failed to fetch cart items"]);
}

$conn->close();
?>

