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

if (isset($data['enrollment_no'])) {
    $enrollment_no = $data['enrollment_no'];

    $query = "DELETE FROM student_profile WHERE enrollment_no = :enrollment_no";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':enrollment_no', $enrollment_no);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Profile deleted successfully"]);
    } else {
        echo json_encode(["message" => "Failed to delete profile"]);
    }
} else {
    echo json_encode(["message" => "Invalid data"]);
}
?>
