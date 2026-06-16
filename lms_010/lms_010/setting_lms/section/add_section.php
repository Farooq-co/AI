<?php
header('Content-Type: application/json');
include '../../connect.php';

$name = trim($_POST['name']);
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Section name is required']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO sections (name, status, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
$stmt->bind_param("ss", $name, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Section added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add section: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>