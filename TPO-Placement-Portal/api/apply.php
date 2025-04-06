<?php
session_start();
include 'db_connection.php'; // Ensure this file connects to the database

// Function to handle the application
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the POST request
    $enrollment_no = $_POST['enrollment_no'];  // Student's enrollment number
    $job_id = $_POST['job_id'];  // Job ID

    // Check if the application already exists for this student and job
    $sql_check = "SELECT * FROM applications WHERE enrollment_no = ? AND job_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $enrollment_no, $job_id);  // "si" means string and integer
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Application already exists
        echo json_encode(["message" => "You have already applied for this job."]);
    } else {
        // Insert the application into the database
        $sql = "INSERT INTO applications (enrollment_no, job_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $enrollment_no, $job_id);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Application submitted successfully!"]);
        } else {
            echo json_encode(["message" => "Error submitting application. Please try again later."]);
        }
    }
}

?>
