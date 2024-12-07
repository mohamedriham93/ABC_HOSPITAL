<?php
// Start the session
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'abc_hospital';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must log in to access this page.");
}

$user_id = $_SESSION['user_id'];

// Get the patient_id using the user_id
$stmt = $conn->prepare("SELECT patient_id FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || !$user['patient_id']) {
    die("No patient associated with this user.");
}

$patient_id = $user['patient_id'];

// Fetch patient details
$stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

if (!$patient) {
    die("Patient not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    // Validate and sanitize input
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $age = intval($_POST['age']);
    $gender = htmlspecialchars(trim($_POST['gender']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $address = htmlspecialchars(trim($_POST['address']));
    $date_of_birth = $_POST['date_of_birth'];

    if (!$email) {
        die("Invalid email format.");
    }

    // Update patient data
    $stmt = $conn->prepare("UPDATE patients SET first_name = ?, last_name = ?, age = ?, gender = ?, email = ?, phone_number = ?, address = ?, date_of_birth = ? WHERE patient_id = ?");
    $stmt->bind_param("ssisssssi", $first_name, $last_name, $age, $gender, $email, $phone_number, $address, $date_of_birth, $patient_id);

    if ($stmt->execute()) {
        echo "Patient information updated successfully!";
        // Refresh patient details
        $patient['first_name'] = $first_name;
        $patient['last_name'] = $last_name;
        $patient['age'] = $age;
        $patient['gender'] = $gender;
        $patient['email'] = $email;
        $patient['phone_number'] = $phone_number;
        $patient['address'] = $address;
        $patient['date_of_birth'] = $date_of_birth;
    } else {
        echo "Error updating patient information: " . $stmt->error;
    }

    $stmt->close();
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Patient Information</title>

    <link rel="stylesheet" href="../css/Navigation_Style.css?version = 1   " />
    <link rel="stylesheet" href="../css/style.css?version= 1">
    <!-- <link rel="stylesheet" href="../css/header.css?version=1"> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="../css/index.css">
    <style>
        /* General page styles */
body {
    /* font-family: Arial, sans-serif; */
    /* margin: 0; */
    /* padding: 0; */
    background: linear-gradient(120deg, #a833c0, #161818);

    color: #333;
    /* min-height: 100vh; */
    /* display: flex; */
    justify-content: center;
    align-items: center;
    background-size: cover;
}

/* Page container */
.container {
    margin-top: 200px;
    max-width: 700px;
    width: 500px;
    background: #fff;
    border-radius: 10px;
    padding: 20px 30px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    animation: fadeIn 1s ease-in-out;
    margin-bottom: 100px;
}

/* Page heading */
h1 {
    text-align: center;
    color: #0056b3;
    margin-bottom: 20px;
}

/* Form labels */
form label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
    color: #555;
}

/* Input fields and select */
form input[type="text"],
form input[type="number"],
form input[type="email"],
form input[type="date"],
form select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 16px;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

/* Input fields on focus */
form input[type="text"]:focus,
form input[type="number"]:focus,
form input[type="email"]:focus,
form input[type="date"]:focus,
form select:focus {
    border-color: #0056b3;
    background-color: #fff;
    outline: none;
    transform: scale(1.02);
}

/* Submit button */
form button {
    background: linear-gradient(120deg, #0056b3, #004494);
    color: #fff;
    padding: 12px 20px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s ease;
}

/* Submit button hover effect */
form button:hover {
    background: linear-gradient(120deg, #004494, #003d7a);
    transform: scale(1.05);
}

/* Success or error messages */
.success-message,
.error-message {
    text-align: center;
    margin: 10px auto;
    padding: 10px;
    border-radius: 6px;
    font-weight: bold;
    max-width: 600px;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
}

/* Fade-in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
 
}

    </style>
</head>
<body>
<div class="header">
        <div class="nav">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="book_appointments.php">Book Appointment</a></li>
                <li><a href="../logout.php">Logout</a></li>
                <li><a href="view_appointments.php">View Appointments</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="header_right">
            <?php if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
                <?= htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']) ?>
            <?php elseif (isset($_SESSION['role']) && isset($_SESSION['username'])): ?>
                <?= htmlspecialchars($_SESSION['username']) ?>
            <?php endif; ?>

            <a href="upload_image.php"><img class="profile" src="../display_image.php" alt="Profile Picture" onerror="this.onerror=null; this.src='../images/no-profile-picture-icon.png';"></a>
        </div>
    </div>
    
    <div class="container">
    <h1>Update Patient Information</h1>

    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($patient['first_name']) ?>" required><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($patient['last_name']) ?>" required><br>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" value="<?= htmlspecialchars($patient['age']) ?>" required><br>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male" <?= $patient['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $patient['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= $patient['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
        </select><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($patient['email']) ?>" required><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($patient['phone_number']) ?>" required><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?= htmlspecialchars($patient['address']) ?>" required><br>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($patient['date_of_birth']) ?>" required><br>

        <button type="submit">Update</button>
    </form>
    </div>
</body>
</html>
