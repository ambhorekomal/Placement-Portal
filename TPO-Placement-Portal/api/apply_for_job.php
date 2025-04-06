<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['enrollment_no']) || !isset($data['job_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
    exit();
}

$enrollment_no = $data['enrollment_no'];
$job_id = $data['job_id'];

try {
    // Insert application into job_applications table (ignore duplicate applications)
    $stmt = $pdo->prepare("INSERT IGNORE INTO job_applications (enrollment_no, job_id, applied_at) VALUES (?, ?, NOW())");
    $stmt->execute([$enrollment_no, $job_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Application submitted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "You have already applied for this job."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
