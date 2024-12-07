<?php
session_start();
include('../config.php'); // This file should contain the $conn (database connection)

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$first_name = "";
$last_name = "";
$age = 0;
$gender = "";
$email = "";
$phone_number = "";
$address = "";
$date_of_birth = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth']; // This should be in YYYY-MM-DD format
    $role = 'Patient';

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO patients (first_name, last_name, age, gender, email, phone_number, address, date_of_birth) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssss", $first_name, $last_name, $age, $gender, $email, $phone_number, $address, $date_of_birth);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert into patients table");
        }

        $patient_id = $stmt->insert_id;

        $insert_user_stmt = $conn->prepare("INSERT INTO users (username, password, role, patient_id) VALUES (?, ?, ?, ?)");
        $insert_user_stmt->bind_param("sssi", $username, $hashed_password, $role, $patient_id);

        if (!$insert_user_stmt->execute()) {
            throw new Exception("Failed to insert into users table");
        }

        $conn->commit();
        echo"";
        echo"";
        echo"";
        echo"";
        echo"";
        echo"";
        echo"";
        echo "Data inserted successfully into both tables.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to insert: " . $e->getMessage();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <link rel="stylesheet" href="../css/dashboard_style.css?version=1">
    <link rel="stylesheet" href="../css/form_style.css?version=1">
    <link rel="stylesheet" href="../css/Dashboard_Navigation.css?version=1">
    <style>
        body {
            background-image: url("../images/img8.jpg");
        }

        button {
            background-color: aquamarine;
        }

        button:hover {
            background-color: black;
            color: white;
        }
    </style>


</head>

<body>
    <div class="nav">
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_patients.php">Manage Patients</a></li>
            <li><a href="manage_doctors.php">Manage Doctors</a></li>
            <li><a href="manage_receptionists.php">Manage Receptionists</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <h2>Add Patient</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" id="date_of_birth" name="date_of_birth" required>

        <!-- <input type="submit" value="Register Patient"> -->
        <br>
        <br>
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required><br><br>

        <input type="submit" value="Add">

        <!-- <p>Already have an Account ?</p> -->
        <!-- <a href="../login.php">Login</a> -->
    </form>

</body>

</html>