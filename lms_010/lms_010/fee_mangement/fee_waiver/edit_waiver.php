<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = sanitizeInt($_POST['id'] ?? 0);
$student_id = sanitizeInt($_POST['student_id'] ?? 0);
$invoice_id = sanitizeInt($_POST['invoice_id'] ?? 0);
$waiver_type = sanitizeText($_POST['waiver_type'] ?? '');
$waiver_amount = sanitizeFloat($_POST['waiver_amount'] ?? 0.00);
$waiver_reason = sanitizeText($_POST['waiver_reason'] ?? '');
$approved_by = sanitizeText($_POST['approved_by'] ?? '');
$status = sanitizeText($_POST['status'] ?? 'Active');

if ($id <= 0 || $student_id <= 0 || empty($waiver_type) || $waiver_amount <= 0) {
    jsonResponse(false, 'Valid waiver, student, waiver type and amount are required.');
}

$update = $conn->prepare('UPDATE fee_waivers SET student_id = ?, invoice_id = ?, waiver_type = ?, waiver_amount = ?, waiver_reason = ?, approved_by = ?, status = ?, updated_at = NOW() WHERE id = ?');
if (!$update) {
    jsonResponse(false, 'Prepare failed: ' . $conn->error);
}
$update->bind_param('iisdsssi', $student_id, $invoice_id, $waiver_type, $waiver_amount, $waiver_reason, $approved_by, $status, $id);
if ($update->execute()) {
    jsonResponse(true, 'Waiver updated.');
} else {
    jsonResponse(false, 'Update failed: ' . $conn->error);
}
