<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include 'db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the student's enrollment_no from the GET request
    if (isset($_GET['enrollment_no'])) {
        $enrollment_no = $_GET['enrollment_no'];

        // Prepare and execute the query to get notifications for the student
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE enrollment_no = :enrollment_no ORDER BY date_sent DESC");
        $stmt->bindParam(':enrollment_no', $enrollment_no);
        $stmt->execute();

        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the notifications as JSON with success status
        echo json_encode([
            'success' => true,
            'notifications' => $notifications
        ]);
    } else {
        // If no enrollment_no is provided
        echo json_encode([
            'success' => false,
            'message' => 'Enrollment number is required.'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
