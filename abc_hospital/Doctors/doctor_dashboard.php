<?php
session_start();
include('../config.php');

// Check login status
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user_id from session

// Fetch doctor_id from users table
$user_query = $conn->prepare("SELECT doctor_id FROM users WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_query->bind_result($doctor_id);
if (!$user_query->fetch()) {
    die("Error: Doctor ID not found.");
}
$user_query->close();

// Fetch doctor details
$doctor_query = $conn->prepare("SELECT name, email, phone_number, address, schedule FROM doctors WHERE id = ?");
$doctor_query->bind_param("i", $doctor_id);
$doctor_query->execute();
$doctor_query->bind_result($doctor_name, $email, $phone_number, $address, $schedule);
if (!$doctor_query->fetch()) {
    die("Error: Doctor details not found.");
}
$doctor_query->close();

// Fetch upcoming appointments
$appointment_query = $conn->prepare("SELECT date, time, status FROM appointments WHERE doctor_id = ? AND date >= CURDATE() ORDER BY date, time");
$appointment_query->bind_param("i", $doctor_id);
$appointment_query->execute();
$appointment_query->store_result();
$appointment_query->bind_result($appointment_date, $appointment_time, $appointment_status);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="../css/Navigation_Style.css?version=1.0" />
    <link rel="stylesheet" href="../css/style.css?version=1">
    <link rel="stylesheet" href="../css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/index.css">

    <style>
        .your-details, .appointments, .actions {
            text-align: center;
            background-color: rgba(21, 37, 32, 0.8);
            color: white;
            border-radius: 10px;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px 40px;
            backdrop-filter: blur(2px);
        }

        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
            color: white;
        }

        table, th, td {
            border: 1px solid white;
            padding: 10px;
            text-align: center;
        }

        .btn-doc {
            background-color: rgb(15 255 6);
            margin: 20px 10px;
            padding: 10px 20px;
            font-size: 16px;
        }

        .btn-doc:hover {
            background-color: rgb(0, 0, 0);
            color: white;
        }

        .header_right {
            text-align: right;
            padding-right: 20px;
        }

        .header_right a img {
            border-radius: 50%;
            height: 40px;
            width: 40px;
        }
        p {
            color: aqua;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="nav">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../about-us.php">News</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="../about-us.php">About Us</a></li>
                <li><a href="view_appointments.php">View Appointments</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
            <div class="header_right">
                <?php if (isset($_SESSION['first_name'], $_SESSION['last_name'])): ?>
                    <?= htmlspecialchars($_SESSION['first_name'] . " " . $_SESSION['last_name']); ?>
                <?php elseif (isset($_SESSION['username'])): ?>
                    <?= htmlspecialchars($_SESSION['username']); ?>
                <?php endif; ?>
                <a href="../update_info.php"><img class="profile" src="../display_image.php" alt="Profile Picture" onerror="this.onerror=null; this.src='../images/no-profile-picture-icon.png';"></a>
            </div>
        </div>
    </div>

    <div class="welcome-doc">
        <h1>Welcome, Dr. <?= htmlspecialchars($doctor_name); ?></h1>
    </div>

    <div class="your-details">
        <h2>Your Details</h2>
        <p>Email: <?= htmlspecialchars($email); ?></p>
        <p>Phone Number: <?= htmlspecialchars($phone_number); ?></p>
        <p>Address: <?= htmlspecialchars($address); ?></p>
        <p>schedule: <?= htmlspecialchars($schedule); ?></p>
        
    </div>

    <div class="actions">
        <h2>Actions</h2>
        <a href="view_appointments.php"><button class="btn-doc">View Appointments</button></a>
        <a href="../update_info.php"><button class="btn-doc">Update Profile</button></a>
    </div>

    <div class="appointments">
        <h2>Your Upcoming Appointments</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
            <?php if ($appointment_query->num_rows > 0): ?>
                <?php while ($appointment_query->fetch()): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment_date); ?></td>
                        <td><?= htmlspecialchars($appointment_time); ?></td>
                        <td><?= htmlspecialchars($appointment_status); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No upcoming appointments</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

</body>

</html>

<?php
$appointment_query->close();
$conn->close();
?>
