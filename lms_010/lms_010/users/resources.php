<?php
// resources/index.php

// 1. Connect to DB
include '../connect.php';

// 2. Fetch all classes (for dropdowns)
$allClasses = [];
$classSql = "SELECT id, name FROM classes WHERE status IN ('Active','Inactive')";
$classResult = $conn->query($classSql);
if ($classResult && $classResult->num_rows > 0) {
  while ($cRow = $classResult->fetch_assoc()) {
    $allClasses[] = $cRow; // e.g. ["id" => 1, "name" => "Class A"]
  }
}

// 3. Fetch all books (for dropdowns)
$allBooks = [];
$bookSql = "SELECT id, name FROM books WHERE status IN ('Active','Inactive')";
$bookResult = $conn->query($bookSql);
if ($bookResult && $bookResult->num_rows > 0) {
  while ($bRow = $bookResult->fetch_assoc()) {
    $allBooks[] = $bRow; // e.g. ["id" => 10, "name" => "Book XYZ"]
  }
}

// 4. Query existing resources (now includes cover_image & resource_file)
$sqlResources = "
  SELECT r.id, r.classes_id, r.books_id, 
         r.cover_image, r.resource_file, 
         r.status,
         c.name AS class_name, b.name AS book_name
  FROM resources r
  JOIN classes c ON r.classes_id = c.id
  JOIN books   b ON r.books_id   = b.id
  WHERE r.status IN ('Active','Inactive')
