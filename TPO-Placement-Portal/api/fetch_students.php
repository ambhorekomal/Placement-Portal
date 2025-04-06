<?php
header("Access-Control-Allow-Origin: *"); //  For development only!  Restrict in production.
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Cache-Control, Pragma"); //  Add Cache-Control
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require 'db.php';

try {
    if (!$pdo) {
        throw new Exception("Database connection failed.");
    }

    $stmt = $pdo->prepare("SELECT * FROM student_profile");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $students]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
