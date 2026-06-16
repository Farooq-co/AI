<?php
include '../../connect.php';
include '../fee_helpers.php';

$search = trim($_GET['search'] ?? '');
$invoiceFilter = intval($_GET['invoice_id'] ?? 0);
$studentFilter = intval($_GET['student_id'] ?? 0);
$whereClauses = [];
$params = [];
$types = '';

if ($search !== '') {
    $whereClauses[] = '(fp.receipt_no LIKE ? OR s.student_name LIKE ? OR fi.invoice_no LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = &$like;
    $params[] = &$like;
    $params[] = &$like;
    $types .= 'sss';
}
if ($invoiceFilter > 0) {
    $whereClauses[] = 'fp.invoice_id = ?';
    $params[] = &$invoiceFilter;
    $types .= 'i';
}
if ($studentFilter > 0) {
    $whereClauses[] = 'fp.student_id = ?';
    $params[] = &$studentFilter;
    $types .= 'i';
}

$query = 'SELECT fp.id, fp.receipt_no, fp.payment_date, fp.amount_paid, fp.status, s.student_name, fi.invoice_no, pm.method_name FROM fee_payments fp LEFT JOIN students s ON fp.student_id = s.id LEFT JOIN fee_invoices fi ON fp.invoice_id = fi.id LEFT JOIN payment_methods pm ON fp.payment_method_id = pm.id';
if (!empty($whereClauses)) {
    $query .= ' WHERE ' . implode(' AND ', $whereClauses);
}
$query .= ' ORDER BY fp.payment_date DESC';
$stmt = $conn->prepare($query);
if ($stmt && !empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$invoices = [];
$invoiceRes = $conn->query('SELECT id, invoice_no FROM fee_invoices ORDER BY created_at DESC');
while ($row = $invoiceRes->fetch_assoc()) {
    $invoices[] = $row;
}
$students = [];
$studentRes = $conn->query('SELECT id, student_name FROM students WHERE status = "Active" ORDER BY student_name');
while ($row = $studentRes->fetch_assoc()) {
    $students[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Fee Payment History</title>
  <?php include '../../parts/links1.php'; ?>
  <?php include '../../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
              <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search receipt, student, invoice" value="<?= htmlspecialchars($search) ?>">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button" onclick="applyFilters()">Search</button>
                </div>
              </div>
            </div>
            <div class="col-md-4 text-right">
              <a href="list_payment.php" class="btn btn-secondary">Back to Payments</a>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <select id="invoiceFilter" class="form-control" onchange="applyFilters()">
                <option value="0">All Invoices</option>
                <?php foreach ($invoices as $invoice): ?>
                  <option value="<?= $invoice['id'] ?>" <?= $invoiceFilter === intval($invoice['id']) ? 'selected' : '' ?>><?= htmlspecialchars($invoice['invoice_no']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <select id="studentFilter" class="form-control" onchange="applyFilters()">
                <option value="0">All Students</option>
                <?php foreach ($students as $student): ?>
                  <option value="<?= $student['id'] ?>" <?= $studentFilter === intval($student['id']) ? 'selected' : '' ?>><?= htmlspecialchars($student['student_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Payment History</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Receipt No</th>
                          <th>Invoice</th>
                          <th>Student</th>
                          <th>Method</th>
                          <th>Amount</th>
                          <th>Date</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                          <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                              <td><?= htmlspecialchars($row['receipt_no']) ?></td>
                              <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                              <td><?= htmlspecialchars($row['student_name']) ?></td>
                              <td><?= htmlspecialchars($row['method_name']) ?></td>
                              <td><?= number_format($row['amount_paid'], 2) ?></td>
                              <td><?= htmlspecialchars($row['payment_date']) ?></td>
                              <td><span class="badge <?= $row['status'] === 'Completed' ? 'badge-success' : ($row['status'] === 'Failed' ? 'badge-danger' : 'badge-warning') ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr><td colspan="7" class="text-center">No payment history found.</td></tr>
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
  <script>
    function applyFilters() {
      const search = document.getElementById('searchInput').value.trim();
      const invoiceId = document.getElementById('invoiceFilter').value;
      const studentId = document.getElementById('studentFilter').value;
      let url = 'payment_history.php?';
      if (search) url += 'search=' + encodeURIComponent(search) + '&';
      if (invoiceId && invoiceId !== '0') url += 'invoice_id=' + encodeURIComponent(invoiceId) + '&';
      if (studentId && studentId !== '0') url += 'student_id=' + encodeURIComponent(studentId) + '&';
      window.location.href = url;
    }
  </script>
</body>
</html>
