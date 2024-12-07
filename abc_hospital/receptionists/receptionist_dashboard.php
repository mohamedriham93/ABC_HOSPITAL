<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Receptionist') {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <!-- <link rel="stylesheet" href="../css/form_style.css"> -->
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
    <link rel="stylesheet" href="../css/Navigation_Style.css">


    <style>
        body {
            background-image: url("../images/img8.jpg");
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;

            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 150%;
        }
    
    </style>
</head>

<body>
    <div class="nav">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="book_appointments.php">Book Appointment</a></li>
            <!-- <li><a href="../logout.php">Logout</a></li> -->
            <li><a href="view_appointments.php">View Appointments</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>
    <?php
    echo "<h2>Welcome, " . htmlspecialchars($_SESSION['username']) . "</h2>";
    ?>

    <div class="dashboard">
        <h2>Receptionist dashboard</h2>

        <div class="menu">
            <a href="view_appointments.php">View Appointments</a>
            <a href="../update_info.php">Update Profile</a>
            <!-- <a href="view_medical_history.php">View Medical History</a> -->
            <a href="../logout.php">Logout</a>
            <a href="../index.php">Home</a>
        </div>

        <div class="content">
            <h3>Your Dashboard Overview</h3>
            <p>Welcome to your Receptionist dashboard. Here you can manage appointments, update your information.</p>
            <br>
            <a href="book_appointments.php" class="button">Book Appointment</a> <!-- New button -->
        </div>
    </div>

</body>

</html>