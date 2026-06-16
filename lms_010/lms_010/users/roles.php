
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Role and Permission</title>
    <?php include '../parts/links1.php'; ?>
    <?php include '../parts/style.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Including necessary scripts and CSS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <!-- Removed duplicate Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Include jQuery (required for Select2 and our script) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
                                            <h4 class="card-title">Add New Role and Permission</h4>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <!-- Save Button -->
                                            <button type="submit" form="rolePermissionForm" class="btn btn-primary">Save Role Permissions</button>
                                        </div>
                                    </div>

                                    <!-- Role Name Input -->
                                    <form id="rolePermissionForm" action="save_role_permissions.php" method="POST">
                                        <div class="form-group mt-4">
                                            <label for="roleName"><strong>Role Name:</strong></label>
                                            <input type="text" id="roleName" name="roleName" class="form-control" placeholder="Enter Role Name" required />
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
                                                        // Create a slug version of the module name for IDs and names
                                                        $moduleSlug = str_replace(' ', '_', strtolower($module));

                                                        echo "<tr>";
                                                        // Display the module name in the 'Name' column
                                                        echo "<td class='name-cell'><strong>{$module}</strong></td>";
                                                        // Module checkbox (now in 'Module' column)
                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input module-checkbox' id='module_{$moduleSlug}' name='{$moduleSlug}[module]'>
                                                                    <label class='form-check-label' for='module_{$moduleSlug}'></label>
                                                                </div>
                                                              </td>";
                                                        // Permission columns with Bootstrap checkboxes
                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input permission-checkbox' id='{$moduleSlug}_view' name='{$moduleSlug}[view]' disabled>
                                                                    <label class='form-check-label' for='{$moduleSlug}_view'></label>
                                                                </div>
                                                              </td>";
                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input permission-checkbox' id='{$moduleSlug}_add' name='{$moduleSlug}[add]' disabled>
                                                                    <label class='form-check-label' for='{$moduleSlug}_add'></label>
                                                                </div>
                                                              </td>";
                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input permission-checkbox' id='{$moduleSlug}_edit' name='{$moduleSlug}[edit]' disabled>
                                                                    <label class='form-check-label' for='{$moduleSlug}_edit'></label>
                                                                </div>
                                                              </td>";
                                                        echo "<td>
                                                                <div class='form-check'>
                                                                    <input type='checkbox' class='form-check-input permission-checkbox' id='{$moduleSlug}_delete' name='{$moduleSlug}[delete]' disabled>
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
        });
    </script>
</body>
</html>
