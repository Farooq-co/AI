<?php
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../fee_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(false, 'Invalid request method');

$id = sanitizeInt($_POST['id'] ?? 0);
$new = sanitizeText($_POST['status'] ?? '');
if ($id <= 0 || $new === '') jsonResponse(false, 'Invalid parameters');

$stmt = $conn->prepare('UPDATE fee_installments SET status = ?, updated_at = NOW() WHERE id = ? AND COALESCE(deleted_at, "") = ""');
if (!$stmt) jsonResponse(false, 'Prepare failed: ' . $conn->error);
$stmt->bind_param('si', $new, $id);
if ($stmt->execute()) {
    $stmt->close();
    jsonResponse(true, 'Status updated.');
} else {
    $err = $stmt->error ?: $conn->error;
    $stmt->close();
    jsonResponse(false, 'Update failed: ' . $err);
}
