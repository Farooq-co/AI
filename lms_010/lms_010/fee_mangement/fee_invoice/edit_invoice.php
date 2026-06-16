<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = sanitizeInt($_POST['id'] ?? 0);
$student_id = sanitizeInt($_POST['student_id'] ?? 0);
$class_id = sanitizeInt($_POST['class_id'] ?? 0);
$session_id = sanitizeInt($_POST['session_id'] ?? 0);
$package_id = sanitizeInt($_POST['package_id'] ?? 0);
$due_date = sanitizeText($_POST['due_date'] ?? '');
$discount = sanitizeFloat($_POST['discount'] ?? 0);
$scholarship = sanitizeFloat($_POST['scholarship'] ?? 0);
$fine = sanitizeFloat($_POST['fine'] ?? 0);
$status = sanitizeText($_POST['status'] ?? 'Pending');

if ($id <= 0 || $student_id <= 0 || $class_id <= 0 || $session_id <= 0 || empty($due_date)) {
    jsonResponse(false, 'Valid invoice, student, class, session and due date are required.');
}

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
$update = $conn->prepare('UPDATE fee_invoices SET student_id = ?, class_id = ?, session_id = ?, package_id = ?, subtotal = ?, discount = ?, scholarship = ?, fine = ?, total_amount = ?, due_date = ?, status = ?, updated_at = NOW() WHERE id = ?');
$update->bind_param('iiiiddddssi', $student_id, $class_id, $session_id, $package_id_value, $subtotal, $discount, $scholarship, $fine, $total_amount, $due_date, $status, $id);

if ($update->execute()) {
    jsonResponse(true, 'Invoice updated successfully.');
} else {
    jsonResponse(false, 'Invoice update failed: ' . $conn->error);
}
