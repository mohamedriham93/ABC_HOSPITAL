<?php

session_start();

$loginMessage = '';
if (!isset($_SESSION['role'])) {
    $loginMessage = "You are not logged in. Please login to continue.";
} else {
    $role = $_SESSION['role'];
}


include 'config.php';

// Query to fetch distinct specializations
$query = "SELECT DISTINCT specialization_name FROM specialization ORDER BY specialization_name";
$result = mysqli_query($conn, $query);

$specializations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $specializations[] = $row['specialization_name'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/Navigation_Style.css?version=1">
    <link rel="stylesheet" href="./css/style.css?version=1">
    <link rel="stylesheet" href="./css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/index.css">


    <link rel="stylesheet" href="./css/Navigation_Style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            /* padding: 20px; */

        }
        .index_box{
            max-width: 1100px;
        }
        

        .specialization {
            margin: 10%;
            text-align: center;
        }

        .specialization-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .specialization-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .specialization-box h3 {
            margin: 0 0 10px;
            color: #007bff;
        }

        .specialization-box button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .specialization-box button:hover {
            background-color: #0056b3;
        }
    </style>
    <title>Specializations</title>
</head>

<body>
    <div class="header">
        <div class="nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">News</a></li>
                <?php if (isset($role)) {
                } else { ?>
                    <li><a href="appointments.php">Book Appointment</a></li>
                <?php } ?>
                <li><a href="#">Gallery</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">About Us</a></li>
            </ul>
            <div class="header_right">
                <?php if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
                    <?= htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']) ?>
                <?php elseif (isset($_SESSION['role']) && isset($_SESSION['username'])): ?>
                    <?= htmlspecialchars($_SESSION['username']) ?>
                <?php endif; ?>
                <a href="login.php"><img class="profile" src="display_image.php" alt="Profile Picture" onerror="this.onerror=null; this.src='images/no-profile-picture-icon.png';"></a>
            </div>
        </div>
    </div>
    <div class="index_box">
        <div class="specialization">
            <h2>Available Specializations</h2>
            <div class="specialization-container">
                <?php foreach ($specializations as $specialization): ?>
                    <div class="specialization-box">
                        <h3><?php echo htmlspecialchars($specialization); ?></h3>
                        <form action="doctors.php" method="GET">
                            <input type="hidden" name="specialization" value="<?php echo htmlspecialchars($specialization); ?>">
                            <button type="submit">View Doctors</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>