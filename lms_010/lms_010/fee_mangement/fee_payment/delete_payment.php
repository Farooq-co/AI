<?php
include '../../connect.php';
include '../fee_helpers.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = sanitizeInt($data['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(false, 'Invalid payment ID.');
}

$delete = $conn->prepare('DELETE FROM fee_payments WHERE id = ?');
$delete->bind_param('i', $id);

if ($delete->execute()) {
    jsonResponse(true, 'Payment deleted successfully.');
} else {
    jsonResponse(false, 'Payment deletion failed: ' . $conn->error);
}
