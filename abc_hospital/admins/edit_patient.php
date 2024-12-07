<?php
session_start();
include('../config.php');

// Check if the user is logged in as Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch patient details based on the ID passed through URL
if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];

    // Fetch patient data from the database
    $result = $conn->query("SELECT * FROM patients WHERE patient_id = '$patient_id'");

    // Check if patient exists
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        // If no patient found, redirect to manage patients page
        header("Location: manage_patients.php");
        exit();
    }
} else {
    // If no patient id is provided, redirect to manage patients page
    header("Location: manage_patients.php");
    exit();
}

// Update patient details when form is submitted
if (isset($_POST['update'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];

    // Update query
    $stmt = $conn->prepare("UPDATE patients SET first_name=?, last_name=?, age=?, gender=?, email=?, phone_number=?, address=?, date_of_birth=? WHERE patient_id=?");
    $stmt->bind_param("ssisssssi", $first_name, $last_name, $age, $gender, $email, $phone_number, $address, $date_of_birth, $patient_id);

    if ($stmt->execute()) {
        // Redirect to manage patients page after successful update
        header("Location: manage_patients.php");
        exit();
    } else {
        $error_message = "Error updating patient details: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient - ABC Hospital</title>
    <link rel="stylesheet" href="../css/dashboard_style.css?version=1">
    <link rel="stylesheet" href="../css/form_style.css?version=1">
    <link rel="stylesheet" href="../css/Dashboard_Navigation.css?version=1">
    <style>
        body {
            background-image: url("../images/img8.jpg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 150%;
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

    <header>
        <h1>Edit Patient Information</h1>
    </header>

    <main>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form action="edit_patient.php?id=<?php echo $patient['patient_id']; ?>" method="POST">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($patient['first_name']); ?>" required><br>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($patient['last_name']); ?>" required><br>

            <label for="age">Age:</label>
            <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($patient['age']); ?>" required><br>

            <label for="gender">Gender:</label>
            <select name="gender" id="gender" required>
                <option value="Male" <?php echo $patient['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $patient['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
            </select><br>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($patient['email']); ?>" required><br>

            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($patient['phone_number']); ?>" required><br>

            <label for="address">Address:</label>
            <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($patient['address']); ?>" required><br>

            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo htmlspecialchars($patient['date_of_birth']); ?>" required><br>

            <button type="submit" name="update">Update Patient</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 ABC Hospital</p>
    </footer>
</body>
</html>
