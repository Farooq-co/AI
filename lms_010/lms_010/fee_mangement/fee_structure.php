<!DOCTYPE html>
<html lang="en">
<head>
  <title>Fee Structure Management</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container-scroller">
    <?php include '../parts/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include '../parts/setting.php'; ?>
      <?php include '../parts/right_sidebar.php'; ?>
      <?php include '../parts/left_sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by Fee Structure" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" data-toggle="modal" data-target="#addStructureModal">
                Add New Fee Structure
              </button>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Fee Structures</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Fee Head</th>
                          <th>Fee Type</th>
                          <th>Class</th>
                          <th>Amount</th>
                          <th>Status</th>
                          <th>Created At</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody id="structureTableBody">
                        <?php
                          include '../connect.php';
                          $sql = "SELECT fs.id, fs.fee_head_id, fs.fee_type_id, fs.class_id, fh.name AS fee_head, ft.name AS fee_type, c.name AS class_name, fs.amount, fs.status, fs.created_at, fs.updated_at FROM fee_structures fs LEFT JOIN fee_heads fh ON fs.fee_head_id = fh.id LEFT JOIN fee_types ft ON fs.fee_type_id = ft.id LEFT JOIN classes c ON fs.class_id = c.id ORDER BY fs.id DESC";
                          $result = $conn->query($sql);
                          if (!$result) {
                              echo "<tr><td colspan='9'>Error: " . $conn->error . "</td></tr>";
                          } elseif ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              $createdAt = date('Y-m-d H:i:s', strtotime($row['created_at']));
                              $updatedAt = date('Y-m-d H:i:s', strtotime($row['updated_at']));
                              echo "<tr>";
                              echo "<td>\n<div class='dropdown'>\n<button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Action</button>\n<div class='dropdown-menu'>\n<a class='dropdown-item' href='#' onclick='openEditModal(" . $row['id'] . ", \"" . htmlspecialchars($row['fee_head'], ENT_QUOTES) . "\", \"" . htmlspecialchars($row['fee_type'], ENT_QUOTES) . "\", \"" . htmlspecialchars($row['class_name'], ENT_QUOTES) . "\", " . $row['amount'] . ", \"" . $row['status'] . "\", " . $row['fee_head_id'] . ", " . $row['fee_type_id'] . ", " . $row['class_id'] . ")'><i class='ti-pencil-alt'></i> Edit</a>\n<a class='dropdown-item' href='#' onclick='toggleStructureStatus(" . $row['id'] . ", \"" . ($row['status'] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row['status'] == 'Active' ? 'Inactive' : 'Active') . "</a>\n<a class='dropdown-item' href='#' onclick='deleteStructure(" . $row['id'] . ")'><i class='ti-trash'></i> Delete</a>\n</div>\n</div>\n</td>";
                              echo "<td>" . $row['id'] . "</td>";
                              echo "<td>" . htmlspecialchars($row['fee_head']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['fee_type']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['class_name']) . "</td>";
                              echo "<td>Rs " . number_format($row['amount'], 2) . "</td>";
                              echo "<td><span class='badge " . ($row['status'] == 'Active' ? 'badge-success' : 'badge-danger') . "'>" . $row['status'] . "</span></td>";
                              echo "<td>" . $createdAt . "</td>";
                              echo "<td>" . $updatedAt . "</td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
                          }
                          $conn->close();
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php include '../parts/footer.php'; ?>
      </div>
    </div>
  </div>

  <div class="modal fade" id="addStructureModal" tabindex="-1" role="dialog" aria-labelledby="addStructureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addStructureModalLabel">Add New Fee Structure</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addStructureForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="headSelect">Fee Head <span class="text-danger">*</span></label>
              <select id="headSelect" name="fee_head_id" class="form-control" required>
                <option value="">Select Fee Head</option>
                <?php
                  include '../connect.php';
                  $heads = $conn->query("SELECT id, name FROM fee_heads WHERE status = 'Active' ORDER BY name");
                  while ($head = $heads->fetch_assoc()) {
                    echo "<option value='" . $head['id'] . "'>" . htmlspecialchars($head['name']) . "</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="typeSelect">Fee Type <span class="text-danger">*</span></label>
              <select id="typeSelect" name="fee_type_id" class="form-control" required>
                <option value="">Select Fee Type</option>
                <?php
                  $types = $conn->query("SELECT id, name FROM fee_types WHERE status = 'Active' ORDER BY name");
                  while ($type = $types->fetch_assoc()) {
                    echo "<option value='" . $type['id'] . "'>" . htmlspecialchars($type['name']) . "</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="classSelect">Class <span class="text-danger">*</span></label>
              <select id="classSelect" name="class_id" class="form-control" required>
                <option value="">Select Class</option>
                <?php
                  $classes = $conn->query("SELECT id, name FROM classes WHERE status = 'Active' ORDER BY name");
                  while ($class = $classes->fetch_assoc()) {
                    echo "<option value='" . $class['id'] . "'>" . htmlspecialchars($class['name']) . "</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="structureAmount">Amount (Rs) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" min="0" class="form-control" id="structureAmount" name="amount" required placeholder="0.00">
            </div>
            <div class="form-group">
              <label for="structureStatus">Status</label>
              <select class="form-control" id="structureStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Fee Structure</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editStructureModal" tabindex="-1" role="dialog" aria-labelledby="editStructureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editStructureModalLabel">Edit Fee Structure</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editStructureForm">
          <input type="hidden" id="editStructureId" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="editHeadSelect">Fee Head <span class="text-danger">*</span></label>
              <select id="editHeadSelect" name="fee_head_id" class="form-control" required>
                <option value="">Select Fee Head</option>
                <?php
                  $heads = $conn->query("SELECT id, name FROM fee_heads WHERE status = 'Active' ORDER BY name");
                  while ($head = $heads->fetch_assoc()) {
                    echo "<option value='" . $head['id'] . "'>" . htmlspecialchars($head['name']) . "</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="editTypeSelect">Fee Type <span class="text-danger">*</span></label>
              <select id="editTypeSelect" name="fee_type_id" class="form-control" required>
                <option value="">Select Fee Type</option>
                <?php
                  $types = $conn->query("SELECT id, name FROM fee_types WHERE status = 'Active' ORDER BY name");
                  while ($type = $types->fetch_assoc()) {
                    echo "<option value='" . $type['id'] . "'>" . htmlspecialchars($type['name']) . "</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="editClassSelect">Class <span class="text-danger">*</span></label>
              <select id="editClassSelect" name="class_id" class="form-control" required>
                <option value="">Select Class</option>
                <?php
                  $classes = $conn->query("SELECT id, name FROM classes WHERE status = 'Active' ORDER BY name");
                  while ($class = $classes->fetch_assoc()) {
                    echo "<option value='" . $class['id'] . "'>" . htmlspecialchars($class['name']) . "</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="editStructureAmount">Amount (Rs) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" min="0" class="form-control" id="editStructureAmount" name="amount" required placeholder="0.00">
            </div>
            <div class="form-group">
              <label for="editStructureStatus">Status</label>
              <select class="form-control" id="editStructureStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Fee Structure</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include '../parts/links2.php'; ?>
  <script>
    function searchTable() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const table = document.querySelector('.table tbody');
      const rows = table.getElementsByTagName('tr');
      for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        const searchable = [cells[2], cells[3], cells[4]];
        let visible = false;
        searchable.forEach(cell => {
          if (cell && cell.textContent.toLowerCase().indexOf(filter) > -1) visible = true;
        });
        rows[i].style.display = visible ? '' : 'none';
      }
    }

    document.getElementById('searchInput').addEventListener('keyup', function(event) {
      if (event.key === 'Enter') searchTable();
    });

    document.getElementById('addStructureForm').addEventListener('submit', function(e) {
      e.preventDefault();
      fetch('fee_structure/add_fee_structure.php', {
        method: 'POST',
        body: new FormData(this)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire({ icon: 'success', title: 'Success!', text: data.message, showConfirmButton: false, timer: 1500 }).then(() => location.reload());
        } else {
          Swal.fire({ icon: 'error', title: 'Error!', text: data.message });
        }
      })
      .catch(() => Swal.fire({ icon: 'error', title: 'Error!', text: 'An error occurred. Please try again.' }));
    });

    function openEditModal(id, feeHead, feeType, className, amount, status, feeHeadId, feeTypeId, classId) {
      document.getElementById('editStructureId').value = id;
      document.getElementById('editHeadSelect').value = feeHeadId;
      document.getElementById('editTypeSelect').value = feeTypeId;
      document.getElementById('editClassSelect').value = classId;
      document.getElementById('editStructureAmount').value = amount;
      document.getElementById('editStructureStatus').value = status;
      $('#editStructureModal').modal('show');
    }

    document.getElementById('editStructureForm').addEventListener('submit', function(e) {
      e.preventDefault();
      fetch('fee_structure/edit_fee_structure.php', {
        method: 'POST',
        body: new FormData(this)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire({ icon: 'success', title: 'Success!', text: data.message, showConfirmButton: false, timer: 1500 }).then(() => location.reload());
        } else {
          Swal.fire({ icon: 'error', title: 'Error!', text: data.message });
        }
      })
      .catch(() => Swal.fire({ icon: 'error', title: 'Error!', text: 'An error occurred. Please try again.' }));
    });

    function toggleStructureStatus(id, newStatus) {
      Swal.fire({ title: 'Change Status?', text: `Do you want to mark this structure as ${newStatus}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, update it!' })
      .then((result) => {
        if (!result.isConfirmed) return;
        fetch('fee_structure/toggle_fee_structure_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire({ icon: 'success', title: 'Updated!', text: data.message, showConfirmButton: false, timer: 1500 }).then(() => location.reload());
          } else {
            Swal.fire({ icon: 'error', title: 'Error!', text: data.message });
          }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error!', text: 'An error occurred. Please try again.' }));
      });
    }

    function deleteStructure(id) {
      Swal.fire({ title: 'Are you sure?', text: 'This action cannot be undone!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, delete it!' })
      .then((result) => {
        if (!result.isConfirmed) return;
        fetch('fee_structure/delete_fee_structure.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, showConfirmButton: false, timer: 1500 }).then(() => location.reload());
          } else {
            Swal.fire({ icon: 'error', title: 'Error!', text: data.message });
          }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error!', text: 'An error occurred. Please try again.' }));
      });
    }
  </script>
</body>
</html>
