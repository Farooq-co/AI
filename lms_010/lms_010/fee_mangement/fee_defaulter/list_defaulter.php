<?php
include '../../connect.php';
include '../fee_helpers.php';

$search = trim($_GET['search'] ?? '');
$classFilter = intval($_GET['class_id'] ?? 0);
$sessionFilter = intval($_GET['session_id'] ?? 0);
$statusFilter = trim($_GET['status'] ?? '');
$whereClauses = ["fi.due_date <= CURDATE()"];
$params = [];
$types = '';

if ($search !== '') {
    $whereClauses[] = '(fi.invoice_no LIKE ? OR s.student_name LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = &$like;
    $params[] = &$like;
    $types .= 'ss';
}
if ($classFilter > 0) {
    $whereClauses[] = 'fi.class_id = ?';
    $params[] = &$classFilter;
    $types .= 'i';
}
if ($sessionFilter > 0) {
    $whereClauses[] = 'fi.session_id = ?';
    $params[] = &$sessionFilter;
    $types .= 'i';
}
if ($statusFilter !== '') {
    $whereClauses[] = 'fi.status = ?';
    $params[] = &$statusFilter;
    $types .= 's';
}

$query = "SELECT fi.id, fi.invoice_no, s.student_name, c.name AS class_name, se.name AS session_name, fi.total_amount, COALESCE(SUM(fp.amount_paid), 0) AS total_paid, (fi.total_amount - COALESCE(SUM(fp.amount_paid), 0)) AS balance_due, fi.due_date, fi.status FROM fee_invoices fi LEFT JOIN students s ON fi.student_id = s.id LEFT JOIN classes c ON fi.class_id = c.id LEFT JOIN sessions se ON fi.session_id = se.id LEFT JOIN fee_payments fp ON fi.id = fp.invoice_id AND fp.status IN ('Verified', 'Completed')";
if (!empty($whereClauses)) {
    $query .= ' WHERE ' . implode(' AND ', $whereClauses);
}
$query .= ' GROUP BY fi.id HAVING balance_due > 0 ORDER BY fi.due_date ASC, fi.invoice_no DESC';

$stmt = $conn->prepare($query);
if ($stmt && !empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$classOptions = getClassOptions($conn);
$sessionOptions = getSessionOptions($conn);
$invoiceStatuses = getInvoiceStatusOptions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fee Defaulter List</title>
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
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search invoice or student" value="<?= htmlspecialchars($search) ?>">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button" onclick="applyFilters()">Search</button>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-right">
              <a href="../fee_invoice/list_invoice.php" class="btn btn-secondary">Back to Invoices</a>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3">
              <select id="classFilter" class="form-control" onchange="applyFilters()">
                <option value="0">All Classes</option>
                <?php foreach ($classOptions as $class): ?>
                  <option value="<?= $class['id'] ?>" <?= $classFilter === intval($class['id']) ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <select id="sessionFilter" class="form-control" onchange="applyFilters()">
                <option value="0">All Sessions</option>
                <?php foreach ($sessionOptions as $session): ?>
                  <option value="<?= $session['id'] ?>" <?= $sessionFilter === intval($session['id']) ? 'selected' : '' ?>><?= htmlspecialchars($session['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <select id="statusFilter" class="form-control" onchange="applyFilters()">
                <option value="">All Invoice Statuses</option>
                <?php foreach ($invoiceStatuses as $status): ?>
                  <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 text-right">
              <button class="btn btn-success" onclick="window.print()">Print Defaulter List</button>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Defaulter Report</h4>
                  <p class="card-description">Invoices with due date passed and outstanding balance.</p>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Invoice No</th>
                          <th>Student</th>
                          <th>Class</th>
                          <th>Session</th>
                          <th>Total Due</th>
                          <th>Paid</th>
                          <th>Balance</th>
                          <th>Due Date</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                          <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                              <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                              <td><?= htmlspecialchars($row['student_name']) ?></td>
                              <td><?= htmlspecialchars($row['class_name']) ?></td>
                              <td><?= htmlspecialchars($row['session_name']) ?></td>
                              <td>Rs <?= number_format($row['total_amount'], 2) ?></td>
                              <td>Rs <?= number_format($row['total_paid'], 2) ?></td>
                              <td>Rs <?= number_format($row['balance_due'], 2) ?></td>
                              <td><?= htmlspecialchars(date('Y-m-d', strtotime($row['due_date']))) ?></td>
                              <td><span class="badge <?= $row['status'] === 'Overdue' ? 'badge-danger' : 'badge-warning' ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                              <td>
                                <a class="btn btn-sm btn-primary" href="../fee_invoice/invoice_details.php?id=<?= $row['id'] ?>">View</a>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="10" class="text-center">No overdue defaulters found.</td>
                          </tr>
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
      const classId = document.getElementById('classFilter').value;
      const sessionId = document.getElementById('sessionFilter').value;
      const status = document.getElementById('statusFilter').value;
      let url = 'list_defaulter.php?';
      if (search) url += 'search=' + encodeURIComponent(search) + '&';
      if (classId && classId !== '0') url += 'class_id=' + encodeURIComponent(classId) + '&';
      if (sessionId && sessionId !== '0') url += 'session_id=' + encodeURIComponent(sessionId) + '&';
      if (status) url += 'status=' + encodeURIComponent(status) + '&';
      window.location.href = url;
    }
  </script>
</body>
</html>
