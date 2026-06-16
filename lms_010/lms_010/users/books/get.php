<?php
include '../../connect.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $sql = "SELECT id, name, status FROM books WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    echo json_encode($book);
  } else {
    echo json_encode(["error" => "Error fetching book details."]);
  }

  $stmt->close();
  $conn->close();
}
?>
