<?php
require 'db.php';

$enrollment_no = $_GET['enrollment_no'];
$stmt = $pdo->prepare("SELECT title, file_type, file_path FROM student_uploads WHERE enrollment_no = ?");
$stmt->execute([$enrollment_no]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($files);
?>
