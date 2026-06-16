<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = sanitizeInt($_GET['id'] ?? 0);
if ($id <= 0) {
    die('Invalid waiver id');
}

$delete = $conn->prepare('DELETE FROM fee_waivers WHERE id = ?');
if (!$delete) {
    die('Prepare failed: ' . $conn->error);
}
$delete->bind_param('i', $id);
if ($delete->execute()) {
    header('Location: list_waiver.php');
    exit;
} else {
    die('Delete failed: ' . $conn->error);
}
