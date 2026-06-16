<?php
// Include the database connection
include '../connect.php';

// Fetch the last backup timestamp
$result = $conn->query("SELECT backup_time FROM backups ORDER BY id DESC LIMIT 1");

// Check if a result was returned
if ($result && $result->num_rows > 0) {
    $last_backup = $result->fetch_assoc()['backup_time'];
    echo "<p><strong>Last Backup DateTime:</strong> " . $last_backup . "</p>";
} else {
    echo "<p><strong>Last Backup DateTime:</strong> No backup has been created yet.</p>";
}

$conn->close();
?>
