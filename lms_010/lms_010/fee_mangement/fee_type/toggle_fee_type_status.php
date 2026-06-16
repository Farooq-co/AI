<?php
header('Content-Type: application/json');
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);
$status = $data['status'];

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid fee type ID']);
    exit;
}

$stmt = $conn->prepare("UPDATE fee_types SET status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status changed to ' . $status]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>