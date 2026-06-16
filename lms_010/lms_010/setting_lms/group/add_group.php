<?php
header('Content-Type: application/json');
include '../../connect.php';

$name = trim($_POST['name']);
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Group name is required']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO groups (name, status, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
$stmt->bind_param("ss", $name, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Group added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add group: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>