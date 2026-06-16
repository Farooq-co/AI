<?php
include_once __DIR__ . '/../../connect.php';
include_once __DIR__ . '/../fee_helpers.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_fine.php');
    exit;
}
$student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
$invoice_id = isset($_POST['invoice_id']) && $_POST['invoice_id'] !== '' ? intval($_POST['invoice_id']) : null;
$fine_type = isset($_POST['fine_type']) ? $conn->real_escape_string($_POST['fine_type']) : 'Fixed Fine';
$rate = isset($_POST['rate']) ? floatval($_POST['rate']) : 0.00;
$days_overdue = isset($_POST['days_overdue']) ? intval($_POST['days_overdue']) : 0;
$due_date = isset($_POST['due_date']) ? $conn->real_escape_string($_POST['due_date']) : date('Y-m-d');
// calculate amount
$calculated = 0.00;
if ($fine_type === 'Fixed Fine') {
    $calculated = $rate;
} elseif ($fine_type === 'Per Day Fine') {
    $calculated = $rate * $days_overdue;
} else {
    // percentage - apply to invoice if available
    if ($invoice_id) {
        $invRes = $conn->query("SELECT total_amount FROM fee_invoices WHERE id=".intval($invoice_id));
        $inv = $invRes ? $invRes->fetch_assoc() : null;
        $total = $inv ? floatval($inv['total_amount']) : 0.00;
        $calculated = ($rate/100.0) * $total;
    } else {
        $calculated = 0.00;
    }
}
// prepare and execute
$stmt = $conn->prepare("INSERT INTO fee_fines (invoice_id, student_id, fine_type, rate, days_overdue, calculated_amount, due_date, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'Active', NOW())");
$stmt->bind_param('iisdids', $invoice_id, $student_id, $fine_type, $rate, $days_overdue, $calculated, $due_date);
$ok = $stmt->execute();
if ($ok) {
    header('Location: list_fine.php');
    exit;
}
echo "Error saving: " . $conn->error;
