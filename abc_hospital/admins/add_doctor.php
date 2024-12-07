<?php
session_start();
include('../config.php');

// Restrict access to admin users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $salary = $_POST['salary'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $schedule = ($_POST['schedule'] === 'Custom') ? $_POST['custom_schedule'] : $_POST['schedule'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $specialization_name = ($_POST['specialization'] === 'Custom-sp') ? $_POST['custom_specialization'] : $_POST['specialization'];
    $experience = $_POST['experience'];

    // Hash the password for secure storage
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Start the database transaction
    $conn->begin_transaction();

    try {
        // Insert data into the `doctors` table
        $stmt = $conn->prepare("INSERT INTO doctors (name, email, gender, age, salary, phone_number, address, schedule) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssidsis", $name, $email, $gender, $age, $salary, $phone_number, $address, $schedule);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting into doctors table: " . $stmt->error);
        }

        // Get the ID of the inserted doctor
        $doctor_id = $stmt->insert_id;

        // Insert data into the `users` table
        $stmt_user = $conn->prepare("INSERT INTO users (username, password, role, doctor_id) VALUES (?, ?, 'Doctor', ?)");
        $stmt_user->bind_param("ssi", $username, $hashed_password, $doctor_id);

        if (!$stmt_user->execute()) {
            throw new Exception("Error inserting into users table: " . $stmt_user->error);
        }

        // Insert data into the `specialization` table
        $stmt_specialization = $conn->prepare("INSERT INTO specialization (doctor_id, specialization_name, experience) VALUES (?, ?, ?)");
        $stmt_specialization->bind_param("isi", $doctor_id, $specialization_name, $experience);

        if (!$stmt_specialization->execute()) {
            throw new Exception("Error inserting into specialization table: " . $stmt_specialization->error);
        }

        // Commit the transaction
        $conn->commit();

        // Redirect to the manage doctors page
        header("Location: manage_doctors.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "Transaction failed: " . $e->getMessage();
    }

    // Close the prepared statements
    $stmt->close();
    $stmt_user->close();
    $stmt_specialization->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Doctor - ABC Hospital</title>
    <link rel="stylesheet" href="../css/Navigation_Style.css?version=1">
    <!-- <link rel="stylesheet" href="../css/style.css?version=1"> -->
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
         input {
            padding: 1cap;
            font-size: 1cap
         }
         
         button {
            background-color: #333;
            color: #ddd;
         }




        /* Container */
        .index_box {
            background: rgba(0, 0, 0, 0.6);
            padding: 40px;
            border-radius: 10px;
            width: 80%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .index_box p a {
            background-color: #ddd;
            padding: 1cap;
            margin-top: 1cap;
            text-decoration: none;
            color: #333;
        }

        :hover.p-box> a {
            background-color: #333;
            color: #ddd;
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
        .p-box {
            padding: 1cap;
            background-color: aquamarine;
            margin-top: 2cap;
            margin: 2cap;
            text-decoration: none;
            color: black;
            text-align: center;
            padding-right: 1cap;
        }

        :hover.p-box {
            background-color: black;
            color: antiquewhite;
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
            <li><a href="appointments.php">View Appointments</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>
 <div class="index_box">

     
        <h2>Add Doctor</h2>


    <main>
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>

            <label for="salary">Salary:</label>
            <input type="number" id="salary" name="salary" step="0.01" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required></textarea>

            <label for="schedule">Schedule:</label>
            <select id="schedule" name="schedule" required>
                <option value="">--Select Schedule--</option>
                <option value="Monday to Friday: 9 AM - 5 PM">Monday to Friday: 9 AM - 5 PM</option>
                <option value="Monday to Saturday: 10 AM - 4 PM">Monday to Saturday: 10 AM - 4 PM</option>
                <option value="Monday to Wednesday: 11 AM - 7 PM">Monday to Wednesday: 11 AM - 7 PM</option>
                <option value="Custom">Custom Schedule</option>
            </select>
            <input type="text" id="custom-schedule" name="custom_schedule" placeholder="Enter Custom Schedule" style="display:none;">
            <label for="specialization">Specialization:</label>
            <select id="specialization" name="specialization" required>
                <option value="">--Select Specialization--</option>
                <option value="Cardiologist">Cardiologist</option>
                <option value="Dermatologist">Dermatologist</option>
                <option value="Pediatrician">Pediatrician</option>
                <option value="Neurologist">Neurologist</option>
                <option value="Custom-sp">Custom Specialization</option>
            </select>
            <input type="text" id="custom-specialization" name="custom_specialization" placeholder="Enter Custom Specialization" style="display:none;">

            <label for="experience">Experience (years):</label>
            <input type="number" id="experience" name="experience" required>


            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>


            <button type="submit">Add Doctor</button>
            <a class="p-box" href="manage_doctors.php">Back to Manage Doctors</a>
        </form>
        
    </main>
    </div>
    <script>
        // Handle custom schedule input visibility
        document.getElementById('schedule').addEventListener('change', function() {
            document.getElementById('custom-schedule').style.display = (this.value === 'Custom') ? 'block' : 'none';
        });

        // Handle custom specialization input visibility
        document.getElementById('specialization').addEventListener('change', function() {
            document.getElementById('custom-specialization').style.display = (this.value === 'Custom-sp') ? 'block' : 'none';
        });
    </script>

    <footer>
        <p>&copy; 2024 ABC Hospital</p>
    </footer>
</body>

</html>