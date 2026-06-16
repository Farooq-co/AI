<?php
header('Content-Type: application/json');
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$status = $data['status'];

$stmt = $conn->prepare("UPDATE mobile_operators SET status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status changed to ' . $status]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}

$stmt->close();
$conn->close();
?>