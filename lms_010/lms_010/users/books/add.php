<?php
include '../../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $status = $_POST['status'];

  // Insert the new book into the books table
  $sql = "INSERT INTO books (name, status) VALUES (?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $name, $status);

  if ($stmt->execute()) {
    echo "New book added successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>
