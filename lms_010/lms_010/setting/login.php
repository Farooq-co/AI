
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Update Login | Password Detail</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
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
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <h4 class="card-title">Login Detail</h4>
                    </div>
                  </div>
                  <div class="table-responsive pt-3">
                    <?php
                    if (isset($_SESSION['message'])) {
                      echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
                      unset($_SESSION['message']);
                    }
                    ?>
                    <form action="login/update_login.php" method="POST">
                      <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                      </div>
                      <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                      </div>
                      <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                      </div>
                      <button type="submit" class="btn btn-primary">Update</button>
                    </form>
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

  <?php include '../parts/links2.php'; ?>
</body>

</html>
