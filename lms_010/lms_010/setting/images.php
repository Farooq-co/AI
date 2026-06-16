<?php
// Database connection
include '../connect.php';

// Fetch existing details
$query = "SELECT * FROM basic_information LIMIT 1";
$result = mysqli_query($conn, $query);
$info = mysqli_fetch_assoc($result);

$notification = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = "../img/"; // Set the directory where you want to save the uploaded files
    
    if (isset($_POST["submit_logo"])) {
        $logo_path = $target_dir . "logo.png";
        $uploadOk = 1;
        
        if (isset($_FILES["logo"]["tmp_name"]) && !empty($_FILES["logo"]["tmp_name"])) {
            $check = getimagesize($_FILES["logo"]["tmp_name"]);
            if ($check === false) {
                $notification .= "Logo file is not an image.<br>";
                $uploadOk = 0;
            } elseif ($_FILES["logo"]["size"] > 500000) {
                $notification .= "Sorry, your logo file is too large.<br>";
                $uploadOk = 0;
            } elseif (!in_array(strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION)), ["jpg", "jpeg", "png", "gif"])) {
                $notification .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed for logo.<br>";
                $uploadOk = 0;
            }
            
            if ($uploadOk && move_uploaded_file($_FILES["logo"]["tmp_name"], $logo_path)) {
                $notification .= "Logo has been uploaded.<br>";
            } else {
                $notification .= "Sorry, there was an error uploading your logo.<br>";
            }
        }
    }
    
    if (isset($_POST["submit_logo1"])) {
        $logo1_path = $target_dir . "logo1.png";
        $uploadOk = 1;
        
        if (isset($_FILES["logo1"]["tmp_name"]) && !empty($_FILES["logo1"]["tmp_name"])) {
            $check = getimagesize($_FILES["logo1"]["tmp_name"]);
            if ($check === false) {
                $notification .= "Secondary logo file is not an image.<br>";
                $uploadOk = 0;
            } elseif ($_FILES["logo1"]["size"] > 500000) {
                $notification .= "Sorry, your secondary logo file is too large.<br>";
                $uploadOk = 0;
            } elseif (!in_array(strtolower(pathinfo($_FILES["logo1"]["name"], PATHINFO_EXTENSION)), ["jpg", "jpeg", "png", "gif"])) {
                $notification .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed for secondary logo.<br>";
                $uploadOk = 0;
            }
            
            if ($uploadOk && move_uploaded_file($_FILES["logo1"]["tmp_name"], $logo1_path)) {
                $notification .= "Secondary logo has been uploaded.<br>";
            } else {
                $notification .= "Sorry, there was an error uploading your secondary logo.<br>";
            }
        }
    }
    
    if (isset($_POST["submit_favicon"])) {
        $favicon_path = $target_dir . "favicon.png";
        $uploadOk = 1;
        
        if (isset($_FILES["favicon"]["tmp_name"]) && !empty($_FILES["favicon"]["tmp_name"])) {
            $check = getimagesize($_FILES["favicon"]["tmp_name"]);
            if ($check === false) {
                $notification .= "Favicon file is not an image.<br>";
                $uploadOk = 0;
            } elseif ($_FILES["favicon"]["size"] > 500000) {
                $notification .= "Sorry, your favicon file is too large.<br>";
                $uploadOk = 0;
            } elseif (!in_array(strtolower(pathinfo($_FILES["favicon"]["name"], PATHINFO_EXTENSION)), ["jpg", "jpeg", "png", "gif"])) {
                $notification .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed for favicon.<br>";
                $uploadOk = 0;
            }
            
            if ($uploadOk && move_uploaded_file($_FILES["favicon"]["tmp_name"], $favicon_path)) {
                $notification .= "Favicon has been uploaded.<br>";
            } else {
                $notification .= "Sorry, there was an error uploading your favicon.<br>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Update Images | Logos</title>
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
                  <?php if (!empty($notification)): ?>
                    <div class="alert alert-info">
                      <?php echo $notification; ?>
                    </div>
                  <?php endif; ?>
                  <h4 class="card-title">Update Logos</h4>
                  
                  <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="logo">Invoice Logo. Ratio(1:1)</label>
                      <input type="file" name="logo" id="logo" class="form-control">
                      <img src="<?= $target_dir . 'logo.png' ?>" alt="Invoice Logo" width="100">
                      <button type="submit" name="submit_logo" class="btn btn-primary mt-2">Upload</button>
                    </div>
                  </form>
                  
                  <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="logo1">Application Logo (Large). Ratio(1:3)</label>
                      <input type="file" name="logo1" id="logo1" class="form-control">
                      <img src="<?= $target_dir . 'logo1.png' ?>" alt="Application Logo (Large)" width="100">
                      <button type="submit" name="submit_logo1" class="btn btn-primary mt-2">Upload</button>
                    </div>
                  </form>
                  
                  <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="favicon">Application Logo (Small). Ratio(1:1)</label>
                      <input type="file" name="favicon" id="favicon" class="form-control">
                      <img src="<?= $target_dir . 'favicon.png' ?>" alt="Application Logo (Small)" width="50">
                      <button type="submit" name="submit_favicon" class="btn btn-primary mt-2">Upload</button>
                    </div>
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
