<?php
include '../../connect.php';
include '../fee_helpers.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = sanitizeInt($data['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(false, 'Invalid discount ID.');
}

$delete = $conn->prepare('DELETE FROM fee_discounts WHERE id = ?');
$delete->bind_param('i', $id);

if ($delete->execute()) {
    jsonResponse(true, 'Discount deleted successfully.');
} else {
    jsonResponse(false, 'Failed to delete discount: ' . $conn->error);
}
