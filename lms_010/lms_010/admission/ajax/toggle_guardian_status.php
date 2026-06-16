<?php
// toggle_guardian_status.php
header('Content-Type: application/json');
session_start();

include '../../connect.php';

$response = ['success' => false, 'message' => ''];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id']) || !isset($input['status'])) {
        $response['message'] = 'Invalid request. Guardian ID and status are required.';
        echo json_encode($response);
        exit;
    }
    
    $guardian_id = intval($input['id']);
    $new_status = trim($input['status']);
    
    if ($guardian_id <= 0) {
        $response['message'] = 'Invalid guardian ID.';
        echo json_encode($response);
        exit;
    }
    
    // Validate status
    $valid_statuses = ['Active', 'Inactive'];
    if (!in_array($new_status, $valid_statuses)) {
        $response['message'] = 'Invalid status value. Status must be either "Active" or "Inactive".';
        echo json_encode($response);
        exit;
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Check if guardian exists
        $checkSql = "SELECT id, father_name, mother_name, status FROM student_guardians WHERE id = $guardian_id";
        $checkResult = mysqli_query($conn, $checkSql);
        
        if (mysqli_num_rows($checkResult) == 0) {
            throw new Exception('Guardian record not found.');
        }
        
        $guardian = mysqli_fetch_assoc($checkResult);
        $old_status = $guardian['status'];
        
        // Check if status is already the same
        if ($old_status === $new_status) {
            mysqli_commit($conn);
            $response['success'] = true;
            $response['message'] = 'Guardian status is already ' . $new_status . '. No changes made.';
            echo json_encode($response);
            exit;
        }
        
        // Update the status
        $updateSql = "UPDATE student_guardians SET status = '$new_status', updated_at = NOW() WHERE id = $guardian_id";
        
        if (mysqli_query($conn, $updateSql)) {
            if (mysqli_affected_rows($conn) > 0) {
                mysqli_commit($conn);
                $response['success'] = true;
                $response['message'] = 'Guardian status for ' . htmlspecialchars($guardian['father_name']) . ' & ' . htmlspecialchars($guardian['mother_name']) . ' has been changed from ' . $old_status . ' to ' . $new_status . '.';
            } else {
                throw new Exception('Failed to update status.');
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