<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$loginMessage = '';
if (!isset($_SESSION['role'])) {
    $loginMessage = "You are not logged in. Please login to continue.";
} else {
    $role = $_SESSION['role'];
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch user details based on role
$sql = "
    SELECT 
        u.role, 
        p.first_name, p.last_name, p.email, p.phone_number, p.address, p.date_of_birth, p.age, p.gender,
        d.name AS doctor_name, d.salary AS doctor_salary, d.phone_number AS doctor_phone,d.email AS email ,d.address AS doctor_address, d.schedule,
        r.first_name AS receptionist_fname, r.last_name AS receptionist_lname, r.salary AS receptionist_salary
    FROM users u
    LEFT JOIN patients p ON u.patient_id = p.patient_id
    LEFT JOIN doctors d ON u.doctor_id = d.id
    LEFT JOIN receptionists r ON u.receptionist_id = r.receptionist_id
    WHERE u.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Validate image upload
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    // Allow only certain file formats (e.g., jpg, jpeg, png)
    $allowed = array('jpg', 'jpeg', 'png');
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB max file size
                // Read the file content into a variable
                $imageContent = file_get_contents($fileTmpName);

                // Check if the image was read correctly
                if ($imageContent === false) {
                    die("Error: Failed to read the image file.");
                }

                // Check if the user already has an image and delete it
                $deleteSql = "DELETE FROM user_pictures WHERE user_id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param('i', $user_id);
                $deleteStmt->execute();
                $deleteStmt->close();

                // Store the new file in the database as BLOB
                $sql = "INSERT INTO user_pictures (user_id, picture, upload_date) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    die("Error preparing SQL statement: " . $conn->error);
                }

                // Bind the user ID (integer) and the image content (blob)
                $stmt->bind_param('ib', $user_id, $imageContent);

                // Send the binary image data using send_long_data
                $stmt->send_long_data(1, $imageContent);

                if ($stmt->execute()) {
                    echo "Image uploaded and stored in the database successfully!";
                } else {
                    echo "Error executing SQL: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Error: Your file is too large.";
            }
        } else {
            echo "Error: There was an error uploading your file.";
        }
    } else {
        echo "Error: You cannot upload files of this type.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $user['role'];

    // Validate POST data before updating
    if ($role == 'Patient') {
        $first_name = isset($_POST['first_name']) ? htmlspecialchars(trim($_POST['first_name'])) : '';
        $last_name = isset($_POST['last_name']) ? htmlspecialchars(trim($_POST['last_name'])) : '';
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
        $phone_number = isset($_POST['phone_number']) ? htmlspecialchars(trim($_POST['phone_number'])) : '';
        $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';
        $date_of_birth = isset($_POST['date_of_birth']) ? htmlspecialchars(trim($_POST['date_of_birth'])) : '';

        // Update patient details
        $update_sql = "UPDATE patients SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address = ?, date_of_birth = ? WHERE patient_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone_number, $address, $date_of_birth, $user['patient_id']);
    } elseif ($role == 'Doctor') {
        $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
        $phone_number = isset($_POST['phone_number']) ? htmlspecialchars(trim($_POST['phone_number'])) : '';
        $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';
        $schedule = isset($_POST['schedule']) ? htmlspecialchars(trim($_POST['schedule'])) : '';
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';

        // Update doctor details
        $update_sql = "UPDATE doctors SET name = ?, phone_number = ?, address = ?, schedule = ?, email =? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssi", $name, $phone_number, $address, $schedule, $email, $user['doctor_id']);
    } elseif ($role == 'Receptionist') {
        $first_name = isset($_POST['first_name']) ? htmlspecialchars(trim($_POST['first_name'])) : '';
        $last_name = isset($_POST['last_name']) ? htmlspecialchars(trim($_POST['last_name'])) : '';
        $phone_number = isset($_POST['phone_number']) ? htmlspecialchars(trim($_POST['phone_number'])) : '';
        $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';

        // Update receptionist details
        $update_sql = "UPDATE receptionists SET first_name = ?, last_name = ?, phone_number = ?, address = ? WHERE receptionist_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssi", $first_name, $last_name, $phone_number, $address, $user['receptionist_id']);
    }

    if ($update_stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <!-- <link rel="stylesheet" href="./css/style.css"> -->
    <link rel="stylesheet" href="./css/Navigation_Style.css?version = 1   " />
    <link rel="stylesheet" href="./css/style.css?version= 1">
    <link rel="stylesheet" href="./css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/index.css">

    <style>
    body {
            /* background-image: url("./images/img4.jpg"); */
            background-size: cover;
            /* font-family: Arial, sans-serif; */
            color: #fff;
            margin: 0;
            padding: 0;
            /* display: flex; */
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        #imagePreviewContainer {
            display: none;
            margin-top: 20px;

        }
        #imagePreview {
            max-width: 100%;
            max-height: 400px;
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 10px;
        }
        input[type="file"] {
            margin: 10px 0;
        }
        input {
            height: 3cap;
        }

        /* Container */
        .index_box {
            background: rgba(0, 0, 0, 0.6);
            padding: 40px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            /* text-align: center; */
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
        button {
            background-color: #333;
            color: #ccc;
            max-width: 150px;
            height:3cap;
            align-self: center;
            font-size: medium;
            margin-bottom: 2cap;
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
                <li><a href="index.php">Home</a></li>
                <li><a href="news.php">News</a></li>
                <?php if (!isset($role)) { ?>
                    <li><a href="appointments.php">Book Appointment</a></li>
                <?php } ?>

                <!-- <li><a href="#">Gallery</a></li> -->
                <!-- <li><a href="#">Contact Us</a></li> -->
                <li><a href="about-us.php">About Us</a></li>

                <?php if (isset($role)) {
                    switch ($role) {
                        case 'Admin': ?>
                            <li><a href="admins/admin_dashboard.php">Dashboard</a></li>
                            <li><a href="Admins/admin_dashboard.php">Manage Users</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php break;
                        case 'Doctor': ?>
                            <li><a href="doctors/doctor_dashboard.php">Dashboard</a></li>
                            <li><a href="doctors/view_appointments.php">View Appointments</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php break;
                        case 'Receptionist': ?>
                            <li><a href="./receptionists/receptionist_dashboard.php">Dashboard</a></li>
                            <li><a href="./receptionists/view_appointments.php">Manage Appointments</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php break;
                        case 'Patient': ?>
                            <li><a href="specializations.php">Doctor Channeling</a></li>
                            <li><a href="Patients/patient_dashboard.php">Dashboard</a></li>
                            <li><a href="Patients/book_appointments.php">Book Appointment</a></li>
                            <li><a href="logout.php">Logout</a></li>

                    <?php break;
                    }
                } else { ?>
                    <li><a href="specializations.php">Doctor Channeling</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php } ?>
            </ul>
            <div class="header_right">
                <?php if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
                    <?= htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']) ?>
                <?php elseif (isset($_SESSION['role']) && isset($_SESSION['username'])): ?>
                    <?= htmlspecialchars($_SESSION['username']) ?>
                <?php endif; ?>

                <a href="update_info.php"><img class="profile" src="display_image.php" alt="Profile Picture" onerror="this.onerror=null; this.src='images/no-profile-picture-icon.png';"></a>
            </div>
        </div>
    </div>
<div class="index_box">
    <!-- <div class="container"> -->
    <h2>Update Profile</h2>
<?php if (!empty($message)): ?>
    <p class="message"><?= htmlspecialchars($message); ?></p>
<?php endif; ?>
<form action="update_info.php" method="POST">
    <?php if ($user['role'] == 'Patient'): ?>
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? ''); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? ''); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>

        <label for="phone_number">Phone:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number'] ?? ''); ?>" required>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required><?= htmlspecialchars($user['address'] ?? ''); ?></textarea>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth'] ?? ''); ?>" required>
        <button type="submit">Update Profile</button>

    <?php elseif ($user['role'] == 'Doctor'): ?>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['doctor_name'] ?? ''); ?>" required>

        <label for="phone_number">Phone:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['doctor_phone'] ?? ''); ?>" required>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required><?= htmlspecialchars($user['doctor_address'] ?? ''); ?></textarea>

        <label for="schedule">Schedule:</label>
        <textarea id="schedule" name="schedule" required><?= htmlspecialchars($user['schedule'] ?? ''); ?></textarea>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>
        <button type="submit">Update Profile</button>

    <?php elseif ($user['role'] == 'Receptionist'): ?>
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['receptionist_fname'] ?? ''); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['receptionist_lname'] ?? ''); ?>" required>

        <label for="phone_number">Phone:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number'] ?? ''); ?>" required>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required><?= htmlspecialchars($user['address'] ?? ''); ?></textarea>
        <button type="submit">Update Profile</button>
    <?php endif; ?>
</form>

       
    <!-- </div> -->
    

   
    <h2>Upload Your Image</h2>
    <form action="update_info.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" required accept="image/*" onchange="previewImage(event)">
        <div id="imagePreviewContainer">
            <h3>Image Preview:</h3>
            <img id="imagePreview" src="#" alt="Image Preview">
        </div>
        <button type="submit">Upload</button>
    </form>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = document.getElementById('imagePreview');
                    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block'; // Show the preview container
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
     <a href="index.php">Back to Home</a>
     </div>

</body>

</html>