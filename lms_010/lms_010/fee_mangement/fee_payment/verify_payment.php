<?php
include '../../connect.php';
include '../fee_helpers.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = sanitizeInt($data['id'] ?? 0);
$status = sanitizeText($data['status'] ?? 'Verified');

if ($id <= 0) {
    jsonResponse(false, 'Invalid payment ID.');
}

$update = $conn->prepare('UPDATE fee_payments SET status = ?, updated_at = NOW() WHERE id = ?');
$update->bind_param('si', $status, $id);

if ($update->execute()) {
    jsonResponse(true, 'Payment status updated successfully.');
} else {
    jsonResponse(false, 'Payment verification failed: ' . $conn->error);
}
