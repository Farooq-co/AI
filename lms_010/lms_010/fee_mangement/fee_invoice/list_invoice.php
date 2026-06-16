<?php
include '../../connect.php';
include '../fee_helpers.php';

$students = getStudentOptions($conn);
$classes = getClassOptions($conn);
$sessions = getSessionOptions($conn);
$packages = getPackageOptions($conn);
$invoiceStatuses = getInvoiceStatusOptions();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';
$studentFilter = intval($_GET['student_id'] ?? 0);
$classFilter = intval($_GET['class_id'] ?? 0);
$sessionFilter = intval($_GET['session_id'] ?? 0);
$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = '(fi.invoice_no LIKE ? OR s.student_name LIKE ? OR c.name LIKE ? OR p.name LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = &$like;
    $params[] = &$like;
    $params[] = &$like;
    $params[] = &$like;
    $types .= 'ssss';
}
if ($statusFilter !== '') {
    $where[] = 'fi.status = ?';
    $params[] = &$statusFilter;
    $types .= 's';
}
if ($studentFilter > 0) {
    $where[] = 'fi.student_id = ?';
    $params[] = &$studentFilter;
    $types .= 'i';
}
if ($classFilter > 0) {
    $where[] = 'fi.class_id = ?';
    $params[] = &$classFilter;
    $types .= 'i';
}
if ($sessionFilter > 0) {
    $where[] = 'fi.session_id = ?';
    $params[] = &$sessionFilter;
    $types .= 'i';
}
$query = "SELECT fi.id, fi.invoice_no, fi.student_id, s.student_name, fi.class_id, c.name AS class_name, fi.session_id, se.name AS session_name, fi.package_id, p.name AS package_name, fi.subtotal, fi.discount, fi.scholarship, fi.fine, fi.total_amount, fi.due_date, fi.status, fi.created_at FROM fee_invoices fi LEFT JOIN students s ON fi.student_id = s.id LEFT JOIN classes c ON fi.class_id = c.id LEFT JOIN sessions se ON fi.session_id = se.id LEFT JOIN fee_packages p ON fi.package_id = p.id";
if (!empty($where)) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}
$query .= ' ORDER BY fi.created_at DESC';
$stmt = $conn->prepare($query);
if ($stmt && !empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Fee Invoice Management</title>
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
                <input type="text" class="form-control" id="searchInput" placeholder="Search invoice, student, class, package" value="<?= htmlspecialchars($search) ?>">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button" onclick="searchInvoices()">Search</button>
                </div>
              </div>
            </div>
            <div class="col-md-8 text-right">
              <button class="btn btn-primary btn-rounded btn-fw" data-toggle="modal" data-target="#addInvoiceModal">Create Invoice</button>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <form id="filterForm" class="form-inline">
                <div class="form-group mr-2">
                  <select name="student_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Students</option>
                    <?php foreach ($students as $student): ?>
                      <option value="<?= $student['id'] ?>" <?= $studentFilter === intval($student['id']) ? 'selected' : '' ?>><?= htmlspecialchars($student['student_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group mr-2">
                  <select name="class_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $class): ?>
                      <option value="<?= $class['id'] ?>" <?= $classFilter === intval($class['id']) ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group mr-2">
                  <select name="session_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Sessions</option>
                    <?php foreach ($sessions as $session): ?>
                      <option value="<?= $session['id'] ?>" <?= $sessionFilter === intval($session['id']) ? 'selected' : '' ?>><?= htmlspecialchars($session['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group mr-2">
                  <select name="status" class="form-control" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Statuses</option>
                    <?php foreach ($invoiceStatuses as $status): ?>
                      <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </form>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Invoice List</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Actions</th>
                          <th>Invoice No</th>
                          <th>Student</th>
                          <th>Class</th>
                          <th>Session</th>
                          <th>Package</th>
                          <th>Total (Rs)</th>
                          <th>Due Date</th>
                          <th>Status</th>
                          <th>Created At</th>
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
                                    <a class="dropdown-item" href="invoice_details.php?id=<?= $row['id'] ?>">View Details</a>
                                    <a class="dropdown-item" href="#" onclick="openEditModal(<?= $row['id'] ?>, <?= $row['student_id'] ?>, <?= $row['class_id'] ?>, <?= $row['session_id'] ?>, <?= $row['package_id'] ?? '0' ?>, '<?= $row['due_date'] ?>', <?= $row['discount'] ?>, <?= $row['scholarship'] ?>, <?= $row['fine'] ?>, '<?= $row['status'] ?>')">Edit</a>
                                    <a class="dropdown-item" href="#" onclick="printInvoice(<?= $row['id'] ?>)">Print</a>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteInvoice(<?= $row['id'] ?>)">Delete</a>
                                  </div>
                                </div>
                              </td>
                              <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                              <td><?= htmlspecialchars($row['student_name']) ?></td>
                              <td><?= htmlspecialchars($row['class_name']) ?></td>
                              <td><?= htmlspecialchars($row['session_name']) ?></td>
                              <td><?= htmlspecialchars($row['package_name']) ?></td>
                              <td><?= number_format($row['total_amount'], 2) ?></td>
                              <td><?= htmlspecialchars($row['due_date']) ?></td>
                              <td><span class="badge <?= $row['status'] === 'Paid' ? 'badge-success' : ($row['status'] === 'Overdue' ? 'badge-danger' : 'badge-warning') ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                              <td><?= htmlspecialchars($row['created_at']) ?></td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr><td colspan="10" class="text-center">No invoices found.</td></tr>
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

  <div class="modal fade" id="addInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="addInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addInvoiceModalLabel">Create Fee Invoice</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addInvoiceForm">
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="invoiceStudent">Student <span class="text-danger">*</span></label>
                <select class="form-control" id="invoiceStudent" name="student_id" required>
                  <option value="">Select Student</option>
                  <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['student_name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="invoiceClass">Class <span class="text-danger">*</span></label>
                <select class="form-control" id="invoiceClass" name="class_id" required>
                  <option value="">Select Class</option>
                  <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="invoiceSession">Session <span class="text-danger">*</span></label>
                <select class="form-control" id="invoiceSession" name="session_id" required>
                  <option value="">Select Session</option>
                  <?php foreach ($sessions as $session): ?>
                    <option value="<?= $session['id'] ?>"><?= htmlspecialchars($session['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="invoicePackage">Fee Package</label>
                <select class="form-control" id="invoicePackage" name="package_id">
                  <option value="">None</option>
                  <?php foreach ($packages as $package): ?>
                    <option value="<?= $package['id'] ?>"><?= htmlspecialchars($package['name']) ?> - Rs <?= number_format($package['total_amount'], 2) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="invoiceDueDate">Due Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="invoiceDueDate" name="due_date" required>
              </div>
              <div class="form-group col-md-4">
                <label for="invoiceDiscount">Discount (Rs)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="invoiceDiscount" name="discount" value="0.00">
              </div>
              <div class="form-group col-md-4">
                <label for="invoiceScholarship">Scholarship (Rs)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="invoiceScholarship" name="scholarship" value="0.00">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="invoiceFine">Fine (Rs)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="invoiceFine" name="fine" value="0.00">
              </div>
              <div class="form-group col-md-6">
                <label for="invoiceStatus">Status</label>
                <select class="form-control" id="invoiceStatus" name="status">
                  <?php foreach ($invoiceStatuses as $status): ?>
                    <option value="<?= $status ?>"><?= $status ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="alert alert-info">
              Invoice items are generated automatically from class fee structures and selected fee package.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Invoice</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="editInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editInvoiceModalLabel">Edit Fee Invoice</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editInvoiceForm">
          <input type="hidden" id="editInvoiceId" name="id">
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="editInvoiceStudent">Student <span class="text-danger">*</span></label>
                <select class="form-control" id="editInvoiceStudent" name="student_id" required>
                  <option value="">Select Student</option>
                  <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['student_name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="editInvoiceClass">Class <span class="text-danger">*</span></label>
                <select class="form-control" id="editInvoiceClass" name="class_id" required>
                  <option value="">Select Class</option>
                  <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="editInvoiceSession">Session <span class="text-danger">*</span></label>
                <select class="form-control" id="editInvoiceSession" name="session_id" required>
                  <option value="">Select Session</option>
                  <?php foreach ($sessions as $session): ?>
                    <option value="<?= $session['id'] ?>"><?= htmlspecialchars($session['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="editInvoicePackage">Fee Package</label>
                <select class="form-control" id="editInvoicePackage" name="package_id">
                  <option value="">None</option>
                  <?php foreach ($packages as $package): ?>
                    <option value="<?= $package['id'] ?>"><?= htmlspecialchars($package['name']) ?> - Rs <?= number_format($package['total_amount'], 2) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="editInvoiceDueDate">Due Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="editInvoiceDueDate" name="due_date" required>
              </div>
              <div class="form-group col-md-4">
                <label for="editInvoiceDiscount">Discount (Rs)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="editInvoiceDiscount" name="discount" value="0.00">
              </div>
              <div class="form-group col-md-4">
                <label for="editInvoiceScholarship">Scholarship (Rs)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="editInvoiceScholarship" name="scholarship" value="0.00">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="editInvoiceFine">Fine (Rs)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="editInvoiceFine" name="fine" value="0.00">
              </div>
              <div class="form-group col-md-6">
                <label for="editInvoiceStatus">Status</label>
                <select class="form-control" id="editInvoiceStatus" name="status">
                  <?php foreach ($invoiceStatuses as $status): ?>
                    <option value="<?= $status ?>"><?= $status ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include '../../parts/links2.php'; ?>
  <script>
    function searchInvoices() {
      const query = encodeURIComponent(document.getElementById('searchInput').value.trim());
      window.location.href = 'list_invoice.php?search=' + query;
    }

    function openEditModal(id, studentId, classId, sessionId, packageId, dueDate, discount, scholarship, fine, status) {
      document.getElementById('editInvoiceId').value = id;
      document.getElementById('editInvoiceStudent').value = studentId;
      document.getElementById('editInvoiceClass').value = classId;
      document.getElementById('editInvoiceSession').value = sessionId;
      document.getElementById('editInvoicePackage').value = packageId;
      document.getElementById('editInvoiceDueDate').value = dueDate;
      document.getElementById('editInvoiceDiscount').value = discount;
      document.getElementById('editInvoiceScholarship').value = scholarship;
      document.getElementById('editInvoiceFine').value = fine;
      document.getElementById('editInvoiceStatus').value = status;
      $('#editInvoiceModal').modal('show');
    }

    document.getElementById('addInvoiceForm').addEventListener('submit', function(event) {
      event.preventDefault();
      fetch('create_invoice.php', {
        method: 'POST',
        body: new FormData(this)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire('Success', data.message, 'success').then(() => window.location.reload());
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Unable to create invoice.', 'error'));
    });

    document.getElementById('editInvoiceForm').addEventListener('submit', function(event) {
      event.preventDefault();
      fetch('edit_invoice.php', {
        method: 'POST',
        body: new FormData(this)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire('Success', data.message, 'success').then(() => window.location.reload());
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Unable to update invoice.', 'error'));
    });

    function deleteInvoice(id) {
      Swal.fire({
        title: 'Confirm Delete',
        text: 'Delete this invoice permanently?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('delete_invoice.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Deleted', data.message, 'success').then(() => window.location.reload());
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Unable to delete invoice.', 'error'));
        }
      });
    }

    function printInvoice(id) {
      window.open('print_invoice.php?id=' + id, '_blank');
    }
  </script>
</body>
</html>
