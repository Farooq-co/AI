<?php
header('Content-Type: application/json');
include '../../connect.php';

$id = $_POST['id'];
$name = trim($_POST['name']);
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Gender name is required']);
    exit;
}

// Check for duplicate entry excluding current record
$checkStmt = $conn->prepare("SELECT id FROM gender WHERE name = ? AND id != ?");
$checkStmt->bind_param("si", $name, $id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Gender already exists']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("UPDATE gender SET name = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("ssi", $name, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Gender updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update gender: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>