<!DOCTYPE html>
<html lang="en">
<head>
  <title>Section Management</title>
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

          <!-- Search and Add Section Button -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by Section Name" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" data-toggle="modal" data-target="#addSectionModal">
                Add New Section
              </button>
            </div>
          </div>

          <!-- Section Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Sections</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Section Name</th>
                          <th>Status</th>
                          <th>Created At</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody id="sectionTableBody">
                        <?php
                          include '../connect.php';

                          // Query to fetch sections
                          $sql = "SELECT id, name, status, created_at, updated_at FROM sections ORDER BY id DESC";
                          $result = $conn->query($sql);

                          if (!$result) {
                              echo "<tr><td colspan='6'>Error: " . $conn->error . "</td></tr>";
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
                                        <a class='dropdown-item' href='#' onclick='openEditModal(" . $row["id"] . ", \"" . htmlspecialchars($row["name"]) . "\", \"" . $row["status"] . "\")'><i class='ti-pencil-alt'></i> Edit</a>
                                        <a class='dropdown-item' href='#' onclick='toggleSectionStatus(" . $row["id"] . ", \"" . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "</a>
                                        <a class='dropdown-item' href='#' onclick='deleteSection(" . $row["id"] . ")'><i class='ti-trash'></i> Delete</a>
                                      </div>
                                    </div>
                                    </td>";
                              
                              echo "<td>" . $row["id"] . "</td>";
                              echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                              echo "<td><span class='badge " . ($row["status"] == 'Active' ? 'badge-success' : 'badge-danger') . "'>" . $row["status"] . "</span></td>";
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
        <!-- footer -->
        <?php include '../parts/footer.php'; ?>
      </div>
    </div>
  </div>

  <!-- Add Section Modal -->
  <div class="modal fade" id="addSectionModal" tabindex="-1" role="dialog" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addSectionModalLabel">Add New Section</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addSectionForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="sectionName">Section Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="sectionName" name="name" required placeholder="Enter section name (e.g., A, B, C or Morning, Evening)">
            </div>
            <div class="form-group">
              <label for="sectionStatus">Status</label>
              <select class="form-control" id="sectionStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Section</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Section Modal -->
  <div class="modal fade" id="editSectionModal" tabindex="-1" role="dialog" aria-labelledby="editSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editSectionModalLabel">Edit Section</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editSectionForm">
          <input type="hidden" id="editSectionId" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="editSectionName">Section Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editSectionName" name="name" required placeholder="Enter section name">
            </div>
            <div class="form-group">
              <label for="editSectionStatus">Status</label>
              <select class="form-control" id="editSectionStatus" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Section</button>
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
        const nameCell = rows[i].getElementsByTagName('td')[2]; // Section name is 3rd column (index 2)
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

    // Add Section via AJAX
    document.getElementById('addSectionForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('section/add_section.php', {
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
    function openEditModal(id, name, status) {
      document.getElementById('editSectionId').value = id;
      document.getElementById('editSectionName').value = name;
      document.getElementById('editSectionStatus').value = status;
      $('#editSectionModal').modal('show');
    }

    // Edit Section via AJAX
    document.getElementById('editSectionForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('section/edit_section.php', {
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

    // Toggle Section Status (Active/Inactive)
    function toggleSectionStatus(id, newStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to mark this section as ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('section/toggle_section_status.php', {
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

    // Delete Section
    function deleteSection(id) {
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
          fetch('section/delete_section.php', {
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