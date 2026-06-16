<?php
session_start(); // Ensure session is started at the beginning

include 'connect.php';

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

// Check if the user is logged in and fetch the `institution_name` and `logo`
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Query to fetch the institution_name and logo for the logged-in user
    $query = "SELECT institution_name, logo FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($institutionName, $logo);
    $stmt->fetch();
    $stmt->close();
    
    // Determine logo path: if logo exists in database and file exists in uploads directory, use it, otherwise use default
    $logoPath = "uploads/logos/default-logo.png"; // Default fallback logo
    if (!empty($logo) && file_exists("uploads/logos/" . $logo)) {
        $logoPath = "uploads/logos/" . $logo;
    }
} else {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Home</title>
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/feather/feather.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="https://directory.aditech.pk/js/select.dataTables.min.css">
  <link rel="stylesheet" href="https://directory.aditech.pk/css/vertical-layout-light/style.css">
  <link rel="shortcut icon" href="https://directory.aditech.pk/images/favicon.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <style>
    .text-primary-blue {
      color: #007bff !important;
    }
    .text-primary-green {
      color: #28a745 !important;
    }
    /* Style for school logo in card - full height and auto width */
    .school-logo-card {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      min-height: 200px;
      padding: 20px;
    }
    .school-logo-card img {
      height: 100%;
      width: auto;
      max-width: 100%;
      object-fit: contain;
    }
  </style>
</head>
<body>
  <div class="container-scroller">

    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="img/logo1.png" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="index.php"><img src="img/favicon.png" alt="logo"/></a>
      </div>

      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item dropdown">
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <i class="fas fa-user-circle fa-2x"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="setting/login.php">
                <i class="ti-settings text-primary"></i>
                Settings
              </a>
              <a class="dropdown-item" href="parts/logout.php">
                <i class="ti-power-off text-primary"></i>
                Logout
              </a>
            </div>
          </li>
          <li class="nav-item nav-settings d-none d-lg-flex">
            <a class="nav-link" href="#">
              <i class="icon-ellipsis"></i>
            </a>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>

    <div class="container-fluid page-body-wrapper">
      <?php include 'index/left_sidebar.php'; ?>

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Welcome to <?= htmlspecialchars($institutionName, ENT_QUOTES) ?></h3>
                  <h6 class="font-weight-normal mb-0">All systems are running smoothly!</h6>
                </div>

                <div class="col-12 col-xl-4">
                  <div class="justify-content-end d-flex">
                    <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                      <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="mdi mdi-calendar"></i> <span id="currentDateTime"></span>
                      </button>
                    </div>
                  </div>
                </div>

                <script>
                  function updateDateTime() {
                    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    const now = new Date();
                    const day = days[now.getDay()];
                    const date = now.getDate();
                    const month = months[now.getMonth()];
                    const year = now.getFullYear();
                    const formattedDate = `${date}-${month}-${year}`;
                    const time = now.toLocaleTimeString();

                    document.getElementById('currentDateTime').textContent = `${day} ${formattedDate} ${time}`;
                  }

                  setInterval(updateDateTime, 1000);
                  updateDateTime();
                </script>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card tale-bg">
                <div class="card-people mt-auto school-logo-card">
                  <img src="<?= htmlspecialchars($logoPath, ENT_QUOTES) ?>" alt="School Logo">
                </div>
              </div>
            </div>
            <?php include 'index/4card.php'; ?>
          </div>

          <?php include 'parts/footer.php'; ?>
        </div>
      </div>   
    </div>
  </div>

  <script src="https://directory.aditech.pk/vendors/js/vendor.bundle.base.js"></script>
  <script src="https://directory.aditech.pk/vendors/chart.js/Chart.min.js"></script>
  <script src="https://directory.aditech.pk/vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="https://directory.aditech.pk/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="https://directory.aditech.pk/js/dataTables.select.min.js"></script>
  <script src="https://directory.aditech.pk/js/off-canvas.js"></script>
  <script src="https://directory.aditech.pk/js/hoverable-collapse.js"></script>
  <script src="https://directory.aditech.pk/js/template.js"></script>
  <script src="https://directory.aditech.pk/js/settings.js"></script>
  <script src="https://directory.aditech.pk/js/todolist.js"></script>
  <script src="https://directory.aditech.pk/js/dashboard.js"></script>
  <script src="https://directory.aditech.pk/js/Chart.roundedBarCharts.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Dummy data for chart
      const dailyPurchaseValues = [];
      const dailySalesValues = [];
      const monthlyPurchaseValues = [];
      const monthlySalesValues = [];

      const dailyLabels = dailyPurchaseValues.map(value => value.date);
      const dailyPurchaseData = dailyPurchaseValues.map(value => value.dailyPurchaseValue);
      const dailySalesData = dailySalesValues.map(value => value.dailySalesValue);

      const monthlyLabels = monthlyPurchaseValues.map(value => value.month);
      const monthlyPurchaseData = monthlyPurchaseValues.map(value => value.monthlyPurchaseValue);
      const monthlySalesData = monthlySalesValues.map(value => value.monthlySalesValue);

      var dailyOptions = {
        chart: {
          type: 'line',
          height: 350
        },
        series: [
          {
            name: 'Daily Purchase Value',
            data: dailyPurchaseData,
            color: '#007bff'
          },
          {
            name: 'Daily Sales Value',
            data: dailySalesData,
            color: '#28a745'
          }
        ],
        xaxis: {
          categories: dailyLabels
        }
      };

      var dailyChart = new ApexCharts(document.querySelector("#order-chart"), dailyOptions);
      dailyChart.render();

      var monthlyOptions = {
        chart: {
          type: 'bar',
          height: 350
        },
        series: [
          {
            name: 'Monthly Purchase Value',
            data: monthlyPurchaseData,
            color: '#007bff'
          },
          {
            name: 'Monthly Sales Value',
            data: monthlySalesData,
            color: '#28a745'
          }
        ],
        xaxis: {
          categories: monthlyLabels
        }
      };

      var monthlyChart = new ApexCharts(document.querySelector("#sales-chart"), monthlyOptions);
      monthlyChart.render();
    });
  </script>
</body>
</html>