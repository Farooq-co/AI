<?php
include '../../connect.php';
include '../fee_helpers.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = sanitizeInt($data['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(false, 'Invalid invoice ID.');
}

$delete = $conn->prepare('DELETE FROM fee_invoices WHERE id = ?');
$delete->bind_param('i', $id);

if ($delete->execute()) {
    jsonResponse(true, 'Invoice deleted successfully.');
} else {
    jsonResponse(false, 'Invoice deletion failed: ' . $conn->error);
}
