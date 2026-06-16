<?php
// ajax/submit_student.php
header('Content-Type: application/json');
session_start();

include '../../connect.php';

// Function to sanitize input
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

// Function to validate mobile number (Pakistan format: 923001234567)
function validateMobile($mobile) {
    if (empty($mobile)) return true; // Optional field
    return preg_match('/^[0-9]{12}$/', $mobile);
}

// Function to validate CNIC format (12345-1234567-1)
function validateCNIC($cnic) {
    if (empty($cnic)) return true; // Optional field
    return preg_match('/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/', $cnic);
}

// Function to validate email
function validateEmail($email) {
    if (empty($email)) return true; // Optional field
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

$response = ['success' => false, 'message' => ''];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ==================== ADDRESS INFORMATION ====================
    $present_address = sanitize($conn, $_POST['present_address'] ?? '');
    $present_city_id = intval($_POST['present_city_id'] ?? 0);
    $present_area_id = !empty($_POST['present_area_id']) ? intval($_POST['present_area_id']) : null;
    $present_country = sanitize($conn, $_POST['present_country'] ?? '');
    $present_province = sanitize($conn, $_POST['present_province'] ?? '');
    
    $permanent_address = sanitize($conn, $_POST['permanent_address'] ?? '');
    $permanent_city_id = !empty($_POST['permanent_city_id']) ? intval($_POST['permanent_city_id']) : null;
    $permanent_area_id = !empty($_POST['permanent_area_id']) ? intval($_POST['permanent_area_id']) : null;
    $permanent_country = sanitize($conn, $_POST['permanent_country'] ?? '');
    $permanent_province = sanitize($conn, $_POST['permanent_province'] ?? '');
    
    // ==================== FATHER'S INFORMATION ====================
    $father_name = sanitize($conn, $_POST['father_name'] ?? '');
    $father_cnic = sanitize($conn, $_POST['father_cnic'] ?? '');
    $father_mobile = sanitize($conn, $_POST['father_mobile'] ?? '');
    $father_mobile_operator = !empty($_POST['father_mobile_operator']) ? intval($_POST['father_mobile_operator']) : null;
    $father_sms = isset($_POST['father_sms']) ? 1 : 0;
    $father_whatsapp = isset($_POST['father_whatsapp']) ? 1 : 0;
    $father_whatsapp_number = sanitize($conn, $_POST['father_whatsapp_number'] ?? '');
    $father_profession = sanitize($conn, $_POST['father_profession'] ?? '');
    $father_education = sanitize($conn, $_POST['father_education'] ?? '');
    $father_email = sanitize($conn, $_POST['father_email'] ?? '');
    
    // ==================== MOTHER'S INFORMATION ====================
    $mother_name = sanitize($conn, $_POST['mother_name'] ?? '');
    $mother_cnic = sanitize($conn, $_POST['mother_cnic'] ?? '');
    $mother_mobile = sanitize($conn, $_POST['mother_mobile'] ?? '');
    $mother_mobile_operator = !empty($_POST['mother_mobile_operator']) ? intval($_POST['mother_mobile_operator']) : null;
    $mother_sms = isset($_POST['mother_sms']) ? 1 : 0;
    $mother_whatsapp = isset($_POST['mother_whatsapp']) ? 1 : 0;
    $mother_whatsapp_number = sanitize($conn, $_POST['mother_whatsapp_number'] ?? '');
    $mother_profession = sanitize($conn, $_POST['mother_profession'] ?? '');
    $mother_education = sanitize($conn, $_POST['mother_education'] ?? '');
    $mother_email = sanitize($conn, $_POST['mother_email'] ?? '');
    
    // ==================== GUARDIAN'S INFORMATION ====================
    $guardian_name = sanitize($conn, $_POST['guardian_name'] ?? '');
    $guardian_cnic = sanitize($conn, $_POST['guardian_cnic'] ?? '');
    $guardian_mobile = sanitize($conn, $_POST['guardian_mobile'] ?? '');
    $guardian_mobile_operator = !empty($_POST['guardian_mobile_operator']) ? intval($_POST['guardian_mobile_operator']) : null;
    $guardian_sms = isset($_POST['guardian_sms']) ? 1 : 0;
    $guardian_whatsapp = isset($_POST['guardian_whatsapp']) ? 1 : 0;
    $guardian_whatsapp_number = sanitize($conn, $_POST['guardian_whatsapp_number'] ?? '');
    $guardian_profession = sanitize($conn, $_POST['guardian_profession'] ?? '');
    $guardian_education = sanitize($conn, $_POST['guardian_education'] ?? '');
    $guardian_email = sanitize($conn, $_POST['guardian_email'] ?? '');
    
    // ==================== VALIDATION ====================
    
    // Required field validation
    if (empty($present_address)) {
        $response['message'] = 'Present address is required';
        echo json_encode($response);
        exit;
    }
    
    if (empty($present_city_id)) {
        $response['message'] = 'Present city is required';
        echo json_encode($response);
        exit;
    }
    
    if (empty($father_name)) {
        $response['message'] = 'Father name is required';
        echo json_encode($response);
        exit;
    }
    
    if (empty($mother_name)) {
        $response['message'] = 'Mother name is required';
        echo json_encode($response);
        exit;
    }
    
    // Optional field validations
    if (!empty($father_cnic) && !validateCNIC($father_cnic)) {
        $response['message'] = 'Invalid Father CNIC format (should be: 12345-1234567-1)';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($mother_cnic) && !validateCNIC($mother_cnic)) {
        $response['message'] = 'Invalid Mother CNIC format (should be: 12345-1234567-1)';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($guardian_cnic) && !validateCNIC($guardian_cnic)) {
        $response['message'] = 'Invalid Guardian CNIC format (should be: 12345-1234567-1)';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($father_mobile) && !validateMobile($father_mobile)) {
        $response['message'] = 'Invalid Father Mobile number (should be 12 digits, e.g., 923001234567)';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($mother_mobile) && !validateMobile($mother_mobile)) {
        $response['message'] = 'Invalid Mother Mobile number (should be 12 digits, e.g., 923001234567)';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($guardian_mobile) && !validateMobile($guardian_mobile)) {
        $response['message'] = 'Invalid Guardian Mobile number (should be 12 digits, e.g., 923001234567)';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($father_email) && !validateEmail($father_email)) {
        $response['message'] = 'Invalid Father email address';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($mother_email) && !validateEmail($mother_email)) {
        $response['message'] = 'Invalid Mother email address';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($guardian_email) && !validateEmail($guardian_email)) {
        $response['message'] = 'Invalid Guardian email address';
        echo json_encode($response);
        exit;
    }
    
    // ==================== INSERT INTO DATABASE ====================
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // SQL query to insert student guardian information
        $sql = "INSERT INTO student_guardians (
                    present_address, present_city_id, present_area_id, present_country, present_province,
                    permanent_address, permanent_city_id, permanent_area_id, permanent_country, permanent_province,
                    father_name, father_cnic, father_mobile, father_mobile_operator, father_sms, father_whatsapp,
                    father_whatsapp_number, father_profession, father_education, father_email,
                    mother_name, mother_cnic, mother_mobile, mother_mobile_operator, mother_sms, mother_whatsapp,
                    mother_whatsapp_number, mother_profession, mother_education, mother_email,
                    guardian_name, guardian_cnic, guardian_mobile, guardian_mobile_operator, guardian_sms, guardian_whatsapp,
                    guardian_whatsapp_number, guardian_profession, guardian_education, guardian_email,
                    created_at
                ) VALUES (
                    '$present_address', $present_city_id, " . ($present_area_id ?? 'NULL') . ", '$present_country', '$present_province',
                    '$permanent_address', " . ($permanent_city_id ?? 'NULL') . ", " . ($permanent_area_id ?? 'NULL') . ", '$permanent_country', '$permanent_province',
                    '$father_name', '$father_cnic', '$father_mobile', " . ($father_mobile_operator ?? 'NULL') . ", $father_sms, $father_whatsapp,
                    '$father_whatsapp_number', '$father_profession', '$father_education', '$father_email',
                    '$mother_name', '$mother_cnic', '$mother_mobile', " . ($mother_mobile_operator ?? 'NULL') . ", $mother_sms, $mother_whatsapp,
                    '$mother_whatsapp_number', '$mother_profession', '$mother_education', '$mother_email',
                    '$guardian_name', '$guardian_cnic', '$guardian_mobile', " . ($guardian_mobile_operator ?? 'NULL') . ", $guardian_sms, $guardian_whatsapp,
                    '$guardian_whatsapp_number', '$guardian_profession', '$guardian_education', '$guardian_email',
                    NOW()
                )";
        
        if (mysqli_query($conn, $sql)) {
            $inserted_id = mysqli_insert_id($conn);
            mysqli_commit($conn);
            
            $response['success'] = true;
            $response['message'] = 'Guardian added successfully!';
            $response['student_id'] = $inserted_id;
        } else {
            throw new Exception(mysqli_error($conn));
        }
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $response['success'] = false;
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
    
} else {
    $response['message'] = 'Invalid request method';
}

mysqli_close($conn);
echo json_encode($response);
?>