<?php
// update_student.php
header('Content-Type: application/json');
session_start();

include '../../connect.php';

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get student ID
$studentId = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
if ($studentId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
    exit;
}

// Get guardian ID
$guardianId = isset($_POST['guardian_id']) ? intval($_POST['guardian_id']) : 0;

// Get form data - FIXED: Use correct field names from the form
$classId = isset($_POST['ddlSchoolClasses']) ? intval($_POST['ddlSchoolClasses']) : 0;
$sectionId = isset($_POST['ddlSchoolClassSection']) ? intval($_POST['ddlSchoolClassSection']) : 0;
$groupId = isset($_POST['ddlGroups']) ? intval($_POST['ddlGroups']) : 0;
$admissionDate = isset($_POST['txtAdmissionDate']) ? $_POST['txtAdmissionDate'] : '';
$admissionNumber = isset($_POST['txtAdmissionNumber']) ? sanitizeInput($_POST['txtAdmissionNumber']) : '';
$studentName = isset($_POST['txtStudentName']) ? sanitizeInput($_POST['txtStudentName']) : '';
$fatherName = isset($_POST['txtFatherName']) ? sanitizeInput($_POST['txtFatherName']) : '';
$dateOfBirth = isset($_POST['txtDateOfBirth']) ? $_POST['txtDateOfBirth'] : '';
$rollNumber = isset($_POST['txtRollNumber']) ? sanitizeInput($_POST['txtRollNumber']) : '';
$studentCategoryId = isset($_POST['ddlStudentCategoryIdFk']) ? intval($_POST['ddlStudentCategoryIdFk']) : 0;
$religionId = isset($_POST['ddlReligionIdFk']) ? intval($_POST['ddlReligionIdFk']) : 0;
$genderId = isset($_POST['ddlGenderIdFk']) ? intval($_POST['ddlGenderIdFk']) : 0;
$bloodGroupId = isset($_POST['ddlBloodGroupIdFk']) ? intval($_POST['ddlBloodGroupIdFk']) : 0;
$admissionEffectiveDate = isset($_POST['txtAdmissionEffectiveDate']) ? $_POST['txtAdmissionEffectiveDate'] : '';
$familyNumber = isset($_POST['txtFamilyNumber']) ? sanitizeInput($_POST['txtFamilyNumber']) : '';
$hobbies = isset($_POST['txtHobbies']) ? sanitizeInput($_POST['txtHobbies']) : '';
$placeOfBirth = isset($_POST['txtplaceofbirth']) ? sanitizeInput($_POST['txtplaceofbirth']) : '';
$feePackageId = isset($_POST['ddlFeePackage']) ? intval($_POST['ddlFeePackage']) : 0;
$studentSearch1 = isset($_POST['StudentSearch1']) ? sanitizeInput($_POST['StudentSearch1']) : '';
$studentSearch2 = isset($_POST['StudentSearch2']) ? sanitizeInput($_POST['StudentSearch2']) : '';
$studentSearch3 = isset($_POST['StudentSearch3']) ? sanitizeInput($_POST['StudentSearch3']) : '';

// Debug logging (remove in production)
error_log("Admission Date: " . $admissionDate);
error_log("Admission Effective Date: " . $admissionEffectiveDate);

// Validate required fields
$requiredFields = [
    'class_id' => $classId,
    'section_id' => $sectionId,
    'admission_date' => $admissionDate,
    'student_name' => $studentName,
    'father_name' => $fatherName,
    'date_of_birth' => $dateOfBirth,
    'student_category_id' => $studentCategoryId,
    'religion_id' => $religionId,
    'gender_id' => $genderId,
    'admission_effective_date' => $admissionEffectiveDate
];

$missingFields = [];
foreach ($requiredFields as $fieldName => $value) {
    if (empty($value) || $value == '0') {
        $missingFields[] = $fieldName;
    }
}

if (!empty($missingFields)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields: ' . implode(', ', $missingFields)]);
    exit;
}

// Handle image upload
$studentPicture = '';
$imageUpdated = false;

// Check if existing picture is passed
$existingPicture = isset($_POST['existing_picture']) ? sanitizeInput($_POST['existing_picture']) : '';

if (isset($_FILES['StudentPicture']) && $_FILES['StudentPicture']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['StudentPicture'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    
    // Get file extension
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    
    if (in_array($fileExt, $allowedExtensions)) {
        // Check file size (2MB max)
        if ($fileSize <= 2 * 1024 * 1024) {
            // Generate unique file name
            $newFileName = time() . '_' . rand(1000, 9999) . '.' . $fileExt;
            $uploadDirectory = '../../uploads/students/';
            
            // Create directory if not exists
            if (!file_exists($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }
            
            $uploadPath = $uploadDirectory . $newFileName;
            
            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                $studentPicture = $newFileName;
                $imageUpdated = true;
                
                // Delete old image if exists
                if (!empty($existingPicture)) {
                    $oldImagePath = $uploadDirectory . $existingPicture;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Image size should be less than 2MB']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, and PNG files are allowed']);
        exit;
    }
}

// If no new image uploaded, keep existing
if (!$imageUpdated && !empty($existingPicture)) {
    $studentPicture = $existingPicture;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Update student record with all fields including dates
    $updateQuery = "UPDATE students SET 
                    class_id = ?,
                    section_id = ?,
                    group_id = ?,
                    guardian_id = ?,
                    admission_date = ?,
                    admission_number = ?,
                    student_name = ?,
                    father_name = ?,
                    date_of_birth = ?,
                    roll_number = ?,
                    student_category_id = ?,
                    religion_id = ?,
                    gender_id = ?,
                    blood_group_id = ?,
                    admission_effective_date = ?,
                    family_number = ?,
                    hobbies = ?,
                    place_of_birth = ?,
                    fee_package_id = ?,
                    student_picture = ?,
                    student_search1 = ?,
                    student_search2 = ?,
                    student_search3 = ?,
                    updated_at = NOW()
                    WHERE id = ?";
    
    $stmt = $conn->prepare($updateQuery);
    
    // Handle NULL for empty values
    $groupId = $groupId > 0 ? $groupId : null;
    $guardianId = $guardianId > 0 ? $guardianId : null;
    $bloodGroupId = $bloodGroupId > 0 ? $bloodGroupId : null;
    $feePackageId = $feePackageId > 0 ? $feePackageId : null;
    
    $stmt->bind_param(
        "iiiiisssssiiiiisssissssi",
        $classId,
        $sectionId,
        $groupId,
        $guardianId,
        $admissionDate,
        $admissionNumber,
        $studentName,
        $fatherName,
        $dateOfBirth,
        $rollNumber,
        $studentCategoryId,
        $religionId,
        $genderId,
        $bloodGroupId,
        $admissionEffectiveDate,
        $familyNumber,
        $hobbies,
        $placeOfBirth,
        $feePackageId,
        $studentPicture,
        $studentSearch1,
        $studentSearch2,
        $studentSearch3,
        $studentId
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update student: " . $stmt->error);
    }
    
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Student record updated successfully',
        'student_id' => $studentId,
        'guardian_id' => $guardianId,
        'admission_date' => $admissionDate,
        'admission_effective_date' => $admissionEffectiveDate
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>