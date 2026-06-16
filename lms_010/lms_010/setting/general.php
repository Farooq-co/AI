<?php
// Database connection
include '../connect.php';

// Fetch existing details
$query = "SELECT * FROM basic_information LIMIT 1";
$result = mysqli_query($conn, $query);
$info = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Update General Detail</title>
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
                  <h4 class="card-title">Update General Detail</h4>

                  <?php if (isset($_SESSION['message'])): ?>
                      <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                          <?php 
                              echo $_SESSION['message']; 
                              unset($_SESSION['message']);
                              unset($_SESSION['message_type']);
                          ?>
                      </div>
                  <?php endif; ?>

                  <form class="forms-sample" action="general/update_general_detail.php" method="post">
                    <div class="form-group">
                      <label for="name">Name</label>
                      <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?php echo htmlspecialchars($info['name']); ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="address">Address</label>
                      <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="<?php echo htmlspecialchars($info['address']); ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="city">City</label>
                      <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php echo htmlspecialchars($info['city']); ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="contact1">Contact 1</label>
                      <input type="text" class="form-control" id="contact1" name="contact1" placeholder="Contact 1" value="<?php echo htmlspecialchars($info['contact1']); ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="contact2">Contact 2</label>
                      <input type="text" class="form-control" id="contact2" name="contact2" placeholder="Contact 2" value="<?php echo htmlspecialchars($info['contact2']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                    <button type="button" class="btn btn-light" onclick="window.location.href='dashboard.php'">Cancel</button>
                  </form>
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
