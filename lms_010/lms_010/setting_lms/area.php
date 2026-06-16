<!DOCTYPE html>
<html lang="en">
<head>
  <title>Area Management</title>
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

          <!-- Search and Add Area Button -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by Area Name, City, Province, or Country" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" data-toggle="modal" data-target="#addAreaModal">
                Add New Area
              </button>
            </div>
          </div>

          <!-- Area Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Areas</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Area Name</th>
                          <th>City</th>
                          <th>Province/State</th>
                          <th>Country</th>
                          <th>Status</th>
                          <th>Created At</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody id="areaTableBody">
                        <?php
                          include '../connect.php';

                          // Query to fetch areas with city, province, and country names
                          $sql = "SELECT a.id, a.name, a.city_id, a.status, a.created_at, a.updated_at, 
                                         c.name as city_name, c.province_id,
                                         p.name as province_name, p.country_id,
                                         cnt.name as country_name
                                  FROM areas a
                                  INNER JOIN cities c ON a.city_id = c.id
                                  INNER JOIN provinces p ON c.province_id = p.id
                                  INNER JOIN countries cnt ON p.country_id = cnt.id
                                  ORDER BY a.id DESC";
                          $result = $conn->query($sql);

                          if (!$result) {
                              echo "<tr><td colspan='9'>Error: " . $conn->error . "</td></tr>";
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
                                        <a class='dropdown-item' href='#' onclick='openEditModal(" . $row["id"] . ", \"" . htmlspecialchars($row["name"]) . "\", " . $row["city_id"] . ", \"" . $row["status"] . "\")'><i class='ti-pencil-alt'></i> Edit</a>
                                        <a class='dropdown-item' href='#' onclick='toggleAreaStatus(" . $row["id"] . ", \"" . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "</a>
                                        <a class='dropdown-item' href='#' onclick='deleteArea(" . $row["id"] . ")'><i class='ti-trash'></i> Delete</a>
                                      </div>
                                    </div>
                                    </td>";
                              
                              echo "<td>" . $row["id"] . "</td>";
                              echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                              echo "<td>" . htmlspecialchars($row["city_name"]) . "</td>";
                              echo "<td>" . htmlspecialchars($row["province_name"]) . "</td>";
                              echo "<td>" . htmlspecialchars($row["country_name"]) . "</td>";
                              echo "<td><span class='badge " . ($row["status"] == 'Active' ? 'badge-success' : 'badge-danger') . "'>" . $row["status"] . "</span></td>";
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
        <!-- footer -->
        <?php include '../parts/footer.php'; ?>
      </div>
    </div>
  </div>

  <!-- Add Area Modal -->
  <div class="modal fade" id="addAreaModal" tabindex="-1" role="dialog" aria-labelledby="addAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAreaModalLabel">Add New Area</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addAreaForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="areaName">Area Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="areaName" name="name" required placeholder="Enter area name (e.g., Downtown, Business District, Residential Area)">
            </div>
            <div class="form-group">
              <label for="cityId">City <span class="text-danger">*</span></label>
              <select class="form-control" id="cityId" name="city_id" required>
                <option value="">Select City</option>
                <?php
                  include '../connect.php';
                  $citySql = "SELECT c.id, c.name, p.name as province_name, cnt.name as country_name 
                              FROM cities c
                              INNER JOIN provinces p ON c.province_id = p.id
                              INNER JOIN countries cnt ON p.country_id = cnt.id
                              WHERE c.status = 'Active' AND p.status = 'Active' AND cnt.status = 'Active'
                              ORDER BY cnt.name, p.name, c.name";
                  $cityResult = $conn->query($citySql);
                  if ($cityResult && $cityResult->num_rows > 0) {
                    while ($city = $cityResult->fetch_assoc()) {
                      echo "<option value='" . $city['id'] . "'>" . htmlspecialchars($city['name']) . " (" . htmlspecialchars($city['province_name']) . ", " . htmlspecialchars($city['country_name']) . ")</option>";
                    }
                  }
                  $conn->close();
                ?>
              </select>
              <small class="form-text text-muted">Select city - shows full location hierarchy</small>
            </div>
            <div class="form-group">
              <label for="areaStatus">Status</label>
              <select class="form-control" id="areaStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Area</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Area Modal -->
  <div class="modal fade" id="editAreaModal" tabindex="-1" role="dialog" aria-labelledby="editAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAreaModalLabel">Edit Area</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editAreaForm">
          <input type="hidden" id="editAreaId" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="editAreaName">Area Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editAreaName" name="name" required placeholder="Enter area name">
            </div>
            <div class="form-group">
              <label for="editCityId">City <span class="text-danger">*</span></label>
              <select class="form-control" id="editCityId" name="city_id" required>
                <option value="">Select City</option>
                <?php
                  include '../connect.php';
                  $citySql = "SELECT c.id, c.name, p.name as province_name, cnt.name as country_name 
                              FROM cities c
                              INNER JOIN provinces p ON c.province_id = p.id
                              INNER JOIN countries cnt ON p.country_id = cnt.id
                              WHERE c.status = 'Active' AND p.status = 'Active' AND cnt.status = 'Active'
                              ORDER BY cnt.name, p.name, c.name";
                  $cityResult = $conn->query($citySql);
                  if ($cityResult && $cityResult->num_rows > 0) {
                    while ($city = $cityResult->fetch_assoc()) {
                      echo "<option value='" . $city['id'] . "'>" . htmlspecialchars($city['name']) . " (" . htmlspecialchars($city['province_name']) . ", " . htmlspecialchars($city['country_name']) . ")</option>";
                    }
                  }
                  $conn->close();
                ?>
              </select>
              <small class="form-text text-muted">Select city - shows full location hierarchy</small>
            </div>
            <div class="form-group">
              <label for="editAreaStatus">Status</label>
              <select class="form-control" id="editAreaStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Area</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <?php include '../parts/links2.php'; ?>

  <script>
    // Search functionality (searches by area name, city, province, or country)
    function searchTable() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const table = document.querySelector('.table tbody');
      const rows = table.getElementsByTagName('tr');
      
      for (let i = 0; i < rows.length; i++) {
        const areaCell = rows[i].getElementsByTagName('td')[2];     // Area name (index 2)
        const cityCell = rows[i].getElementsByTagName('td')[3];      // City name (index 3)
        const provinceCell = rows[i].getElementsByTagName('td')[4];  // Province name (index 4)
        const countryCell = rows[i].getElementsByTagName('td')[5];   // Country name (index 5)
        
        let showRow = false;
        
        if (areaCell) {
          const areaValue = areaCell.textContent || areaCell.innerText;
          if (areaValue.toLowerCase().indexOf(filter) > -1) {
            showRow = true;
          }
        }
        
        if (!showRow && cityCell) {
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

    // Add Area via AJAX
    document.getElementById('addAreaForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('area/add_area.php', {
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
    function openEditModal(id, name, cityId, status) {
      document.getElementById('editAreaId').value = id;
      document.getElementById('editAreaName').value = name;
      document.getElementById('editCityId').value = cityId;
      document.getElementById('editAreaStatus').value = status;
      $('#editAreaModal').modal('show');
    }

    // Edit Area via AJAX
    document.getElementById('editAreaForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('area/edit_area.php', {
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

    // Toggle Area Status (Active/Inactive)
    function toggleAreaStatus(id, newStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to mark this area as ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('area/toggle_area_status.php', {
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

    // Delete Area
    function deleteArea(id) {
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
          fetch('area/delete_area.php', {
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