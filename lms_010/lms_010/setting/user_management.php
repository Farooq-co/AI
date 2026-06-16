
<!DOCTYPE html>
<html lang="en">
<head>
  <title>User Management</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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

          <!-- Search and Add User Button -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by Username or Email" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 text-left">
              <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" data-toggle="modal" data-target="#addUserModal">
                Add New User
              </button>
            </div>
          </div>

          <!-- Add User Modal -->
          <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="addUserForm">
                    <div class="form-group">
                      <label for="addUsername">Username</label>
                      <input type="text" class="form-control" id="addUsername" name="username" required>
                    </div>
                    <div class="form-group">
                      <label for="addEmail">Email</label>
                      <input type="email" class="form-control" id="addEmail" name="email" required>
                    </div>
                    <div class="form-group">
                      <label for="addPassword">Password</label>
                      <input type="password" class="form-control" id="addPassword" name="password" required>
                    </div>
                    <div class="form-group">
                      <label for="addRole">Role/Permissions</label>
                      <select class="form-control" id="addRole" name="role_id" required>
                        <option value="">Select Role</option>
                        <?php
                          include '../connect.php';
                          $roleQuery = "SELECT role_id, role_name FROM roles";
                          $roleResult = $conn->query($roleQuery);
                          while ($roleRow = $roleResult->fetch_assoc()) {
                            echo "<option value='" . $roleRow['role_id'] . "'>" . $roleRow['role_name'] . "</option>";
                          }
                          $conn->close();
                        ?>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit User Modal -->
          <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="form-group">
                      <label for="editUsername">Username</label>
                      <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                      <label for="editEmail">Email</label>
                      <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                      <label for="editPassword">Password (Leave blank to keep current password)</label>
                      <input type="password" class="form-control" id="editPassword" name="password">
                    </div>
                    <div class="form-group">
                      <label for="editRole">Role/Permissions</label>
                      <select class="form-control" id="editRole" name="role_id" required>
                        <option value="">Select Role</option>
                        <?php
                          include '../connect.php';
                          $roleQuery = "SELECT role_id, role_name FROM roles";
                          $roleResult = $conn->query($roleQuery);
                          while ($roleRow = $roleResult->fetch_assoc()) {
                            echo "<option value='" . $roleRow['role_id'] . "'>" . $roleRow['role_name'] . "</option>";
                          }
                          $conn->close();
                        ?>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- User Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Users</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Username</th>
                          <th>Email</th>
                          <th>Role/Permissions</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody id="userTableBody">
                        <?php
                          include '../connect.php';

                          $sql = "SELECT users.id, users.username, users.email, users.status, roles.role_name
                                  FROM users
                                  INNER JOIN roles ON users.role_id = roles.role_id";
                          $result = $conn->query($sql);

                          if (!$result) {
                              echo "<tr><td colspan='6'>Error: " . $conn->error . "</td></tr>";
                          } elseif ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              echo "<tr>";
                              echo "<td>
                                    <div class='dropdown'>
                                      <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        Action
                                      </button>
                                      <div class='dropdown-menu'>
                                        <a class='dropdown-item' href='#' onclick='openEditModal(" . $row["id"] . ")'><i class='ti-pencil-alt'></i> Edit</a>
                                        <a class='dropdown-item' href='#' onclick='changeUserStatus(" . $row["id"] . ", \"" . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "</a>
                                        <a class='dropdown-item' href='#' onclick='deleteUser(" . $row["id"] . ")'><i class='ti-trash'></i> Delete</a>
                                      </div>
                                    </div>
                                  </td>";
                              echo "<td>" . $row["id"] . "</td>";
                              echo "<td>" . $row["username"] . "</td>";
                              echo "<td>" . $row["email"] . "</td>";
                              echo "<td>" . $row["role_name"] . "</td>";
                              echo "<td><span class='badge " . ($row["status"] == 'Active' ? 'badge-success' : 'badge-danger') . "'>" . $row["status"] . "</span></td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr><td colspan='6'>No records found</td></tr>";
                          }

                          $conn->close();
                        ?>
                      </tbody>
                    </table>
                  </div>

                  <!-- Pagination (if needed) -->
                  <!-- Add your pagination code here if necessary -->

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

  <!-- Scripts -->
  <?php include '../parts/links2.php'; ?>

  <!-- Custom JavaScript -->
  <script>
    // Add User
    $('#addUserForm').submit(function(e) {
      e.preventDefault();
      $.ajax({
        url: 'user_actions.php',
        type: 'POST',
        data: {
          action: 'add',
          username: $('#addUsername').val(),
          email: $('#addEmail').val(),
          password: $('#addPassword').val(),
          role_id: $('#addRole').val()
        },
        success: function(response) {
          alert(response);
          location.reload();
        }
      });
    });

    // Open Edit Modal
    function openEditModal(id) {
      $.ajax({
        url: 'user_actions.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'get_user', id: id },
        success: function(user) {
          $('#editUserId').val(user.id);
          $('#editUsername').val(user.username);
          $('#editEmail').val(user.email);
          $('#editPassword').val('');
          $('#editRole').val(user.role_id);
          $('#editUserModal').modal('show');
        }
      });
    }

    // Edit User
    $('#editUserForm').submit(function(e) {
      e.preventDefault();
      $.ajax({
        url: 'user_actions.php',
        type: 'POST',
        data: {
          action: 'edit',
          id: $('#editUserId').val(),
          username: $('#editUsername').val(),
          email: $('#editEmail').val(),
          password: $('#editPassword').val(),
          role_id: $('#editRole').val()
        },
        success: function(response) {
          alert(response);
          location.reload();
        }
      });
    });

    // Change User Status
    function changeUserStatus(id, status) {
      if (confirm('Are you sure you want to mark this user as ' + status + '?')) {
        $.ajax({
          url: 'user_actions.php',
          type: 'POST',
          data: { action: 'change_status', id: id, status: status },
          success: function(response) {
            alert(response);
            location.reload();
          }
        });
      }
    }

    // Delete User
    function deleteUser(id) {
      if (confirm('Are you sure you want to delete this user?')) {
        $.ajax({
          url: 'user_actions.php',
          type: 'POST',
          data: { action: 'delete', id: id },
          success: function(response) {
            alert(response);
            location.reload();
          }
        });
      }
    }

    // Search Function (Optional)
    function searchTable() {
      var input = document.getElementById("searchInput");
      var filter = input.value.toLowerCase();
      var table = document.getElementById("userTableBody");
      var tr = table.getElementsByTagName("tr");
      for (var i = 0; i < tr.length; i++) {
        var tdUsername = tr[i].getElementsByTagName("td")[2];
        var tdEmail = tr[i].getElementsByTagName("td")[3];
        if (tdUsername || tdEmail) {
          var usernameText = tdUsername.textContent || tdUsername.innerText;
          var emailText = tdEmail.textContent || tdEmail.innerText;
          if (usernameText.toLowerCase().indexOf(filter) > -1 || emailText.toLowerCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    }
  </script>
</body>
</html>
