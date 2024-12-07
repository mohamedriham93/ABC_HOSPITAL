<?php
session_start();
include('../config.php'); // Database connection

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch counts for statistics
$patient_count = $conn->query("SELECT COUNT(*) as count FROM patients")->fetch_assoc()['count'];
$doctor_count = $conn->query("SELECT COUNT(*) as count FROM doctors")->fetch_assoc()['count'];
$receptionist_count = $conn->query("SELECT COUNT(*) as count FROM receptionists")->fetch_assoc()['count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ABC Hospital</title>
    <link rel="stylesheet" href="../css/dashboard_style.css?varsion=1">
    <link rel="stylesheet" href="../css/Dashboard_Navigation.css?version=1">
    <!-- <link rel="stylesheet" href="../css/Navigation_Style.css"> -->
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
    <style>
        body{
            background-image: url("../images/img8.jpg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 150%;
        }

        h2 {
            text-align: center;
        }
       
    </style>
</head>
<body>
    <div class="nav">
        <ul>
        <li><a href="../index.php">Home</a></li>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_patients.php">Manage Patients</a></li>
            <li><a href="manage_doctors.php">Manage Doctors</a></li>
            <li><a href="manage_receptionists.php">Manage Receptionists</a></li>
            <li><a href="#">View Appointments</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <header>
        <br>
        <br>
        <br>
        <h1>Welcome to the Admin Dashboard</h1>
    </header>

    <main>
    <h2>Statistics</h2>
        <section class="statistics">
           
            <div class="stat-box">
                <h3>Total Patients</h3>
                <p><?php echo htmlspecialchars($patient_count); ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Doctors</h3>
                <p><?php echo htmlspecialchars($doctor_count); ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Receptionists</h3>
                <p><?php echo htmlspecialchars($receptionist_count); ?></p>
            </div>
        </section>

        <section class="actions">
            <h2>Actions</h2>
            <button><a href="add_patient2.php">Add Patient</a></button>
            <button><a href="add_doctor.php">Add Doctor</a></button>
            <button><a href="add_receptionist.php">Add Receptionist</a></button>
            <button><a href="../receptionists/view_appointments.php">View Appointments</a></button>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2024 ABC Hospital</p>
    </footer>
</body>
</html>
