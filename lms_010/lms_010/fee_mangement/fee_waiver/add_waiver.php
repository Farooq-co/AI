<?php
include '../../connect.php';
include '../fee_helpers.php';

$student_id = sanitizeInt($_POST['student_id'] ?? 0);
$invoice_id = sanitizeInt($_POST['invoice_id'] ?? 0);
$waiver_type = sanitizeText($_POST['waiver_type'] ?? '');
$waiver_amount = sanitizeFloat($_POST['waiver_amount'] ?? 0.00);
$waiver_reason = sanitizeText($_POST['waiver_reason'] ?? '');
$approved_by = sanitizeText($_POST['approved_by'] ?? '');
$status = sanitizeText($_POST['status'] ?? 'Active');

if ($student_id <= 0 || empty($waiver_type) || $waiver_amount <= 0) {
    jsonResponse(false, 'Student, waiver type and amount are required.');
}

$insert = $conn->prepare('INSERT INTO fee_waivers (student_id, invoice_id, waiver_type, waiver_amount, waiver_reason, approved_by, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
if (!$insert) {
    jsonResponse(false, 'Prepare failed: ' . $conn->error);
}
$insert->bind_param('iisdsss', $student_id, $invoice_id, $waiver_type, $waiver_amount, $waiver_reason, $approved_by, $status);
if ($insert->execute()) {
    jsonResponse(true, 'Waiver created.', ['id' => $insert->insert_id]);
} else {
    jsonResponse(false, 'Create failed: ' . $insert->error);
}
