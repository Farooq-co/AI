<?php
header('Content-Type: application/json');
include '../../connect.php';

$feeHeadId = intval($_POST['fee_head_id']);
$feeTypeId = intval($_POST['fee_type_id']);
$classId = intval($_POST['class_id']);
$amount = floatval($_POST['amount']);
$status = $_POST['status'];

if ($feeHeadId <= 0 || $feeTypeId <= 0 || $classId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Fee head, fee type, and class are required']);
    exit;
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than zero']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO fee_structures (fee_head_id, fee_type_id, class_id, amount, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
$stmt->bind_param("iiids", $feeHeadId, $feeTypeId, $classId, $amount, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee structure added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add fee structure: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>