<?php
include '../../connect.php';
include '../fee_helpers.php';

$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$methodFilter = intval($_GET['payment_method_id'] ?? 0);
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
if ($statusFilter !== '') {
    $whereClauses[] = 'fp.status = ?';
    $params[] = &$statusFilter;
    $types .= 's';
}
if ($methodFilter > 0) {
    $whereClauses[] = 'fp.payment_method_id = ?';
    $params[] = &$methodFilter;
    $types .= 'i';
}
$query = 'SELECT fp.id, fp.receipt_no, fp.invoice_id, fp.student_id, fp.amount_paid, fp.payment_method_id, fp.transaction_id, fp.bank_name, fp.branch_name, fp.cheque_number, fp.reference_number, fp.remarks, fp.received_by, pm.method_name, fp.payment_date, fp.status, s.student_name, fi.invoice_no FROM fee_payments fp LEFT JOIN students s ON fp.student_id = s.id LEFT JOIN fee_invoices fi ON fp.invoice_id = fi.id LEFT JOIN payment_methods pm ON fp.payment_method_id = pm.id';
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
$methods = getPaymentMethods($conn);
$paymentStatuses = getPaymentStatusOptions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Fee Payment Management</title>
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
                <input type="text" id="searchInput" class="form-control" placeholder="Search receipt, student, invoice" value="<?= htmlspecialchars($search) ?>">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button" onclick="applyFilters()">Search</button>
                </div>
              </div>
            </div>
            <div class="col-md-8 text-right">
              <button class="btn btn-primary btn-rounded btn-fw" data-toggle="modal" data-target="#addPaymentModal">Add Payment</button>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <form id="filterForm" class="form-inline">
                <div class="form-group mr-2">
                  <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <?php foreach ($paymentStatuses as $status): ?>
                      <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group mr-2">
                  <select name="payment_method_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Methods</option>
                    <?php foreach ($methods as $method): ?>
                      <option value="<?= $method['id'] ?>" <?= $methodFilter === intval($method['id']) ? 'selected' : '' ?>><?= htmlspecialchars($method['method_name']) ?></option>
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
                  <h4 class="card-title">Payment Records</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Actions</th>
                          <th>Receipt No</th>
                          <th>Invoice</th>
                          <th>Student</th>
                          <th>Amount Paid</th>
                          <th>Method</th>
                          <th>Payment Date</th>
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
                                    <a class="dropdown-item" href="#" onclick="openEditModal(<?= $row['id'] ?>, <?= $row['amount_paid'] ?>, <?= $row['payment_method_id'] ?? 0 ?>, '<?= htmlspecialchars($row['transaction_id'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($row['bank_name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($row['branch_name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($row['cheque_number'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($row['reference_number'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($row['payment_date']) ?>', '<?= htmlspecialchars($row['remarks'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($row['received_by'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($row['status']) ?>')">Edit</a>
                                    <a class="dropdown-item" href="#" onclick="verifyPayment(<?= $row['id'] ?>)">Verify</a>
                                    <a class="dropdown-item" href="#" onclick="printReceipt(<?= $row['id'] ?>)">Print Receipt</a>
                                    <a class="dropdown-item text-danger" href="#" onclick="deletePayment(<?= $row['id'] ?>)">Delete</a>
                                  </div>
                                </div>
                              </td>
                              <td><?= htmlspecialchars($row['receipt_no']) ?></td>
                              <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                              <td><?= htmlspecialchars($row['student_name']) ?></td>
                              <td><?= number_format($row['amount_paid'], 2) ?></td>
                              <td><?= htmlspecialchars($row['method_name']) ?></td>
                              <td><?= htmlspecialchars($row['payment_date']) ?></td>
                              <td><span class="badge <?= $row['status'] === 'Completed' ? 'badge-success' : ($row['status'] === 'Failed' ? 'badge-danger' : 'badge-warning') ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr><td colspan="8" class="text-center">No payments found.</td></tr>
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

  <div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addPaymentForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="paymentInvoice">Invoice ID <span class="text-danger">*</span></label>
              <input type="number" name="invoice_id" id="paymentInvoice" class="form-control" min="1" required>
            </div>
            <div class="form-group">
              <label for="paymentStudent">Student ID <span class="text-danger">*</span></label>
              <input type="number" name="student_id" id="paymentStudent" class="form-control" min="1" required>
            </div>
            <div class="form-group">
              <label for="paymentAmount">Amount Paid (Rs) <span class="text-danger">*</span></label>
              <input type="number" name="amount_paid" id="paymentAmount" class="form-control" step="0.01" min="0.01" required>
            </div>
            <div class="form-group">
              <label for="paymentMethod">Payment Method <span class="text-danger">*</span></label>
              <select name="payment_method_id" id="paymentMethod" class="form-control" required>
                <option value="">Select Method</option>
                <?php foreach ($methods as $method): ?>
                  <option value="<?= $method['id'] ?>"><?= htmlspecialchars($method['method_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="transactionId">Transaction ID</label>
                <input type="text" name="transaction_id" id="transactionId" class="form-control">
              </div>
              <div class="form-group col-md-6">
                <label for="referenceNumber">Reference No.</label>
                <input type="text" name="reference_number" id="referenceNumber" class="form-control">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="bankName">Bank Name</label>
                <input type="text" name="bank_name" id="bankName" class="form-control">
              </div>
              <div class="form-group col-md-6">
                <label for="branchName">Branch Name</label>
                <input type="text" name="branch_name" id="branchName" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label for="chequeNumber">Cheque Number</label>
              <input type="text" name="cheque_number" id="chequeNumber" class="form-control">
            </div>
            <div class="form-group">
              <label for="paymentDate">Payment Date <span class="text-danger">*</span></label>
              <input type="date" name="payment_date" id="paymentDate" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="receivedBy">Received By</label>
              <input type="text" name="received_by" id="receivedBy" class="form-control">
            </div>
            <div class="form-group">
              <label for="paymentRemarks">Remarks</label>
              <textarea name="remarks" id="paymentRemarks" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group">
              <label for="paymentStatus">Status</label>
              <select name="status" id="paymentStatus" class="form-control">
                <?php foreach ($paymentStatuses as $status): ?>
                  <option value="<?= $status ?>"><?= $status ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Payment</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editPaymentModal" tabindex="-1" role="dialog" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editPaymentModalLabel">Edit Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editPaymentForm">
          <input type="hidden" name="id" id="editPaymentId">
          <div class="modal-body">
            <div class="form-group">
              <label for="editPaymentAmount">Amount Paid (Rs) <span class="text-danger">*</span></label>
              <input type="number" name="amount_paid" id="editPaymentAmount" class="form-control" step="0.01" min="0.01" required>
            </div>
            <div class="form-group">
              <label for="editPaymentMethod">Payment Method <span class="text-danger">*</span></label>
              <select name="payment_method_id" id="editPaymentMethod" class="form-control" required>
                <option value="">Select Method</option>
                <?php foreach ($methods as $method): ?>
                  <option value="<?= $method['id'] ?>"><?= htmlspecialchars($method['method_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="editTransactionId">Transaction ID</label>
              <input type="text" name="transaction_id" id="editTransactionId" class="form-control">
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="editBankName">Bank Name</label>
                <input type="text" name="bank_name" id="editBankName" class="form-control">
              </div>
              <div class="form-group col-md-6">
                <label for="editBranchName">Branch Name</label>
                <input type="text" name="branch_name" id="editBranchName" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label for="editChequeNumber">Cheque Number</label>
              <input type="text" name="cheque_number" id="editChequeNumber" class="form-control">
            </div>
            <div class="form-group">
              <label for="editReferenceNumber">Reference No.</label>
              <input type="text" name="reference_number" id="editReferenceNumber" class="form-control">
            </div>
            <div class="form-group">
              <label for="editPaymentDate">Payment Date <span class="text-danger">*</span></label>
              <input type="date" name="payment_date" id="editPaymentDate" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="editReceivedBy">Received By</label>
              <input type="text" name="received_by" id="editReceivedBy" class="form-control">
            </div>
            <div class="form-group">
              <label for="editRemarks">Remarks</label>
              <textarea name="remarks" id="editRemarks" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group">
              <label for="editPaymentStatus">Status</label>
              <select name="status" id="editPaymentStatus" class="form-control">
                <?php foreach ($paymentStatuses as $status): ?>
                  <option value="<?= $status ?>"><?= $status ?></option>
                <?php endforeach; ?>
              </select>
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
    function applyFilters() {
      const search = document.getElementById('searchInput').value.trim();
      const status = document.querySelector('select[name="status"]').value;
      const method = document.querySelector('select[name="payment_method_id"]').value;
      let url = 'list_payment.php?';
      if (search) url += 'search=' + encodeURIComponent(search) + '&';
      if (status) url += 'status=' + encodeURIComponent(status) + '&';
      if (method) url += 'payment_method_id=' + encodeURIComponent(method) + '&';
      window.location.href = url;
    }

    document.getElementById('addPaymentForm').addEventListener('submit', function(e) {
      e.preventDefault();
      fetch('add_payment.php', {
        method: 'POST',
        body: new FormData(this)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire('Success', data.message, 'success').then(() => location.reload());
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Unable to save payment.', 'error'));
    });

    function openEditModal(id, amountPaid, methodId, transactionId, bankName, branchName, chequeNumber, referenceNumber, paymentDate, remarks, receivedBy, status) {
      document.getElementById('editPaymentId').value = id;
      document.getElementById('editPaymentAmount').value = amountPaid;
      document.getElementById('editPaymentMethod').value = methodId;
      document.getElementById('editTransactionId').value = transactionId;
      document.getElementById('editBankName').value = bankName;
      document.getElementById('editBranchName').value = branchName;
      document.getElementById('editChequeNumber').value = chequeNumber;
      document.getElementById('editReferenceNumber').value = referenceNumber;
      document.getElementById('editPaymentDate').value = paymentDate;
      document.getElementById('editRemarks').value = remarks;
      document.getElementById('editReceivedBy').value = receivedBy;
      document.getElementById('editPaymentStatus').value = status;
      $('#editPaymentModal').modal('show');
    }

    document.getElementById('editPaymentForm').addEventListener('submit', function(e) {
      e.preventDefault();
      fetch('edit_payment.php', {
        method: 'POST',
        body: new FormData(this)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire('Success', data.message, 'success').then(() => location.reload());
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Unable to update payment.', 'error'));
    });

    function verifyPayment(id) {
      Swal.fire({
        title: 'Verify Payment',
        text: 'Mark this payment as verified?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, verify'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('verify_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, status: 'Verified' })
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Verified', data.message, 'success').then(() => location.reload());
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Unable to verify payment.', 'error'));
        }
      });
    }

    function deletePayment(id) {
      Swal.fire({
        title: 'Delete Payment',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('delete_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Deleted', data.message, 'success').then(() => location.reload());
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Unable to delete payment.', 'error'));
        }
      });
    }

    function printReceipt(id) {
      window.open('print_receipt.php?id=' + id, '_blank');
    }
  </script>
</body>
</html>
