<!DOCTYPE html>
<html lang="en">
<head>
  <title>Province / State Management</title>
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

          <!-- Search and Add Province/State Button -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by Province/State Name or Country" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" data-toggle="modal" data-target="#addProvinceModal">
                Add New Province/State
              </button>
            </div>
          </div>

          <!-- Province/State Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Provinces / States</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Province/State Name</th>
                          <th>Country</th>
                          <th>Status</th>
                          <th>Created At</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody id="provinceTableBody">
                        <?php
                          include '../connect.php';

                          // Query to fetch provinces/states with country name
                          $sql = "SELECT p.id, p.name, p.country_id, p.status, p.created_at, p.updated_at, c.name as country_name 
                                  FROM provinces p
                                  INNER JOIN countries c ON p.country_id = c.id
                                  ORDER BY p.id DESC";
                          $result = $conn->query($sql);

                          if (!$result) {
                              echo "<tr><td colspan='7'>Error: " . $conn->error . "</td></tr>";
                          } elseif ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              // Format dates
                              $createdAt = date('Y-m-d H:i:s', strtotime($row['created_at']));
                              $updatedAt = date('Y-m-d H:i:s', strtotime($row['updated_at']));
                              
                              echo "<tr>";
                              // Action dropdown
                              echo "<td>
                                    <div class='dropdown'>
                                      <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        Action
                                      </button>
                                      <div class='dropdown-menu'>
                                        <a class='dropdown-item' href='#' onclick='openEditModal(" . $row["id"] . ", \"" . htmlspecialchars($row["name"]) . "\", " . $row["country_id"] . ", \"" . $row["status"] . "\")'><i class='ti-pencil-alt'></i> Edit</a>
                                        <a class='dropdown-item' href='#' onclick='toggleProvinceStatus(" . $row["id"] . ", \"" . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "</a>
                                        <a class='dropdown-item' href='#' onclick='deleteProvince(" . $row["id"] . ")'><i class='ti-trash'></i> Delete</a>
                                      </div>
                                    </div>
                                    </td>";
                              
                              echo "<td>" . $row["id"] . "</td>";
                              echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                              echo "<td>" . htmlspecialchars($row["country_name"]) . "</td>";
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

  <!-- Add Province/State Modal -->
  <div class="modal fade" id="addProvinceModal" tabindex="-1" role="dialog" aria-labelledby="addProvinceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addProvinceModalLabel">Add New Province/State</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addProvinceForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="provinceName">Province/State Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="provinceName" name="name" required placeholder="Enter province/state name">
            </div>
            <div class="form-group">
              <label for="countryId">Country <span class="text-danger">*</span></label>
              <select class="form-control" id="countryId" name="country_id" required>
                <option value="">Select Country</option>
                <?php
                  include '../connect.php';
                  $countrySql = "SELECT id, name FROM countries WHERE status = 'Active' ORDER BY name";
                  $countryResult = $conn->query($countrySql);
                  if ($countryResult && $countryResult->num_rows > 0) {
                    while ($country = $countryResult->fetch_assoc()) {
                      echo "<option value='" . $country['id'] . "'>" . htmlspecialchars($country['name']) . "</option>";
                    }
                  }
                  $conn->close();
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="provinceStatus">Status</label>
              <select class="form-control" id="provinceStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Province/State</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Province/State Modal -->
  <div class="modal fade" id="editProvinceModal" tabindex="-1" role="dialog" aria-labelledby="editProvinceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProvinceModalLabel">Edit Province/State</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editProvinceForm">
          <input type="hidden" id="editProvinceId" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="editProvinceName">Province/State Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editProvinceName" name="name" required placeholder="Enter province/state name">
            </div>
            <div class="form-group">
              <label for="editCountryId">Country <span class="text-danger">*</span></label>
              <select class="form-control" id="editCountryId" name="country_id" required>
                <option value="">Select Country</option>
                <?php
                  include '../connect.php';
                  $countrySql = "SELECT id, name FROM countries WHERE status = 'Active' ORDER BY name";
                  $countryResult = $conn->query($countrySql);
                  if ($countryResult && $countryResult->num_rows > 0) {
                    while ($country = $countryResult->fetch_assoc()) {
                      echo "<option value='" . $country['id'] . "'>" . htmlspecialchars($country['name']) . "</option>";
                    }
                  }
                  $conn->close();
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="editProvinceStatus">Status</label>
              <select class="form-control" id="editProvinceStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Province/State</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <?php include '../parts/links2.php'; ?>

  <script>
    // Search functionality (searches by province name OR country name)
    function searchTable() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const table = document.querySelector('.table tbody');
      const rows = table.getElementsByTagName('tr');
      
      for (let i = 0; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByTagName('td')[2]; // Province name (index 2)
        const countryCell = rows[i].getElementsByTagName('td')[3]; // Country name (index 3)
        
        let showRow = false;
        
        if (nameCell) {
          const nameValue = nameCell.textContent || nameCell.innerText;
          if (nameValue.toLowerCase().indexOf(filter) > -1) {
            showRow = true;
          }
        }
        
        if (!showRow && countryCell) {
          const countryValue = countryCell.textContent || countryCell.innerText;
          if (countryValue.toLowerCase().indexOf(filter) > -1) {
            showRow = true;
          }
        }
        
        rows[i].style.display = showRow ? '' : 'none';
      }
    }

    // Trigger search on Enter key
    document.getElementById('searchInput').addEventListener('keyup', function(event) {
      if (event.key === 'Enter') {
        searchTable();
      }
    });

    // Add Province/State via AJAX
    document.getElementById('addProvinceForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('state/add_province.php', {
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
    function openEditModal(id, name, countryId, status) {
      document.getElementById('editProvinceId').value = id;
      document.getElementById('editProvinceName').value = name;
      document.getElementById('editCountryId').value = countryId;
      document.getElementById('editProvinceStatus').value = status;
      $('#editProvinceModal').modal('show');
    }

    // Edit Province/State via AJAX
    document.getElementById('editProvinceForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('state/edit_province.php', {
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

    // Toggle Province/State Status (Active/Inactive)
    function toggleProvinceStatus(id, newStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to mark this province/state as ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('state/toggle_province_status.php', {
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

    // Delete Province/State
    function deleteProvince(id) {
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
          fetch('state/delete_province.php', {
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