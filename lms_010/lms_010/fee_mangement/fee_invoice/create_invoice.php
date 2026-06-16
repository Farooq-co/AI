<?php
include '../../connect.php';
include '../fee_helpers.php';

$student_id = sanitizeInt($_POST['student_id'] ?? 0);
$class_id = sanitizeInt($_POST['class_id'] ?? 0);
$session_id = sanitizeInt($_POST['session_id'] ?? 0);
$package_id = sanitizeInt($_POST['package_id'] ?? 0);
$due_date = sanitizeText($_POST['due_date'] ?? '');
$discount = sanitizeFloat($_POST['discount'] ?? 0);
$scholarship = sanitizeFloat($_POST['scholarship'] ?? 0);
$fine = sanitizeFloat($_POST['fine'] ?? 0);
$status = sanitizeText($_POST['status'] ?? 'Pending');

if ($student_id <= 0 || $class_id <= 0 || $session_id <= 0 || empty($due_date)) {
    jsonResponse(false, 'Student, class, session and due date are required.');
}

$invoice_no = generateInvoiceNo($conn);
$subtotal = 0.00;

$stmt = $conn->prepare('SELECT SUM(amount) FROM fee_structures WHERE class_id = ? AND status = "Active"');
$stmt->bind_param('i', $class_id);
$stmt->execute();
$stmt->bind_result($subtotal);
$stmt->fetch();
$stmt->close();

if ($package_id > 0) {
    $pkgStmt = $conn->prepare('SELECT total_amount FROM fee_packages WHERE id = ? AND status = "Active"');
    $pkgStmt->bind_param('i', $package_id);
    $pkgStmt->execute();
    $pkgStmt->bind_result($packageAmount);
    if ($pkgStmt->fetch()) {
        $subtotal += floatval($packageAmount);
    }
    $pkgStmt->close();
}

$total_amount = max(0.00, $subtotal - $discount - $scholarship + $fine);

$package_id_value = $package_id > 0 ? $package_id : null;
$insert = $conn->prepare('INSERT INTO fee_invoices (invoice_no, student_id, class_id, session_id, package_id, subtotal, discount, scholarship, fine, total_amount, due_date, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
$insert->bind_param('siiiiddddss', $invoice_no, $student_id, $class_id, $session_id, $package_id_value, $subtotal, $discount, $scholarship, $fine, $total_amount, $due_date, $status);

if ($insert->execute()) {
    $invoice_id = $insert->insert_id;
    $itemStmt = $conn->prepare('INSERT INTO invoice_items (invoice_id, fee_head_id, fee_type_id, description, amount, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    $structureStmt = $conn->prepare('SELECT fee_head_id, fee_type_id, amount FROM fee_structures WHERE class_id = ? AND status = "Active"');
    $structureStmt->bind_param('i', $class_id);
    $structureStmt->execute();
    $structureStmt->bind_result($fee_head_id, $fee_type_id, $feeAmount);
    while ($structureStmt->fetch()) {
        $description = 'Fee for head ' . $fee_head_id . ' / type ' . $fee_type_id;
        $itemStmt->bind_param('iiisd', $invoice_id, $fee_head_id, $fee_type_id, $description, $feeAmount);
        $itemStmt->execute();
    }
    $structureStmt->close();
    $itemStmt->close();

    jsonResponse(true, 'Invoice created successfully.', ['invoice_id' => $invoice_id]);
} else {
    jsonResponse(false, 'Invoice creation failed: ' . $conn->error);
}