";
$resResult = $conn->query($sqlResources);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Resources Management</title>
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

        <!-- ========================================== -->
        <!-- Search & Add Button                        -->
        <!-- ========================================== -->
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <div class="input-group">
                <input type="text" class="form-control" id="searchResourceInput" placeholder="Search by ID, Class, or Book">
                <div class="input-group-append">
                  <button class="btn btn-sm btn-primary" type="button" onclick="searchResourceTable()">Search</button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 text-left">
            <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom:10px;" data-toggle="modal" data-target="#addResourceModal">
              Add New Resource
            </button>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- Add Resource Modal                         -->
        <!-- (Unchanged except for existing fields)     -->
        <!-- ========================================== -->
        <div class="modal fade" id="addResourceModal" tabindex="-1" role="dialog" aria-labelledby="addResourceModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addResourceModalLabel">Add New Resource</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="addResourceForm">
                  <!-- Select Class -->
                  <div class="form-group">
                    <label for="resourceClass">Select Class</label>
                    <select class="form-control" id="resourceClass" name="resourceClass" required>
                      <option value="">-- Select Class --</option>
                      <?php
                      foreach ($allClasses as $class) {
                        echo "<option value='{$class['id']}'>{$class['name']}</option>";
                      }
                      ?>
                    </select>
                  </div>

                  <!-- Select Book -->
                  <div class="form-group">
                    <label for="resourceBook">Select Book</label>
                    <select class="form-control" id="resourceBook" name="resourceBook" required>
                      <option value="">-- Select Book --</option>
                      <?php
                      foreach ($allBooks as $book) {
                        echo "<option value='{$book['id']}'>{$book['name']}</option>";
                      }
                      ?>
                    </select>
                  </div>

                  <!-- Status -->
                  <div class="form-group">
                    <label for="resourceStatus">Status</label>
                    <select class="form-control" id="resourceStatus" name="resourceStatus" required>
                      <option value="Active">Active</option>
                      <option value="Inactive">Inactive</option>
                      <option value="Deleted">Deleted</option>
                    </select>
                  </div>

                  <!-- No file inputs in Add Modal as per requirement -->

                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- Edit Resource Modal                        -->
        <!-- (Unchanged except for existing fields)     -->
        <!-- ========================================== -->
        <div class="modal fade" id="editResourceModal" tabindex="-1" role="dialog" aria-labelledby="editResourceModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editResourceModalLabel">Edit Resource</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="editResourceForm">
                  <input type="hidden" id="editResourceId" name="editResourceId" />

                  <!-- Select Class -->
                  <div class="form-group">
                    <label for="editResourceClass">Select Class</label>
                    <select class="form-control" id="editResourceClass" name="editResourceClass" required>
                      <option value="">-- Select Class --</option>
                      <?php
                      foreach ($allClasses as $class) {
                        echo "<option value='{$class['id']}'>{$class['name']}</option>";
                      }
                      ?>
                    </select>
                  </div>

                  <!-- Select Book -->
                  <div class="form-group">
                    <label for="editResourceBook">Select Book</label>
                    <select class="form-control" id="editResourceBook" name="editResourceBook" required>
                      <option value="">-- Select Book --</option>
                      <?php
                      foreach ($allBooks as $book) {
                        echo "<option value='{$book['id']}'>{$book['name']}</option>";
                      }
                      ?>
                    </select>
                  </div>

                  <!-- Status -->
                  <div class="form-group">
                    <label for="editResourceStatus">Status</label>
                    <select class="form-control" id="editResourceStatus" name="editResourceStatus" required>
                      <option value="Active">Active</option>
                      <option value="Inactive">Inactive</option>
                      <option value="Deleted">Deleted</option>
                    </select>
                  </div>

                  <!-- No file inputs in Edit Modal as per requirement -->

                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- Upload Cover Image Modal                   -->
        <!-- ========================================== -->
        <div class="modal fade" id="uploadCoverImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadCoverImageModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <form id="uploadCoverImageForm" enctype="multipart/form-data">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="uploadCoverImageModalLabel">Upload Cover Image</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" id="uploadCoverImageResourceId" name="resource_id" />
                  <div class="form-group">
                    <label for="coverImageFile">Choose Cover Image</label>
                    <input type="file" class="form-control-file" id="coverImageFile" name="cover_image" accept="image/*" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Upload</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- Upload Resource File Modal                 -->
        <!-- ========================================== -->
        <div class="modal fade" id="uploadResourceFileModal" tabindex="-1" role="dialog" aria-labelledby="uploadResourceFileModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <form id="uploadResourceFileForm" enctype="multipart/form-data">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="uploadResourceFileModalLabel">Upload Resource File</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" id="uploadResourceFileResourceId" name="resource_id" />
                  <div class="form-group">
                    <label for="resourceFileInput">Choose File (PDF, DOC, etc.)</label>
                    <input type="file" class="form-control-file" id="resourceFileInput" name="resource_file" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Upload</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- Resources Table                            -->
        <!-- ========================================== -->
        <div class="row">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Resources</h4>
                <div class="table-responsive pt-3">
                  <table class="table table-bordered" id="resourcesTable">
                    <thead>
                      <tr>
                        <th>Action</th>
                        <th>ID</th>
                        <th>Class</th>
                        <th>Book</th>
                        <!-- New columns -->
                        <th>Cover Image</th>
                        <th>Resource File</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody id="resourceTableBody">
                      <?php
                      if ($resResult && $resResult->num_rows > 0) {
                        while ($rRow = $resResult->fetch_assoc()) {
                          $coverImagePath = !empty($rRow['cover_image']) ? "../uploads/cover_images/{$rRow['cover_image']}" : "";
                          $resourceFilePath = !empty($rRow['resource_file']) ? "../uploads/resource_files/{$rRow['resource_file']}" : "";

                          echo "<tr>";
                          echo "<td>
                                  <div class='dropdown'>
                                    <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown'>
                                      Action
                                    </button>
                                    <div class='dropdown-menu'>
                                      <a class='dropdown-item' href='#' onclick='editResource({$rRow['id']})'><i class='bi bi-pencil'></i> Edit</a>";
                          if ($rRow['status'] !== 'Active') {
                            echo "<a class='dropdown-item' href='#' onclick='markResourceAsActive({$rRow['id']})'><i class='bi bi-check-circle'></i> Mark as Active</a>";
                          }
                          if ($rRow['status'] !== 'Inactive') {
                            echo "<a class='dropdown-item' href='#' onclick='markResourceAsInactive({$rRow['id']})'><i class='bi bi-x-circle'></i> Mark as Inactive</a>";
                          }
                          echo "<a class='dropdown-item' href='#' onclick='deleteResource({$rRow['id']})'><i class='bi bi-trash'></i> Delete</a>";
                          echo "</div></div>
                                </td>";

                          echo "<td>{$rRow['id']}</td>";
                          echo "<td>{$rRow['class_name']}</td>";
                          echo "<td>{$rRow['book_name']}</td>";

                          // Cover Image column
                          echo "<td>";
                          if (!empty($rRow['cover_image']) && file_exists($coverImagePath)) {
                            echo "<img src='{$coverImagePath}' alt='Cover' style='width:60px; height:auto;' />";
                          } else {
                            echo "No Image";
                          }
                          echo " <button class='btn btn-sm btn-info ml-2' onclick='openUploadCoverModal({$rRow['id']})'>Upload</button>";
                          echo "</td>";

                          // Resource File column
                          echo "<td>";
                          if (!empty($rRow['resource_file']) && file_exists($resourceFilePath)) {
                            // Could show a link to download or view
                            echo "<a href='{$resourceFilePath}' target='_blank'>View File</a>";
                          } else {
                            echo "No File";
                          }
                          echo " <button class='btn btn-sm btn-info ml-2' onclick='openUploadResourceFileModal({$rRow['id']})'>Upload</button>";
                          echo "</td>";

                          // Status column
                          $badgeClass = ($rRow['status'] == 'Active') ? 'badge-success' : 'badge-danger';
                          echo "<td><span class='badge {$badgeClass}'>{$rRow['status']}</span></td>";

                          echo "</tr>";
                        }
                      } else {
                        echo "<tr><td colspan='7'>No resources found</td></tr>";
                      }
                      $conn->close();
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- row -->

      </div> <!-- content-wrapper -->
      <?php include '../parts/footer.php'; ?>
    </div> <!-- main-panel -->
  </div> <!-- container-fluid page-body-wrapper -->
</div> <!-- container-scroller -->

<?php include '../parts/links2.php'; ?>

