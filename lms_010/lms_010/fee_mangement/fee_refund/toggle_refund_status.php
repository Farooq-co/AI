<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = sanitizeInt($_GET['id'] ?? 0);
$status = sanitizeText($_GET['status'] ?? '');
if ($id <= 0 || $status === '') {
    die('Invalid request');
}

$update = $conn->prepare('UPDATE fee_refunds SET status = ?, updated_at = NOW() WHERE id = ?');
if (!$update) {
    die('Prepare failed: ' . $conn->error);
}
$update->bind_param('si', $status, $id);
if ($update->execute()) {
    header('Location: list_refund.php');
    exit;
} else {
    die('Status update failed: ' . $conn->error);
}
