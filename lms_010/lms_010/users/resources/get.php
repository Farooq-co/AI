<?php
// resources/get.php
include '../../connect.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $sql = "
    SELECT id, classes_id, books_id, status
    FROM resources
    WHERE id = ?
    LIMIT 1
  ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    $result = $stmt->get_result();
    $resource = $result->fetch_assoc();
    echo json_encode($resource);
  } else {
    echo json_encode(["error" => "Error fetching resource details."]);
  }

  $stmt->close();
  $conn->close();
}
?>
