<?php
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../fee_helpers.php';

// GET to fetch details, POST to update
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = sanitizeInt($_GET['id'] ?? 0);
    if ($id <= 0) jsonResponse(false, 'Invalid installment id');
    $stmt = $conn->prepare('SELECT id, invoice_id, installment_no, due_date, amount, paid_amount, remaining_amount, status FROM fee_installments WHERE id = ? AND COALESCE(deleted_at, "") = "" LIMIT 1');
    if (!$stmt) jsonResponse(false, 'Prepare failed: ' . $conn->error);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    if (!$row) jsonResponse(false, 'Installment not found');
    jsonResponse(true, 'OK', ['installment' => $row]);
}

// POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = sanitizeInt($_POST['id'] ?? 0);
    $due_date = sanitizeText($_POST['due_date'] ?? '');
    $amount = sanitizeFloat($_POST['amount'] ?? 0.00);
    $paid_amount = sanitizeFloat($_POST['paid_amount'] ?? 0.00);
    $remaining = max(0.00, $amount - $paid_amount);
    $status = sanitizeText($_POST['status'] ?? 'Pending');

    if ($id <= 0 || $amount <= 0) jsonResponse(false, 'Invalid data');

    $stmt = $conn->prepare('UPDATE fee_installments SET due_date = ?, amount = ?, paid_amount = ?, remaining_amount = ?, status = ?, updated_at = NOW() WHERE id = ? AND COALESCE(deleted_at, "") = ""');
    if (!$stmt) jsonResponse(false, 'Prepare failed: ' . $conn->error);
    $stmt->bind_param('sddssi', $due_date, $amount, $paid_amount, $remaining, $status, $id);
    if ($stmt->execute()) {
        $affected = $stmt->affected_rows;
        $stmt->close();
        jsonResponse(true, 'Installment updated.', ['affected' => $affected]);
    } else {
        $err = $stmt->error ?: $conn->error;
        $stmt->close();
        jsonResponse(false, 'Update failed: ' . $err);
    }
}

jsonResponse(false, 'Invalid request method');
