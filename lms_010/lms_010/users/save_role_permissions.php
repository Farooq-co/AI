<?php
include '../connect.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the role name from the form
    $roleName = trim($_POST['roleName']);

    if (empty($roleName)) {
        die('Role name is required.');
    }

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Insert the role into the 'roles' table
        $stmt = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
        $stmt->bind_param("s", $roleName);
        $stmt->execute();
        $roleId = $stmt->insert_id; // Get the ID of the inserted role
        $stmt->close();

        // Prepare the statement for inserting permissions
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

        // Redirect to roles.php after successful save
        header("Location: role_list.php");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        die("Error saving role permissions: " . $e->getMessage());
    }
} else {
    die('Invalid request method.');
}
?>
