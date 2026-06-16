<?php
header('Content-Type: application/json');
include '../../connect.php';

$name = trim($_POST['name']);
$country_id = $_POST['country_id'];
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Province/State name is required']);
    exit;
}

if (empty($country_id)) {
    echo json_encode(['success' => false, 'message' => 'Please select a country']);
    exit;
}

// Check if province already exists for this country
$checkStmt = $conn->prepare("SELECT id FROM provinces WHERE name = ? AND country_id = ?");
$checkStmt->bind_param("si", $name, $country_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Province/State already exists for this country']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("INSERT INTO provinces (name, country_id, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
$stmt->bind_param("sis", $name, $country_id, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Province/State added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add province/state: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>