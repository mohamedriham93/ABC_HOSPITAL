<?php
// Include the database connection file
include('../config.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $doctor_id = $_POST['doctor_id'];
    $patient_id = $_POST['patient_id'];
    $appointment_date = $_POST['date'];
    $appointment_time = $_POST['time'];

    // Fetch doctor's name based on the doctor_id
    $doctor_name_query = "SELECT name FROM doctors WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $doctor_name_query)) {
        mysqli_stmt_bind_param($stmt, 'i', $doctor_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $doctor_name);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    // Insert appointment into the database
    $insert_query = "INSERT INTO appointments (doctor_id, patient_id, date, time, status, doctor_name) 
                     VALUES (?, ?, ?, ?, 'Pending', ?)";
    if ($stmt = mysqli_prepare($conn, $insert_query)) {
        mysqli_stmt_bind_param($stmt, 'iisss', $doctor_id, $patient_id, $appointment_date, $appointment_time, $doctor_name);

        if (mysqli_stmt_execute($stmt)) {
            echo "Appointment successfully booked.";
        } else {
            echo "Error: Could not book appointment.";
        }

        mysqli_stmt_close($stmt);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
