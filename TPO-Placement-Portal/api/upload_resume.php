<?php
header("Access-Control-Allow-Origin: *"); // Allow cross-origin requests
header("Access-Control-Allow-Methods: POST"); // Allow POST method
header("Access-Control-Allow-Headers: Content-Type"); // Allow headers for content type

session_start();
include 'db.php'; // Include the database connection file


// Check if the request method is POST and if the file is uploaded
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    // Get the enrollment number and file title from the form data
    $enrollment_no = $_POST['enrollment_no'] ?? null;
    $title = $_POST['title'] ?? 'Resume';

    if (!$enrollment_no) {
        echo json_encode(["success" => false, "message" => "Enrollment number is required."]);
        exit();
    }

    // Check if the enrollment number exists in the student_profile table
    try {
        $check_stmt = $pdo->prepare("SELECT enrollment_no FROM student_profile WHERE enrollment_no = ?");
        $check_stmt->execute([$enrollment_no]);
        if ($check_stmt->rowCount() == 0) {
            echo json_encode(["success" => false, "message" => "Enrollment number not found in student_profile."]);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
        exit();
    }

    // File upload logic
    function uploadFile($file, $upload_dir) {
        $allowed_extensions = ['pdf', 'jpg', 'png']; // Allowed file extensions
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        // Check if the file extension is allowed
        if (!in_array(strtolower($file_ext), $allowed_extensions)) {
            echo json_encode(["success" => false, "message" => "Only PDF, JPG, and PNG files are allowed."]);
            exit();
        }

        // Check if the directory exists, if not create it
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($file['name']);
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file to the server's directory
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return $file_path; // Return the file path if upload is successful
        } else {
            echo json_encode(["success" => false, "message" => "File upload failed."]);
            exit();
        }
    }

    // Upload the file
    $upload_dir = 'uploads/'; // The directory where the file will be stored
    $file_path = uploadFile($_FILES['file'], $upload_dir);

    // Insert file details into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO student_uploads (enrollment_no, title, file_type, file_path) VALUES (?, ?, ?, ?)");
        $file_ext = pathinfo($file_path, PATHINFO_EXTENSION); // Get the file extension
        $stmt->execute([$enrollment_no, $title, $file_ext, $file_path]);

        echo json_encode(["success" => true, "message" => "File uploaded successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to save the file details in the database: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}


?>
