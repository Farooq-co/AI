<?php
include '../../parts/session_check.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
include '../../connect.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $contact1 = mysqli_real_escape_string($conn, trim($_POST['contact1']));
    $contact2 = isset($_POST['contact2']) ? mysqli_real_escape_string($conn, trim($_POST['contact2'])) : '';

    // Validate required fields
    if (empty($name) || empty($address) || empty($city) || empty($contact1)) {
        $_SESSION['message'] = 'All fields except Contact 2 are required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../general.php');
        exit();
    }

    // Prepare the SQL statement
    $query = "UPDATE basic_information SET name='$name', address='$address', city='$city', contact1='$contact1', contact2='$contact2'";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Details updated successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error updating details: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'danger';
    }

    // Redirect back to the form
    header('Location: ../general.php');
    exit();
} else {
    // Redirect if the request method is not POST
    header('Location: ../general.php');
    exit();
}
?>
