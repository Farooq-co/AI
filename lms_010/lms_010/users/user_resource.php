<?php
// books_display.php

// 1. Connect to DB
include '../connect.php';

// 2. Fetch active users
$userQuery = "SELECT id, institution_name FROM users WHERE status = 'Active'";
$userResult = $conn->query($userQuery);
$users = [];
if ($userResult && $userResult->num_rows > 0) {
    while ($row = $userResult->fetch_assoc()) {
        $users[$row['id']] = $row['institution_name'];
    }
}

// 3. Fetch all active resources, BUT only the LATEST entry for each (books_id, classes_id)
$resourceQuery = "
    SELECT 
        r2.id AS resource_id,
        CONCAT(b.name, ' - ', c.name) AS resource_details
    FROM (
        -- Subquery to get the MAX(id) per (books_id, classes_id)
        SELECT books_id, classes_id, MAX(id) AS max_id
        FROM resources
        WHERE status = 'Active'
        GROUP BY books_id, classes_id
    ) AS sub
    JOIN resources r2 ON r2.id = sub.max_id
    JOIN books b ON r2.books_id = b.id
    JOIN classes c ON r2.classes_id = c.id
    ORDER BY b.name, c.name
";
$resourceResult = $conn->query($resourceQuery);
$resources = [];
if ($resourceResult && $resourceResult->num_rows > 0) {
    while ($row = $resourceResult->fetch_assoc()) {
        $resources[$row['resource_id']] = $row['resource_details'];
    }
}

// 4. Fetch user-resource relationships
$userResourceQuery = "
  SELECT 
      user_id, 
      resource_id 
  FROM 
      user_resource_assignments;
";
$userResourceResult = $conn->query($userResourceQuery);
$userResources = [];
if ($userResourceResult && $userResourceResult->num_rows > 0) {
    while ($row = $userResourceResult->fetch_assoc()) {
        $userResources[$row['user_id']][$row['resource_id']] = true;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Resources Display</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    /* Table Styling */
    .table-container {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      overflow-x: auto; /* Enable horizontal scrolling */
      width: 100%; /* Make container responsive */
    }

    .table {
      border-collapse: collapse;
      width: 100%;
      min-width: 1200px; /* Ensure table does not shrink below this width */
    }

    .table th, .table td {
      text-align: center;
      padding: 12px;
      border: 1px solid #e0e0e0;
      white-space: nowrap; /* Prevent text wrapping */
    }

    .table th {
      background-color: #f8f9fa;
      font-weight: bold;
      color: #333;
    }

    .table td {
      background-color: #ffffff;
    }

    .table tr:hover td {
      background-color: #f9f9f9;
    }

    /* Checkbox Styling */
    .form-check-input {
      margin: 0;
      position: static; /* Ensure checkbox aligns with table cells */
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
      .table-container {
        padding: 10px;
      }

      .table {
        min-width: 100%; /* Adjust table to fit screen size */
      }
    }
  </style>
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
        <h4 class="card-title mb-4">Resources Display</h4>
        <div class="table-container">
          <form action="save_user_resources.php" method="post">
            <table class="table">
              <thead>
                <tr>
                  <th>Institution Name</th>
                  <?php foreach ($resources as $resourceId => $resourceDetails): ?>
                    <th>
                      <?= htmlspecialchars($resourceDetails, ENT_QUOTES) ?>
                    </th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $userId => $institutionName): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($institutionName, ENT_QUOTES) ?></strong></td>
                    <?php foreach ($resources as $resourceId => $resourceDetails): ?>
                      <td>
                        <input 
                          type="checkbox" 
                          class="form-check-input" 
                          name="user_resources[<?= $userId ?>][]" 
                          value="<?= $resourceId ?>" 
                          <?= isset($userResources[$userId][$resourceId]) ? 'checked' : '' ?>
                        >
                      </td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <button type="submit" class="btn btn-primary mt-3">Save</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../parts/links2.php'; ?>
</body>
</html>
