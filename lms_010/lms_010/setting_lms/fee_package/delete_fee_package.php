<?php
header('Content-Type: application/json');
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);

// Check if package is being used by any students/invoices before deleting
$checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM student_fee_packages WHERE fee_package_id = ?");
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete: This fee package is assigned to ' . $row['count'] . ' student(s)']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$stmt = $conn->prepare("DELETE FROM fee_packages WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee package deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete fee package']);
}

$stmt->close();
$conn->close();
?>