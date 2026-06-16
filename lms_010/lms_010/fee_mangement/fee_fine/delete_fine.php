<?php
include_once __DIR__ . '/../../connect.php';
include_once __DIR__ . '/../fee_helpers.php';
if (!isset($_GET['id'])) {
    header('Location: list_fine.php');
    exit;
}
$id = intval($_GET['id']);
$res = $conn->query("DELETE FROM fee_fines WHERE id={$id}");
header('Location: list_fine.php');
exit;
