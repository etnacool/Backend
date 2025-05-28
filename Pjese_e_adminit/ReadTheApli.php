<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include '../connectDB.php';

$conn->set_charset("utf8");

$sql = "SELECT 
            id, 
            name, 
            surname, 
            bday, 
            nr_tel, 
            adresa, 
            email, 
            education, 
            university, 
            title, 
            y_of_Graduation, 
            position, 
            the_select_time, 
            photo 
        FROM aplikimi";

$result = $conn->query($sql);
$applicants = [];

if ($result === false) {
    echo json_encode(["error" => "Gabim në ekzekutimin e query-t: " . $conn->error]);
    exit;
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['photo'])) {
            
            $row['photo'] = 'data:image/jpeg;base64,' . base64_encode($row['photo']);
        } else {
            $row['photo'] = null;
        }
        $applicants[] = $row;
    }
}

echo json_encode($applicants);
$conn->close();
?>



