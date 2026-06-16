<?php
header('Content-Type: application/json');
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

$stmt = $conn->prepare("DELETE FROM sections WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Section deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete section']);
}

$stmt->close();
$conn->close();
?>