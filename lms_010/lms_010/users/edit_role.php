<?php
include '../connect.php';

// Check if 'id' parameter is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Invalid role ID.');
}

$roleId = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the role name from the form
    $roleName = trim($_POST['roleName']);

    if (empty($roleName)) {
        $error = 'Role name is required.';
    } else {
        // Begin a transaction
        $conn->begin_transaction();

        try {
            // Update the role name in the 'roles' table
            $stmt = $conn->prepare("UPDATE roles SET role_name = ? WHERE role_id = ?");
            $stmt->bind_param("si", $roleName, $roleId);
            $stmt->execute();
            $stmt->close();

            // Delete existing permissions for the role
            $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $stmt->bind_param("i", $roleId);
            $stmt->execute();
            $stmt->close();

            // Prepare the statement for inserting new permissions
            $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, module_name, can_view, can_add, can_edit, can_delete) VALUES (?, ?, ?, ?, ?, ?)");

            // Loop through the modules and their permissions
            foreach ($_POST as $moduleSlug => $permissions) {
                if ($moduleSlug == 'roleName') {
                    continue; // Skip the roleName field
                }

                // Convert the slug back to the module name
                $moduleName = ucwords(str_replace('_', ' ', $moduleSlug));

                // Check if the module is enabled
                if (isset($permissions['module'])) {
                    // Set permission values
                    $canView = isset($permissions['view']) ? 1 : 0;
                    $canAdd = isset($permissions['add']) ? 1 : 0;
                    $canEdit = isset($permissions['edit']) ? 1 : 0;
                    $canDelete = isset($permissions['delete']) ? 1 : 0;

                    // Bind parameters and execute
                    $stmt->bind_param("isiiii", $roleId, $moduleName, $canView, $canAdd, $canEdit, $canDelete);
                    $stmt->execute();
                }
            }

            $stmt->close();

            // Commit the transaction
            $conn->commit();

            // Redirect to roles.php after successful update
            header("Location: role_list.php");
            exit();

        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $error = "Error updating role permissions: " . $e->getMessage();
        }
    }
}

// Fetch the role information and permissions
// Get role name
$stmt = $conn->prepare("SELECT role_name FROM roles WHERE role_id = ?");
$stmt->bind_param("i", $roleId);
$stmt->execute();
$stmt->bind_result($roleName);
$stmt->fetch();
$stmt->close();

if (!$roleName) {
    die('Role not found.');
}

