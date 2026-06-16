<?php
session_start();
include 'connect.php'; // Include database connection

$error = ''; // Error message placeholder

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Query to fetch user details
    $sql = "SELECT id, username, password, role_id, allow_additional_discount
            FROM users
            WHERE username = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['allow_additional_discount'] = (int) $user['allow_additional_discount'];

            // Redirect based on role
            if ($user['role_id'] == 1) {
                header("Location: admin_dashboard.php");
            } elseif ($user['role_id'] == 2) {
                header("Location: user_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "Invalid username. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>AdiTech Software - Login</title>
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/feather/feather.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/css/vertical-layout-light/style.css">
  <link rel="shortcut icon" href="https://directory.aditech.pk/images/favicon.png" />
</head>
<body>
<div class="container-scroller">
  <div class="container-fluid page-body-wrapper full-page-wrapper">
    <div class="content-wrapper d-flex align-items-center auth px-0">
      <div class="row w-100 mx-0">
        <div class="col-lg-4 mx-auto">
          <div class="auth-form-light text-left py-5 px-4 px-sm-5">
            <div class="brand-logo">
              <img src="img/logo.png" alt="logo">
            </div>
            <h4>Welcome back!</h4>
            <h6 class="font-weight-light">Sign in to continue.</h6>
            <?php if (!empty($error)): ?>
              <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>
            <form class="pt-3" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
              <div class="form-group">
                <input type="text" class="form-control form-control-lg" name="username" placeholder="Username" required>
              </div>
              <div class="form-group">
                <input type="password" class="form-control form-control-lg" name="password" placeholder="Password" required>
              </div>
              <div class="mt-3">
                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
              </div>
              <div class="my-2 d-flex justify-content-between align-items-center">
                <div class="form-check">
                  <label class="form-check-label text-muted">
                    <input type="checkbox" class="form-check-input"> Keep me signed in
                  </label>
                </div>
                <a href="#" class="auth-link text-black">Forgot password?</a>
              </div>
              <div class="text-center mt-4 font-weight-light">
                Don't have an account?<br> Call/WhatsApp: +92 300 4080300
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://directory.aditech.pk/vendors/js/vendor.bundle.base.js"></script>
<script src="https://directory.aditech.pk/js/template.js"></script>
</body>
</html>
