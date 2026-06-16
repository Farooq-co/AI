<!DOCTYPE html>
<html lang="en">
<head>
  <title>Fee Type Management</title>
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
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by Fee Type" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" data-toggle="modal" data-target="#addTypeModal">
                Add New Fee Type
              </button>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Fee Types</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Fee Type</th>
                          <th>Status</th>
                          <th>Created At</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody id="typeTableBody">
                        <?php
                          include '../connect.php';
                          $sql = "SELECT id, name, status, created_at, updated_at FROM fee_types ORDER BY id DESC";
                          $result = $conn->query($sql);
                          if (!$result) {
                              echo "<tr><td colspan='6'>Error: " . $conn->error . "</td></tr>";
                          } elseif ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              $createdAt = date('Y-m-d H:i:s', strtotime($row['created_at']));
                              $updatedAt = date('Y-m-d H:i:s', strtotime($row['updated_at']));
                              echo "<tr>";
                              echo "<td>\n<div class='dropdown'>\n<button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Action</button>\n<div class='dropdown-menu'>\n<a class='dropdown-item' href='#' onclick='openEditModal(" . $row['id'] . ", \"" . htmlspecialchars($row['name'], ENT_QUOTES) . "\", \"" . $row['status'] . "\")'><i class='ti-pencil-alt'></i> Edit</a>\n<a class='dropdown-item' href='#' onclick='toggleTypeStatus(" . $row['id'] . ", \"" . ($row['status'] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row['status'] == 'Active' ? 'Inactive' : 'Active') . "</a>\n<a class='dropdown-item' href='#' onclick='deleteType(" . $row['id'] . ")'><i class='ti-trash'></i> Delete</a>\n</div>\n</div>\n</td>";
                              echo "<td>" . $row['id'] . "</td>";
                              echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                              echo "<td><span class='badge " . ($row['status'] == 'Active' ? 'badge-success' : 'badge-danger') . "'>" . $row['status'] . "</span></td>";
                              echo "<td>" . $createdAt . "</td>";
                              echo "<td>" . $updatedAt . "</td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
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

  <div class="modal fade" id="addTypeModal" tabindex="-1" role="dialog" aria-labelledby="addTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTypeModalLabel">Add New Fee Type</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addTypeForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="typeName">Fee Type Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="typeName" name="name" required placeholder="Enter fee type name">
            </div>
            <div class="form-group">
              <label for="typeStatus">Status</label>
              <select class="form-control" id="typeStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Fee Type</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editTypeModal" tabindex="-1" role="dialog" aria-labelledby="editTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editTypeModalLabel">Edit Fee Type</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editTypeForm">
          <input type="hidden" id="editTypeId" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="editTypeName">Fee Type Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editTypeName" name="name" required placeholder="Enter fee type name">
            </div>
            <div class="form-group">
              <label for="editTypeStatus">Status</label>
              <select class="form-control" id="editTypeStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Fee Type</button>
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
        const nameCell = rows[i].getElementsByTagName('td')[2];
        if (nameCell) {
          const nameValue = nameCell.textContent || nameCell.innerText;
          rows[i].style.display = nameValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
        }
      }
    }

    document.getElementById('searchInput').addEventListener('keyup', function(event) {
      if (event.key === 'Enter') searchTable();
    });

    document.getElementById('addTypeForm').addEventListener('submit', function(e) {
      e.preventDefault();
      fetch('fee_type/add_fee_type.php', {
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

    function openEditModal(id, name, status) {
      document.getElementById('editTypeId').value = id;
      document.getElementById('editTypeName').value = name;
      document.getElementById('editTypeStatus').value = status;
      $('#editTypeModal').modal('show');
    }

    document.getElementById('editTypeForm').addEventListener('submit', function(e) {
      e.preventDefault();
      fetch('fee_type/edit_fee_type.php', {
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

    function toggleTypeStatus(id, newStatus) {
      Swal.fire({ title: 'Change Status?', text: `Do you want to mark this type as ${newStatus}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, update it!' })
      .then((result) => {
        if (!result.isConfirmed) return;
        fetch('fee_type/toggle_fee_type_status.php', {
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

    function deleteType(id) {
      Swal.fire({ title: 'Are you sure?', text: 'This action cannot be undone!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, delete it!' })
      .then((result) => {
        if (!result.isConfirmed) return;
        fetch('fee_type/delete_fee_type.php', {
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
