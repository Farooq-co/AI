<!DOCTYPE html>
<html lang="en">
<head>
  <title>City Management</title>
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

          <!-- Search and Add City Button -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by City Name or Province" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" data-toggle="modal" data-target="#addCityModal">
                Add New City
              </button>
            </div>
          </div>

          <!-- City Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Cities</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>City Name</th>
                          <th>Province/State</th>
                          <th>Country</th>
                          <th>Status</th>
                          <th>Created At</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody id="cityTableBody">
                        <?php
                          include '../connect.php';

                          // Query to fetch cities with province and country names
                          $sql = "SELECT c.id, c.name, c.province_id, c.status, c.created_at, c.updated_at, 
                                         p.name as province_name, p.country_id,
                                         cnt.name as country_name
                                  FROM cities c
                                  INNER JOIN provinces p ON c.province_id = p.id
                                  INNER JOIN countries cnt ON p.country_id = cnt.id
                                  ORDER BY c.id DESC";
                          $result = $conn->query($sql);

                          if (!$result) {
                              echo "<tr><td colspan='8'>Error: " . $conn->error . "</td></tr>";
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
                                        <a class='dropdown-item' href='#' onclick='openEditModal(" . $row["id"] . ", \"" . htmlspecialchars($row["name"]) . "\", " . $row["province_id"] . ", \"" . $row["status"] . "\")'><i class='ti-pencil-alt'></i> Edit</a>
                                        <a class='dropdown-item' href='#' onclick='toggleCityStatus(" . $row["id"] . ", \"" . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "</a>
                                        <a class='dropdown-item' href='#' onclick='deleteCity(" . $row["id"] . ")'><i class='ti-trash'></i> Delete</a>
                                      </div>
                                    </div>
                                    </td>";
                              
                              echo "<td>" . $row["id"] . "</td>";
                              echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                              echo "<td>" . htmlspecialchars($row["province_name"]) . "</td>";
                              echo "<td>" . htmlspecialchars($row["country_name"]) . "</td>";
                              echo "<td><span class='badge " . ($row["status"] == 'Active' ? 'badge-success' : 'badge-danger') . "'>" . $row["status"] . "</span></td>";
                              echo "<td>" . $createdAt . "</td>";
                              echo "<td>" . $updatedAt . "</td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
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

  <!-- Add City Modal -->
  <div class="modal fade" id="addCityModal" tabindex="-1" role="dialog" aria-labelledby="addCityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCityModalLabel">Add New City</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addCityForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="cityName">City Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="cityName" name="name" required placeholder="Enter city name">
            </div>
            <div class="form-group">
              <label for="provinceId">Province/State <span class="text-danger">*</span></label>
              <select class="form-control" id="provinceId" name="province_id" required>
                <option value="">Select Province/State</option>
                <?php
                  include '../connect.php';
                  $provinceSql = "SELECT p.id, p.name, c.name as country_name 
                                  FROM provinces p
                                  INNER JOIN countries c ON p.country_id = c.id
                                  WHERE p.status = 'Active' AND c.status = 'Active'
                                  ORDER BY c.name, p.name";
                  $provinceResult = $conn->query($provinceSql);
                  if ($provinceResult && $provinceResult->num_rows > 0) {
                    while ($province = $provinceResult->fetch_assoc()) {
                      echo "<option value='" . $province['id'] . "'>" . htmlspecialchars($province['name']) . " (" . htmlspecialchars($province['country_name']) . ")</option>";
                    }
                  }
                  $conn->close();
                ?>
              </select>
              <small class="form-text text-muted">Select province/state first, then country will be auto-filled</small>
            </div>
            <div class="form-group">
              <label for="cityStatus">Status</label>
              <select class="form-control" id="cityStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add City</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit City Modal -->
  <div class="modal fade" id="editCityModal" tabindex="-1" role="dialog" aria-labelledby="editCityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editCityModalLabel">Edit City</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editCityForm">
          <input type="hidden" id="editCityId" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="editCityName">City Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editCityName" name="name" required placeholder="Enter city name">
            </div>
            <div class="form-group">
              <label for="editProvinceId">Province/State <span class="text-danger">*</span></label>
              <select class="form-control" id="editProvinceId" name="province_id" required>
                <option value="">Select Province/State</option>
                <?php
                  include '../connect.php';
                  $provinceSql = "SELECT p.id, p.name, c.name as country_name 
                                  FROM provinces p
                                  INNER JOIN countries c ON p.country_id = c.id
                                  WHERE p.status = 'Active' AND c.status = 'Active'
                                  ORDER BY c.name, p.name";
                  $provinceResult = $conn->query($provinceSql);
                  if ($provinceResult && $provinceResult->num_rows > 0) {
                    while ($province = $provinceResult->fetch_assoc()) {
                      echo "<option value='" . $province['id'] . "'>" . htmlspecialchars($province['name']) . " (" . htmlspecialchars($province['country_name']) . ")</option>";
                    }
                  }
                  $conn->close();
                ?>
              </select>
              <small class="form-text text-muted">Select province/state first, then country will be auto-filled</small>
            </div>
            <div class="form-group">
              <label for="editCityStatus">Status</label>
              <select class="form-control" id="editCityStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update City</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <?php include '../parts/links2.php'; ?>

  <script>
    // Search functionality (searches by city name OR province name OR country name)
    function searchTable() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const table = document.querySelector('.table tbody');
      const rows = table.getElementsByTagName('tr');
      
      for (let i = 0; i < rows.length; i++) {
        const cityCell = rows[i].getElementsByTagName('td')[2]; // City name (index 2)
        const provinceCell = rows[i].getElementsByTagName('td')[3]; // Province name (index 3)
        const countryCell = rows[i].getElementsByTagName('td')[4]; // Country name (index 4)
        
        let showRow = false;
        
        if (cityCell) {
          const cityValue = cityCell.textContent || cityCell.innerText;
          if (cityValue.toLowerCase().indexOf(filter) > -1) {
            showRow = true;
          }
        }
        
        if (!showRow && provinceCell) {
          const provinceValue = provinceCell.textContent || provinceCell.innerText;
          if (provinceValue.toLowerCase().indexOf(filter) > -1) {
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

    // Add City via AJAX
    document.getElementById('addCityForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('city/add_city.php', {
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
    function openEditModal(id, name, provinceId, status) {
      document.getElementById('editCityId').value = id;
      document.getElementById('editCityName').value = name;
      document.getElementById('editProvinceId').value = provinceId;
      document.getElementById('editCityStatus').value = status;
      $('#editCityModal').modal('show');
    }

    // Edit City via AJAX
    document.getElementById('editCityForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('city/edit_city.php', {
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

    // Toggle City Status (Active/Inactive)
    function toggleCityStatus(id, newStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to mark this city as ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('city/toggle_city_status.php', {
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

    // Delete City
    function deleteCity(id) {
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
          fetch('city/delete_city.php', {
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