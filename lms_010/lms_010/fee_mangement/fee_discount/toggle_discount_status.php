<?php
include '../../connect.php';
include '../fee_helpers.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = sanitizeInt($data['id'] ?? 0);
$status = sanitizeText($data['status'] ?? 'Inactive');

if ($id <= 0) {
    jsonResponse(false, 'Invalid discount ID.');
}

$update = $conn->prepare('UPDATE fee_discounts SET status = ?, updated_at = NOW() WHERE id = ?');
$update->bind_param('si', $status, $id);

if ($update->execute()) {
    jsonResponse(true, 'Status updated successfully.');
} else {
    jsonResponse(false, 'Failed to update status: ' . $conn->error);
}
