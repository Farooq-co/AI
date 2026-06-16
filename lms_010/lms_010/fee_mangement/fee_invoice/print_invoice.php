<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die('Invalid invoice ID.');
}

$stmt = $conn->prepare('SELECT fi.invoice_no, fi.student_id, s.student_name, s.father_name, fi.class_id, c.name AS class_name, fi.session_id, se.name AS session_name, fi.package_id, p.name AS package_name, fi.subtotal, fi.discount, fi.scholarship, fi.fine, fi.total_amount, fi.due_date, fi.status, fi.created_at FROM fee_invoices fi LEFT JOIN students s ON fi.student_id = s.id LEFT JOIN classes c ON fi.class_id = c.id LEFT JOIN sessions se ON fi.session_id = se.id LEFT JOIN fee_packages p ON fi.package_id = p.id WHERE fi.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();
$stmt->close();

if (!$invoice) {
    die('Invoice not found.');
}

$itemStmt = $conn->prepare('SELECT ii.description, ii.amount, fh.name AS fee_head, ft.name AS fee_type FROM invoice_items ii LEFT JOIN fee_heads fh ON ii.fee_head_id = fh.id LEFT JOIN fee_types ft ON ii.fee_type_id = ft.id WHERE ii.invoice_id = ?');
$itemStmt->bind_param('i', $id);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();
$items = $itemResult->fetch_all(MYSQLI_ASSOC);
$itemStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Print Invoice <?= htmlspecialchars($invoice['invoice_no']) ?></title>
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/css/vertical-layout-light/style.css">
  <style>
    body { padding: 20px; }
    .invoice-header { margin-bottom: 20px; }
    .invoice-summary .table th, .invoice-summary .table td { border: none; }
  </style>
</head>
<body onload="window.print();">
  <div class="container">
    <div class="row invoice-header">
      <div class="col-md-6">
        <h2>Invoice</h2>
        <p><strong>Invoice No:</strong> <?= htmlspecialchars($invoice['invoice_no']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($invoice['created_at']))) ?></p>
      </div>
      <div class="col-md-6 text-right">
        <p><strong>Status:</strong> <?= htmlspecialchars($invoice['status']) ?></p>
        <p><strong>Due Date:</strong> <?= htmlspecialchars($invoice['due_date']) ?></p>
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-md-6">
        <h5>Student Details</h5>
        <p><strong>Name:</strong> <?= htmlspecialchars($invoice['student_name']) ?></p>
        <p><strong>Father Name:</strong> <?= htmlspecialchars($invoice['father_name']) ?></p>
        <p><strong>Class:</strong> <?= htmlspecialchars($invoice['class_name']) ?></p>
        <p><strong>Session:</strong> <?= htmlspecialchars($invoice['session_name']) ?></p>
      </div>
      <div class="col-md-6">
        <h5>Fee Summary</h5>
        <p><strong>Package:</strong> <?= htmlspecialchars($invoice['package_name']) ?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Description</th>
              <th>Fee Head</th>
              <th>Fee Type</th>
              <th>Amount (Rs)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['description']) ?></td>
                <td><?= htmlspecialchars($item['fee_head']) ?></td>
                <td><?= htmlspecialchars($item['fee_type']) ?></td>
                <td><?= number_format($item['amount'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row invoice-summary">
      <div class="col-md-6"></div>
      <div class="col-md-6">
        <table class="table">
          <tr>
            <th>Subtotal</th>
            <td>Rs <?= number_format($invoice['subtotal'], 2) ?></td>
          </tr>
          <tr>
            <th>Discount</th>
            <td>Rs <?= number_format($invoice['discount'], 2) ?></td>
          </tr>
          <tr>
            <th>Scholarship</th>
            <td>Rs <?= number_format($invoice['scholarship'], 2) ?></td>
          </tr>
          <tr>
            <th>Fine</th>
            <td>Rs <?= number_format($invoice['fine'], 2) ?></td>
          </tr>
          <tr>
            <th>Total Amount</th>
            <td>Rs <?= number_format($invoice['total_amount'], 2) ?></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
