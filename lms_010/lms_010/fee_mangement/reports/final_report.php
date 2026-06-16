<?php
include '../../connect.php';
include '../fee_helpers.php';

// Invoice summary
$invoiceSummary = ['total_invoices' => 0, 'total_amount' => 0.0, 'total_discount' => 0.0, 'total_scholarship' => 0.0, 'total_fine' => 0.0];
if ($res = $conn->query("SELECT COUNT(*) AS total_invoices, COALESCE(SUM(total_amount),0) AS total_amount, COALESCE(SUM(discount),0) AS total_discount, COALESCE(SUM(scholarship),0) AS total_scholarship, COALESCE(SUM(fine),0) AS total_fine FROM fee_invoices")) {
    $invoiceSummary = $res->fetch_assoc();
    $res->free();
}

// Payment summary
$paymentSummary = ['total_payments' => 0, 'total_paid' => 0.0];
if ($res = $conn->query("SELECT COUNT(*) AS total_payments, COALESCE(SUM(amount_paid),0) AS total_paid FROM fee_payments WHERE status IN ('Verified', 'Completed')")) {
    $paymentSummary = $res->fetch_assoc();
    $res->free();
}

// Refund summary
$refundSummary = ['total_refunds' => 0, 'total_refunded' => 0.0];
if ($res = $conn->query("SELECT COUNT(*) AS total_refunds, COALESCE(SUM(refund_amount),0) AS total_refunded FROM fee_refunds")) {
    $refundSummary = $res->fetch_assoc();
    $res->free();
}

// Waiver summary
$waiverSummary = ['total_waivers' => 0, 'total_waived' => 0.0];
if ($res = $conn->query("SELECT COUNT(*) AS total_waivers, COALESCE(SUM(waiver_amount),0) AS total_waived FROM fee_waivers")) {
    $waiverSummary = $res->fetch_assoc();
    $res->free();
}

// Outstanding balance and overdue summary
$outstandingSummary = ['outstanding_invoices' => 0, 'outstanding_amount' => 0.0];
$overdueSummary = ['overdue_invoices' => 0, 'overdue_amount' => 0.0];
$outstandingSql = "SELECT COUNT(*) AS outstanding_invoices, COALESCE(SUM(fi.total_amount - COALESCE(p.total_paid,0)),0) AS outstanding_amount FROM fee_invoices fi LEFT JOIN (SELECT invoice_id, SUM(amount_paid) AS total_paid FROM fee_payments WHERE status IN ('Verified', 'Completed') GROUP BY invoice_id) p ON fi.id = p.invoice_id WHERE fi.status <> 'Paid'";
if ($res = $conn->query($outstandingSql)) {
    $outstandingSummary = $res->fetch_assoc();
    $res->free();
}
$overdueSql = "SELECT COUNT(*) AS overdue_invoices, COALESCE(SUM(fi.total_amount - COALESCE(p.total_paid,0)),0) AS overdue_amount FROM fee_invoices fi LEFT JOIN (SELECT invoice_id, SUM(amount_paid) AS total_paid FROM fee_payments WHERE status IN ('Verified', 'Completed') GROUP BY invoice_id) p ON fi.id = p.invoice_id WHERE fi.status <> 'Paid' AND fi.due_date < CURDATE()";
if ($res = $conn->query($overdueSql)) {
    $overdueSummary = $res->fetch_assoc();
    $res->free();
}

