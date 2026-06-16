<?php
header('Content-Type: application/json');
include '../../connect.php';

$name = trim($_POST['name']);
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Country name is required']);
    exit;
}

// Check if country already exists
$checkStmt = $conn->prepare("SELECT id FROM countries WHERE name = ?");
$checkStmt->bind_param("s", $name);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Country already exists']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("INSERT INTO countries (name, status, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
$stmt->bind_param("ss", $name, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Country added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add country: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>