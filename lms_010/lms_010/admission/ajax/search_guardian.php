<?php
// ajax/search_guardian.php
header('Content-Type: application/json');
session_start();

include '../../connect.php';

$response = ['success' => false, 'message' => '', 'guardians' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchTerm = trim(mysqli_real_escape_string($conn, $_POST['search']));
    
    if (empty($searchTerm)) {
        $response['message'] = 'Please enter a search term';
        echo json_encode($response);
        exit;
    }
    
    $query = "SELECT 
                sg.id,
                sg.father_name, sg.father_cnic, sg.father_mobile, sg.father_email,
                sg.father_whatsapp_number, sg.father_profession, sg.father_education,
                sg.mother_name, sg.mother_cnic, sg.mother_mobile, sg.mother_email,
                sg.mother_whatsapp_number, sg.mother_profession, sg.mother_education,
                sg.guardian_name, sg.guardian_cnic, sg.guardian_mobile, sg.guardian_email,
                sg.guardian_whatsapp_number, sg.guardian_profession, sg.guardian_education,
                sg.present_address, sg.present_city_id, sg.present_area_id, 
                sg.present_country, sg.present_province,
                sg.permanent_address, sg.permanent_city_id, sg.permanent_area_id,
                sg.permanent_country, sg.permanent_province
              FROM student_guardians sg
              WHERE sg.father_mobile LIKE '%$searchTerm%'
                 OR sg.father_whatsapp_number LIKE '%$searchTerm%'
                 OR sg.father_cnic LIKE '%$searchTerm%'
                 OR sg.father_name LIKE '%$searchTerm%'
                 OR sg.mother_mobile LIKE '%$searchTerm%'
                 OR sg.mother_whatsapp_number LIKE '%$searchTerm%'
                 OR sg.mother_cnic LIKE '%$searchTerm%'
                 OR sg.mother_name LIKE '%$searchTerm%'
                 OR sg.guardian_mobile LIKE '%$searchTerm%'
                 OR sg.guardian_whatsapp_number LIKE '%$searchTerm%'
                 OR sg.guardian_cnic LIKE '%$searchTerm%'
                 OR sg.guardian_name LIKE '%$searchTerm%'
              ORDER BY sg.id DESC
              LIMIT 20";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $guardians = [];
        while ($guardian = mysqli_fetch_assoc($result)) {
            $guardians[] = $guardian;
        }
        
        $response['success'] = true;
        $response['guardians'] = $guardians;
        $response['message'] = count($guardians) . ' guardian(s) found';
    } else {
        $response['message'] = 'No guardian found matching "' . htmlspecialchars($searchTerm) . '"';
    }
} else {
    $response['message'] = 'Invalid request';
}

mysqli_close($conn);
echo json_encode($response);
?>