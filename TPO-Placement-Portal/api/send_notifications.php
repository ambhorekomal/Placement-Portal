<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  // Allow any origin, you can restrict it to a specific domain later
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');  // Allow methods you want
header('Access-Control-Allow-Headers: Content-Type, Authorization');  // Allow Content-Type in headers for preflight

// Handle preflight requests for CORS (this is the OPTIONS request)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php';  // Include your database connection

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the POST data (JSON body)
    $data = json_decode(file_get_contents("php://input"));

    // Check if students and message are provided in the request
    if (isset($data->students) && isset($data->message)) {
        $students = $data->students;
        $message = $data->message;

        // Prepare and execute inserting notifications for each student
        $stmt = $pdo->prepare("
            INSERT INTO notifications (enrollment_no, message) 
            VALUES (:enrollment_no, :message) 
            ON DUPLICATE KEY UPDATE 
                message = VALUES(message), 
                date_sent = CURRENT_TIMESTAMP, 
                is_read = 0
        ");
        
        // Loop through each student and bind parameters
        foreach ($students as $enrollment_no) {
            $stmt->bindParam(':enrollment_no', $enrollment_no);
            $stmt->bindParam(':message', $message);
            $stmt->execute();
        }

        // Send success response
        echo json_encode([
            'success' => true,
            'message' => 'Notifications sent to the shortlisted students.'
        ]);
    } else {
        // If students or message data is missing
        echo json_encode([
            'success' => false,
            'message' => 'Missing students or message data.'
        ]);
    }

} catch (PDOException $e) {
    // Handle database connection or query errors
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
