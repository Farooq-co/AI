<!DOCTYPE html>
<html lang="en">
<head>
  <title>Student Management</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 for better alerts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .student-picture {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
    .table td {
      vertical-align: middle;
    }
    .badge-active {
      background-color: #28a745;
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 11px;
    }
    .badge-inactive {
      background-color: #dc3545;
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 11px;
    }
    .table-responsive {
      overflow-x: auto;
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

          <!-- Search and Add Student Button -->
          <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <div class="input-group">
                          <input type="text" class="form-control" id="searchInput" placeholder="Search by Student Name, Father Name, Roll Number, or Admission Number" aria-label="Search">
                          <div class="input-group-append">
                              <button class="btn btn-sm btn-primary" type="button" onclick="searchTable()">Search</button>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6 text-right">
                  <button type="button" class="btn btn-primary btn-rounded btn-fw" style="margin-bottom: 10px;" onclick="window.location.href='add_student.php'">
                      Add New Student
                  </button>
                  <button type="button" class="btn btn-info btn-rounded btn-fw" style="margin-bottom: 10px;" onclick="window.open('ajax/print_student_list.php', '_blank');">
                      <i class="bi bi-printer"></i> Print List
                  </button>
              </div>
          </div>

          <!-- Student Table -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Student Records</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>ID</th>
                          <th>Photo</th>
                          <th>Student Info</th>
                          <th>Academic Info</th>
                          <th>Father Name</th>
                          <th>Date of Birth</th>
                          <th>Gender/Religion</th>
                          <th>Status</th>
                          <th>Created At</th>
                        </tr>
                        <tr>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th>Name / Admission No / Roll No</th>
                          <th>Class / Section / Group</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody id="studentTableBody">
                        <?php
                          include '../connect.php';

                          // Query to fetch students with related data
                          $sql = "SELECT s.*, 
                                  c.name as class_name,
                                  sec.name as section_name,
                                  g.name as group_name,
                                  cat.name as category_name,
                                  rel.name as religion_name,
                                  gen.name as gender_name,
                                  bg.name as blood_group_name
                                  FROM students s 
                                  LEFT JOIN classes c ON s.class_id = c.id 
                                  LEFT JOIN sections sec ON s.section_id = sec.id
                                  LEFT JOIN `groups` g ON s.group_id = g.id
                                  LEFT JOIN student_category cat ON s.student_category_id = cat.id
                                  LEFT JOIN religion rel ON s.religion_id = rel.id
                                  LEFT JOIN gender gen ON s.gender_id = gen.id
                                  LEFT JOIN blood_group bg ON s.blood_group_id = bg.id
                                  ORDER BY s.id DESC";
                          $result = $conn->query($sql);

                          if (!$result) {
                              echo "<tr><td colspan='10'>Error: " . $conn->error . "</td></tr>";
                          } elseif ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              // Format date
                              $createdAt = date('Y-m-d H:i:s', strtotime($row['created_at']));
                              $dob = date('d-m-Y', strtotime($row['date_of_birth']));
                              $admissionDate = date('d-m-Y', strtotime($row['admission_date']));
                              $admissionEffectiveDate = date('d-m-Y', strtotime($row['admission_effective_date']));
                              
                              // Student Information
                              $studentInfo = "<strong>" . htmlspecialchars($row['student_name']) . "</strong><br>";
                              $studentInfo .= "Admission No: " . (htmlspecialchars($row['admission_number'] ?? 'N/A')) . "<br>";
                              $studentInfo .= "Roll No: " . (htmlspecialchars($row['roll_number'] ?? 'N/A')) . "<br>";
                              $studentInfo .= "Admission Date: " . $admissionDate;
                              
                              // Academic Information
                              $academicInfo = "<strong>Class:</strong> " . htmlspecialchars($row['class_name'] ?? 'N/A') . "<br>";
                              $academicInfo .= "<strong>Section:</strong> " . htmlspecialchars($row['section_name'] ?? 'N/A') . "<br>";
                              $academicInfo .= "<strong>Group:</strong> " . htmlspecialchars($row['group_name'] ?? 'N/A') . "<br>";
                              $academicInfo .= "<strong>Category:</strong> " . htmlspecialchars($row['category_name'] ?? 'N/A') . "<br>";
                              $academicInfo .= "<strong>Fee Package ID:</strong> " . ($row['fee_package_id'] ?? 'N/A');
                              
                              // Student Photo
                              $photoHtml = '';
                              if (!empty($row['student_picture']) && file_exists('../uploads/students/' . $row['student_picture'])) {
                                  $photoHtml = '<img src="../uploads/students/' . htmlspecialchars($row['student_picture']) . '" class="student-picture" alt="Student Photo">';
                              } else {
                                  $photoHtml = '<div class="student-picture bg-secondary d-flex align-items-center justify-content-center text-white" style="border-radius: 50%;">?</div>';
                              }
                              
                              // Gender and Religion
                              $genderReligion = "<strong>Gender:</strong> " . htmlspecialchars($row['gender_name'] ?? 'N/A') . "<br>";
                              $genderReligion .= "<strong>Religion:</strong> " . htmlspecialchars($row['religion_name'] ?? 'N/A') . "<br>";
                              $genderReligion .= "<strong>Blood Group:</strong> " . htmlspecialchars($row['blood_group_name'] ?? 'N/A');
                              
                              // Status badge
                              $statusBadge = ($row['status'] == 'Active') 
                                  ? '<span class="badge-active">Active</span>' 
                                  : '<span class="badge-inactive">Inactive</span>';
                              
                              echo "<tr>";
                              // Action dropdown
                              echo "<td>
                                    <div class='dropdown'>
                                      <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        Action
                                      </button>
                                      <div class='dropdown-menu'>
                                        <a class='dropdown-item' href='admission_report.php?id=" . $row['id'] . "' target='_blank'>
                                            <i class='ti-eye'></i> Admission Report
                                        </a>
                                        <a class='dropdown-item' href='edit_student.php?id=" . $row["id"] . "'><i class='ti-pencil-alt'></i> Edit</a>
                                        <a class='dropdown-item' href='#' onclick='toggleStudentStatus(" . $row["id"] . ", \"" . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "\")'><i class='ti-exchange-vertical'></i> Mark as " . ($row["status"] == 'Active' ? 'Inactive' : 'Active') . "</a>
                                        <a class='dropdown-item' href='#' onclick='deleteStudent(" . $row["id"] . ")'><i class='ti-trash'></i> Delete</a>
                                      </div>
                                    </div>
                                    </td>";
                              echo "<td>" . $row["id"] . "</td>";
                              echo "<td>" . $photoHtml . "</td>";
                              echo "<td>" . $studentInfo . "</td>";
                              echo "<td>" . $academicInfo . "</td>";
                              echo "<td>" . htmlspecialchars($row['father_name'] ?? 'N/A') . "</td>";
                              echo "<td>" . $dob . "</td>";
                              echo "<td>" . $genderReligion . "</td>";
                              echo "<td>" . $statusBadge . "</td>";
                              echo "<td>" . $createdAt . "</td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr><td colspan='10' class='text-center'>No student records found</td></tr>";
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
        const studentInfoCell = rows[i].getElementsByTagName('td')[3];  // Student Info column
        const fatherNameCell = rows[i].getElementsByTagName('td')[5];   // Father Name column
        const academicInfoCell = rows[i].getElementsByTagName('td')[4]; // Academic Info column
        
        if (studentInfoCell || fatherNameCell || academicInfoCell) {
          const studentValue = studentInfoCell ? (studentInfoCell.textContent || studentInfoCell.innerText).toLowerCase() : '';
          const fatherValue = fatherNameCell ? (fatherNameCell.textContent || fatherNameCell.innerText).toLowerCase() : '';
          const academicValue = academicInfoCell ? (academicInfoCell.textContent || academicInfoCell.innerText).toLowerCase() : '';
          
          if (studentValue.indexOf(filter) > -1 || fatherValue.indexOf(filter) > -1 || academicValue.indexOf(filter) > -1) {
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

    // Toggle Student Status (Active/Inactive)
    function toggleStudentStatus(id, newStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to mark this student record as ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('ajax/toggle_student_status.php', {
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

    // Delete Student
    function deleteStudent(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone! This will permanently delete the student record.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('ajax/delete_student.php', {
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