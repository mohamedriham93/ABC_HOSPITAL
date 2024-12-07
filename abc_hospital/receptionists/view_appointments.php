<?php
// Include the database connection file
include('../config.php');

// Fetch all appointments
$query = "SELECT a.id, a.patient_id, a.doctor_id, a.date, a.time, a.status, a.doctor_name, p.first_name AS patient_first_name, p.last_name AS patient_last_name
          FROM appointments a
          JOIN patients p ON a.patient_id = p.patient_id";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link rel="stylesheet" href="../css/Navigation_Style.css?version=1">
    <link rel="stylesheet" href="../css/style.css?version=1">
    <link rel="stylesheet" href="../css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/doctor-images.css?version=1">
    <link rel="stylesheet" href="../css/icons.css?version=1">

    <style>
        /* Add any custom styles here if needed */

        td {
            height:3cap;
        }
    </style>
</head>
<body>
<div class="nav">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="book_appointments.php">Book Appointment</a></li>
            <!-- <li><a href="../logout.php">Logout</a></li> -->
            <li><a href="receptionist_dashboard.php">Dashboard</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

<div class="index_box">
    <h2>View and Edit Appointments</h2>

    <!-- Table to display appointments -->
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop through all appointments
            while ($appointment = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($appointment['patient_first_name'] . " " . $appointment['patient_last_name']) . "</td>";
                echo "<td>" . htmlspecialchars($appointment['doctor_name']) . "</td>";
                echo "<td>" . htmlspecialchars($appointment['date']) . "</td>";
                echo "<td>" . htmlspecialchars($appointment['time']) . "</td>";
                echo "<td>" . htmlspecialchars($appointment['status']) . "</td>";
                echo "<td>
                        <a href='edit_appointment.php?id=" . $appointment['id'] . "'>
                            <div class='container'>
                                <img class='image1' src='../images/icons/Edit.jpg' alt='Edit'>
                                <div class='overlay'>
                                    <div class='text'>Edit</div>
                                </div>
                            </div>
                        </a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
