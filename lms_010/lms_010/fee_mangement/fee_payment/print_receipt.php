<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die('Invalid receipt ID.');
}

$stmt = $conn->prepare('SELECT fp.receipt_no, fp.amount_paid, fp.transaction_id, fp.bank_name, fp.branch_name, fp.cheque_number, fp.reference_number, fp.payment_date, fp.remarks, fp.received_by, fp.status, pm.method_name, fi.invoice_no, s.student_name, c.name AS class_name, se.name AS session_name FROM fee_payments fp LEFT JOIN payment_methods pm ON fp.payment_method_id = pm.id LEFT JOIN fee_invoices fi ON fp.invoice_id = fi.id LEFT JOIN students s ON fp.student_id = s.id LEFT JOIN classes c ON fi.class_id = c.id LEFT JOIN sessions se ON fi.session_id = se.id WHERE fp.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    die('Receipt not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Print Receipt <?= htmlspecialchars($payment['receipt_no']) ?></title>
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/css/vertical-layout-light/style.css">
  <style>
    body { padding: 20px; }
    .receipt-header { margin-bottom: 20px; }
  </style>
</head>
<body onload="window.print();">
  <div class="container">
    <div class="row receipt-header">
      <div class="col-md-6">
        <h2>Payment Receipt</h2>
        <p><strong>Receipt No:</strong> <?= htmlspecialchars($payment['receipt_no']) ?></p>
        <p><strong>Payment Date:</strong> <?= htmlspecialchars($payment['payment_date']) ?></p>
      </div>
      <div class="col-md-6 text-right">
        <p><strong>Status:</strong> <?= htmlspecialchars($payment['status']) ?></p>
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-md-6">
        <h5>Student Details</h5>
        <p><strong>Name:</strong> <?= htmlspecialchars($payment['student_name']) ?></p>
        <p><strong>Invoice:</strong> <?= htmlspecialchars($payment['invoice_no']) ?></p>
      </div>
      <div class="col-md-6">
        <h5>Academic Details</h5>
        <p><strong>Class:</strong> <?= htmlspecialchars($payment['class_name']) ?></p>
        <p><strong>Session:</strong> <?= htmlspecialchars($payment['session_name']) ?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <table class="table table-bordered">
          <tr><th>Payment Method</th><td><?= htmlspecialchars($payment['method_name']) ?></td></tr>
          <tr><th>Transaction ID</th><td><?= htmlspecialchars($payment['transaction_id']) ?></td></tr>
          <tr><th>Amount Paid</th><td>Rs <?= number_format($payment['amount_paid'], 2) ?></td></tr>
          <tr><th>Bank Name</th><td><?= htmlspecialchars($payment['bank_name']) ?></td></tr>
          <tr><th>Branch Name</th><td><?= htmlspecialchars($payment['branch_name']) ?></td></tr>
          <tr><th>Cheque/Ref No.</th><td><?= htmlspecialchars($payment['cheque_number'] ?: $payment['reference_number']) ?></td></tr>
          <tr><th>Received By</th><td><?= htmlspecialchars($payment['received_by']) ?></td></tr>
          <tr><th>Remarks</th><td><?= nl2br(htmlspecialchars($payment['remarks'])) ?></td></tr>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
