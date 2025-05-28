<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: GET,POST');
header('Access-Control-Allow-Headers: X-Requested-With');

include '../connectDB.php'; 

$sql = "SELECT * FROM service1"; 

$result = mysqli_query($conn, $sql);

if ($result) {
  
    $services = array();  

    while ($row = mysqli_fetch_assoc($result)) {
        $services[] = $row;  
    }

    echo json_encode($services);  
} else {
    echo json_encode(array('error' => 'Query execution failed'));
}

mysqli_close($conn);
?>

