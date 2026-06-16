<?php
header('Content-Type: application/json');
include '../../connect.php';

$name = trim($_POST['name']);
$province_id = $_POST['province_id'];
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'City name is required']);
    exit;
}

if (empty($province_id)) {
    echo json_encode(['success' => false, 'message' => 'Please select a province/state']);
    exit;
}

// Check if city already exists for this province
$checkStmt = $conn->prepare("SELECT id FROM cities WHERE name = ? AND province_id = ?");
$checkStmt->bind_param("si", $name, $province_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'City already exists for this province/state']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("INSERT INTO cities (name, province_id, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
$stmt->bind_param("sis", $name, $province_id, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'City added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add city: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>