<!DOCTYPE html>
<html lang="en">
<head>
  <title>Classes Management</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by ID or Name" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" data-toggle="modal" data-target="#addClassModal">
                Add New Class
              </button>
            </div>
          </div>

          <!-- Add Modal -->
          <div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-labelledby="addClassModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="addClassModalLabel">Add New Class</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="addClassForm">
                    <div class="form-group">
                      <label for="name">Name</label>
                      <input type="text" class="form-control" id="name" name="name" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                      <label for="status">Status</label>
                      <select class="form-control" id="status" name="status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Deleted">Deleted</option>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Modal -->
          <div class="modal fade" id="editClassModal" tabindex="-1" role="dialog" aria-labelledby="editClassModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editClassModalLabel">Edit Class</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="editClassForm">
                    <input type="hidden" id="editClassId" name="editClassId">
                    <div class="form-group">
                      <label for="editName">Name</label>
                      <input type="text" class="form-control" id="editName" name="editName" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                      <label for="editStatus">Status</label>
                      <select class="form-control" id="editStatus" name="editStatus" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Deleted">Deleted</option>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Classes</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody id="classTableBody">
                        <?php
                          include '../connect.php';

                          $sql = "SELECT id, name, status FROM classes WHERE status IN ('Active', 'Inactive')";
                          $result = $conn->query($sql);

                          if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              echo "<tr>";
                              echo "<td>
                              <div class='dropdown'>
                                <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown'>
                                  Action
                                </button>
                                <div class='dropdown-menu'>
                                  <a class='dropdown-item' href='#' onclick='editClass(\"{$row['id']}\")'><i class='bi bi-pencil'></i> Edit</a>";
                              if ($row['status'] !== 'Active') {
                                echo "<a class='dropdown-item' href='#' onclick='markAsActive(\"{$row['id']}\")'><i class='bi bi-check-circle'></i> Mark as Active</a>";
                              }
                              if ($row['status'] !== 'Inactive') {
                                echo "<a class='dropdown-item' href='#' onclick='markAsInactive(\"{$row['id']}\")'><i class='bi bi-x-circle'></i> Mark as Inactive</a>";
                              }
                              echo "<a class='dropdown-item' href='#' onclick='deleteClass(\"{$row['id']}\")'><i class='bi bi-trash'></i> Delete</a>";
                              echo "</div></div></td>";
                              echo "<td>{$row['id']}</td>";
                              echo "<td>{$row['name']}</td>";
                              echo "<td><span class='badge " . ($row['status'] == 'Active' ? 'badge-success' : 'badge-danger') . "'>{$row['status']}</span></td>";
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
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php include '../parts/footer.php'; ?>
      </div>
    </div>
  </div>

  <?php include 'classes/script.php'; ?>
  <?php include '../parts/links2.php'; ?>
</body>
</html>
