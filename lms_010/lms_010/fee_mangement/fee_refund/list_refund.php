<?php
include '../../connect.php';
include '../fee_helpers.php';

$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = '(fr.refund_reason LIKE ? OR s.student_name LIKE ? OR fp.receipt_no LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = &$like;
    $params[] = &$like;
    $params[] = &$like;
    $types .= 'sss';
}
if ($statusFilter !== '') {
    $where[] = 'fr.status = ?';
    $params[] = &$statusFilter;
    $types .= 's';
}

$query = 'SELECT fr.id, fr.payment_id, fr.refund_amount, fr.refund_reason, fr.approved_by, fr.refund_date, fr.status, s.student_name, fp.receipt_no, fp.amount_paid FROM fee_refunds fr LEFT JOIN fee_payments fp ON fr.payment_id = fp.id LEFT JOIN students s ON fr.payment_id = fp.student_id';
if (!empty($where)) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}
$query .= ' ORDER BY fr.refund_date DESC';

$stmt = $conn->prepare($query);
if ($stmt && !empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$statuses = ['Pending', 'Approved', 'Rejected', 'Completed'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Fee Refund Management</title>
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
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search refund, student, receipt" value="<?= htmlspecialchars($search) ?>">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button" onclick="applyFilters()">Search</button>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <select id="statusFilter" class="form-control" onchange="applyFilters()">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $status): ?>
                  <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 text-right">
              <button class="btn btn-primary btn-rounded btn-fw" data-toggle="modal" data-target="#addRefundModal">Add Refund</button>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Refund Records</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Actions</th>
                          <th>Refund ID</th>
                          <th>Receipt No</th>
                          <th>Student</th>
                          <th>Refund Amount</th>
                          <th>Refund Date</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                          <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                              <td>
                                <div class="dropdown">
                                  <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action</button>
                                  <div class="dropdown-menu">
                                    <a class="dropdown-item" href="edit_refund.php?id=<?= $row['id'] ?>">Edit</a>
                                    <a class="dropdown-item" href="delete_refund.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete refund?')">Delete</a>
                                    <a class="dropdown-item" href="toggle_refund_status.php?id=<?= $row['id'] ?>&status=Approved">Approve</a>
                                    <a class="dropdown-item" href="toggle_refund_status.php?id=<?= $row['id'] ?>&status=Rejected">Reject</a>
                                  </div>
                                </div>
                              </td>
                              <td><?= htmlspecialchars($row['id']) ?></td>
                              <td><?= htmlspecialchars($row['receipt_no']) ?></td>
                              <td><?= htmlspecialchars($row['student_name']) ?></td>
                              <td><?= htmlspecialchars(number_format($row['refund_amount'], 2)) ?></td>
                              <td><?= htmlspecialchars($row['refund_date']) ?></td>
                              <td><?= htmlspecialchars($row['status']) ?></td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr><td colspan="7" class="text-center">No refunds found.</td></tr>
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

  <div class="modal fade" id="addRefundModal" tabindex="-1" role="dialog" aria-labelledby="addRefundModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addRefundModalLabel">Add Refund</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addRefundForm" method="post" action="add_refund.php">
          <div class="modal-body">
            <div class="form-group">
              <label>Payment ID</label>
              <input type="number" name="payment_id" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Refund Amount</label>
              <input type="number" name="refund_amount" step="0.01" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Refund Date</label>
              <input type="date" name="refund_date" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Refund Reason</label>
              <textarea name="refund_reason" class="form-control"></textarea>
            </div>
            <div class="form-group">
              <label>Approved By</label>
              <input type="text" name="approved_by" class="form-control">
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <?php foreach ($statuses as $status): ?>
                  <option value="<?= $status ?>"><?= $status ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Refund</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function applyFilters() {
      const q = document.getElementById('searchInput').value;
      const status = document.getElementById('statusFilter').value;
      window.location.href = '?search=' + encodeURIComponent(q) + '&status=' + encodeURIComponent(status);
    }
  </script>
</body>
</html>
