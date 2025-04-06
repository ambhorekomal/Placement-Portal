<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include the database connection
include('db.php');

// Query to fetch placed students
$query = "SELECT * FROM placed_students";
$stmt = $pdo->query($query);

// Create an array to store student data
$placed_students = [];

// Fetch each student and store the data in the array
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $placed_students[] = [
        'name' => $row['name'],
        'enrollment_no' => $row['enrollment_no'],
        'company_name' => $row['company_name'],
        'position' => $row['position'],
        'salary' => $row['salary'],
        'placement_date' => $row['placement_date']
    ];
}

// Return the data as JSON
echo json_encode($placed_students);
?>
