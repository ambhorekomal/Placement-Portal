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

// Get input data
$data = json_decode(file_get_contents("php://input"), true);

// Check if data is valid
if (empty($data['username']) || empty($data['password'])) {
    echo json_encode(["status" => "error", "message" => "Username or password not provided"]);
    exit();
}

$username = $data['username'];
$password = $data['password'];

try {
    // Prepare SQL to get the TPO user based on the provided username
    $stmt = $pdo->prepare("SELECT * FROM tpo WHERE username = ? AND role = 'tpo'");
    $stmt->execute([$username]);
    $tpo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists and the password is correct
    if ($tpo) {
        if (password_verify($password, $tpo['password'])) {
            // Password matches, return success
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "user" => [
                    "username" => $tpo['username'],
                    "role" => $tpo['role']
                ]
            ]);
        } else {
            // Password does not match
            echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        }
    } else {
        // User not found in the database
        echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    }
} catch (PDOException $e) {
    // Handle database error
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
