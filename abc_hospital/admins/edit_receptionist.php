<?php
session_start();
include('../config.php');

// Ensure only Admin can access the page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    // Sanitize the input
    $receptionist_id = intval($_GET['id']);

    // Fetch the current receptionist details securely using prepared statements
    $stmt = $conn->prepare("SELECT * FROM receptionists WHERE receptionist_id = ?");
    $stmt->bind_param("i", $receptionist_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Receptionist not found!";
        $stmt->close();
        $conn->close();
        exit();
    }
    $receptionist = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Invalid Request!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated data from the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $hire_date = $_POST['hire_date'];
    $salary = $_POST['salary'];

    // Update the receptionist details in the database securely using prepared statements
    $stmt = $conn->prepare("
        UPDATE receptionists 
        SET first_name = ?, last_name = ?, gender = ?, email = ?, phone_number = ?, 
            address = ?, hire_date = ?, salary = ?
        WHERE receptionist_id = ?
    ");
    $stmt->bind_param(
        "sssssssdi",
        $first_name,
        $last_name,
        $gender,
        $email,
        $phone_number,
        $address,
        $hire_date,
        $salary,
        $receptionist_id
    );

    if ($stmt->execute()) {
        echo "Record updated successfully!";
        header("Location: manage_receptionists.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Receptionist - ABC Hospital</title>
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
        <h1>Edit Receptionist</h1>
    </header>

    <main>
        <form method="POST" action="">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($receptionist['first_name']); ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($receptionist['last_name']); ?>" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo $receptionist['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $receptionist['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
            </select>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($receptionist['email']); ?>" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($receptionist['phone_number']); ?>" required>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($receptionist['address']); ?></textarea>

            <label for="hire_date">Hire Date:</label>
            <input type="date" id="hire_date" name="hire_date" value="<?php echo htmlspecialchars($receptionist['hire_date']); ?>" required>

            <label for="salary">Salary:</label>
            <input type="number" id="salary" name="salary" value="<?php echo htmlspecialchars($receptionist['salary']); ?>" required>

            <button type="submit">Update</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 ABC Hospital</p>
    </footer>
</body>
</html>
