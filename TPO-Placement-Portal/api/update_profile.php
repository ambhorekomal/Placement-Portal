<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['enrollment_no']) && isset($data['name']) && isset($data['age']) && isset($data['address']) && isset($data['course']) && isset($data['year']) && isset($data['percentage'])) {
    $enrollment_no = $data['enrollment_no'];
    $name = $data['name'];
    $age = $data['age'];
    $address = $data['address'];
    $course = $data['course'];
    $year = $data['year'];
    $percentage = $data['percentage'];

    $query = "UPDATE student_profile 
              SET name = :name, age = :age, address = :address, course = :course, year = :year, percentage = :percentage 
              WHERE enrollment_no = :enrollment_no";
    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':enrollment_no', $enrollment_no);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':age', $age);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':course', $course);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':percentage', $percentage);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["message" => "Failed to update profile"]);
    }
} else {
    echo json_encode(["message" => "Invalid data"]);
}
?>