<script>
// ==================== ADD Resource ====================
document.getElementById('addResourceForm').addEventListener('submit', function(e) {
  e.preventDefault();

  let classes_id = document.getElementById('resourceClass').value;
  let books_id   = document.getElementById('resourceBook').value;
  let status     = document.getElementById('resourceStatus').value;

  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'resources/add.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function() {
    if (this.status == 200) {
      location.reload();
    } else {
      alert('Error adding resource.');
    }
  };
  xhr.send(
    'classes_id=' + encodeURIComponent(classes_id) +
    '&books_id='  + encodeURIComponent(books_id)  +
    '&status='    + encodeURIComponent(status)
  );
});

// ==================== EDIT Resource ====================
function editResource(id) {
  let xhr = new XMLHttpRequest();
  xhr.open('GET', 'resources/get.php?id=' + id, true);
  xhr.onload = function() {
    if (this.status == 200) {
      let resData = JSON.parse(this.responseText);
      // Populate the form
      document.getElementById('editResourceId').value      = resData.id;
      document.getElementById('editResourceClass').value   = resData.classes_id;
      document.getElementById('editResourceBook').value    = resData.books_id;
      document.getElementById('editResourceStatus').value  = resData.status;

      // Show the modal
      $('#editResourceModal').modal('show');
    } else {
      alert('Error fetching resource details.');
    }
  };
  xhr.send();
}

document.getElementById('editResourceForm').addEventListener('submit', function(e) {
  e.preventDefault();

  let id         = document.getElementById('editResourceId').value;
  let classes_id = document.getElementById('editResourceClass').value;
  let books_id   = document.getElementById('editResourceBook').value;
  let status     = document.getElementById('editResourceStatus').value;

  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'resources/edit.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function() {
    if (this.status == 200) {
      location.reload();
    } else {
      alert('Error updating resource.');
    }
  };
  xhr.send(
    'id='          + encodeURIComponent(id) +
    '&classes_id=' + encodeURIComponent(classes_id) +
    '&books_id='   + encodeURIComponent(books_id) +
    '&status='     + encodeURIComponent(status)
  );
});

// ==================== UPDATE STATUS ====================
function updateResourceStatus(id, newStatus) {
  if (!confirm('Change status to ' + newStatus + '?')) return;

  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'resources/update_status.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function() {
    if (this.status == 200) {
      location.reload();
    } else {
      alert('Error updating status.');
    }
  };
  xhr.send('id=' + encodeURIComponent(id) + '&status=' + encodeURIComponent(newStatus));
}

function markResourceAsActive(id) {
  updateResourceStatus(id, 'Active');
}
function markResourceAsInactive(id) {
  updateResourceStatus(id, 'Inactive');
}
function deleteResource(id) {
  updateResourceStatus(id, 'Deleted');
}

// ==================== SEARCH Function ====================
function searchResourceTable() {
  let input  = document.getElementById("searchResourceInput");
  let filter = input.value.toUpperCase();
  let table  = document.getElementById("resourcesTable");
  let tr     = table.getElementsByTagName("tr");

  for (let i = 1; i < tr.length; i++) {
    let tds = tr[i].getElementsByTagName("td");
    if (tds.length < 5) continue;

    // ID: tds[1], Class: tds[2], Book: tds[3]
    let idVal     = (tds[1].textContent || tds[1].innerText).toUpperCase();
    let classVal  = (tds[2].textContent || tds[2].innerText).toUpperCase();
    let bookVal   = (tds[3].textContent || tds[3].innerText).toUpperCase();

    if (idVal.indexOf(filter) > -1 || classVal.indexOf(filter) > -1 || bookVal.indexOf(filter) > -1) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }
  }
}

// ==================== UPLOAD COVER IMAGE ====================
function openUploadCoverModal(resourceId) {
  document.getElementById('uploadCoverImageResourceId').value = resourceId;
  document.getElementById('coverImageFile').value = '';
  $('#uploadCoverImageModal').modal('show');
}

document.getElementById('uploadCoverImageForm').addEventListener('submit', function(e) {
  e.preventDefault();

  let formData = new FormData(this);
  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'resources/upload_files.php', true);

  xhr.onload = function() {
    if (xhr.status === 200) {
      // Refresh the page or update row dynamically
      location.reload();
    } else {
      alert('Error uploading cover image.');
    }
  };
  xhr.send(formData);
});

// ==================== UPLOAD RESOURCE FILE ====================
function openUploadResourceFileModal(resourceId) {
  document.getElementById('uploadResourceFileResourceId').value = resourceId;
  document.getElementById('resourceFileInput').value = '';
  $('#uploadResourceFileModal').modal('show');
}

document.getElementById('uploadResourceFileForm').addEventListener('submit', function(e) {
  e.preventDefault();

  let formData = new FormData(this);
  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'resources/upload_files.php', true);

  xhr.onload = function() {
    if (xhr.status === 200) {
      location.reload();
    } else {
      alert('Error uploading resource file.');
    }
  };
  xhr.send(formData);
});
</script>

</body>
</html>
