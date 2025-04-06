<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db.php';

$enrollment_no = isset($_GET['enrollment_no']) ? $_GET['enrollment_no'] : '';

if ($enrollment_no) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM student_profile WHERE enrollment_no = ?");
        $stmt->execute([$enrollment_no]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($profile) {
            echo json_encode([
                'status' => 'success',
                'profile' => $profile
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No profile found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Enrollment number is required'
    ]);
}
?>
