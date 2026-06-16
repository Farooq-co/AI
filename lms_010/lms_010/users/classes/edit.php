<?php
include '../../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = $_POST['editClassId'];  // Ensure this matches the JS variable
  $name = $_POST['editName'];
  $status = $_POST['editStatus'];

  // Update the class details in the classes table
  $sql = "UPDATE classes SET name = ?, status = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssi", $name, $status, $id);

  if ($stmt->execute()) {
    echo "Class updated successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>
