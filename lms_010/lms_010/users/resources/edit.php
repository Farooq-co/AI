<?php
// resources/edit.php
include '../../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id         = $_POST['id'];
  $classes_id = $_POST['classes_id'];
  $books_id   = $_POST['books_id'];
  $status     = $_POST['status'];

  $sql = "
    UPDATE resources
    SET classes_id = ?,
        books_id   = ?,
        status     = ?
    WHERE id = ?
  ";
  $stmt = $conn->prepare($sql);
  // classes_id (int), books_id (int), status (string), id (int)
  $stmt->bind_param("iisi", $classes_id, $books_id, $status, $id);

  if ($stmt->execute()) {
    echo "Resource updated successfully";
  } else {
    echo "Error: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>
