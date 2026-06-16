<?php
header('Content-Type: application/json');
include '../../connect.php';

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

$stmt = $conn->prepare("INSERT INTO fee_packages (name, total_amount, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
$stmt->bind_param("sds", $name, $totalAmount, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee package added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add fee package: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>