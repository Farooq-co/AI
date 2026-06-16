<?php
header('Content-Type: application/json');
include '../../connect.php';

$id = $_POST['id'];
$name = trim($_POST['name']);
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Country name is required']);
    exit;
}

// Check if country already exists (excluding current)
$checkStmt = $conn->prepare("SELECT id FROM countries WHERE name = ? AND id != ?");
$checkStmt->bind_param("si", $name, $id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Country already exists']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("UPDATE countries SET name = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("ssi", $name, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Country updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update country: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>