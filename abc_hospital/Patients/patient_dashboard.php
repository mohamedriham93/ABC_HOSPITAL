<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
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
    <!-- <link rel="stylesheet" href="../css/dashboard.css?version=1"> -->
    <!-- <link rel="stylesheet" href="../css/form_style.css"> -->
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
    <!-- <link rel="stylesheet" href="../css/Navigation_Style.css"> -->
    <link rel="stylesheet" href="../css/Navigation_Style.css?version = 1   " />
    <link rel="stylesheet" href="../css/style.css?version= 1">
    <link rel="stylesheet" href="../css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/index.css">


    <style>
        body {
            background-image: url("../images/img8.jpg");
            margin: 0;
            padding: 0;
            text-align: center;

            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover
        }

        .button2 {
            background-color: aqua;
            width: 100px;
            height: 50px;
            margin: 10px;
            padding: 10px;
            text-decoration: none;
            color:purple;
        }

        .button {
            background-color: white;
            text-decoration: none;
            padding: 10px;
            color: black;
            
        }
        :hover.button {
            background-color: black;
            color: beige;
            text-decoration: none;

        }

        .button2:hover {
            background-color: black;
            color: white;
            text-decoration: none;
        }

        h3, p {
            color: aliceblue;
        }

        @media (max-width: 768px) {
            .button2 {
            background-color: aqua;
            width: 100px;
            height: 50px;
            font-size: medium;
            margin: 20px;
            text-decoration: none;
        }

        .button2:hover {
            background-color: black;
            color: white;
            text-decoration: none;
        }
            
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="nav">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="book_appointments.php">Book Appointment</a></li>
                <!-- <li><a href="../logout.php">Logout</a></li> -->
                 <li><a href="../specializations.php">View Doctors</a></li>
                <li><a href="view_appointments.php">View Appointments</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="header_right">
            <?php if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
                <?= htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']) ?>
            <?php elseif (isset($_SESSION['role']) && isset($_SESSION['username'])): ?>
                <?= htmlspecialchars($_SESSION['username']) ?>
            <?php endif; ?>

            <a href="..update_info.php"><img class="profile" src="../display_image.php" alt="Profile Picture" onerror="this.onerror=null; this.src='../images/no-profile-picture-icon.png';"></a>
        </div>
    </div>

    <div class="index_box">
        <h2>Patient Dashboard</h2>

        <div class="menu">
            <a href="view_appointments.php" class="button2">View Appointments</a>
            <a href="../update_info.php" class="button2">Update Profile</a>
            <!-- <a href="view_medical_history.php">View</a> -->
            
            <a href="../index.php" class="button2">Home</a>
            <a href="../logout.php" class="button2">Logout</a>
        </div>

        <div class="content">
            <h3>Your Dashboard Overview</h3>
            <p>Welcome to your dashboard. Here you can manage your appointments, update your personal information, and view your medical history.</p>
            <br>
            <a href="book_appointments.php" class="button">Book Appointment</a> <!-- New button -->
        </div>
    </div>

</body>

</html>