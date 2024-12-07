<?php
session_start();
include('../config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Get doctor ID from URL
if (isset($_GET['id'])) {
    $doctor_id = $_GET['id'];
    // Fetch doctor details
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();
    $stmt->close();
}

// Fetch specialization details for this doctor
$stmt2 = $conn->prepare("SELECT * FROM specialization WHERE doctor_id = ?");
$stmt2->bind_param("i", $doctor_id);
$stmt2->execute();
$specialization_result = $stmt2->get_result();
$specialization = $specialization_result->fetch_assoc();
$stmt2->close();

// Update doctor data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $schedule = $_POST['schedule'];
    $specialization_name = $_POST['specialization_name'];
    $experience = $_POST['experience'];

    // Update doctor details
    $stmt3 = $conn->prepare("UPDATE doctors SET name=?, email=?, phone_number=?, address=?, schedule=? WHERE id=?");
    $stmt3->bind_param("sssssi", $name, $email, $phone_number, $address, $schedule, $doctor_id);

    if ($stmt3->execute()) {
        // Update specialization details
        $stmt4 = $conn->prepare("UPDATE specialization SET specialization_name=?, experience=? WHERE doctor_id=?");
        $stmt4->bind_param("ssi", $specialization_name, $experience, $doctor_id);
        $stmt4->execute();
        
        header("Location: manage_doctors.php"); // Redirect to manage doctors page
        exit();
    } else {
        echo "Error: " . $stmt3->error;
    }

    $stmt3->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor - ABC Hospital</title>
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
            padding: 1.5cap;
            font-size: 1.5cap
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
            max-width: 600px;
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

    <div class="index_box">


        <h2>Edit Doctor</h2>
   

    <main>
        <form method="POST" action="">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($doctor['phone_number']); ?>" required>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($doctor['address']); ?></textarea>

            <label for="schedule">Schedule:</label>
            <input type="text" id="schedule" name="schedule" value="<?php echo htmlspecialchars($doctor['schedule']); ?>" required>

            <label for="specialization_name">Specialization:</label>
            <input type="text" id="specialization_name" name="specialization_name" value="<?php echo htmlspecialchars($specialization['specialization_name']); ?>" required>

            <label for="experience">Experience (years):</label>
            <input type="number" id="experience" name="experience" value="<?php echo htmlspecialchars($specialization['experience']); ?>" required>

            <button type="submit">Update Doctor</button>
        </form>
        <p class="p-box"><a href="manage_doctors.php">Back to Manage Doctors</a></p>
    </main>
    </div>

    <footer>
        <p>&copy; 2024 ABC Hospital</p>
    </footer>
</body>
</html>
