<?php
include '../../parts/session_check.php';
include '../../connect.php'; // Ensure this file includes your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];

  // Retrieve the user details from the database
  $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();
  
  if ($stmt->num_rows > 0) {
    $stmt->bind_result($stored_password);
    $stmt->fetch();

    // Verify the current password
    if (password_verify($current_password, $stored_password)) {
      // Hash the new password
      $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);

      // Update the password in the database
      $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
      $update_stmt->bind_param("ss", $hashed_new_password, $username);

      if ($update_stmt->execute()) {
        $_SESSION['message'] = "Password updated successfully";
      } else {
        $_SESSION['message'] = "Error updating password: " . $update_stmt->error;
      }

      $update_stmt->close();
    } else {
      $_SESSION['message'] = "Current password is incorrect.";
    }
  } else {
    $_SESSION['message'] = "User not found.";
  }

  $stmt->close();
  $conn->close();
} else {
  $_SESSION['message'] = "Invalid request method.";
}

header("Location: ../login.php"); // Redirect back to the form
exit();
?>
