<?php
header('Content-Type: application/json');
include '../../connect.php';

$id = $_POST['id'];
$name = trim($_POST['name']);
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Blood group name is required']);
    exit;
}

$stmt = $conn->prepare("UPDATE blood_group SET name = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("ssi", $name, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Blood group updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update blood group: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>