<?php
include '../../connect.php';
include '../fee_helpers.php';

$student_id = sanitizeInt(isset($_POST['student_id']) ? $_POST['student_id'] : 0);
$type = sanitizeText(isset($_POST['type']) ? $_POST['type'] : '');
$percentage = sanitizeFloat(isset($_POST['percentage']) ? $_POST['percentage'] : 0.00);
$fixed_amount = sanitizeFloat(isset($_POST['fixed_amount']) ? $_POST['fixed_amount'] : 0.00);
$reason = sanitizeText(isset($_POST['reason']) ? $_POST['reason'] : '');
$approved_by = sanitizeText(isset($_POST['approved_by']) ? $_POST['approved_by'] : '');
$status = sanitizeText(isset($_POST['status']) ? $_POST['status'] : 'Active');

if ($student_id <= 0 || empty($type)) {
    jsonResponse(false, 'Student and discount type are required.');
}

$insert = $conn->prepare('INSERT INTO fee_discounts (student_id, type, percentage, fixed_amount, reason, approved_by, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
$insert->bind_param('isddsss', $student_id, $type, $percentage, $fixed_amount, $reason, $approved_by, $status);

if ($insert->execute()) {
    jsonResponse(true, 'Discount created successfully.', ['id' => $insert->insert_id]);
} else {
    jsonResponse(false, 'Failed to create discount: ' . $conn->error);
}