// Top overdue invoices
$topOverdue = [];
$topOverdueSql = "SELECT fi.invoice_no, s.student_name, c.name AS class_name, fi.due_date, fi.total_amount, COALESCE(p.total_paid,0) AS total_paid, (fi.total_amount - COALESCE(p.total_paid,0)) AS balance_due FROM fee_invoices fi LEFT JOIN students s ON fi.student_id = s.id LEFT JOIN classes c ON fi.class_id = c.id LEFT JOIN (SELECT invoice_id, SUM(amount_paid) AS total_paid FROM fee_payments WHERE status IN ('Verified', 'Completed') GROUP BY invoice_id) p ON fi.id = p.invoice_id WHERE fi.status <> 'Paid' AND fi.due_date < CURDATE() ORDER BY balance_due DESC LIMIT 10";
if ($res = $conn->query($topOverdueSql)) {
    while ($row = $res->fetch_assoc()) {
        $topOverdue[] = $row;
    }
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Final Fee Summary Report</title>
  <?php include '../../parts/links1.php'; ?>
  <?php include '../../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .report-card { border: 1px solid #e3e6f0; border-radius: .35rem; padding: 1rem; background: #fff; }
    .report-card h5 { margin-bottom: .75rem; }
    .report-card .value { font-size: 1.6rem; font-weight: 700; }
    .report-card .label { color: #6c757d; }
    .report-summary { margin-bottom: 1.5rem; }
    .table-report th, .table-report td { vertical-align: middle; }
  </style>
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
          <div class="row mb-3">
            <div class="col-md-8">
              <h3>Final Fee Summary Report</h3>
              <p class="text-muted">A consolidated fee summary for invoices, payments, refunds, waivers, and overdue balances.</p>
            </div>
            <div class="col-md-4 text-right">
              <button class="btn btn-success" onclick="window.print()"><i class="bi bi-printer"></i> Print Report</button>
            </div>
          </div>

          <div class="row report-summary">
            <div class="col-md-4 mb-3">
              <div class="report-card shadow-sm">
                <h5>Invoices</h5>
                <div class="value"><?= htmlspecialchars($invoiceSummary['total_invoices']) ?></div>
                <div class="label">Total invoices</div>
                <div class="mt-2">Total billed: Rs <?= number_format($invoiceSummary['total_amount'], 2) ?></div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="report-card shadow-sm">
                <h5>Payments</h5>
                <div class="value"><?= htmlspecialchars($paymentSummary['total_payments']) ?></div>
                <div class="label">Completed payments</div>
                <div class="mt-2">Total paid: Rs <?= number_format($paymentSummary['total_paid'], 2) ?></div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="report-card shadow-sm">
                <h5>Balances</h5>
                <div class="value"><?= htmlspecialchars($outstandingSummary['outstanding_invoices']) ?></div>
                <div class="label">Open invoices</div>
                <div class="mt-2">Outstanding: Rs <?= number_format($outstandingSummary['outstanding_amount'], 2) ?></div>
              </div>
            </div>
          </div>

          <div class="row report-summary">
            <div class="col-md-4 mb-3">
              <div class="report-card shadow-sm">
                <h5>Waivers</h5>
                <div class="value"><?= htmlspecialchars($waiverSummary['total_waivers']) ?></div>
                <div class="label">Waivers recorded</div>
                <div class="mt-2">Total waived: Rs <?= number_format($waiverSummary['total_waived'], 2) ?></div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="report-card shadow-sm">
                <h5>Refunds</h5>
                <div class="value"><?= htmlspecialchars($refundSummary['total_refunds']) ?></div>
                <div class="label">Refund records</div>
                <div class="mt-2">Total refunded: Rs <?= number_format($refundSummary['total_refunded'], 2) ?></div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="report-card shadow-sm">
                <h5>Overdue</h5>
                <div class="value"><?= htmlspecialchars($overdueSummary['overdue_invoices']) ?></div>
                <div class="label">Overdue invoices</div>
                <div class="mt-2">Overdue amount: Rs <?= number_format($overdueSummary['overdue_amount'], 2) ?></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Top Overdue Invoices</h4>
                  <p class="card-description">Largest unpaid balances with past due dates.</p>
                  <div class="table-responsive">
                    <table class="table table-bordered table-report">
                      <thead>
                        <tr>
                          <th>Invoice No</th>
                          <th>Student</th>
                          <th>Class</th>
                          <th>Due Date</th>
                          <th>Total</th>
                          <th>Paid</th>
                          <th>Balance</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($topOverdue)): ?>
                          <?php foreach ($topOverdue as $row): ?>
                            <tr>
                              <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                              <td><?= htmlspecialchars($row['student_name']) ?></td>
                              <td><?= htmlspecialchars($row['class_name']) ?></td>
                              <td><?= htmlspecialchars($row['due_date']) ?></td>
                              <td>Rs <?= number_format($row['total_amount'], 2) ?></td>
                              <td>Rs <?= number_format($row['total_paid'], 2) ?></td>
                              <td>Rs <?= number_format($row['balance_due'], 2) ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr><td colspan="7" class="text-center">No overdue invoices found.</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
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
