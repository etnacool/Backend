<?php
ob_start();

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'connectDB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoName = basename($_FILES['photo']['name']);
        $photoTmp = $_FILES['photo']['tmp_name'];
        $photoName = preg_replace("/[^A-Za-z0-9_\-\.]/", '_', $photoName);

        if (move_uploaded_file($photoTmp, "uploads/" . $photoName)) {
            $photo = "uploads/" . $photoName;
            $photo = $conn->real_escape_string($photo);
        } else {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "Dështoi ruajtja e fotos."]);
            exit();
        }
    }

    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : "";
    $surname = isset($_POST['surname']) ? $conn->real_escape_string($_POST['surname']) : "";
    $bday = isset($_POST['bday']) ? $conn->real_escape_string($_POST['bday']) : date("Y-m-d");
    $nr_tel = isset($_POST['nr_tel']) ? $conn->real_escape_string($_POST['nr_tel']) : "";
    $adresa = isset($_POST['adresa']) ? $conn->real_escape_string($_POST['adresa']) : "";
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : "";
    $education = isset($_POST['education']) ? $conn->real_escape_string($_POST['education']) : "";
    $university = isset($_POST['university']) ? $conn->real_escape_string($_POST['university']) : "";
    $title = isset($_POST['title']) ? $conn->real_escape_string($_POST['title']) : "";
    $y_of_Graduation = isset($_POST['y_of_Graduation']) ? $conn->real_escape_string($_POST['y_of_Graduation']) : date("Y-m-d");
    $position = isset($_POST['position']) ? $conn->real_escape_string($_POST['position']) : "";
    $the_select_time = isset($_POST['the_select_time']) ? $conn->real_escape_string($_POST['the_select_time']) : "";

    if (empty($name) || empty($email)) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Emri dhe emaili janë të detyrueshëm."]);
        exit();
    }

    $checkQuery = "SELECT id FROM aplikimi WHERE email = '$email'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Ky email është përdorur më parë. Ju lutemi përdorni një tjetër."]);
        exit();
    }

    $sql = "INSERT INTO aplikimi 
        (photo, name, surname, bday, nr_tel, adresa, email, education, university, title, y_of_Graduation, position, the_select_time)
        VALUES
        ('$photo', '$name', '$surname', '$bday', '$nr_tel', '$adresa', '$email', '$education', '$university', '$title', '$y_of_Graduation', '$position', '$the_select_time')";

    if ($conn->query($sql) === TRUE) {
        ob_clean();
        echo json_encode(["status" => "success", "message" => "Të dhënat u shtuan me sukses."]);
    } else {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Gabim gjatë futjes së të dhënave: " . $conn->error]);
    }

} else {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Kërkesa duhet të jetë POST."]);
}

$conn->close();