<?php
header('Content-Type: application/json');
include '../../connect.php';

$id = intval($_POST['id']);
$name = trim($_POST['name']);
$totalAmount = floatval($_POST['total_amount']);
$status = $_POST['status'];

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Package name is required']);
    exit;
}

if ($totalAmount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Total amount must be greater than 0']);
    exit;
}

$stmt = $conn->prepare("UPDATE fee_packages SET name = ?, total_amount = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("sdsi", $name, $totalAmount, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee package updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update fee package: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>