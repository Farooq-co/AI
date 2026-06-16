<?php
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../fee_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$id = sanitizeInt($_POST['id'] ?? 0);
if ($id <= 0) jsonResponse(false, 'Invalid id');

// Soft delete: set deleted_at
$stmt = $conn->prepare('UPDATE fee_installments SET deleted_at = NOW(), updated_at = NOW() WHERE id = ? AND COALESCE(deleted_at, "") = ""');
if (!$stmt) jsonResponse(false, 'Prepare failed: ' . $conn->error);
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    $affected = $stmt->affected_rows;
    $stmt->close();
    jsonResponse(true, 'Installment deleted.', ['affected' => $affected]);
} else {
    $err = $stmt->error ?: $conn->error;
    $stmt->close();
    jsonResponse(false, 'Delete failed: ' . $err);
}
