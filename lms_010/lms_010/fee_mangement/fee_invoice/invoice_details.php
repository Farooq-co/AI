<?php
include '../../connect.php';
include '../fee_helpers.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die('Invalid invoice ID.');
}

$stmt = $conn->prepare('SELECT fi.invoice_no, fi.student_id, s.student_name, fi.class_id, c.name AS class_name, fi.session_id, se.name AS session_name, fi.package_id, p.name AS package_name, fi.subtotal, fi.discount, fi.scholarship, fi.fine, fi.total_amount, fi.due_date, fi.status, fi.created_at FROM fee_invoices fi LEFT JOIN students s ON fi.student_id = s.id LEFT JOIN classes c ON fi.class_id = c.id LEFT JOIN sessions se ON fi.session_id = se.id LEFT JOIN fee_packages p ON fi.package_id = p.id WHERE fi.id = ?');
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

$paymentStmt = $conn->prepare('SELECT fp.receipt_no, fp.amount_paid, pm.method_name, fp.payment_date, fp.status FROM fee_payments fp LEFT JOIN payment_methods pm ON fp.payment_method_id = pm.id WHERE fp.invoice_id = ? ORDER BY fp.payment_date DESC');
$paymentStmt->bind_param('i', $id);
$paymentStmt->execute();
$paymentResult = $paymentStmt->get_result();
$payments = $paymentResult->fetch_all(MYSQLI_ASSOC);
$paymentStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Invoice Details</title>
  <?php include '../../parts/links1.php'; ?>
  <?php include '../../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <div class="container-scroller">
    <?php include '../../parts/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include '../../parts/setting.php'; ?>
      <?php include '../../parts/right_sidebar.php'; ?>
      <?php include '../../parts/left_sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Invoice Details</h4>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <p><strong>Invoice No:</strong> <?= htmlspecialchars($invoice['invoice_no']) ?></p>
                      <p><strong>Student:</strong> <?= htmlspecialchars($invoice['student_name']) ?></p>
                      <p><strong>Class:</strong> <?= htmlspecialchars($invoice['class_name']) ?></p>
                      <p><strong>Session:</strong> <?= htmlspecialchars($invoice['session_name']) ?></p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Package:</strong> <?= htmlspecialchars($invoice['package_name']) ?></p>
                      <p><strong>Due Date:</strong> <?= htmlspecialchars($invoice['due_date']) ?></p>
                      <p><strong>Status:</strong> <?= htmlspecialchars($invoice['status']) ?></p>
                      <p><strong>Created At:</strong> <?= htmlspecialchars($invoice['created_at']) ?></p>
                    </div>
                  </div>
                  <h5>Invoice Items</h5>
                  <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Description</th>
                          <th>Fee Head</th>
                          <th>Fee Type</th>
                          <th>Amount</th>
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
                  <h5>Summary</h5>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <p><strong>Subtotal:</strong> Rs <?= number_format($invoice['subtotal'], 2) ?></p>
                      <p><strong>Discount:</strong> Rs <?= number_format($invoice['discount'], 2) ?></p>
                      <p><strong>Scholarship:</strong> Rs <?= number_format($invoice['scholarship'], 2) ?></p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Fine:</strong> Rs <?= number_format($invoice['fine'], 2) ?></p>
                      <p><strong>Total Amount:</strong> Rs <?= number_format($invoice['total_amount'], 2) ?></p>
                    </div>
                  </div>
                  <h5>Payments</h5>
                  <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Receipt No</th>
                          <th>Amount Paid</th>
                          <th>Payment Method</th>
                          <th>Payment Date</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($payments)): ?>
                          <?php foreach ($payments as $payment): ?>
                            <tr>
                              <td><?= htmlspecialchars($payment['receipt_no']) ?></td>
                              <td><?= number_format($payment['amount_paid'], 2) ?></td>
                              <td><?= htmlspecialchars($payment['method_name']) ?></td>
                              <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                              <td><?= htmlspecialchars($payment['status']) ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr><td colspan="5" class="text-center">No payments recorded.</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                  <a href="print_invoice.php?id=<?= $id ?>" class="btn btn-primary" target="_blank">Print Invoice</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php include '../../parts/footer.php'; ?>
      </div>
    </div>
  </div>
  <?php include '../../parts/links2.php'; ?>
</body>
</html>
