<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'add') {
        // Add New User with optional logo
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role_id = $_POST['role_id'];
        $institution_name = $_POST['institution_name'];
        
        // Handle logo upload
        $logoFileName = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $targetDir = "../uploads/logos/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $logoFileName = uniqid() . '_' . time() . '.' . $fileExtension;
                $targetFile = $targetDir . $logoFileName;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
                    // File uploaded successfully
                } else {
                    $logoFileName = null;
                }
            }
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, status, role_id, institution_name, logo) VALUES (?, ?, ?, 'Active', ?, ?, ?)");
        $stmt->bind_param("sssiss", $username, $email, $hashed_password, $role_id, $institution_name, $logoFileName);

        if ($stmt->execute()) {
            echo "New user added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();

    } elseif ($action == 'upload_logo') {
        // Standalone logo upload
        $user_id = $_POST['user_id'];
        $logoFileName = null;
        
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $targetDir = "../uploads/logos/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                // Get current logo to delete if exists
                $getCurrentStmt = $conn->prepare("SELECT logo FROM users WHERE id = ?");
                $getCurrentStmt->bind_param("i", $user_id);
                $getCurrentStmt->execute();
                $result = $getCurrentStmt->get_result();
                $currentUser = $result->fetch_assoc();
                
                if ($currentUser && !empty($currentUser['logo'])) {
                    $oldFilePath = $targetDir . $currentUser['logo'];
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $getCurrentStmt->close();
                
                $logoFileName = uniqid() . '_' . time() . '.' . $fileExtension;
                $targetFile = $targetDir . $logoFileName;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
                    // Update database
                    $updateStmt = $conn->prepare("UPDATE users SET logo = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $logoFileName, $user_id);
                    
                    if ($updateStmt->execute()) {
                        echo "Logo uploaded successfully.";
                    } else {
                        echo "Error updating database: " . $updateStmt->error;
                    }
                    $updateStmt->close();
                } else {
                    echo "Error moving uploaded file.";
                }
            } else {
                echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        } else {
            echo "No file uploaded or upload error.";
        }

    } elseif ($action == 'get_user') {
        // Get User Data for Editing (including logo)
        $id = $_POST['id'];

        $stmt = $conn->prepare("SELECT id, username, email, role_id, institution_name, logo FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            echo json_encode($user);
        } else {
            echo json_encode([]);
        }

        $stmt->close();

    } elseif ($action == 'edit') {
        // Edit User with optional logo change
        $id = $_POST['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role_id = $_POST['role_id'];
        $institution_name = $_POST['institution_name'];
        
        // Handle logo upload if new logo is provided
        $logoFileName = null;
        $updateLogo = false;
        
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $targetDir = "../uploads/logos/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                // Get current logo to delete if exists
                $getCurrentStmt = $conn->prepare("SELECT logo FROM users WHERE id = ?");
                $getCurrentStmt->bind_param("i", $id);
                $getCurrentStmt->execute();
                $result = $getCurrentStmt->get_result();
                $currentUser = $result->fetch_assoc();
                
                if ($currentUser && !empty($currentUser['logo'])) {
                    $oldFilePath = $targetDir . $currentUser['logo'];
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $getCurrentStmt->close();
                
                $logoFileName = uniqid() . '_' . time() . '.' . $fileExtension;
                $targetFile = $targetDir . $logoFileName;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
                    $updateLogo = true;
                }
            }
        }

        if (!empty($password)) {
            // Hash the new password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            if ($updateLogo) {
                // Update with new password and new logo
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, role_id = ?, institution_name = ?, logo = ? WHERE id = ?");
                $stmt->bind_param("sssissi", $username, $email, $hashed_password, $role_id, $institution_name, $logoFileName, $id);
            } else {
                // Update with new password only
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, role_id = ?, institution_name = ? WHERE id = ?");
                $stmt->bind_param("sssisi", $username, $email, $hashed_password, $role_id, $institution_name, $id);
            }
        } else {
            // Do not update the password
            if ($updateLogo) {
                // Update with new logo only
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role_id = ?, institution_name = ?, logo = ? WHERE id = ?");
                $stmt->bind_param("ssissi", $username, $email, $role_id, $institution_name, $logoFileName, $id);
            } else {
                // Update without password and without logo
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role_id = ?, institution_name = ? WHERE id = ?");
                $stmt->bind_param("ssisi", $username, $email, $role_id, $institution_name, $id);
            }
        }

        if ($stmt->execute()) {
            echo "User updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();

    } elseif ($action == 'change_status') {
        // Change User Status
        $id = $_POST['id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            echo "User status updated to $status.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();

    } elseif ($action == 'delete') {
        // Delete User with their logo
        $id = $_POST['id'];
        
        // Get logo filename before deleting user
        $getLogoStmt = $conn->prepare("SELECT logo FROM users WHERE id = ?");
        $getLogoStmt->bind_param("i", $id);
        $getLogoStmt->execute();
        $result = $getLogoStmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && !empty($user['logo'])) {
            $logoPath = "../uploads/logos/" . $user['logo'];
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        }
        $getLogoStmt->close();

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "User deleted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();

    } else {
        echo "Invalid action.";
    }

    $conn->close();
}
?>