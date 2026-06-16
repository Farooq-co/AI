<?php
header('Content-Type: application/json');
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

$stmt = $conn->prepare("DELETE FROM provinces WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Province/State deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete province/state']);
}

$stmt->close();
$conn->close();
?>