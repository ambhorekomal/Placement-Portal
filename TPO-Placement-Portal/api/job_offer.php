<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $jobTitle = $_POST['jobTitle'];
    $salary = $_POST['salary'];
    $qualifications = $_POST['qualifications'];
    $eligibility = $_POST['eligibility'];
    $workMode = $_POST['workMode'];
    $location = $_POST['location'];

    // Prepare SQL query to insert data into the database
    $sql = "INSERT INTO jobs (job_title, salary, qualifications, eligibility, work_mode, location) 
            VALUES (:jobTitle, :salary, :qualifications, :eligibility, :workMode, :location)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':jobTitle', $jobTitle);
    $stmt->bindParam(':salary', $salary);
    $stmt->bindParam(':qualifications', $qualifications);
    $stmt->bindParam(':eligibility', $eligibility);
    $stmt->bindParam(':workMode', $workMode);
    $stmt->bindParam(':location', $location);

    if ($stmt->execute()) {
        // Respond with a success message
        echo json_encode(['success' => true, 'message' => 'Job offer uploaded successfully']);
    } else {
        // Respond with an error message
        echo json_encode(['success' => false, 'message' => 'Error: Could not upload job offer']);
    }
}
?>
