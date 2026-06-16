<?php
header('Content-Type: application/json');
include '../../connect.php';

$id = $_POST['id'];
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

// Check if city already exists for this province (excluding current)
$checkStmt = $conn->prepare("SELECT id FROM cities WHERE name = ? AND province_id = ? AND id != ?");
$checkStmt->bind_param("sii", $name, $province_id, $id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'City already exists for this province/state']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("UPDATE cities SET name = ?, province_id = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("sisi", $name, $province_id, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'City updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update city: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>