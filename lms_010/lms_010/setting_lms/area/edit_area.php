<?php
header('Content-Type: application/json');
include '../../connect.php';

$id = $_POST['id'];
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

// Check if area already exists for this city (excluding current)
$checkStmt = $conn->prepare("SELECT id FROM areas WHERE name = ? AND city_id = ? AND id != ?");
$checkStmt->bind_param("sii", $name, $city_id, $id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Area already exists for this city']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("UPDATE areas SET name = ?, city_id = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("sisi", $name, $city_id, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Area updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update area: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>