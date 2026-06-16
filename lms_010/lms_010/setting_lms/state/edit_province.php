<?php
header('Content-Type: application/json');
include '../../connect.php';

$id = $_POST['id'];
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

// Check if province already exists for this country (excluding current)
$checkStmt = $conn->prepare("SELECT id FROM provinces WHERE name = ? AND country_id = ? AND id != ?");
$checkStmt->bind_param("sii", $name, $country_id, $id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Province/State already exists for this country']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("UPDATE provinces SET name = ?, country_id = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("sisi", $name, $country_id, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Province/State updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update province/state: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>