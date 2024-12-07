<?php
session_start();
include('../config.php');

// Check if the user is an Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Add new receptionist
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $hire_date = $_POST['hire_date'];
    $salary = $_POST['salary'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Start the transaction
    $conn->begin_transaction();

    try {
        // Insert into the receptionists table
        $stmt = $conn->prepare("INSERT INTO receptionists (first_name, last_name, gender, email, phone_number, address, hire_date, salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $first_name, $last_name, $gender, $email, $phone_number, $address, $hire_date, $salary);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert into receptionists table: " . $stmt->error);
        }

        // Get the last inserted receptionist ID
        $receptionist_id = $stmt->insert_id;

        // Insert username and password into the users table
        $stmt_user = $conn->prepare("INSERT INTO users (username, password, role, receptionist_id) VALUES (?, ?, 'Receptionist', ?)");
        $stmt_user->bind_param("ssi", $username, $hashed_password, $receptionist_id);

        if (!$stmt_user->execute()) {
            throw new Exception("Failed to insert into users table: " . $stmt_user->error);
        }

        // If both inserts are successful, commit the transaction
        $conn->commit();

        // Redirect to manage receptionists page
        header("Location: manage_receptionists.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback the transaction if any query fails
        $conn->rollback();
        echo "Transaction failed: " . $e->getMessage();
    }

    // Close the statements
    $stmt->close();
    $stmt_user->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Receptionist - ABC Hospital</title>
    <link rel="stylesheet" href="../css/dashboard_style.css?version=1">
    <link rel="stylesheet" href="../css/Dashboard_Navigation.css?version=1">
    <link rel="stylesheet" href="../css/form_style.css?version=1">
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
        <li><a href="../index.php">Home</a></li>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="manage_patients.php">Manage Patients</a></li>
        <li><a href="manage_doctors.php">Manage Doctors</a></li>
        <li><a href="manage_receptionists.php">Manage Receptionists</a></li>
        <li><a href="appointments.php">View Appointments</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<header>
    <br><br><br><br>
    <h1>Add Receptionist</h1>
</header>

<main>
    <form method="POST" action="">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>

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
        <textarea id="address" name="address" required></textarea>

        <label for="hire_date">Hire Date:</label>
        <input type="date" id="hire_date" name="hire_date" required>

        <label for="salary">Salary:</label>
        <input type="number" id="salary" name="salary" step="0.01" required>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Add Receptionist</button>
    </form>
    <p><a href="manage_receptionists.php">Back to Manage Receptionists</a></p>
</main>

<footer>
    <p>&copy; 2024 ABC Hospital</p>
</footer>
</body>
</html>
