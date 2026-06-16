<?php
header('Content-Type: application/json');
include '../../connect.php';

$id = intval($_POST['id']);
$feeHeadId = intval($_POST['fee_head_id']);
$feeTypeId = intval($_POST['fee_type_id']);
$classId = intval($_POST['class_id']);
$amount = floatval($_POST['amount']);
$status = $_POST['status'];

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid fee structure ID']);
    exit;
}

if ($feeHeadId <= 0 || $feeTypeId <= 0 || $classId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Fee head, fee type, and class are required']);
    exit;
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than zero']);
    exit;
}

$stmt = $conn->prepare("UPDATE fee_structures SET fee_head_id = ?, fee_type_id = ?, class_id = ?, amount = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("iiidsi", $feeHeadId, $feeTypeId, $classId, $amount, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee structure updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update fee structure: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>