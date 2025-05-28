<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: GET,POST');
header('Access-Control-Allow-Headers: X-Requested-With');

include 'connectDB.php'; 

$sql = "SELECT * FROM projects"; 


$result = mysqli_query($conn, $sql);

if ($result) {
  
    $projects = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $projects[] = $row;
    }

    echo json_encode($projects);
} else {
    
    echo json_encode(array('error' => 'Query execution failed'));
}


mysqli_close($conn);


?>
