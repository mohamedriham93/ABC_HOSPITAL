<?php
session_start();

$loginMessage = '';
if (!isset($_SESSION['role'])) {
    $loginMessage = "You are not logged in. Please login to continue.";
} else {
    $role = $_SESSION['role'];
}
require 'config.php';
$sql = "
    SELECT 
        d.id AS doctor_id,
        d.name, 
        d.age, 
        d.email, 
        d.schedule, 
        d.gender, 
        d.salary, 
        up.picture 
    FROM 
        doctors d
    INNER JOIN 
        users u ON d.id = u.doctor_id
    INNER JOIN 
        user_pictures up ON u.user_id = up.user_id
";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching doctor details: " . $conn->error);
}

$doctors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[$row['doctor_id']] = $row;
    }
}

// Fetch specializations
$specialization_sql = "SELECT doctor_id, specialization_name, experience FROM specialization";
$specialization_result = $conn->query($specialization_sql);

$specializations = [];
if ($specialization_result->num_rows > 0) {
    while ($row = $specialization_result->fetch_assoc()) {
        $specializations[$row['doctor_id']][] = $row;
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABC Hospital - Home</title>
    <link rel="stylesheet" href="./css/Navigation_Style.css?version = 1   " />
    <link rel="stylesheet" href="./css/style.css?version= 1">
    <link rel="stylesheet" href="./css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/doctor-images.css?version=1">


    <!-- <script src="http://192.168.43.13:3000/hook.js"></script> -->

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

    <header>
        <div class="index_box">
            <div class="logo">
                <img class="logo_image" src="images/LOGO2.png" alt="ABC Hospital Logo">
                <h2>Welcome to ABC Hospital</h2>
            </div>
            <?php if (isset($role)) {
                switch ($role) {
                    case 'Admin': ?>
                        <p>As an admin at ABC Hospital, you have full control over the management and coordination of hospital operations.
                            Our intuitive platform gives you the tools to oversee appointments, manage doctor and receptionist schedules, and ensure smooth
                            daily operations. With powerful features designed for efficiency and oversight, you play a crucial role in
                            maintaining the highest standards of care and service at our hospital.
                        </p>
                    <?php break;
                    case 'Doctor': ?>
                        <p>At ABC Hospital, we provide a user-friendly platform designed specifically for healthcare professionals like you.
                            Our system simplifies appointment scheduling, patient management, and communication, allowing you to focus on what matters most
                            delivering exceptional care. Join our network of dedicated doctors and contribute to improving patient outcomes with ease and efficiency.
                        </p><?php break;
                        case 'Receptionist': ?>
                        <p>At ABC Hospital, we empower our receptionists with a streamlined platform to manage appointments, patient inquiries,
                            and doctor schedules efficiently. With user-friendly tools and seamless communication features, you can ensure smooth
                            operations and deliver top-notch customer service to our patients. Join our team and be a key part of creating a welcoming
                            and organized experience for everyone who walks through our doors.
                        </p>

                    <?php break;
                        case 'Patient': ?>
                        <p>Our commitment to excellence ensures that your health and well-being are our top priority.
                            With an easy-to-use online appointment booking system and a team of dedicated healthcare professionals,
                            we make it simple for you to access the care you need.
                            Explore our services and get to know our doctors, who are here to support you every step of the way.
                        </p><?php break;
                    }
                } else { ?>
                <p>Our commitment to excellence ensures that your health and well-being are our top priority.
                    With an easy-to-use online appointment booking system and a team of dedicated healthcare professionals,
                    we make it simple for you to access the care you need.
                    Explore our services and get to know our doctors, who are here to support you every step of the way.
                </p>
            <?php } ?>


            <div class="main_menu">
                <?php if (isset($role)) {
                    switch ($role) {
                        case 'Admin': ?>
                            <button onclick="window.location.href='admins/admin_dashboard.php'"><a href="admins/admin_dashboard.php">Admin Dashboard</a></button>
                        <?php break;
                        case 'Doctor': ?>
                            <button onclick="window.location.href='doctors/doctor_dashboard.php'"><a href="Doctors/doctor_dashboard.php">Doctor Dashboard</a></button>
                        <?php break;
                        case 'Receptionist': ?>
                            <button onclick="window.location.href='receptionists/receptionist_dashboard.php'"><a href="receptionists/receptionist_dashboard.php">Reception Dashboard</a></button>
                        <?php break;
                        case 'Patient': ?>
                            <button onclick="window.location.href='patients/patient_dashboard.php'"><a href="Patients/book_appointments.php">Book an Appointment</a></button>
                    <?php break;
                    }
                } else { ?>
                    <a href=""></a><button onclick="window.location.href='login.php'"><a href="login.php">Login</a></button>
                <?php } ?>
            </div>
        </div>
    </header>

    <main>



        <h3 style="text-align:center; margin-top:20px;">Your Health Matters</h3>
        <p style="text-align:center; font-size:1.1rem;">Book your appointment with our expert doctors today!</p>


    </main>




    <div class="index_box2">
        <h1>Our Doctors</h1>
        <p>Meet our dedicated team of healthcare professionals</p>
        <div class="carousel-container">
            <?php if (!empty($doctors)): ?>
                <?php foreach ($doctors as $doctor_id => $doctor): ?>
                    <div class="doctor-card <?= $doctor_id === array_key_first($doctors) ? 'active' : ''; ?>" data-index="<?= $doctor_id; ?>">
                        <img class="doc-image" src="data:image/jpeg;base64,<?= base64_encode($doctor['picture']); ?>" alt="Doctor Image">

                        <h3><?= htmlspecialchars($doctor['name']); ?></h3>
                        <div class="specializations">
                            <h4>Specializations</h4>
                            <ul>
                                <?php if (!empty($specializations[$doctor_id])): ?>
                                    <?php foreach ($specializations[$doctor_id] as $spec): ?>
                                        <li><?= htmlspecialchars($spec['specialization_name']); ?> (<?= $spec['experience']; ?> years)</li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>No specializations listed.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <p>Age: <?= htmlspecialchars($doctor['age']); ?></p>
                        <p>Email: <?= htmlspecialchars($doctor['email']); ?></p>
                        <p>Gender: <?= htmlspecialchars($doctor['gender']); ?></p>
                        <p>Schedule: <?= htmlspecialchars($doctor['schedule']); ?></p>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No doctors found.</p>
            <?php endif; ?>

            <button class="arrow-button arrow-left">←</button>
            <button class="arrow-button arrow-right">→</button>
        </div>



        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const cards = document.querySelectorAll('.doctor-card');
                let currentIndex = 0;

                const showCard = (index) => {
                    cards.forEach((card, i) => {
                        card.classList.toggle('active', i === index);
                    });
                };

                const handleNavigation = (direction) => {
                    currentIndex = (currentIndex + direction + cards.length) % cards.length;
                    showCard(currentIndex);
                };

                document.querySelector('.arrow-left').addEventListener('click', () => handleNavigation(-1));
                document.querySelector('.arrow-right').addEventListener('click', () => handleNavigation(1));
            });
        </script>
    </div>

    <div class="footer">
        <p>&copy; 2024 ABC Hospital. All Rights Reserved.</p>
    </div>
    <div class="social-media">
        <a href="https://web.facebook.com/mohamed.riham.1485" class="fa fa-facebook"></a>
        <a href="https://www.instagram.com/cherry_boy_1029/" class="fa fa-instagram"></a>
        <a href="https://www.linkedin.com/in/mohamedriham" class="fa fa-linkedin"></a>
    </div>
    <br>

    <p>..</p>
</body>

</html>