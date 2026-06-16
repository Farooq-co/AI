<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = sanitizeInt($_POST['id'] ?? 0);
$amount_paid = sanitizeFloat($_POST['amount_paid'] ?? 0);
$payment_method_id = sanitizeInt($_POST['payment_method_id'] ?? 0);
$transaction_id = sanitizeText($_POST['transaction_id'] ?? '');
$bank_name = sanitizeText($_POST['bank_name'] ?? '');
$branch_name = sanitizeText($_POST['branch_name'] ?? '');
$cheque_number = sanitizeText($_POST['cheque_number'] ?? '');
$reference_number = sanitizeText($_POST['reference_number'] ?? '');
$payment_date = sanitizeText($_POST['payment_date'] ?? '');
$remarks = sanitizeText($_POST['remarks'] ?? '');
$received_by = sanitizeText($_POST['received_by'] ?? '');
$status = sanitizeText($_POST['status'] ?? 'Pending');

if ($id <= 0 || $amount_paid <= 0 || $payment_method_id <= 0 || empty($payment_date)) {
    jsonResponse(false, 'Valid payment and required fields are required.');
}

$update = $conn->prepare('UPDATE fee_payments SET amount_paid = ?, payment_method_id = ?, transaction_id = ?, bank_name = ?, branch_name = ?, cheque_number = ?, reference_number = ?, payment_date = ?, remarks = ?, received_by = ?, status = ?, updated_at = NOW() WHERE id = ?');
$update->bind_param('idissssssssi', $amount_paid, $payment_method_id, $transaction_id, $bank_name, $branch_name, $cheque_number, $reference_number, $payment_date, $remarks, $received_by, $status, $id);

if ($update->execute()) {
    jsonResponse(true, 'Payment updated successfully.');
} else {
    jsonResponse(false, 'Payment update failed: ' . $conn->error);
}
