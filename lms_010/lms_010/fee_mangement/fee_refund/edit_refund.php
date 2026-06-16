<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = sanitizeInt($_POST['id'] ?? 0);
$refund_amount = sanitizeFloat($_POST['refund_amount'] ?? 0.00);
$refund_reason = sanitizeText($_POST['refund_reason'] ?? '');
$approved_by = sanitizeText($_POST['approved_by'] ?? '');
$refund_date = sanitizeText($_POST['refund_date'] ?? '');
$status = sanitizeText($_POST['status'] ?? 'Pending');

if ($id <= 0 || $refund_amount <= 0 || empty($refund_date)) {
    jsonResponse(false, 'Refund id, amount, and date are required.');
}

$update = $conn->prepare('UPDATE fee_refunds SET refund_amount = ?, refund_reason = ?, approved_by = ?, refund_date = ?, status = ?, updated_at = NOW() WHERE id = ?');
if (!$update) {
    jsonResponse(false, 'Prepare failed: ' . $conn->error);
}
$update->bind_param('dssssi', $refund_amount, $refund_reason, $approved_by, $refund_date, $status, $id);
if ($update->execute()) {
    jsonResponse(true, 'Refund updated successfully.');
} else {
    jsonResponse(false, 'Update failed: ' . $conn->error);
}
