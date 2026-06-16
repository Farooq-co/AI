<?php
// ajax/save_student.php
header('Content-Type: application/json');
session_start();

include '../../connect.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data
    $class_id = isset($_POST['ddlSchoolClasses']) ? intval($_POST['ddlSchoolClasses']) : 0;
    $section_id = isset($_POST['ddlSchoolClassSection']) ? intval($_POST['ddlSchoolClassSection']) : 0;
    $group_id = isset($_POST['ddlGroups']) ? intval($_POST['ddlGroups']) : 0;
    $admission_date = isset($_POST['txtAdmissionDate']) ? mysqli_real_escape_string($conn, $_POST['txtAdmissionDate']) : '';
    $admission_number = isset($_POST['txtAdmissionNumber']) ? mysqli_real_escape_string($conn, $_POST['txtAdmissionNumber']) : '';
    $student_name = isset($_POST['txtStudentName']) ? mysqli_real_escape_string($conn, $_POST['txtStudentName']) : '';
    $father_name = isset($_POST['txtFatherName']) ? mysqli_real_escape_string($conn, $_POST['txtFatherName']) : '';
    $date_of_birth = isset($_POST['txtDateOfBirth']) ? mysqli_real_escape_string($conn, $_POST['txtDateOfBirth']) : '';
    $roll_number = isset($_POST['txtRollNumber']) ? mysqli_real_escape_string($conn, $_POST['txtRollNumber']) : '';
    $student_category_id = isset($_POST['ddlStudentCategoryIdFk']) ? intval($_POST['ddlStudentCategoryIdFk']) : 0;
    $religion_id = isset($_POST['ddlReligionIdFk']) ? intval($_POST['ddlReligionIdFk']) : 0;
    $gender_id = isset($_POST['ddlGenderIdFk']) ? intval($_POST['ddlGenderIdFk']) : 0;
    $blood_group_id = isset($_POST['ddlBloodGroupIdFk']) ? intval($_POST['ddlBloodGroupIdFk']) : 0;
    $admission_effective_date = isset($_POST['txtAdmissionEffectiveDate']) ? mysqli_real_escape_string($conn, $_POST['txtAdmissionEffectiveDate']) : '';
    $family_number = isset($_POST['txtFamilyNumber']) ? mysqli_real_escape_string($conn, $_POST['txtFamilyNumber']) : '';
    $hobbies = isset($_POST['txtHobbies']) ? mysqli_real_escape_string($conn, $_POST['txtHobbies']) : '';
    $place_of_birth = isset($_POST['txtplaceofbirth']) ? mysqli_real_escape_string($conn, $_POST['txtplaceofbirth']) : '';
    $fee_package_id = isset($_POST['ddlFeePackage']) ? intval($_POST['ddlFeePackage']) : 0;
    $guardian_id = isset($_POST['guardian_id']) ? intval($_POST['guardian_id']) : 0;
    $student_search1 = isset($_POST['StudentSearch1']) ? mysqli_real_escape_string($conn, $_POST['StudentSearch1']) : '';
    $student_search2 = isset($_POST['StudentSearch2']) ? mysqli_real_escape_string($conn, $_POST['StudentSearch2']) : '';
    $student_search3 = isset($_POST['StudentSearch3']) ? mysqli_real_escape_string($conn, $_POST['StudentSearch3']) : '';
    
    // Validate required fields
    if ($class_id <= 0) {
        $response['message'] = 'Please select a valid Class';
        echo json_encode($response);
        exit;
    }
    
    if ($section_id <= 0) {
        $response['message'] = 'Please select a valid Section';
        echo json_encode($response);
        exit;
    }
    
    if (empty($admission_date)) {
        $response['message'] = 'Admission date is required';
        echo json_encode($response);
        exit;
    }
    
    if (empty($student_name)) {
        $response['message'] = 'Student name is required';
        echo json_encode($response);
        exit;
    }
    
    if (empty($father_name)) {
        $response['message'] = 'Father name is required';
        echo json_encode($response);
        exit;
    }
    
    if (empty($date_of_birth)) {
        $response['message'] = 'Date of birth is required';
        echo json_encode($response);
        exit;
    }
    
    if ($student_category_id <= 0) {
        $response['message'] = 'Please select a valid Student Category';
        echo json_encode($response);
        exit;
    }
    
    if ($religion_id <= 0) {
        $response['message'] = 'Please select a valid Religion';
        echo json_encode($response);
        exit;
    }
    
    if ($gender_id <= 0) {
        $response['message'] = 'Please select a valid Gender';
        echo json_encode($response);
        exit;
    }
    
    if (empty($admission_effective_date)) {
        $response['message'] = 'Admission effective date is required';
        echo json_encode($response);
        exit;
    }
    
    // Handle file upload
    $student_picture = '';
    if (isset($_FILES['StudentPicture']) && $_FILES['StudentPicture']['error'] == 0 && $_FILES['StudentPicture']['size'] > 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['StudentPicture']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $upload_dir = '../../uploads/students/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $student_picture = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
            $upload_path = $upload_dir . $student_picture;
            
            if (!move_uploaded_file($_FILES['StudentPicture']['tmp_name'], $upload_path)) {
                $student_picture = '';
            }
        }
    }
    
    // Prepare the INSERT query
    $group_value = ($group_id > 0) ? $group_id : 'NULL';
    $blood_group_value = ($blood_group_id > 0) ? $blood_group_id : 'NULL';
    $fee_package_value = ($fee_package_id > 0) ? $fee_package_id : 'NULL';
    $guardian_value = ($guardian_id > 0) ? $guardian_id : 'NULL';
    $roll_number_value = !empty($roll_number) ? "'$roll_number'" : 'NULL';
    $family_number_value = !empty($family_number) ? "'$family_number'" : 'NULL';
    $hobbies_value = !empty($hobbies) ? "'$hobbies'" : 'NULL';
    $place_of_birth_value = !empty($place_of_birth) ? "'$place_of_birth'" : 'NULL';
    $student_search1_value = !empty($student_search1) ? "'$student_search1'" : 'NULL';
    $student_search2_value = !empty($student_search2) ? "'$student_search2'" : 'NULL';
    $student_search3_value = !empty($student_search3) ? "'$student_search3'" : 'NULL';
    $admission_number_value = !empty($admission_number) ? "'$admission_number'" : 'NULL';
    $student_picture_value = !empty($student_picture) ? "'$student_picture'" : 'NULL';
    
    $query = "INSERT INTO students (
        class_id, section_id, group_id, admission_date, admission_number, 
        student_name, father_name, date_of_birth, roll_number, 
        student_category_id, religion_id, gender_id, blood_group_id, 
        admission_effective_date, family_number, hobbies, place_of_birth, 
        fee_package_id, guardian_id, student_picture, 
        student_search1, student_search2, student_search3, 
        created_at, status
    ) VALUES (
        $class_id, $section_id, $group_value, '$admission_date', $admission_number_value,
        '$student_name', '$father_name', '$date_of_birth', $roll_number_value,
        $student_category_id, $religion_id, $gender_id, $blood_group_value,
        '$admission_effective_date', $family_number_value, $hobbies_value, $place_of_birth_value,
        $fee_package_value, $guardian_value, $student_picture_value,
        $student_search1_value, $student_search2_value, $student_search3_value,
        NOW(), 'Active'
    )";
    
    if (mysqli_query($conn, $query)) {
        $student_id = mysqli_insert_id($conn);
        $response['success'] = true;
        $response['message'] = 'Student saved successfully';
        $response['student_id'] = $student_id;
    } else {
        $response['message'] = 'Database error: ' . mysqli_error($conn);
    }
    
} else {
    $response['message'] = 'Invalid request method';
}

mysqli_close($conn);
echo json_encode($response);
?>