<?php
// delete_guardian.php
header('Content-Type: application/json');
session_start();

include '../../connect.php';

$response = ['success' => false, 'message' => ''];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        $response['message'] = 'Invalid request. Guardian ID is required.';
        echo json_encode($response);
        exit;
    }
    
    $guardian_id = intval($input['id']);
    
    if ($guardian_id <= 0) {
        $response['message'] = 'Invalid guardian ID.';
        echo json_encode($response);
        exit;
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Check if guardian exists
        $checkSql = "SELECT id, father_name, mother_name FROM student_guardians WHERE id = $guardian_id";
        $checkResult = mysqli_query($conn, $checkSql);
        
        if (mysqli_num_rows($checkResult) == 0) {
            throw new Exception('Guardian record not found.');
        }
        
        $guardian = mysqli_fetch_assoc($checkResult);
        
        // Delete the guardian record
        $deleteSql = "DELETE FROM student_guardians WHERE id = $guardian_id";
        
        if (mysqli_query($conn, $deleteSql)) {
            if (mysqli_affected_rows($conn) > 0) {
                mysqli_commit($conn);
                $response['success'] = true;
                $response['message'] = 'Guardian record for ' . htmlspecialchars($guardian['father_name']) . ' & ' . htmlspecialchars($guardian['mother_name']) . ' has been deleted successfully.';
            } else {
                throw new Exception('No records were deleted.');
            }
        } else {
            throw new Exception(mysqli_error($conn));
        }
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $response['success'] = false;
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
    
} else {
    $response['message'] = 'Invalid request method. Only POST method is allowed.';
}

mysqli_close($conn);
echo json_encode($response);
?>