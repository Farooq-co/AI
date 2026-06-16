
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Role and Permission Management</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
    <?php include '../parts/navbar.php'; ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- parts -->
      <?php include '../parts/setting.php'; ?>
      <?php include '../parts/right_sidebar.php'; ?>
      <?php include '../parts/left_sidebar.php'; ?>

      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <!-- Search and Add New Role Button -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by ID or Role Name" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <a href="roles.php" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;">
                Add New Role
              </a>
            </div>
          </div>

          <!-- Roles Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <!-- Table Header -->
                  <div class="row">
                    <div class="col-md-6">
                      <h4 class="card-title">Role List</h4>
                    </div>
                    <div class="col-md-6 text-right">
                      <!-- Entries Dropdown -->
                      <div class="form-group">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary">Show entries</button>
                          <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="entriesDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="entriesDropdownButton">
                            <a class="dropdown-item" href="#" onclick="changeEntriesPerPage(20)">20</a>
                            <a class="dropdown-item" href="#" onclick="changeEntriesPerPage(50)">50</a>
                            <a class="dropdown-item" href="#" onclick="changeEntriesPerPage(100)">100</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Table Content -->
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th class="sortable" onclick="sortTable(1)">ID</th>
                          <th class="sortable" onclick="sortTable(2)">Role Name</th>
                          <th class="sortable" onclick="sortTable(3)">Created At</th>
                        </tr>
                      </thead>
                      <tbody id="roleTableBody">
                        <?php
                          include '../connect.php';

                          $sql = "SELECT role_id, role_name, created_at FROM roles";
                          $result = $conn->query($sql);

                          if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              echo "<tr>";
                              echo "<td>
                                    <div class='dropdown'>
                                      <button class='btn btn-primary btn-sm dropdown-toggle' type='button' id='dropdownMenuSizeButton3' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        Action
                                      </button>
                                      <div class='dropdown-menu' aria-labelledby='dropdownMenuSizeButton3'>
                                        <a class='dropdown-item' href='edit_role.php?id=" . $row["role_id"] . "'><i class='ti-pencil-alt'></i> Edit Role</a>
                                        <a class='dropdown-item' href='#' onclick='deleteRole(" . $row["role_id"] . ")'><i class='ti-trash'></i> Delete Role</a>
                                      </div>
                                    </div>
                                  </td>";
                              echo "<td>" . $row["role_id"] . "</td>";
                              echo "<td>" . htmlspecialchars($row["role_name"]) . "</td>";
                              echo "<td>" . date("d-M-Y h:iA", strtotime($row["created_at"])) . "</td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr><td colspan='4'>No records found</td></tr>";
                          }

                          $conn->close();
                        ?>
                      </tbody>
                    </table>
                  </div>

                  <!-- Pagination -->
                  <div class="d-flex justify-content-center">
                    <div id="pagination" class="btn-group mt-3" role="group" aria-label="Pagination">
                      <button type="button" class="btn btn-primary" onclick="prevPage()">Previous</button>
                      <div id="pageNumbers" class="btn-group" role="group"></div>
                      <button type="button" class="btn btn-primary" onclick="nextPage()">Next</button>
                    </div>
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

  <!-- Include your additional scripts -->
  <?php include '../parts/links2.php'; ?>
  <?php include '../parts/script_table.php'; ?>

  <!-- JavaScript functions for delete confirmation -->
  <script>
    function deleteRole(roleId) {
      if (confirm('Are you sure you want to delete this role?')) {
        window.location.href = 'delete_role.php?id=' + roleId;
      }
    }
  </script>
</body>
</html>
