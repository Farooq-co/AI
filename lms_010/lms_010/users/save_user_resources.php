<?php
// save_user_resources.php

// Include database connection
include '../connect.php';

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_resources'])) {
    // Array of user-resource relationships
    $userResources = $_POST['user_resources'];

    // Table name where the relationships will be saved
    $tableName = "user_resource_assignments";

    // Delete existing relationships to avoid duplication
    foreach ($userResources as $userId => $resources) {
        $userId = intval($userId); // Sanitize user ID
        $deleteQuery = "DELETE FROM `$tableName` WHERE user_id = $userId";
        $conn->query($deleteQuery);

        // Insert new relationships for the user
        $insertQuery = "INSERT INTO `$tableName` (user_id, resource_id) VALUES ";
        $values = [];
        foreach ($resources as $resourceId) {
            $resourceId = intval($resourceId); // Sanitize resource ID
            $values[] = "($userId, $resourceId)";
        }

        if (!empty($values)) {
            $insertQuery .= implode(", ", $values);
            $conn->query($insertQuery);
        }
    }

    // Redirect to success or display a success message
    header("Location: user_resource.php?success=1");
    exit;
} else {
    // Redirect back if no data was submitted
    header("Location: user_resource.php?error=1");
    exit;
}
