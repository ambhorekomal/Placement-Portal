<?php
// CORS Headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Allow specific methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content response
    exit;
}

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['action'])) {
    echo json_encode(["status" => "error", "message" => "Action not specified"]);
    exit;
}

$action = $data['action']; // "signup_student", "signup_tpo", "login_student", "login_tpo"

// Signup for Students
if ($action === "signup_student") {
    if (empty($data['enrollment_no']) || empty($data['username']) || empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    $enrollment_no = $data['enrollment_no'];
    $username = $data['username'];
    $name = $data['name'];
    $email = $data['email'];
    $password = $data['password']; // No hashing applied here

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format"]);
        exit;
    }

    if (strlen($password) < 8) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE enrollment_no = ? OR username = ? OR email = ?");
        $stmt->execute([$enrollment_no, $username, $email]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(["status" => "error", "message" => "Enrollment No, Username, or Email already exists"]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO students (enrollment_no, username, name, email, password, role) VALUES (?, ?, ?, ?, ?, 'student')");
        $stmt->execute([$enrollment_no, $username, $name, $email, $password]); // Storing plain password

        echo json_encode(["status" => "success", "message" => "Signup successful"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}

// Signup for TPO
elseif ($action === "signup_tpo") {
    if (empty($data['username']) || empty($data['password'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    $username = $data['username'];
    $password = $data['password'];

    if (strlen($password) < 8) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tpo WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(["status" => "error", "message" => "Username already exists"]);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO tpo (username, password, role) VALUES (?, ?, 'tpo')");
        $stmt->execute([$username, $hashedPassword]);

        echo json_encode(["status" => "success", "message" => "Signup successful"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}

// Login for Students
elseif ($action === "login_student") {
    if (empty($data['username']) || empty($data['password'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    $username = $data['username'];
    $password = $data['password'];

    try {
        $stmt = $pdo->prepare("SELECT password FROM students WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || $password !== $user['password']) { // Checking plain password
            echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        } else {
            echo json_encode(["status" => "success", "message" => "Login successful"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}

// Login for TPO
elseif ($action === "login_tpo") {
    if (empty($data['username']) || empty($data['password'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    $username = $data['username'];
    $password = $data['password'];

    try {
        $stmt = $pdo->prepare("SELECT password FROM tpo WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        } else {
            echo json_encode(["status" => "success", "message" => "Login successful"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}
?>
