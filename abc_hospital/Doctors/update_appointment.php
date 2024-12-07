<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];

    $update_query = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    if ($update_query === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $update_query->bind_param("si", $new_status, $appointment_id);
    
    if ($update_query->execute()) {
        header("Location: view_appointments.php");
        exit();
    } else {
        die('Execute failed: ' . $update_query->error);
    }

}

$conn->close();
?>
