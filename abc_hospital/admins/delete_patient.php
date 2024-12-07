<?php
session_start();
include('../config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE patient_id = ?");
    $stmt->bind_param("i", $patient_id);

    if ($stmt->execute()) {

        $stmt2 = $conn->prepare("DELETE FROM patients WHERE patient_id = ?");
        $stmt2->bind_param("i", $patient_id);

        if (!$stmt2->execute()) {
            echo "Error deleting from Patients: " . $stmt2->error;
            $stmt2->close();
            $conn->close();
            exit();
        }
        $stmt2->close();

        header("Location: manage_patients.php");


        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
