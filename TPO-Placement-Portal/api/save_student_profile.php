<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
    $enrollment_no = $input['enrollment_no'];
    $name = $input['name'];
    $age = $input['age'];
    $address = $input['address'];
    $course = $input['course'];
    $year = $input['year'];
    $percentage = $input['percentage'];

    try {
        // Check if the profile already exists for the enrollment number
        $stmt = $pdo->prepare("SELECT * FROM student_profile WHERE enrollment_no = ?");
        $stmt->execute([$enrollment_no]);
        $existingProfile = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingProfile) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE student_profile SET name = ?, age = ?, address = ?, course = ?, year = ?, percentage = ? WHERE enrollment_no = ?");
            $stmt->execute([$name, $age, $address, $course, $year, $percentage, $enrollment_no]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Profile updated successfully'
            ]);
        } else {
            // Insert new profile
            $stmt = $pdo->prepare("INSERT INTO student_profile (enrollment_no, name, age, address, course, year, percentage) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$enrollment_no, $name, $age, $address, $course, $year, $percentage]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Profile created successfully'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request or missing data'
    ]);
}
?>
