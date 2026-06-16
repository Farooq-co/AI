<?php
header('Content-Type: application/json');
include '../../connect.php';

$name = trim($_POST['name']);
$city_id = $_POST['city_id'];
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Area name is required']);
    exit;
}

if (empty($city_id)) {
    echo json_encode(['success' => false, 'message' => 'Please select a city']);
    exit;
}

// Check if area already exists for this city
$checkStmt = $conn->prepare("SELECT id FROM areas WHERE name = ? AND city_id = ?");
$checkStmt->bind_param("si", $name, $city_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Area already exists for this city']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("INSERT INTO areas (name, city_id, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
$stmt->bind_param("sis", $name, $city_id, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Area added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add area: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>