<?php
include '../../connect.php';
include '../fee_helpers.php';

$invoice_id = sanitizeInt($_POST['invoice_id'] ?? 0);
$student_id = sanitizeInt($_POST['student_id'] ?? 0);
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

if ($invoice_id <= 0 || $student_id <= 0 || $amount_paid <= 0 || $payment_method_id <= 0 || empty($payment_date)) {
    jsonResponse(false, 'Invoice, student, amount, payment method and payment date are required.');
}

$receipt_no = generateReceiptNo($conn);
$insert = $conn->prepare('INSERT INTO fee_payments (invoice_id, student_id, receipt_no, amount_paid, payment_method_id, transaction_id, bank_name, branch_name, cheque_number, reference_number, payment_date, remarks, received_by, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
$insert->bind_param('iisdisssssssss', $invoice_id, $student_id, $receipt_no, $amount_paid, $payment_method_id, $transaction_id, $bank_name, $branch_name, $cheque_number, $reference_number, $payment_date, $remarks, $received_by, $status);

if ($insert->execute()) {
    jsonResponse(true, 'Payment saved successfully.', ['payment_id' => $insert->insert_id]);
} else {
    jsonResponse(false, 'Payment save failed: ' . $conn->error);
}
