<?php
// resources/update_status.php
include '../../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id     = $_POST['id'];
  $status = $_POST['status'];

  $sql = "UPDATE resources SET status = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("si", $status, $id);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "error";
  }

  $stmt->close();
  $conn->close();
}
?>
