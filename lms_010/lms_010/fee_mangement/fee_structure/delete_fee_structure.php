<?php
header('Content-Type: application/json');
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid fee structure ID']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM fee_structures WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee structure deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete fee structure: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>