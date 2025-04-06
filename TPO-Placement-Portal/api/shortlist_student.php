<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = 'localhost';
$dbname = 'training_placement';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "
        SELECT 
            sp.enrollment_no, sp.name, sp.age, sp.address, sp.course, sp.year, sp.percentage,
            GROUP_CONCAT(ja.job_id) AS jobs_applied
        FROM student_profile sp
        LEFT JOIN job_applications ja ON sp.enrollment_no = ja.enrollment_no
        WHERE 1
    ";

    if (isset($_GET['course'])) {
        $query .= " AND sp.course = :course";
    }

    if (isset($_GET['year'])) {
        $query .= " AND sp.year = :year";
    }

    if (isset($_GET['percentage'])) {
        $query .= " AND sp.percentage >= :percentage";
    }

    $query .= " GROUP BY sp.enrollment_no";

    $stmt = $pdo->prepare($query);

    if (isset($_GET['course'])) {
        $stmt->bindParam(':course', $_GET['course']);
    }

    if (isset($_GET['year'])) {
        $stmt->bindParam(':year', $_GET['year']);
    }

    if (isset($_GET['percentage'])) {
        $stmt->bindParam(':percentage', $_GET['percentage']);
    }

    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($students ?: []);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
