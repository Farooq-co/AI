<!DOCTYPE html>
<html lang="en">
<head>
  <title>User Management</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 for better alerts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .logo-preview-sm {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ddd;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .logo-preview-sm:hover {
      transform: scale(1.1);
      border-color: #007bff;
    }
    .logo-preview-modal {
      max-width: 150px;
      max-height: 150px;
      border-radius: 8px;
      margin-top: 10px;
      border: 1px solid #ddd;
    }
    .user-logo-cell {
      text-align: center;
      vertical-align: middle;
      width: 60px;
    }
    .no-logo {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #f0f0f0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      color: #999;
      cursor: pointer;
      border: 2px solid #ddd;
    }
  </style>
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
                  <input type="text" class="form-control" id="searchInput" placeholder="Search by Username, Email, or Institution" aria-label="Search">
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
                  <form id="addUserForm" enctype="multipart/form-data">
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
                      <label for="addInstitution">Institution Name</label>
                      <input type="text" class="form-control" id="addInstitution" name="institution_name" required>
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
                    <div class="form-group">
                      <label for="addLogo">User Logo (Optional)</label>
                      <input type="file" class="form-control-file" id="addLogo" name="logo" accept="image/*">
                      <small class="form-text text-muted">Upload a profile image for the user (JPG, PNG, GIF). Max size: 2MB.</small>
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
                  <form id="editUserForm" enctype="multipart/form-data">
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
                      <label for="editInstitution">Institution Name</label>
                      <input type="text" class="form-control" id="editInstitution" name="institution_name" required>
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
                    <div class="form-group">
                      <label>Current Logo</label>
                      <div id="currentLogoPreview" class="text-center"></div>
                      <label for="editLogo" class="mt-2">Change Logo (Optional)</label>
                      <input type="file" class="form-control-file" id="editLogo" name="logo" accept="image/*">
                      <small class="form-text text-muted">Upload new image to replace existing logo.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Upload Logo Modal (Separate modal for standalone logo upload) -->
          <div class="modal fade" id="uploadLogoModal" tabindex="-1" role="dialog" aria-labelledby="uploadLogoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="uploadLogoModalLabel">Upload User Logo</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="uploadLogoForm" enctype="multipart/form-data">
                    <input type="hidden" id="uploadUserId" name="user_id">
                    <div class="form-group">
                      <label for="userLogoFile">Select Logo Image</label>
                      <input type="file" class="form-control-file" id="userLogoFile" name="logo" accept="image/*" required>
                      <small class="form-text text-muted">Supported formats: JPG, PNG, GIF. Max size: 2MB.</small>
                    </div>
                    <div id="logoPreview" class="text-center"></div>
                    <button type="submit" class="btn btn-primary mt-2">Upload Logo</button>
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
                          <th>Logo</th>
                          <th>ID</th>
                          <th>Institution Name</th>
                          <th>Username</th>
                          <th>Email</th>
                          <th>Role/Permissions</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody id="userTableBody">
                        <?php
                          include '../connect.php';

                          // Modify query to include logo field
                          $sql = "SELECT users.id, users.username, users.email, users.institution_name, users.status, users.logo, roles.role_name
                                  FROM users
                                  INNER JOIN roles ON users.role_id = roles.role_id";
                          $result = $conn->query($sql);

                          // Define base path for uploads
                          $basePath = dirname(__DIR__); // Go up one level from users folder
                          $uploadDir = '../uploads/logos/';
                          
                          if (!$result) {
                              echo "<tr><td colspan='8'>Error: " . $conn->error . "</td></tr>";
                          } elseif ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              // Determine logo source with proper path checking
                              $logoPath = '';
                              $hasLogo = false;
                              
                              if (!empty($row['logo'])) {
                                  $fullPath = __DIR__ . '/../uploads/logos/' . $row['logo'];
                                  if (file_exists($fullPath)) {
                                      $logoPath = '../uploads/logos/' . $row['logo'];
                                      $hasLogo = true;
                                  }
                              }
                              
                              // If no logo found, use Font Awesome icon
                              if (!$hasLogo) {
                                  $logoPath = '';
                              }
                              
                              echo "<tr>";
                              // Action dropdown with Upload Logo button
                              echo "<td>
                                    <div class='dropdown'>
                                      <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        Action
                                      </button>
                                      <div class='dropdown-menu'>
                                        <a class='dropdown-item' href='#' onclick='openEditModal(" . $row["id"] . ")'><i class='ti-pencil-alt'></i> Edit</a>
                                        <a class='dropdown-item' href='#' onclick='openUploadLogoModal(" . $row["id"] . ")'><i class='ti-image'></i> Upload Logo</a>
                                        <a class='dropdown-item' href='#' onclick='changeUserStatus(" . $row["id"] . ", \"" . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "</a>
                                        <a class='dropdown-item' href='#' onclick='deleteUser(" . $row["id"] . ")'><i class='ti-trash'></i> Delete</a>
                                      </div>
                                    </div>
                                   </td>";
                              
                              // Logo column with click preview
                              echo "<td class='user-logo-cell'>";
                              if ($hasLogo && $logoPath) {
                                  echo "<img src='" . $logoPath . "' class='logo-preview-sm' alt='Logo' onclick='showLogoPreview(\"" . $logoPath . "\", \"" . addslashes($row["username"]) . "\")'>";
                              } else {
                                  echo "<div class='no-logo' onclick='showNoLogoMessage(\"" . addslashes($row["username"]) . "\")'>";
                                  echo "<i class='ti-user'></i>";
                                  echo "</div>";
                              }
                              echo "</td>";
                              
                              echo "<td>" . $row["id"] . "</td>";
                              echo "<td>" . htmlspecialchars($row["institution_name"]) . "</td>";
                              echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                              echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                              echo "<td>" . htmlspecialchars($row["role_name"]) . "</td>";
                              echo "<td><span class='badge " . ($row["status"] == 'Active' ? 'badge-success' : 'badge-danger') . "'>" . $row["status"] . "</span></td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr><td colspan='8'>No records found</td></tr>";
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

  <!-- Scripts -->
  <?php include '../parts/links2.php'; ?>

  <!-- Custom JavaScript -->
  <script>
    // Helper function to show SweetAlert for logo preview
    function showLogoPreview(imgSrc, userName) {
      Swal.fire({
        title: userName + "'s Logo",
        imageUrl: imgSrc,
        imageWidth: 250,
        imageHeight: 250,
        imageAlt: 'User Logo',
        confirmButtonText: 'Close',
        customClass: {
          image: 'logo-preview-modal'
        }
      });
    }
    
    function showNoLogoMessage(userName) {
      Swal.fire({
        title: 'No Logo Available',
        text: userName + ' does not have a logo uploaded yet. You can upload one using the "Upload Logo" option in the Action menu.',
        icon: 'info',
        confirmButtonText: 'OK'
      });
    }

    // Open Upload Logo Modal
    function openUploadLogoModal(userId) {
      $('#uploadUserId').val(userId);
      $('#logoPreview').html('');
      $('#userLogoFile').val('');
      $('#uploadLogoModal').modal('show');
    }

    // Preview logo before upload
    $('#userLogoFile').on('change', function() {
      const file = this.files[0];
      if (file) {
        // Check file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
          Swal.fire('Error!', 'File size must be less than 2MB.', 'error');
          $(this).val('');
          $('#logoPreview').html('');
          return;
        }
        
        // Check file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
          Swal.fire('Error!', 'Only JPG, JPEG, PNG, and GIF files are allowed.', 'error');
          $(this).val('');
          $('#logoPreview').html('');
          return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#logoPreview').html('<img src="' + e.target.result + '" class="logo-preview-modal" alt="Preview" style="max-width:200px; max-height:200px;">');
        }
        reader.readAsDataURL(file);
      } else {
        $('#logoPreview').html('');
      }
    });

    // Handle Upload Logo Form Submission
    $('#uploadLogoForm').submit(function(e) {
      e.preventDefault();
      
      const fileInput = $('#userLogoFile')[0];
      if (!fileInput.files[0]) {
        Swal.fire('Error!', 'Please select a file to upload.', 'error');
        return;
      }
      
      const formData = new FormData(this);
      formData.append('action', 'upload_logo');

      $.ajax({
        url: 'user_actions.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.includes('successfully')) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response,
              confirmButtonText: 'OK'
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response,
              confirmButtonText: 'OK'
            });
          }
        },
        error: function(xhr, status, error) {
          Swal.fire('Error!', 'Failed to upload logo. Error: ' + error, 'error');
        }
      });
    });

    // Add User with optional logo
    $('#addUserForm').submit(function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      formData.append('action', 'add');

      $.ajax({
        url: 'user_actions.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.includes('successfully')) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response,
              confirmButtonText: 'OK'
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response,
              confirmButtonText: 'OK'
            });
          }
        },
        error: function() {
          Swal.fire('Error!', 'Failed to add user. Please try again.', 'error');
        }
      });
    });

    // Open Edit Modal with existing logo preview
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
          $('#editInstitution').val(user.institution_name);
          
          // Show current logo preview in edit modal
          let logoHtml = '';
          if (user.logo && user.logo !== '') {
            // Check if file exists
            const logoPath = '../uploads/logos/' + user.logo;
            logoHtml = '<img src="' + logoPath + '" class="logo-preview-modal" style="max-width:100px; max-height:100px;" alt="Current Logo" onerror="this.style.display=\'none\'"><br><small class="text-muted">Current Logo</small>';
          } else {
            logoHtml = '<div class="text-muted"><i class="ti-user"></i> No logo uploaded</div>';
          }
          $('#currentLogoPreview').html(logoHtml);
          $('#editLogo').val('');
          $('#editUserModal').modal('show');
        },
        error: function() {
          Swal.fire('Error!', 'Failed to load user data.', 'error');
        }
      });
    }

    // Edit User with optional logo change
    $('#editUserForm').submit(function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      formData.append('action', 'edit');

      $.ajax({
        url: 'user_actions.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.includes('successfully')) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response,
              confirmButtonText: 'OK'
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response,
              confirmButtonText: 'OK'
            });
          }
        },
        error: function() {
          Swal.fire('Error!', 'Failed to update user. Please try again.', 'error');
        }
      });
    });

    // Change User Status
    function changeUserStatus(id, status) {
      Swal.fire({
        title: 'Are you sure?',
        text: `You want to mark this user as ${status}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'user_actions.php',
            type: 'POST',
            data: { action: 'change_status', id: id, status: status },
            success: function(response) {
              Swal.fire({
                icon: 'success',
                title: 'Status Updated!',
                text: response,
                confirmButtonText: 'OK'
              }).then(() => {
                location.reload();
              });
            },
            error: function() {
              Swal.fire('Error!', 'Failed to update status.', 'error');
            }
          });
        }
      });
    }

    // Delete User
    function deleteUser(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'user_actions.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
              Swal.fire(
                'Deleted!',
                response,
                'success'
              ).then(() => {
                location.reload();
              });
            },
            error: function() {
              Swal.fire('Error!', 'Failed to delete user.', 'error');
            }
          });
        }
      });
    }

    // Search Function
    function searchTable() {
      var input = document.getElementById("searchInput");
      var filter = input.value.toLowerCase();
      var table = document.getElementById("userTableBody");
      var tr = table.getElementsByTagName("tr");
      for (var i = 0; i < tr.length; i++) {
        var tdInstitution = tr[i].getElementsByTagName("td")[3];
        var tdUsername = tr[i].getElementsByTagName("td")[4];
        var tdEmail = tr[i].getElementsByTagName("td")[5];
        if (tdInstitution && tdUsername && tdEmail) {
          var institutionText = tdInstitution.textContent || tdInstitution.innerText;
          var usernameText = tdUsername.textContent || tdUsername.innerText;
          var emailText = tdEmail.textContent || tdEmail.innerText;
          if (institutionText.toLowerCase().indexOf(filter) > -1 || usernameText.toLowerCase().indexOf(filter) > -1 || emailText.toLowerCase().indexOf(filter) > -1) {
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