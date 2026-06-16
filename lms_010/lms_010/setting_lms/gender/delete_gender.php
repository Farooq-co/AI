<?php
header('Content-Type: application/json');
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

// Check if gender is being used before deleting (optional but recommended)
// Add any foreign key checks here if needed

$stmt = $conn->prepare("DELETE FROM gender WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Gender deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete gender']);
}

$stmt->close();
$conn->close();
?>