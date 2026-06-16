<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = sanitizeInt(isset($_POST['id']) ? $_POST['id'] : 0);
$student_id = sanitizeInt(isset($_POST['student_id']) ? $_POST['student_id'] : 0);
$type = sanitizeText(isset($_POST['type']) ? $_POST['type'] : '');
$percentage = sanitizeFloat(isset($_POST['percentage']) ? $_POST['percentage'] : 0.00);
$fixed_amount = sanitizeFloat(isset($_POST['fixed_amount']) ? $_POST['fixed_amount'] : 0.00);
$reason = sanitizeText(isset($_POST['reason']) ? $_POST['reason'] : '');
$approved_by = sanitizeText(isset($_POST['approved_by']) ? $_POST['approved_by'] : '');
$status = sanitizeText(isset($_POST['status']) ? $_POST['status'] : 'Active');

if ($id <= 0 || $student_id <= 0 || empty($type)) {
    jsonResponse(false, 'Valid discount and student are required.');
}

$update = $conn->prepare('UPDATE fee_discounts SET student_id = ?, type = ?, percentage = ?, fixed_amount = ?, reason = ?, approved_by = ?, status = ?, updated_at = NOW() WHERE id = ?');
$update->bind_param('isddsssi', $student_id, $type, $percentage, $fixed_amount, $reason, $approved_by, $status, $id);

if ($update->execute()) {
    jsonResponse(true, 'Discount updated successfully.');
} else {
    jsonResponse(false, 'Failed to update discount: ' . $conn->error);
}
