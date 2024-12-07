<?php
session_start();
include('config.php');

// Ensure the user is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the patient_id
$patient_query = $mysqli->prepare("SELECT patient_id FROM users WHERE user_id = ?");
$patient_query->bind_param("i", $user_id);
$patient_query->execute();
$patient_result = $patient_query->get_result();
$patient_row = $patient_result->fetch_assoc();
$patient_id = $patient_row['patient_id'];

if (!$patient_id) {
    echo "Error: No patient record found for this user.";
    exit();
}

// Fetch specializations
$specializations_query = "SELECT DISTINCT specialization_name FROM specialization";
$specializations_result = $mysqli->query($specializations_query);

// Check if doctor_id is passed via URL
$doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : null;
$doctor_name = '';
if ($doctor_id) {
    // Fetch the doctor's name if doctor_id is set
    $doctor_name_query = $mysqli->prepare("SELECT name FROM doctors WHERE id = ?");
    $doctor_name_query->bind_param("i", $doctor_id);
    $doctor_name_query->execute();
    $doctor_name_result = $doctor_name_query->get_result();
    $doctor_row = $doctor_name_result->fetch_assoc();
    $doctor_name = $doctor_row['name'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form when submitted
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Get the doctor's name
    $doctor_name_query = $mysqli->prepare("SELECT name FROM doctors WHERE id = ?");
    $doctor_name_query->bind_param("i", $doctor_id);
    $doctor_name_query->execute();
    $doctor_name_result = $doctor_name_query->get_result();
    $doctor_row = $doctor_name_result->fetch_assoc();
    $doctor_name = $doctor_row['name'];

    // Insert the appointment into the database
    $stmt = $mysqli->prepare("INSERT INTO appointments (patient_id, doctor_id, date, time, doctor_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $doctor_name);

    if ($stmt->execute()) {
        echo "Appointment booked successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="../css/Navigation_Style.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/index.css">

    <style>
        /* Global Styles */
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
            gap: 20px;
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
    <div class="header">
        <div class="nav">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="#">News</a></li>
               

                <!-- <li><a href="#">Gallery</a></li> -->
                <li><a href="#">Contact Us</a></li>
                <li><a href="../about-us.php">About Us</a></li>
                <li><a href="../specializations.php">Doctor Channeling</a></li>
                <li><a href="patient_dashboard.php">Patient Dashboard</a></li>
                <li><a href="book_appointments.php">Book Appointment</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
            <div class="header_right">
                <?php if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
                    <?= htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']) ?>
                <?php elseif (isset($_SESSION['role']) && isset($_SESSION['username'])): ?>
                    <?= htmlspecialchars($_SESSION['username']) ?>
                <?php endif; ?>

                <a href="..update_info.php"><img class="profile" src="display_image.php" alt="Profile Picture" onerror="this.onerror=null; this.src='../images/no-profile-picture-icon.png';"></a>
            </div>
        </div>
    </div>

    <div class="index_box">
        <h2>Book an Appointment</h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="specialization">Choose a Specialization:</label>
            <select id="specialization" name="specialization" required onchange="fetchDoctors()">
                <option value="">Select Specialization</option>
                <?php
                while ($row = $specializations_result->fetch_assoc()) {
                    echo "<option value='" . $row['specialization_name'] . "'>" . $row['specialization_name'] . "</option>";
                }
                ?>
            </select>

            <label for="doctor_id">Choose a Doctor:</label>
            <select id="doctor_id" name="doctor_id" required onchange="showDoctorSchedule()">
                <option value="">Select Doctor</option>
            </select>

            <div class="schedule-info" id="doctor-schedule">
                Please select a doctor to view their schedule.
            </div>

            <label for="appointment_date">Appointment Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" required>

            <label for="appointment_time">Appointment Time:</label>
            <input type="time" id="appointment_time" name="appointment_time" required>

            <input type="submit" value="Book Appointment">
        </form>
    </div>
    <script>
        // Fetch doctors based on specialization
        function fetchDoctors() {
            const specialization = document.getElementById('specialization').value;
            const doctorSelect = document.getElementById('doctor_id');
            const scheduleInfo = document.getElementById('doctor-schedule');

            if (specialization) {
                fetch(`fetch_doctors.php?specialization=${specialization}`)
                    .then(response => response.json())
                    .then(data => {
                        doctorSelect.innerHTML = "<option value=''>Select Doctor</option>";
                        data.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.id;
                            option.textContent = `${doctor.name} - ${doctor.specialization_name}`;
                            doctorSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching doctors:', error));
            } else {
                doctorSelect.innerHTML = "<option value=''>Select Doctor</option>";
                scheduleInfo.innerHTML = "Please select a doctor to view their schedule.";
            }
        }

        // Fetch and display the selected doctor's schedule
        function showDoctorSchedule() {
            const doctorId = document.getElementById('doctor_id').value;
            const scheduleInfo = document.getElementById('doctor-schedule');

            if (doctorId) {
                fetch(`fetch_schedule.php?doctor_id=${doctorId}`)
                    .then(response => response.text())
                    .then(data => {
                        scheduleInfo.innerHTML = "<strong>Schedule:</strong> " + data;
                    })
                    .catch(error => console.error('Error fetching schedule:', error));
            } else {
                scheduleInfo.innerHTML = "Please select a doctor to view their schedule.";
            }
        }
    </script>
</body>

</html>