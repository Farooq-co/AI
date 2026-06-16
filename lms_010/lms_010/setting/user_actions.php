<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $action = $_POST['action'];

  if ($action == 'add') {
    // Add New User
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, status, role_id) VALUES (?, ?, ?, 'Active', ?)");
    $stmt->bind_param("sssi", $username, $email, $hashed_password, $role_id);

    if ($stmt->execute()) {
      echo "New user added successfully.";
    } else {
      echo "Error: " . $stmt->error;
    }

    $stmt->close();

  } elseif ($action == 'get_user') {
    // Get User Data for Editing
    $id = $_POST['id'];

    $stmt = $conn->prepare("SELECT id, username, email, role_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $result = $stmt->get_result();
      $user = $result->fetch_assoc();
      echo json_encode($user);
    } else {
      echo json_encode([]);
    }

    $stmt->close();

  } elseif ($action == 'edit') {
    // Edit User
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    if (!empty($password)) {
      // Hash the new password
      $hashed_password = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, role_id = ? WHERE id = ?");
      $stmt->bind_param("sssii", $username, $email, $hashed_password, $role_id, $id);
    } else {
      // Do not update the password
      $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role_id = ? WHERE id = ?");
      $stmt->bind_param("ssii", $username, $email, $role_id, $id);
    }

    if ($stmt->execute()) {
      echo "User updated successfully.";
    } else {
      echo "Error: " . $stmt->error;
    }

    $stmt->close();

  } elseif ($action == 'change_status') {
    // Change User Status
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
      echo "User status updated to $status.";
    } else {
      echo "Error: " . $stmt->error;
    }

    $stmt->close();

  } elseif ($action == 'delete') {
    // Delete User
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      echo "User deleted successfully.";
    } else {
      echo "Error: " . $stmt->error;
    }

    $stmt->close();

  } else {
    echo "Invalid action.";
  }

  $conn->close();
}
?>
