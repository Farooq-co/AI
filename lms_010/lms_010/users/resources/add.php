<?php
// resources/add.php
include '../../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $classes_id = $_POST['classes_id'];
  $books_id   = $_POST['books_id'];
  $status     = $_POST['status'];

  $sql = "INSERT INTO resources (classes_id, books_id, status) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  // classes_id (int), books_id (int), status (string)
  $stmt->bind_param("iis", $classes_id, $books_id, $status);

  if ($stmt->execute()) {
    echo "New resource added successfully";
  } else {
    echo "Error: " . $conn->error;
  }
  $stmt->close();
  $conn->close();
}
?>
