<?php
include '../../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = $_POST['editBookId'];  // Ensure this matches the JS variable
  $name = $_POST['editName'];
  $status = $_POST['editStatus'];

  // Update the book details in the books table
  $sql = "UPDATE books SET name = ?, status = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssi", $name, $status, $id);

  if ($stmt->execute()) {
    echo "Book updated successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>
