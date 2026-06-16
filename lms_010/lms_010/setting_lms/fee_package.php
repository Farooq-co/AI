<!DOCTYPE html>
<html lang="en">
<head>
  <title>Fee Package Management</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 for better alerts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
  <div class="container-scroller">
    <!-- Navbar -->
    <?php include '../parts/navbar.php'; ?>
    
    <div class="container-fluid page-body-wrapper">
      <!-- Sidebars -->
      <?php include '../parts/setting.php'; ?>
      <?php include '../parts/right_sidebar.php'; ?>
      <?php include '../parts/left_sidebar.php'; ?>

      <div class="main-panel">
        <div class="content-wrapper">

          <!-- Search and Add Fee Package Button -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by Package Name" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" data-toggle="modal" data-target="#addPackageModal">
                Add New Fee Package
              </button>
            </div>
          </div>

          <!-- Fee Package Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Fee Packages</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Package Name</th>
                          <th>Total Amount (Rs)</th>
                          <th>Status</th>
                          <th>Created At</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody id="packageTableBody">
                        <?php
                          include '../connect.php';

                          // Query to fetch fee packages
                          $sql = "SELECT id, name, total_amount, status, created_at, updated_at FROM fee_packages ORDER BY id DESC";
                          $result = $conn->query($sql);

                          if (!$result) {
                              echo "<tr><td colspan='7'>Error: " . $conn->error . "</td></tr>";
                          } elseif ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              // Format dates
                              $createdAt = date('Y-m-d H:i:s', strtotime($row['created_at']));
                              $updatedAt = date('Y-m-d H:i:s', strtotime($row['updated_at']));
                              $totalAmount = number_format($row['total_amount'], 2);
                              
                              echo "<tr>";
                              // Action dropdown
                              echo "<td>
                                    <div class='dropdown'>
                                      <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        Action
                                      </button>
                                      <div class='dropdown-menu'>
                                        <a class='dropdown-item' href='#' onclick='openEditModal(" . $row["id"] . ", \"" . htmlspecialchars($row["name"]) . "\", " . $row["total_amount"] . ", \"" . $row["status"] . "\")'><i class='ti-pencil-alt'></i> Edit</a>
                                        <a class='dropdown-item' href='#' onclick='togglePackageStatus(" . $row["id"] . ", \"" . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "</a>
                                        <a class='dropdown-item' href='#' onclick='deletePackage(" . $row["id"] . ")'><i class='ti-trash'></i> Delete</a>
                                      </div>
                                    </div>
                                    </td>";
                              
                              echo "<td>" . $row["id"] . "</td>";
                              echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                              echo "<td>Rs " . $totalAmount . "</td>";
                              echo "<td><span class='badge " . ($row["status"] == 'Active' ? 'badge-success' : 'badge-danger') . "'>" . $row["status"] . "</span></td>";
                              echo "<td>" . $createdAt . "</td>";
                              echo "<td>" . $updatedAt . "</td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr><td colspan='7' class='text-center'>No records found</td></tr>";
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
        <!-- footer -->
        <?php include '../parts/footer.php'; ?>
      </div>
    </div>
  </div>

  <!-- Add Fee Package Modal -->
  <div class="modal fade" id="addPackageModal" tabindex="-1" role="dialog" aria-labelledby="addPackageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addPackageModalLabel">Add New Fee Package</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addPackageForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="packageName">Package Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="packageName" name="name" required placeholder="Enter package name (e.g., Basic, Premium, etc.)">
            </div>
            <div class="form-group">
              <label for="totalAmount">Total Amount (Rs) <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rs</span>
                </div>
                <input type="number" class="form-control" id="totalAmount" name="total_amount" required placeholder="0.00" step="0.01" min="0">
              </div>
            </div>
            <div class="form-group">
              <label for="packageStatus">Status</label>
              <select class="form-control" id="packageStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Package</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Fee Package Modal -->
  <div class="modal fade" id="editPackageModal" tabindex="-1" role="dialog" aria-labelledby="editPackageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editPackageModalLabel">Edit Fee Package</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editPackageForm">
          <input type="hidden" id="editPackageId" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="editPackageName">Package Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editPackageName" name="name" required placeholder="Enter package name">
            </div>
            <div class="form-group">
              <label for="editTotalAmount">Total Amount (Rs) <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rs</span>
                </div>
                <input type="number" class="form-control" id="editTotalAmount" name="total_amount" required placeholder="0.00" step="0.01" min="0">
              </div>
            </div>
            <div class="form-group">
              <label for="editPackageStatus">Status</label>
              <select class="form-control" id="editPackageStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Package</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <?php include '../parts/links2.php'; ?>

  <script>
    // Search functionality
    function searchTable() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const table = document.querySelector('.table tbody');
      const rows = table.getElementsByTagName('tr');
      
      for (let i = 0; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByTagName('td')[2]; // Package name is 3rd column (index 2)
        if (nameCell) {
          const nameValue = nameCell.textContent || nameCell.innerText;
          if (nameValue.toLowerCase().indexOf(filter) > -1) {
            rows[i].style.display = '';
          } else {
            rows[i].style.display = 'none';
          }
        }
      }
    }

    // Trigger search on Enter key
    document.getElementById('searchInput').addEventListener('keyup', function(event) {
      if (event.key === 'Enter') {
        searchTable();
      }
    });

    // Add Fee Package via AJAX
    document.getElementById('addPackageForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('fee_package/add_fee_package.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: data.message,
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: data.message
          });
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'An error occurred. Please try again.'
        });
      });
    });

    // Open Edit Modal
    function openEditModal(id, name, totalAmount, status) {
      document.getElementById('editPackageId').value = id;
      document.getElementById('editPackageName').value = name;
      document.getElementById('editTotalAmount').value = totalAmount;
      document.getElementById('editPackageStatus').value = status;
      $('#editPackageModal').modal('show');
    }

    // Edit Fee Package via AJAX
    document.getElementById('editPackageForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('fee_package/edit_fee_package.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: data.message,
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: data.message
          });
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'An error occurred. Please try again.'
        });
      });
    });

    // Toggle Package Status (Active/Inactive)
    function togglePackageStatus(id, newStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to mark this fee package as ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('fee_package/toggle_fee_package_status.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id, status: newStatus })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                location.reload();
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message
              });
            }
          })
          .catch(error => {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: 'An error occurred. Please try again.'
            });
          });
        }
      });
    }

    // Delete Fee Package
    function deletePackage(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('fee_package/delete_fee_package.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                location.reload();
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message
              });
            }
          })
          .catch(error => {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: 'An error occurred. Please try again.'
            });
          });
        }
      });
    }
  </script>
</body>
</html>