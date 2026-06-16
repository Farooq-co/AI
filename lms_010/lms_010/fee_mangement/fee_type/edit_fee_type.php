<?php
header('Content-Type: application/json');
include '../../connect.php';

$id = intval($_POST['id']);
$name = trim($_POST['name']);
$status = $_POST['status'];

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid fee type ID']);
    exit;
}

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Fee type name is required']);
    exit;
}

$stmt = $conn->prepare("UPDATE fee_types SET name = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("ssi", $name, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee type updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update fee type: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>