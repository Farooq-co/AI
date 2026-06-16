<?php 
include '../connect.php';  // Adjust the path as necessary
global $conn;  // Ensure $conn is available globally
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Download Backup</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    /* Adding padding to the single column */
    .padded-column {
        padding: 100px;
    }
  </style>
</head>

<body>
  <div class="container-scroller">
    <!-- Include Navbar -->
    <?php include '../parts/navbar.php'; ?>

    <div class="container-fluid page-body-wrapper">
      <!-- Include Sidebars -->
      <?php include '../parts/setting.php'; ?>
      <?php include '../parts/right_sidebar.php'; ?>
      <?php include '../parts/left_sidebar.php'; ?>

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body padded-column">
                  <h4 class="card-title">Download Backup</h4>

                  <?php if (isset($_SESSION['message'])): ?>
                      <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                          <?php 
                              echo $_SESSION['message']; 
                              unset($_SESSION['message']);
                              unset($_SESSION['message_type']);
                          ?>
                      </div>
                  <?php endif; ?>

<!-- Single column with button and time -->
<div class="text-center">
  <!-- Button: Download Database Backup -->
  <button type="button" class="btn btn-success btn-icon-text mb-3" onclick="window.location.href='backup/download_backup.php'">
      <i class="ti-upload btn-icon-prepend"></i>
      Download Backup
  </button>

  <!-- Last Backup DateTime -->
  <p><strong>Last Backup DateTime:</strong> 
    <?php 
      global $conn;

      // Fetch the last backup datetime from the database
      $result = $conn->query("SELECT backup_datetime FROM backup_log ORDER BY id DESC LIMIT 1");

      if ($result && $result->num_rows > 0) {
          $row = $result->fetch_assoc();
          // Format the datetime
          echo date("d-M-Y h:iA", strtotime($row['backup_datetime']));
      } else {
          echo 'No backup has been performed yet.';
      }
    ?>
  </p>
</div>
<!-- End of single column -->

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

  <?php include '../parts/links2.php'; ?>
</body>

</html>
