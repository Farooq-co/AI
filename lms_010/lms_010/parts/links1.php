  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- plugins:css -->
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/feather/feather.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/css/vendor.bundle.base.css">
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  <!-- inject:css -->
  <link rel="stylesheet" href="https://directory.aditech.pk/css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="https://directory.aditech.pk/images/favicon.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


  <?php
session_start(); // Ensure session is started at the beginning

include '../connect.php';

// Check if role_id is set in the session
if (isset($_SESSION['role_id'])) {
    $role_id = $_SESSION['role_id'];
} else {
    // Redirect to login if role_id is not set
    header("Location: login.php");
    exit();
}

// Fetch all permissions for permitted modules for this role
$query = "SELECT module_name, can_view, can_add, can_edit, can_delete FROM role_permissions WHERE role_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $role_id);
$stmt->execute();
$result = $stmt->get_result();

$allowedModules = [];
while ($row = $result->fetch_assoc()) {
    $allowedModules[$row['module_name']] = [
        'can_view' => $row['can_view'],
        'can_add' => $row['can_add'],
        'can_edit' => $row['can_edit'],
        'can_delete' => $row['can_delete'],
    ];
}

$stmt->close();

?>
