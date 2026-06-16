<?php
header('Content-Type: application/json');
include '../../connect.php';

$city_id = $_POST['city_id'];

$response = ['areas' => []];

// Get areas for this city
$areaSql = "SELECT id, name FROM areas WHERE city_id = ? AND status = 'Active' ORDER BY name";
$areaStmt = $conn->prepare($areaSql);
$areaStmt->bind_param("i", $city_id);
$areaStmt->execute();
$areaResult = $areaStmt->get_result();

while ($area = $areaResult->fetch_assoc()) {
    $response['areas'][] = $area;
}

echo json_encode($response);

$conn->close();
?>