<?php
header('Content-Type: application/json');
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid fee type ID']);
    exit;
}

$checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM fee_structures WHERE fee_type_id = ?");
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$row = $result->fetch_assoc();
$checkStmt->close();

if ($row['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete: This fee type is assigned to fee structures']);
    $conn->close();
    exit;
}

$stmt = $conn->prepare("DELETE FROM fee_types WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee type deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete fee type: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>