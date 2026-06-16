<?php
include '../../connect.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $sql = "SELECT id, name, status FROM classes WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    $result = $stmt->get_result();
    $class = $result->fetch_assoc();
    echo json_encode($class);
  } else {
    echo json_encode(["error" => "Error fetching class details."]);
  }

  $stmt->close();
  $conn->close();
}
?>
