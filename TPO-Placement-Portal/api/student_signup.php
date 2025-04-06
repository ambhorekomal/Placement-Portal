<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['enrollment_no']) || empty($data['name']) || empty($data['email']) || empty($data['password'])) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

$enrollment_no = $data['enrollment_no'];
$name = $data['name'];
$email = $data['email'];
$password = $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long"]);
    exit;
}

try {
    // Check if enrollment_no already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE enrollment_no = ?");
    $stmt->execute([$enrollment_no]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(["status" => "error", "message" => "Enrollment No already exists"]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into students table
    $stmt = $pdo->prepare("INSERT INTO students (enrollment_no, name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$enrollment_no, $name, $email, $hashedPassword]);

    echo json_encode(["status" => "success", "message" => "Signup successful"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
