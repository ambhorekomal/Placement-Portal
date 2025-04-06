<?php
// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight (CORS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username']; // Treat username as enrollment_no
$password = $data['password'];

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE enrollment_no = ?");
    $stmt->execute([$username]); // Treat username as enrollment_no
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Compare passwords directly (without hashing)
    if ($user && $password === $user['password']) {
        echo json_encode(["status" => "success", "message" => "Login successful", "user" => $user]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
