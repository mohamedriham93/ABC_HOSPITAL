<?php
// Include the database connection file
include('../config.php');

// Fetch all doctors
$doctor_query = "SELECT id, name FROM doctors";
$doctor_result = mysqli_query($conn, $doctor_query);

// Fetch all patients
$patient_query = "SELECT patient_id, first_name, last_name FROM patients";
$patient_result = mysqli_query($conn, $patient_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Dashboard - Book Appointments</title>
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
            
            <li><a href="view_appointments.php">View Appointments</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="index_box">
        <h2>Book Appointment</h2>

        <!-- Appointment Booking Form -->
        <form action="book_appointment.php" method="POST">
            <!-- Dropdown for Doctors -->
            <label for="doctor">Select Doctor:</label>
            <select name="doctor_id" id="doctor_id" onchange="showDoctorSchedule()" required>
                <option value="">-- Select Doctor --</option>
                <?php
                while ($doctor = mysqli_fetch_assoc($doctor_result)) {
                    echo "<option value='" . $doctor['id'] . "'>" . $doctor['name'] . "</option>";
                }
                ?>
            </select><br><br>
            <div class="schedule-info" id="doctor-schedule">
                Please select a doctor to view their schedule.
            </div>

            <!-- Dropdown for Patients -->
            <label for="patient">Select Patient:</label>
            <select name="patient_id" required>
                <option value="">-- Select Patient --</option>
                <?php
                while ($patient = mysqli_fetch_assoc($patient_result)) {
                    echo "<option value='" . $patient['patient_id'] . "'>" . $patient['first_name'] . " " . $patient['last_name'] . "</option>";
                }
                ?>
            </select><br><br>

            <!-- Doctor's Schedule Info -->
            

            <!-- Appointment Date and Time -->
            <label for="date">Appointment Date:</label>
            <input type="date" name="date" required><br><br>

            <label for="time">Appointment Time:</label>
            <input type="time" name="time" required><br><br>

            <!-- Submit Button -->
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
        // Fetch and display the selected doctor's schedule
        function showDoctorSchedule() {
            const doctorId = document.getElementById('doctor_id').value;
            const scheduleInfo = document.getElementById('doctor-schedule');

            if (doctorId) {
                fetch(`fetch_schedule.php?doctor_id=${doctorId}`)
                    .then(response => response.text())
                    .then(data => {
                        scheduleInfo.innerHTML = `<strong>Schedule:</strong> ${data}`;
                    })
                    .catch(error => console.error('Error fetching schedule:', error));
            } else {
                scheduleInfo.innerHTML = "Please select a doctor to view their schedule.";
            }
        }
    </script>
</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>