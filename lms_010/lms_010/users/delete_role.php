<?php
include '../connect.php';

// Check if 'id' parameter is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Invalid role ID.');
}

$roleId = intval($_GET['id']);

// Begin a transaction
$conn->begin_transaction();

try {
    // Delete permissions associated with the role
    $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $stmt->close();

    // Delete the role
    $stmt = $conn->prepare("DELETE FROM roles WHERE role_id = ?");
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $stmt->close();

    // Commit the transaction
    $conn->commit();

    // Redirect to roles.php after successful deletion
    header("Location: role_list.php");
    exit();

} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    die("Error deleting role: " . $e->getMessage());
}
?>
