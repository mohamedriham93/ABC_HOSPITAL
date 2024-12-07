<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../login.php");
    exit();
}

include('../config.php'); 

// Get the logged-in user's user_id
$user_id = $_SESSION['user_id'];

// Retrieve the patient_id from the users table
$patient_query = "SELECT patient_id FROM users WHERE user_id = ?";
$patient_stmt = $conn->prepare($patient_query);

if (!$patient_stmt) {
    die("SQL Error: " . $conn->error);
}

$patient_stmt->bind_param("i", $user_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();

if ($patient_result->num_rows === 0) {
    die("Patient not found.");
}

$patient_row = $patient_result->fetch_assoc();
$patient_id = $patient_row['patient_id'];

// Now use the patient_id to retrieve appointments
$query = "SELECT date, time, doctor_name, status FROM appointments WHERE patient_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointments</title>
    <link rel="stylesheet" href="../css/form_style.css">
    <link rel="stylesheet" href="../css/style.css?version= 1">
    <link rel="stylesheet" href="../css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/Navigation_Style.css">

    <style>

        .index_box {
            text-align: center;
        }

        button {
            color:black;
            background-color: aqua;
        }
    </style>
</head>
<body>
<div class="nav">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="book_appointments.php">Book Appointment</a></li>
            <li><a href="patient_dashboard.php">Dashboard</a></li>
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
    
<div class="index_box">
<h2>Your Appointments</h2>



<table>
    <tr>
        <th>Date</th>
        <th>Time</th>
        <th>Doctor</th>
        <th>Status</th>
    </tr>

    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['time'] . "</td>";
        echo "<td>" . $row['doctor_name'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    ?>

</table>
</div>

<a href="patient_dashboard.php"><button>Back to Dashboard</button></a>
<br>
<br>
<a class="right" href="book_appointments.php"><button>Book Appointment</button></a>

</body>
</html>

<?php
$stmt->close();
$patient_stmt->close();
$conn->close();
?>
