<?php
// Database connection
$connection = mysqli_connect("localhost", "root", "", "abc_hospital");

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $appointment_id = $_POST['appointment_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $status = $_POST['status'];

    // Sanitize the input to prevent SQL injection
    $appointment_id = mysqli_real_escape_string($connection, $appointment_id);
    $appointment_date = mysqli_real_escape_string($connection, $appointment_date);
    $appointment_time = mysqli_real_escape_string($connection, $appointment_time);
    $status = mysqli_real_escape_string($connection, $status);

    // Update query to modify the appointment in the database
    $update_query = "UPDATE appointments 
                     SET date = '$appointment_date', time = '$appointment_time', status = '$status' 
                     WHERE id = $appointment_id";

    // Execute the query
    if (mysqli_query($connection, $update_query)) {
        // Redirect to the appointments page or show a success message
        header("Location: view_appointments.php"); // Or any other page you prefer
        exit();
    } else {
        // Error handling if the query fails
        echo "Error updating appointment: " . mysqli_error($connection);
    }
}
?>
