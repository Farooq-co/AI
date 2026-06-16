<?php
include '../../connect.php';
include '../fee_helpers.php';

$payment_id = sanitizeInt($_POST['payment_id'] ?? 0);
$refund_amount = sanitizeFloat($_POST['refund_amount'] ?? 0.00);
$refund_reason = sanitizeText($_POST['refund_reason'] ?? '');
$approved_by = sanitizeText($_POST['approved_by'] ?? '');
$refund_date = sanitizeText($_POST['refund_date'] ?? '');
$status = sanitizeText($_POST['status'] ?? 'Pending');

if ($payment_id <= 0 || $refund_amount <= 0 || empty($refund_date)) {
    jsonResponse(false, 'Payment, refund amount, and refund date are required.');
}

$stmt = $conn->prepare('SELECT id, student_id FROM fee_payments WHERE id = ?');
if (!$stmt) {
    jsonResponse(false, 'Prepare failed: ' . $conn->error);
}
$stmt->bind_param('i', $payment_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$payment) {
    jsonResponse(false, 'Payment not found.');
}

$insert = $conn->prepare('INSERT INTO fee_refunds (payment_id, refund_amount, refund_reason, approved_by, refund_date, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
if (!$insert) {
    jsonResponse(false, 'Prepare failed: ' . $conn->error);
}
$insert->bind_param('idssss', $payment_id, $refund_amount, $refund_reason, $approved_by, $refund_date, $status);
if ($insert->execute()) {
    jsonResponse(true, 'Refund recorded.', ['refund_id' => $insert->insert_id]);
} else {
    jsonResponse(false, 'Refund save failed: ' . $insert->error);
}
