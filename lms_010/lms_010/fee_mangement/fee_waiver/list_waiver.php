<?php
include '../../connect.php';
include '../fee_helpers.php';

$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$whereClauses = [];
$params = [];
$types = '';

if ($search !== '') {
    $whereClauses[] = '(fw.waiver_reason LIKE ? OR s.student_name LIKE ? OR fi.invoice_no LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = &$like;
    $params[] = &$like;
    $params[] = &$like;
    $types .= 'sss';
}
if ($statusFilter !== '') {
    $whereClauses[] = 'fw.status = ?';
    $params[] = &$statusFilter;
    $types .= 's';
}

$query = 'SELECT fw.id, fw.student_id, fw.invoice_id, fw.waiver_type, fw.waiver_amount, fw.waiver_reason, fw.approved_by, fw.status, fw.refund_date, s.student_name, fi.invoice_no FROM fee_waivers fw LEFT JOIN students s ON fw.student_id = s.id LEFT JOIN fee_invoices fi ON fw.invoice_id = fi.id';
if (!empty($whereClauses)) {
    $query .= ' WHERE ' . implode(' AND ', $whereClauses);
}
$query .= ' ORDER BY fw.id DESC';
$stmt = $conn->prepare($query);
if ($stmt && !empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$statuses = ['Active', 'Inactive'];
$waiverTypes = getWaiverTypes();
$students = getStudentOptions($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Fee Waiver Management</title>
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
                <input type="text" id="searchInput" class="form-control" placeholder="Search student, invoice or reason" value="<?= htmlspecialchars($search) ?>">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button" onclick="applyFilters()">Search</button>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <select id="statusFilter" class="form-control" onchange="applyFilters()">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $status): ?>
                  <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-5 text-right">
              <button class="btn btn-primary btn-rounded btn-fw" data-toggle="modal" data-target="#addWaiverModal">Add Waiver</button>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Waiver Records</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Actions</th>
                          <th>ID</th>
                          <th>Student</th>
                          <th>Invoice</th>
                          <th>Type</th>
                          <th>Amount</th>
                          <th>Reason</th>
                          <th>Approved By</th>
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
                                    <a class="dropdown-item" href="edit_waiver.php?id=<?= $row['id'] ?>">Edit</a>
                                    <a class="dropdown-item" href="delete_waiver.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete waiver?')">Delete</a>
                                    <a class="dropdown-item" href="toggle_waiver_status.php?id=<?= $row['id'] ?>&status=<?= $row['status'] === 'Active' ? 'Inactive' : 'Active' ?>">Set <?= $row['status'] === 'Active' ? 'Inactive' : 'Active' ?></a>
                                  </div>
                                </div>
                              </td>
                              <td><?= htmlspecialchars($row['id']) ?></td>
                              <td><?= htmlspecialchars($row['student_name']) ?></td>
                              <td><?= htmlspecialchars($row['invoice_no'] ?? 'N/A') ?></td>
                              <td><?= htmlspecialchars($row['waiver_type']) ?></td>
                              <td><?= htmlspecialchars(number_format($row['waiver_amount'], 2)) ?></td>
                              <td><?= htmlspecialchars($row['waiver_reason']) ?></td>
                              <td><?= htmlspecialchars($row['approved_by']) ?></td>
                              <td><span class="badge <?= $row['status'] === 'Active' ? 'badge-success' : 'badge-secondary' ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr><td colspan="9" class="text-center">No waivers found.</td></tr>
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

  <div class="modal fade" id="addWaiverModal" tabindex="-1" role="dialog" aria-labelledby="addWaiverModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addWaiverModalLabel">Add Waiver</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post" action="add_waiver.php">
          <div class="modal-body">
            <div class="form-group">
              <label>Student</label>
              <select name="student_id" class="form-control" required>
                <option value="">Select student</option>
                <?php foreach ($students as $student): ?>
                  <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['student_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Invoice ID</label>
              <input type="number" name="invoice_id" class="form-control" min="1">
            </div>
            <div class="form-group">
              <label>Waiver Type</label>
              <select name="waiver_type" class="form-control" required>
                <?php foreach ($waiverTypes as $type): ?>
                  <option value="<?= $type ?>"><?= $type ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Waiver Amount</label>
              <input type="number" name="waiver_amount" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
              <label>Reason</label>
              <textarea name="waiver_reason" class="form-control"></textarea>
            </div>
            <div class="form-group">
              <label>Approved By</label>
              <input type="text" name="approved_by" class="form-control">
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Waiver</button>
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
