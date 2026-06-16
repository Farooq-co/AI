<?php
// resources/upload_files.php

include '../../connect.php';

// Basic checks
if (!isset($_POST['resource_id'])) {
  http_response_code(400);
  echo "No resource_id provided.";
  exit;
}

$resourceId = intval($_POST['resource_id']);

// Prepare filenames for potential uploads
$coverImageName = null;
$resourceFileName = null;

// Handle Cover Image
if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == UPLOAD_ERR_OK) {
  $coverImageTmp  = $_FILES['cover_image']['tmp_name'];
  $coverImageOrig = basename($_FILES['cover_image']['name']); // original filename
  $extension      = pathinfo($coverImageOrig, PATHINFO_EXTENSION);

  // Generate a unique filename, or store as-is
  $coverImageName = "cover_" . time() . "_" . rand(1000,9999) . "." . $extension;
  $targetPathCover = "../../uploads/cover_images/" . $coverImageName;

  if (!move_uploaded_file($coverImageTmp, $targetPathCover)) {
    http_response_code(500);
    echo "Failed to upload cover image.";
    exit;
  }
}

// Handle Resource File
if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] == UPLOAD_ERR_OK) {
  $resourceFileTmp  = $_FILES['resource_file']['tmp_name'];
  $resourceFileOrig = basename($_FILES['resource_file']['name']);
  $extension        = pathinfo($resourceFileOrig, PATHINFO_EXTENSION);

  $resourceFileName = "file_" . time() . "_" . rand(1000,9999) . "." . $extension;
  $targetPathFile = "../../uploads/resource_files/" . $resourceFileName;

  if (!move_uploaded_file($resourceFileTmp, $targetPathFile)) {
    http_response_code(500);
    echo "Failed to upload resource file.";
    exit;
  }
}

// Update the database
$updateFields = [];
if ($coverImageName !== null) {
  $updateFields[] = "cover_image = '$coverImageName'";
}
if ($resourceFileName !== null) {
  $updateFields[] = "resource_file = '$resourceFileName'";
}

if (!empty($updateFields)) {
  $updateSql = "UPDATE resources SET " . implode(', ', $updateFields) . " WHERE id = $resourceId";
  if (!$conn->query($updateSql)) {
    http_response_code(500);
    echo "Database update failed: " . $conn->error;
    exit;
  }
}

$conn->close();
http_response_code(200);
echo "Upload successful";
