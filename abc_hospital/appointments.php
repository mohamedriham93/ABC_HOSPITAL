<?php
include 'config.php';

// Query to fetch limited doctor details along with their specializations
$query = "
    SELECT d.id AS doctor_id, d.name AS doctor_name, s.specialization_name, s.experience 
    FROM doctors d
    JOIN specialization s ON d.id = s.doctor_id
    ORDER BY d.id, s.experience DESC
    LIMIT 5 -- Adjust this limit as needed
";
$result = mysqli_query($conn, $query);

// Initialize an array to group specializations by doctor
$doctors = [];
while ($row = mysqli_fetch_assoc($result)) {
    $doctor_id = $row['doctor_id'];
    if (!isset($doctors[$doctor_id])) {
        $doctors[$doctor_id] = [
            'name' => $row['doctor_name'],
            'specializations' => []
        ];
    }
    $doctors[$doctor_id]['specializations'][] = [
        'name' => $row['specialization_name'],
        'experience' => $row['experience']
    ];
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
    <style>
        .index_box {
            text-align: center;
        }

        .index_box > h3 {
            color: aliceblue;
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 80%;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: rgb(0, 0, 0);
        }
        .list {
            background-color: cadetblue;
            color: black;
        }
        :hover.list {
            color: beige;
            background-color: rgb(0, 0, 0,0.1);
        }
    </style>
    <title>Book Appointment</title>
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

    <header>
        <div class="index_box">
            <h2>Book an Appointment</h2>
            <h3>Available Doctors</h3>
            <table>
                <tr>
                    <th>Doctor Name</th>
                    <th>Specialization & (Experience)</th>
                </tr>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td rowspan="<?= count($doctor['specializations']) ?>"><?= htmlspecialchars($doctor['name']) ?></td>
                        <td><?= htmlspecialchars($doctor['specializations'][0]['name']) ?> (<?= htmlspecialchars($doctor['specializations'][0]['experience']) ?> years)</td>
                    </tr>
                    <?php for ($i = 1; $i < count($doctor['specializations']); $i++): ?>
                        <tr>
                            <td><?= htmlspecialchars($doctor['specializations'][$i]['name']) ?> (<?= htmlspecialchars($doctor['specializations'][$i]['experience']) ?> years)</td>
                        </tr>
                    <?php endfor; ?>
                <?php endforeach; ?>
            </table>

            <!-- Link to Full List -->
            <div style="margin-top: 20px;">
                <a href="specializations.php"><button class="list">View Full List of Specializations</button></a>
            </div>

            <h3>Login First</h3>
            <div class="appointment_login">
                <a href="login.php"><button type="login">Login</button></a>
                <a href="Patients/Patient_Registration.php"><button type="signup">Register</button></a>
            </div>
        </div>
    </header>

    <main>
        <h3 style="text-align:center; margin-top:20px;">Your Health Matters</h3>
        <p style="text-align:center; font-size:1.1rem;">Book your appointment with our expert doctors today!</p>
    </main>
    <br>
    <br>
    <br>

    <div class="footer">
        <p>&copy; 2024 ABC Hospital</p>
    </div>
</body>

</html>
