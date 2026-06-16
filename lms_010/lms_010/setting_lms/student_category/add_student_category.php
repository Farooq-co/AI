<?php
header('Content-Type: application/json');
include '../../connect.php';

$name = trim($_POST['name']);
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Category name is required']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO student_category (name, status, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
$stmt->bind_param("ss", $name, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Category added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add category: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>