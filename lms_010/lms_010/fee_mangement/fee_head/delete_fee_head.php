<?php
header('Content-Type: application/json');
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid fee head ID']);
    exit;
}

$checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM fee_structures WHERE fee_head_id = ?");
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$row = $result->fetch_assoc();
$checkStmt->close();

if ($row['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete: This fee head is assigned to fee structures']);
    $conn->close();
    exit;
}

$stmt = $conn->prepare("DELETE FROM fee_heads WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee head deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete fee head: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>