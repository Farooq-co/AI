<?php
include '../../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $status = $_POST['status'];

  // Insert the new class into the classes table
  $sql = "INSERT INTO classes (name, status) VALUES (?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $name, $status);

  if ($stmt->execute()) {
    echo "New class added successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>
