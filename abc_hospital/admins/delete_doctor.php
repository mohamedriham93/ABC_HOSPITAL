<?php
session_start();
include('../config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $doctor_id = $_GET['id'];

    // Delete doctor specialization first
    $stmt1 = $conn->prepare("DELETE FROM specialization WHERE doctor_id = ?");
    $stmt1->bind_param("i", $doctor_id);
    $stmt1->execute();

    // Now delete doctor
    $stmt2 = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt2->bind_param("i", $doctor_id);
    $stmt2->execute();

    header("Location: manage_doctors.php"); // Redirect to manage doctors page
    exit();
}

$conn->close();
?>
