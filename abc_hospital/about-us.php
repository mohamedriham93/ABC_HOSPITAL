
<?php
session_start();

$loginMessage = '';
if (!isset($_SESSION['role'])) {
    $loginMessage = "You are not logged in. Please login to continue.";
} else {
    $role = $_SESSION['role'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ABC Hospital</title>
    <!-- <link rel="stylesheet" href="./css/style.css"> -->
    <link rel="stylesheet" href="./css/Navigation_Style.css?version=1.0">
    <!-- <link rel="stylesheet" href="./css/header.css"> -->
     <link rel="stylesheet" href="./css/about-us.css">
    <style>
        
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
                            <li><a href="admins/admin_dashboard.php">Admin Dashboard</a></li>
                            <li><a href="Admins/admin_dashboard.php">Manage Users</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php break;
                        case 'Doctor': ?>
                            <li><a href="doctors/doctor_dashboard.php">Doctor Dashboard</a></li>
                            <li><a href="doctors/view_appointments.php">View Appointments</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php break;
                        case 'Receptionist': ?>
                            <li><a href="./receptionists/receptionist_dashboard.php">Reception Dashboard</a></li>
                            <li><a href="./receptionists/view_appointments.php">Manage Appointments</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php break;
                        case 'Patient': ?>
                            <li><a href="Patients/patient_dashboard.php">Patient Dashboard</a></li>
                            <li><a href="Patients/book_appointments.php">Book Appointment</a></li>
                            <li><a href="logout.php">Logout</a></li>
                    <?php break;
                    }
                } else { ?>
                    <li><a href="login.php">Login</a></li>
                <?php } ?>
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
        
        <h1>About Us - ABC Hospital</h1>
    </header>

    <div class="container">
        <section>
            <h2 class="section-title">Welcome to ABC Hospital</h2>
            <p style="color: black;" class="section-description">At ABC Hospital, we are dedicated to providing exceptional care and services to our patients. Our team of experts, cutting-edge technology, and patient-first approach ensure that you receive the best care possible. Learn more about our values, mission, and the people who make it all happen!</p>

            <div class="about-content">
                <div class="about-item">
                    <img class="about-pic" src="./images/about-us/compassion.png" alt="Our Values">
                    <h3>Our Values</h3>
                    <p>We believe in compassion, integrity, and excellence. Our values drive us to deliver the highest standard of healthcare services to our community.</p>
                </div>

                <div class="about-item">
                    <img class="about-pic" src="./images/about-us/mission.png" alt="Our Mission">
                    <h3>Our Mission</h3>
                    <p>Our mission is to improve the health and well-being of our patients through innovative treatments and exceptional patient care.</p>
                </div>

                <div class="about-item">
                    <img class="about-pic" src="./images/about-us/team-of-doctors.png" alt="Meet Our Team">
                    <h3>Meet Our Team</h3>
                    <p>Our dedicated team of doctors, nurses, and healthcare professionals work together to provide the best care possible for every patient.</p>
                </div>

                <div  class="about-item">
                    <img class="about-pic" src="./images/about-us/state-of-the-art.png" alt="Our Technology">
                    <h3>Our Technology</h3>
                    <p>We use state-of-the-art medical equipment and technology to ensure precise diagnostics, effective treatments, and improved outcomes for our patients.</p>
                </div>
            </div>

            <a href="mailto:mohamedriham@gmail.com" class="cta-btn">Contact Us</a>
        </section>

        <div class="email-link">
            <p>If you have any questions, feel free to reach out to us at <strong><a href="mailto:mohamedriham93@gmail.com">mohamedriham93@gmail.com</a></strong></p>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 ABC Hospital. All rights reserved.</p>
    </footer>

</body>
</html>
