<!DOCTYPE html>
<html lang="en">
<head>
  <title>Guardian Management</title>
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

          <!-- Search and Add Guardian Button -->
          <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <div class="input-group">
                          <input type="text" class="form-control" id="searchInput" placeholder="Search by Father Name, Mother Name, or Guardian Name" aria-label="Search">
                          <div class="input-group-append">
                              <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6 text-right">
                  <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" onclick="window.location.href='add_guardian.php'">
                      Add New Guardian
                  </button>
                  <button type="button" class="btn btn-info btn-rounded btn-fw" style="margin-bottom: 10px;" onclick="window.open('ajax/print_guardian_list.php', '_blank');">
                      <i class="bi bi-printer"></i> Print List
                  </button>
              </div>
          </div>

          <!-- Guardian Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Guardian Records</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Father Information</th>
                          <th>Mother Information</th>
                          <th>Guardian Information</th>
                          <th>Present Address</th>
                          <th>Permanent Address</th>
                          <th>Status</th>
                          <th>Created At</th>
                        </tr>
                        <tr>
                          <th></th>
                          <th></th>
                          <th>Name / CNIC / Contact</th>
                          <th>Name / CNIC / Contact</th>
                          <th>Name / CNIC / Contact</th>
                          <th>Address / City / Area</th>
                          <th>Address / City / Area</th>
                          <th></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody id="guardianTableBody">
                        <?php
                          include '../connect.php';

                          // Query to fetch guardians with city and area names
                          $sql = "SELECT sg.*, 
                                  pc.name as present_city_name, 
                                  pa.name as present_area_name,
                                  perc.name as permanent_city_name,
                                  pera.name as permanent_area_name
                                  FROM student_guardians sg 
                                  LEFT JOIN cities pc ON sg.present_city_id = pc.id 
                                  LEFT JOIN areas pa ON sg.present_area_id = pa.id
                                  LEFT JOIN cities perc ON sg.permanent_city_id = perc.id 
                                  LEFT JOIN areas pera ON sg.permanent_area_id = pera.id
                                  ORDER BY sg.id DESC";
                          $result = $conn->query($sql);

                          if (!$result) {
                              echo "<tr><td colspan='9'>Error: " . $conn->error . "</td></tr>";
                          } elseif ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              // Format date
                              $createdAt = date('Y-m-d H:i:s', strtotime($row['created_at']));
                              
                              // Father Information
                              $fatherInfo = "<strong>" . htmlspecialchars($row['father_name']) . "</strong><br>";
                              $fatherInfo .= "CNIC: " . (htmlspecialchars($row['father_cnic'] ?? 'N/A')) . "<br>";
                              $fatherInfo .= "Mobile: " . (htmlspecialchars($row['father_mobile'] ?? 'N/A'));
                              if(!empty($row['father_whatsapp_number'])) {
                                $fatherInfo .= "<br>WhatsApp: " . htmlspecialchars($row['father_whatsapp_number']);
                              }
                              
                              // Mother Information
                              $motherInfo = "<strong>" . htmlspecialchars($row['mother_name']) . "</strong><br>";
                              $motherInfo .= "CNIC: " . (htmlspecialchars($row['mother_cnic'] ?? 'N/A')) . "<br>";
                              $motherInfo .= "Mobile: " . (htmlspecialchars($row['mother_mobile'] ?? 'N/A'));
                              if(!empty($row['mother_whatsapp_number'])) {
                                $motherInfo .= "<br>WhatsApp: " . htmlspecialchars($row['mother_whatsapp_number']);
                              }
                              
                              // Guardian Information
                              $guardianInfo = "<strong>" . htmlspecialchars($row['guardian_name']) . "</strong><br>";
                              $guardianInfo .= "CNIC: " . (htmlspecialchars($row['guardian_cnic'] ?? 'N/A')) . "<br>";
                              $guardianInfo .= "Mobile: " . (htmlspecialchars($row['guardian_mobile'] ?? 'N/A'));
                              if(!empty($row['guardian_whatsapp_number'])) {
                                $guardianInfo .= "<br>WhatsApp: " . htmlspecialchars($row['guardian_whatsapp_number']);
                              }
                              
                              // Present Address
                              $presentAddress = htmlspecialchars($row['present_address']) . "<br>";
                              $presentAddress .= "<strong>City:</strong> " . htmlspecialchars($row['present_city_name'] ?? 'N/A') . "<br>";
                              $presentAddress .= "<strong>Area:</strong> " . htmlspecialchars($row['present_area_name'] ?? 'N/A') . "<br>";
                              $presentAddress .= "<strong>Country:</strong> " . htmlspecialchars($row['present_country'] ?? 'N/A') . "<br>";
                              $presentAddress .= "<strong>Province:</strong> " . htmlspecialchars($row['present_province'] ?? 'N/A');
                              
                              // Permanent Address
                              if(!empty($row['permanent_address'])) {
                                $permanentAddress = htmlspecialchars($row['permanent_address']) . "<br>";
                                $permanentAddress .= "<strong>City:</strong> " . htmlspecialchars($row['permanent_city_name'] ?? 'N/A') . "<br>";
                                $permanentAddress .= "<strong>Area:</strong> " . htmlspecialchars($row['permanent_area_name'] ?? 'N/A') . "<br>";
                                $permanentAddress .= "<strong>Country:</strong> " . htmlspecialchars($row['permanent_country'] ?? 'N/A') . "<br>";
                                $permanentAddress .= "<strong>Province:</strong> " . htmlspecialchars($row['permanent_province'] ?? 'N/A');
                              } else {
                                $permanentAddress = "Same as Present Address";
                              }
                              
                              echo "<tr>";
                              // Action dropdown
                              echo "<td>
                                    <div class='dropdown'>
                                      <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        Action
                                      </button>
                                      <div class='dropdown-menu'>
                                        <a class='dropdown-item' href='edit_guardian.php?id=" . $row["id"] . "'><i class='ti-pencil-alt'></i> Edit</a>
                                        <a class='dropdown-item' href='#' onclick='toggleGuardianStatus(" . $row["id"] . ", \"" . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "</a>
                                        <a class='dropdown-item' href='#' onclick='deleteGuardian(" . $row["id"] . ")'><i class='ti-trash'></i> Delete</a>
                                      </div>
                                    </div>
                                    </td>";
                              
                              echo "<td>" . $row["id"] . "</td>";
                              echo "<td>" . $fatherInfo . "</td>";
                              echo "<td>" . $motherInfo . "</td>";
                              echo "<td>" . $guardianInfo . "</td>";
                              echo "<td>" . $presentAddress . "</td>";
                              echo "<td>" . $permanentAddress . "</td>";
                              echo "<td><span class='badge " . ($row["status"] == 'Active' ? 'badge-success' : 'badge-danger') . "'>" . $row["status"] . "</span></td>";
                              echo "<td>" . $createdAt . "</td>";
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
        const fatherCell = rows[i].getElementsByTagName('td')[2];
        const motherCell = rows[i].getElementsByTagName('td')[3];
        const guardianCell = rows[i].getElementsByTagName('td')[4];
        
        if (fatherCell || motherCell || guardianCell) {
          const fatherValue = fatherCell ? (fatherCell.textContent || fatherCell.innerText).toLowerCase() : '';
          const motherValue = motherCell ? (motherCell.textContent || motherCell.innerText).toLowerCase() : '';
          const guardianValue = guardianCell ? (guardianCell.textContent || guardianCell.innerText).toLowerCase() : '';
          
          if (fatherValue.indexOf(filter) > -1 || motherValue.indexOf(filter) > -1 || guardianValue.indexOf(filter) > -1) {
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

    // Toggle Guardian Status (Active/Inactive)
    function toggleGuardianStatus(id, newStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to mark this guardian record as ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('ajax/toggle_guardian_status.php', {
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

    // Delete Guardian
    function deleteGuardian(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone! This will permanently delete the guardian record.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('ajax/delete_guardian.php', {
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