<?php
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../fee_helpers.php';

$id = sanitizeInt($_GET['id'] ?? 0);
if ($id <= 0) {
    die('Invalid ledger id');
}
$stmt = $conn->prepare('SELECT f.id, f.student_id, s.student_name, f.opening_balance, f.fee_charges, f.discount_total, f.scholarship_total, f.fine_total, f.payments_total, f.refunds_total, f.closing_balance, f.last_updated FROM fee_ledgers f LEFT JOIN students s ON f.student_id = s.id WHERE f.id = ? LIMIT 1');
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
if (!$row) {
    die('Ledger record not found');
}
?>
<div class="container mt-3">
  <h3>Ledger Details</h3>
  <table class="table table-bordered">
    <tr><th>ID</th><td><?= htmlspecialchars($row['id']) ?></td></tr>
    <tr><th>Student</th><td><?= htmlspecialchars($row['student_name']) ?></td></tr>
    <tr><th>Opening Balance</th><td><?= htmlspecialchars(formatMoney($row['opening_balance'])) ?></td></tr>
    <tr><th>Fee Charges</th><td><?= htmlspecialchars(formatMoney($row['fee_charges'])) ?></td></tr>
    <tr><th>Discount Total</th><td><?= htmlspecialchars(formatMoney($row['discount_total'])) ?></td></tr>
    <tr><th>Scholarship Total</th><td><?= htmlspecialchars(formatMoney($row['scholarship_total'])) ?></td></tr>
    <tr><th>Fine Total</th><td><?= htmlspecialchars(formatMoney($row['fine_total'])) ?></td></tr>
    <tr><th>Payments Total</th><td><?= htmlspecialchars(formatMoney($row['payments_total'])) ?></td></tr>
    <tr><th>Refunds Total</th><td><?= htmlspecialchars(formatMoney($row['refunds_total'])) ?></td></tr>
    <tr><th>Closing Balance</th><td><?= htmlspecialchars(formatMoney($row['closing_balance'])) ?></td></tr>
    <tr><th>Last Updated</th><td><?= htmlspecialchars($row['last_updated']) ?></td></tr>
  </table>
</div>
