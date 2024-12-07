<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user id from session

$doctor_query = $conn->prepare("SELECT doctor_id FROM users WHERE user_id = ?");
if ($doctor_query === false) {
    die('Prepare failed: ' . $conn->error);
}

$doctor_query->bind_param("i", $user_id);
$doctor_query->execute();
$doctor_query->bind_result($doctor_id);
$doctor_query->fetch();
$doctor_query->close();

$appointments_query = $conn->prepare("
    SELECT a.id, a.date, a.time, a.status, p.first_name, p.last_name 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.patient_id 
    WHERE a.doctor_id = ? 
    ORDER BY a.date ASC, a.time ASC
");

if ($appointments_query === false) {
    die('Prepare failed: ' . $conn->error);
}

$appointments_query->bind_param("i", $doctor_id);

if (!$appointments_query->execute()) {
    die('Execute failed: ' . $appointments_query->error);
}

$appointments_query->store_result(); // Store result to check the number of rows
$appointments_query->bind_result($appointment_id, $appointment_date, $appointment_time, $appointment_status, $patient_first_name, $patient_last_name);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link rel="stylesheet" href="../css/appointments_style.css">
    <link rel="stylesheet" href="../css/Navigation_Style.css?version = 1   " />
    <link rel="stylesheet" href="../css/style.css?version= 1">
    <link rel="stylesheet" href="../css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/index.css">

    <style>
      

        h1 {
            color: aquamarine;
        }

        .btn {
            padding: 0.2rem;
            margin: 0.2rem;
            background-color: rgb(26, 220, 254);
            border-radius: 8px;
            backdrop-filter: blur(5px);
            color: black;
            font-size: 0.8rem;
            text-decoration: none;
            border: none;
        }

        .run {
            background-color: black;
            color: aquamarine;
            margin-top: 2rem;
            
        }

        :hover.run {
            background-color: blueviolet;
            color: black;

        }
    </style>
</head>

<body>
    <div class="header">
        <div class="nav">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="#">News</a></li>
                <!-- <li><a href="#">Gallery</a></li> -->
                <li><a href="#">Contact Us</a></li>
                <li><a href="../about-us.php">About Us</a></li>
                <!-- <li><a href="doctors/doctor_dashboard.php">Doctor Dashboard</a></li> -->
                <li><a href="view_appointments.php">View Appointments</a></li>
                <li><a href="../logout.php">Logout</a></li>


            </ul>
            <div class="header_right">
                <?php if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
                    <?= htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']) ?>
                <?php elseif (isset($_SESSION['role']) && isset($_SESSION['username'])): ?>
                    <?= htmlspecialchars($_SESSION['username']) ?>
                <?php endif; ?>

                <a href="../upload_image.php"><img class="profile" src="../display_image.php" alt="Profile Picture" onerror="this.onerror=null; this.src='../images/no-profile-picture-icon.png';"></a>
            </div>
        </div>
    </div>

    <div class="index_box">
        <h1>Your Appointments</h1>

        <table>
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($appointments_query->num_rows > 0) {
                    while ($appointments_query->fetch()) {
                        echo "<tr>";
                        echo "<td data-label='Appointment ID'>" . htmlspecialchars($appointment_id) . "</td>";
                        echo "<td data-label='Patient Name'>" . htmlspecialchars($patient_first_name . ' ' . $patient_last_name) . "</td>";
                        echo "<td data-label='Date'>" . htmlspecialchars($appointment_date) . "</td>";
                        echo "<td data-label='Time'>" . htmlspecialchars($appointment_time) . "</td>";
                        echo "<td data-label='Status'>" . htmlspecialchars($appointment_status) . "</td>";
                        echo "<td data-label='Actions'>
                            <form method='post' action='update_appointment.php'>
                                <input type='hidden' name='appointment_id' value='$appointment_id'>
                                <button class='btn' type='submit' name='status' value='Pending' " . ($appointment_status === 'Pending' ? 'disabled' : '') . ">Set Pending</button>
                                <button class='btn' type='submit' name='status' value='Confirmed' " . ($appointment_status === 'Confirmed' ? 'disabled' : '') . ">Confirm</button>
                                <button class='btn' type='submit' name='status' value='Cancelled' " . ($appointment_status === 'Cancelled' ? 'disabled' : '') . ">Cancel</button>
                                <button class='btn' type='submit' name='status' value='Completed' " . ($appointment_status === 'Completed' ? 'disabled' : '') . ">Complete</button>
                            </form>
                          </td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <a href="doctor_dashboard.php"><button class="run">Back to Dashboard</button></a>
</body>

</html>

<?php
$appointments_query->close();
$conn->close();
?>