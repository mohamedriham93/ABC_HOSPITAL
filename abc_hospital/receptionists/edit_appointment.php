<?php
// Secure and improved appointment editing page

// Check if the appointment ID is set and is a valid integer
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo "Invalid appointment ID.";
    exit;
}

$appointment_id = intval($_GET['id']);

// Database connection
$connection = mysqli_connect("localhost", "root", "", "abc_hospital");

// Check connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Use prepared statements to prevent SQL injection
$query = "SELECT appointments.*, patients.first_name, patients.last_name
          FROM appointments
          INNER JOIN patients ON appointments.patient_id = patients.patient_id
          WHERE appointments.id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $appointment_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$appointment = mysqli_fetch_assoc($result);

// Check if the appointment exists
if (!$appointment) {
    echo "Appointment not found.";
    exit;
}

$patient_first_name = htmlspecialchars($appointment['first_name']);
$patient_last_name = htmlspecialchars($appointment['last_name']);
$doctor_name = htmlspecialchars($appointment['doctor_name']);
$appointment_date = htmlspecialchars($appointment['date']);
$appointment_time = htmlspecialchars($appointment['time']);
$status = htmlspecialchars($appointment['status']);

mysqli_close($connection);
?>

<!-- Displaying the Appointment Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="../css/Navigation_Style.css?version=1">
    <link rel="stylesheet" href="../css/style.css?version=1">
    <link rel="stylesheet" href="../css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/doctor-images.css?version=1">
    <link rel="stylesheet" href="../css/icons.css?version=1">

    <style>
        body {
            background-image: url("../images/img4.jpg");
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
            /* display: flex; */
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        input:disabled {
            padding-top: 0.8cap;
            font-size: large;

            
        }
 


        /* Container */
        .index_box {
            background: rgba(0, 0, 0, 0.6);
            padding: 40px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #fff;
            text-transform: uppercase;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            font-size: 1.1rem;
            color: #fff;
            margin-bottom: 8px;
        }

        select,
        input[type="date"],
        input[type="time"] {
            padding: 12px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
            color: #333;
            transition: border-color 0.3s;
        }

        select:focus,
        input[type="date"]:focus,
        input[type="time"]:focus {
            border-color: #3498db;
            outline: none;
        }

        input[type="submit"] {
            padding: 12px 20px;
            font-size: 1.2rem;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        /* Doctor Schedule Section */
        .schedule-info {
            padding: 15px;
            background-color: #f2f2f2;
            border-radius: 8px;
            color: #333;
            text-align: center;
            font-size: 1rem;
        }

        .schedule-info strong {
            font-weight: bold;
        }

        /* Select and Input Hover Effect */
        select:hover,
        input[type="date"]:hover,
        input[type="time"]:hover {
            border-color: #3498db;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .index_box {
                width: 90%;
                padding: 30px;
            }

            h2 {
                font-size: 1.5rem;
            }

            label {
                font-size: 1rem;
            }

            select,
            input[type="date"],
            input[type="time"],
            input[type="submit"] {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<div class="nav">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="receptionist_dashboard.php">Receptionist Dashboard</a></li>
            <li><a href="book_appointments.php">Book Appointments</a></li>
            
            <!-- <li><a href="view_appointments.php">View Appointments</a></li> -->
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

        <div class="index_box">
    <h2>Edit Appointment</h2>
    <form action="edit_appointment_process.php" method="POST">
        <label>Patient Name:</label>
        <input type="text" value="<?php echo $patient_first_name . ' ' . $patient_last_name; ?>" disabled>

        <label>Doctor Name:</label>
        <input type="text" value="<?php echo $doctor_name; ?>" disabled>

        <label>Appointment Date:</label>
        <input type="date" name="appointment_date" value="<?php echo $appointment_date; ?>" required>

        <label>Appointment Time:</label>
        <input type="time" name="appointment_time" value="<?php echo $appointment_time; ?>" required>

        <label>Status:</label>
        <select name="status" required>
            <option value="Pending" <?php if ($status == 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Confirmed" <?php if ($status == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
            <option value="Cancelled" <?php if ($status == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
            <option value="Completed" <?php if ($status == 'Completed') echo 'selected'; ?>>Completed</option>
        </select>

        <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
        <input type="submit" value="Save Changes">
    </form>
    </div>
</body>
</html>
