<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'db.php';

if (!isset($_GET['enrollment_no'])) {
    echo json_encode(["status" => "error", "message" => "Enrollment No required"]);
    exit();
}

$enrollment_no = $_GET['enrollment_no'];

try {
    // Get student percentage
    $stmt = $pdo->prepare("SELECT percentage FROM student_profile WHERE enrollment_no = ?");
    $stmt->execute([$enrollment_no]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(["status" => "error", "message" => "Student not found"]);
        exit();
    }

    $percentage = floatval($student['percentage']);

    // Fetch jobs where eligibility <= student's percentage
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE eligibility <= ? ORDER BY created_at DESC");
    $stmt->execute([$percentage]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "success", "percentage" => $percentage, "jobs" => $jobs], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    error_log("Error fetching student jobs: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Database error."]);
}
?>
