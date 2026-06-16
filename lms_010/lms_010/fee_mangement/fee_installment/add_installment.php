<?php
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../fee_helpers.php';

// Accept POST to create a new installment
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method.');
}

$invoice_id = sanitizeInt($_POST['invoice_id'] ?? 0);
$installment_no = sanitizeText($_POST['installment_no'] ?? '');
$due_date = sanitizeText($_POST['due_date'] ?? '');
$amount = sanitizeFloat($_POST['amount'] ?? 0.00);
$paid_amount = sanitizeFloat($_POST['paid_amount'] ?? 0.00);
$remaining = max(0.00, $amount - $paid_amount);
$status = sanitizeText($_POST['status'] ?? 'Pending');

if ($invoice_id <= 0 || $amount <= 0 || empty($installment_no)) {
    jsonResponse(false, 'Invoice, installment number, and amount are required.');
}

// Verify invoice exists
$stmt = $conn->prepare('SELECT id FROM fee_invoices WHERE id = ? AND COALESCE(deleted_at, "") = "" LIMIT 1');
if (!$stmt) jsonResponse(false, 'Prepare failed: ' . $conn->error);
$stmt->bind_param('i', $invoice_id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res->fetch_assoc()) {
    $stmt->close();
    jsonResponse(false, 'Invoice not found.');
}
$stmt->close();

$ins = $conn->prepare('INSERT INTO fee_installments (invoice_id, installment_no, due_date, amount, paid_amount, remaining_amount, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
if (!$ins) jsonResponse(false, 'Prepare failed: ' . $conn->error);
$ins->bind_param('issddds', $invoice_id, $installment_no, $due_date, $amount, $paid_amount, $remaining, $status);
if ($ins->execute()) {
    $id = $ins->insert_id;
    $ins->close();
    jsonResponse(true, 'Installment created.', ['id' => $id]);
} else {
    $err = $ins->error ?: $conn->error;
    $ins->close();
    jsonResponse(false, 'Failed to create installment: ' . $err);
}
