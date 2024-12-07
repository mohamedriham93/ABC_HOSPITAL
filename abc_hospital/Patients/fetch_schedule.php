<?php
include('config.php');

if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];

    // Fetch schedule for the selected doctor
    $query = "SELECT schedule FROM doctors WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo $row['schedule'];
}
?>