// Get role permissions
$permissions = [];
$stmt = $conn->prepare("SELECT module_name, can_view, can_add, can_edit, can_delete FROM role_permissions WHERE role_id = ?");
$stmt->bind_param("i", $roleId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $moduleSlug = strtolower(str_replace(' ', '_', $row['module_name']));
    $permissions[$moduleSlug] = [
        'module' => 1,
        'view' => $row['can_view'],
        'add' => $row['can_add'],
        'edit' => $row['can_edit'],
        'delete' => $row['can_delete'],
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Role</title>
    <?php include '../parts/links1.php'; ?>
    <?php include '../parts/style.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Custom styling for the table */
        .permissions-table th, .permissions-table td {
            vertical-align: middle !important;
            text-align: center;
        }
        .permissions-table .name-cell {
            text-align: left;
        }
        .form-check {
            display: flex;
            justify-content: center;
        }
        /* Increase the size of the checkbox headings */
        .permissions-table th {
            font-size: 1.1em;
            font-weight: bold;
        }
        /* Align the button with the heading */
        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        /* Disable checkbox style */
        .form-check-input[disabled] {
            cursor: not-allowed;
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
                    <div class="row mb-4">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <!-- Header Row with Title and Button -->
                                    <div class="row header-row">
                                        <div class="col-md-6">
                                            <h4 class="card-title">Edit Role</h4>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <!-- Save Button -->
                                            <button type="submit" form="rolePermissionForm" class="btn btn-primary">Update Role Permissions</button>
                                        </div>
                                    </div>

                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>

                                    <!-- Role Name Input -->
                                    <form id="rolePermissionForm" action="" method="POST">
                                        <div class="form-group mt-4">
                                            <label for="roleName"><strong>Role Name:</strong></label>
                                            <input type="text" id="roleName" name="roleName" class="form-control" value="<?php echo htmlspecialchars($roleName); ?>" required />
                                        </div>

                                        <!-- Permissions Table -->
                                        <div class="table-responsive pt-3">
                                            <table class="table table-bordered permissions-table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Module</th>
                                                        <th>View</th>
                                                        <th>Add</th>
                                                        <th>Edit</th>
                                                        <th>Delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Define the modules list
                                                    $modules = [
                                                        "Parties", "Products", "Sale", "Sale Return", "Sale Order", "Purchase", "Purchase Return", "Purchase Order", "Accounts", "Transactions", 
                                                        "Purchase Reports", "Sale Reports", "Stock Reports", "Account Reports", 
                                                        "Services", "RecycleBin", "User Management", "Settings"
                                                    ];

                                                    // Generate rows for each module
                                                    foreach ($modules as $module) {
                                                        $moduleSlug = str_replace(' ', '_', strtolower($module));

                                                        // Check if permissions exist for this module
                                                        $modulePermissions = isset($permissions[$moduleSlug]) ? $permissions[$moduleSlug] : [];

                                                        echo "<tr>";
                                                        // Display the module name in the 'Name' column
                                                        echo "<td class='name-cell'><strong>{$module}</strong></td>";
                                                        // Module checkbox
                                                        $moduleChecked = isset($modulePermissions['module']) ? 'checked' : '';
                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input module-checkbox' id='module_{$moduleSlug}' name='{$moduleSlug}[module]' {$moduleChecked}>
                                                                    <label class='form-check-label' for='module_{$moduleSlug}'></label>
                                                                </div>
                                                              </td>";
                                                        // Permission checkboxes
                                                        $viewChecked = (isset($modulePermissions['view']) && $modulePermissions['view']) ? 'checked' : '';
                                                        $addChecked = (isset($modulePermissions['add']) && $modulePermissions['add']) ? 'checked' : '';
                                                        $editChecked = (isset($modulePermissions['edit']) && $modulePermissions['edit']) ? 'checked' : '';
                                                        $deleteChecked = (isset($modulePermissions['delete']) && $modulePermissions['delete']) ? 'checked' : '';

                                                        $disabled = isset($modulePermissions['module']) ? '' : 'disabled';

                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input permission-checkbox' id='{$moduleSlug}_view' name='{$moduleSlug}[view]' {$viewChecked} {$disabled}>
                                                                    <label class='form-check-label' for='{$moduleSlug}_view'></label>
                                                                </div>
                                                              </td>";
                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input permission-checkbox' id='{$moduleSlug}_add' name='{$moduleSlug}[add]' {$addChecked} {$disabled}>
                                                                    <label class='form-check-label' for='{$moduleSlug}_add'></label>
                                                                </div>
                                                              </td>";
                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input permission-checkbox' id='{$moduleSlug}_edit' name='{$moduleSlug}[edit]' {$editChecked} {$disabled}>
                                                                    <label class='form-check-label' for='{$moduleSlug}_edit'></label>
                                                                </div>
                                                              </td>";
                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input permission-checkbox' id='{$moduleSlug}_delete' name='{$moduleSlug}[delete]' {$deleteChecked} {$disabled}>
                                                                    <label class='form-check-label' for='{$moduleSlug}_delete'></label>
                                                                </div>
                                                              </td>";
                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form> <!-- End of Form -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include '../parts/footer.php'; ?>
            </div>
        </div>
    </div>

    <!-- Include your additional scripts -->
    <?php include '../parts/links2.php'; ?>
    <?php include '../parts/script_table.php'; ?>

    <!-- JavaScript to handle enabling/disabling permissions -->
    <script>
        $(document).ready(function() {
            $('.module-checkbox').on('change', function() {
                var moduleSlug = $(this).attr('id').replace('module_', '');
                var isChecked = $(this).is(':checked');
                // Enable or disable permission checkboxes based on module checkbox
                $('input[id^="' + moduleSlug + '_"]').prop('disabled', !isChecked);
                if (!isChecked) {
                    // Uncheck all permissions if module is unchecked
                    $('input[id^="' + moduleSlug + '_"]').prop('checked', false);
                }
            });

            // Trigger change event on page load to set the correct state
            $('.module-checkbox').each(function() {
                $(this).trigger('change');
            });
        });
    </script>
</body>
</html>
